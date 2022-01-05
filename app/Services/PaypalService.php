<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Traits\ConsumesExternalServices;

class PayPalService {

    use ConsumesExternalServices;

    protected $baseUri;
    protected $clientId;
    protected $clientSecret;


    // Inicializar valores del servicio de Paypal
    public function __construct(){
        $this->baseUri = config('services.paypal.base_uri');
        $this->clientId = config('services.paypal.client_id');
        $this->clientSecret = config('services.paypal.client_secret');
    }

    // Indicando que los valores pasan por referencia y se reflejan inmediatamente
    public function resolveAuthorization(&$queryParams, &$formParams, &$headers){

        // resolver access token
        $headers['Authorization'] = $this->resolveAccesToken();

    }

    // Decodifica la respuesta del json
    public function decodeResponse($response){
        return json_decode($response);
    }

    // Funcion Para formar la cabecera de Authorization con el clientId y el clientSecret
    // Referencia: https://developer.paypal.com/docs/api/reference/get-an-access-token/
    public function resolveAccesToken(){
        $credentials = base64_encode("{$this->clientId}:{$this->clientSecret}");

        return "Basic {$credentials}";
    }

    // Inicio del proceso de pago de paypal
    public function handlePayment(Request $request){
        //se genera una orden llamando a la funcion createOrder y enviando el monto y la divisa
        $order = $this->createOrder($request->value, $request->currency);

        // Genera una coleecion de los links generados por la orden
        $orderLinks = collect($order->links);
        // se guarda el objeto approve en links
        $approve = $orderLinks->where('rel', 'approve')->first();
        // se guarda en session el ID generado al crear la orden
        session()->put('approvalId', $order->id);
        // te redirecciona al checkout de Paypal
        return redirect($approve->href);
    }

    // Validacion de pago por plataforma Paypal
    public function handleApproval(){

        // se verifica que exista la session en ID de aprovación
        if(session()->has('approvalId')){
            // Se obtiene el id de la session
            $approvalId = session()->get('approvalId');
            // se manda llamar la funcion para capturar y validar el pago
            $payment = $this->capturePayment($approvalId);

            /// Se obtienen los datos del pago que te devuelve Paypal
            // https://developer.paypal.com/api/orders/v2/#orders-capture-response
            $name = $payment->payer->name->given_name;
            $payment = $payment->purchase_units[0]->payments->captures[0]->amount;
            $amount = $payment->value;
            $currency = $payment->currency_code;
            // Se redirecciona al home con el mensaje de success.
            return redirect('home')->withSuccess(['payment' => "Thanks, {$name}. We received your {$amount} {$currency} payment."]);
        }

        // Se envia el mensaje de error redireccionado al home.
        return redirect('home')->withErrors('We were unable to confirm your payment. Try again, please');
    }

    // Usando el metodo reutilizable Trait App\Traits\ConsumeExternalServices.php crea una orden enviando los parametros requeridos como:
    // Method HTTP, la URL destino, queryParams (default: vacio),  los header requeridos por paypal (default: vacio), y si es Json (Boolean)
    // Referencia: https://developer.paypal.com/api/orders/v2
    public function createOrder($value, $currency){
        return $this->makeRequest(
            'POST',
            '/v2/checkout/orders',
            [],
            [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    0 => [
                        'amount' => [
                            'currency_code' => strtoupper($currency),
                            'value' => round($value * $factor = $this->resolveFactor($currency)) / $factor
                            ]
                            ]
                        ],
                        'application_context' => [
                            'brand_name' => config('app.name'),
                            'shipping_preference' => 'NO_SHIPPING',
                            'user_action' => 'PAY_NOW',
                            'return_url' => route('payments.approval'),
                            'cancel_url' => route('payments.cancelled')
                            ]
                        ],
                        [],
                        true
                    );
    }

    // Para capturar el pago se usa el trait reutilizable nuevamente
    // Method HTTP, la URL destino, queryParams (default: vacio),  los header requeridos por paypal (default: vacio), y si es Json (Boolean)
    // Referencia: https://developer.paypal.com/api/orders/v2/#orders_capture
    public function capturePayment($approvalId){
        return $this->makeRequest(
            'POST',
            "/v2/checkout/orders/{$approvalId}/capture",
            [],
            [],
            [
                'Content-Type' => 'application/json'
                ]
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

