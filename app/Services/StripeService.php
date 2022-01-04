<?php


namespace App\Services;

use Illuminate\Http\Request;
use App\Traits\ConsumesExternalServices;

class StripeService {

    use ConsumesExternalServices;

    protected $baseUri;
    protected $secret;
    protected $key;


    // Inicializar valores del servucui Struoe
    public function __construct(){
        $this->baseUri = config('services.stripe.base_uri');
        $this->secret = config('services.stripe.secret');
        $this->key = config('services.stripe.key');
    }

    // Indicando que los valores pasan por referencia y se reflejan inmediatamente
    public function resolveAuthorization(&$queryParams, &$formParams, &$headers){
        // Resuelve el token de autorización
        $headers['Authorization'] = $this->resolveAccesToken();
    }

    public function decodeResponse($response){
        return json_decode($response);
    }

    // Funcion para generar la cabecera de Authorization con la clave secret de Stripe
    // Referencia https://stripe.com/docs/api/authentication
    public function resolveAccesToken(){
        return "Bearer {$this->secret}";
    }

    // Inicia el proceso de pago de Stripe
    public function handlePayment(Request $request){
        // Se valida que tenga un metodo de pago
        $request->validate([
            'payment_method' => 'required'
        ]);

        // se crea el intento de pago con la funcion createIntent se envia el monto, divisa y metodo de pago
        $intent = $this->createIntent($request->value, $request->currency, $request->payment_method);
        // Se mete en session el ID del Intent de pago
        session()->put('paymentIntentId', $intent->id);
        // Te redirecciona para aprobacion del pago.
        return redirect()->route('payments.approval');
    }

    // Validación de pago por plataforma Stripe
    public function handleApproval(){
        // Verificar si existe el ID de intencion de Pago
        if(session()->has('paymentIntentId')){
            // Se obtiene el ID de la intención de pago
            $paymentIntentId = session()->get('paymentIntentId');
            // Se manda a llamar la funcion confirmPayment para confirmar el pago  realizado
            $confirmation = $this->confirmPayment($paymentIntentId);

            // En caso de requerir 3D secure realiza la validación y confirmación redireccionando a nueva vista para el 3D secure
            // enviando el clientSecret
            if($confirmation->status === 'requires_action'){
                $clientSecret = $confirmation->client_secret;

                return view('stripe.3d-secure')->with([
                    'clientSecret' => $clientSecret
                ]);
            }

            // En caso de no requererir nada adicional valida el success y envia los datos que te devuelve al home
            // Referencia: https://stripe.com/docs/api/payment_intents/capture
            if($confirmation->status === 'succeeded'){
                $name = $confirmation->charges->data[0]->billing_details->name;
                $currency = strtoupper($confirmation->currency);
                $amount = $confirmation->amount / $this->resolveFactor($currency);
                // Redirecciona al home con mensaje de success
                return redirect()->route('home')->withSuccess(['payment' => "Thanks, {$name}. We received your {$amount} {$currency} payment."]);
            }


        }
        // Se envia el mensaje de error redireccionado al home
        return redirect()->route('home')->withErrors('We were unable to confirm your payment. Try again, please');
    }

    // Se crea la intencion de pago y se realiza la peticion usando el componente reutilizable Trait App\Traits\ConsumeExternalServices.php
    // Method HTTP, la URL destino, queryParams (default: vacio),  los header requeridos por paypal (default: vacio), y si es Json (Boolean)
    // Referencia: https://stripe.com/docs/api/payment_intents/create
    public function createIntent($value, $currency, $paymentMethod){
        return $this->makeRequest(
            'POST',
            '/v1/payment_intents',
            [],
            [
                'amount' => round($value * $this->resolveFactor($currency)),
                'currency' => strtolower($currency),
                'payment_method' => $paymentMethod,
                'confirmation_method' => 'manual'

            ]
            );
    }

    // Para confirmar el pago se vuelve a utilizar el Trait ConsumeExternalService.php
    // Method HTTP, la URL destino, queryParams (default: vacio),  los header requeridos por paypal (default: vacio), y si es Json (Boolean)
    // Referencia: https://stripe.com/docs/api/payment_intents/confirm
    public function confirmPayment($paymentIntentId){
        return $this->makeRequest(
            'POST',
            "/v1/payment_intents/${paymentIntentId}/confirm"
        );
    }

    // Esta funcion redondea los valores para monedas que no admiten decimales como el Yen japones, en caso de admitir mas divisas así
    // Se pueden agregar al array o bien crear un valor en la base de datos.
    public function resolveFactor($currency){
        $zeroDecimalCurrencies = ['JPY'];
        if(in_array(strtoupper($currency), $zeroDecimalCurrencies)){
            return 1;
        }
        return 100;
    }
}

