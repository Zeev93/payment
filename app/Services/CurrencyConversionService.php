<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Traits\ConsumesExternalServices;

class CurrencyConversionService {

    use ConsumesExternalServices;

    protected $baseUri;
    protected $apiKey;


    // Inicializar valores del servicio de CurrencyConversionAPI
    public function __construct(){
        $this->baseUri = config('services.currency_conversion.base_uri');
        $this->apiKey = config('services.currency_conversion.api_key');
    }

    // Indicando que los valores pasan por referencia y se reflejan inmediatamente
    public function resolveAuthorization(&$queryParams, &$formParams, &$headers){
        $queryParams['apiKey'] = $this->resolveAccesToken();
    }

    // Decodifica la respuesta del json
    public function decodeResponse($response){
        return json_decode($response);
    }

    public function resolveAccesToken(){
        return $this->apiKey;
    }

    public function convertCurrency($from, $to){
        $response = $this->makeRequest(
            'GET',
            '/api/v7/convert',
            [
                'q' => "{$from}_{$to}",
                'compact' => 'ultra'
            ],
        );
        return $response->{strtoupper("{$from}_{$to}")};
    }


}

