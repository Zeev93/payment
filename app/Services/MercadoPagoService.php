<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Traits\ConsumesExternalServices;

class MercadoPagoService {

    use ConsumesExternalServices;

    protected $baseUri;
    protected $key;
    protected $secret;
    protected $baseCurrency;


    // Inicializar valores del servicio de MercadoPago
    public function __construct(){
        $this->baseUri = config('services.mercadopago.base_uri');
        $this->key = config('services.mercadopago.key');
        $this->secret = config('services.mercadopago.secret');
        $this->baseCurrency = config('services.mercadopago.base_currency');
    }

    // Indicando que los valores pasan por referencia y se reflejan inmediatamente
    public function resolveAuthorization(&$queryParams, &$formParams, &$headers){

    }

    // Decodifica la respuesta del json
    public function decodeResponse($response){
        return json_decode($response);
    }

    // Funcion Para formar la cabecera de Authorization con el clientId y el clientSecret
    // Referencia: https://developer.paypal.com/docs/api/reference/get-an-access-token/
    public function resolveAccesToken(){

    }

    // Inicio del proceso de pago de paypal
    public function handlePayment(Request $request){

    }


    // Esta funcion redondea los valores para monedas que no admiten decimales como el Yen japones, en caso de admitir mas divisas así
    // Se pueden agregar al array o bien crear un valor en la base de datos.
    public function resolveFactor($currency){
    }
}

