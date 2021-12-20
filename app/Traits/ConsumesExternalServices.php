<?php

namespace App\Traits;

use GuzzleHttp\Client;

trait ConsumesExternalServices
{
    public function makeRequest($method, $requestUrl, $queryParams = [], $formParams = [], $headers = [], $isJsonRequest = false){

        // Se crea el cliente HttpGuzzle
        $client = new Client ([
            // Identificar baseUri para calcular a Guzzle desde donde construir la URL
            'base_uri' => $this->baseUri,
            'verify' => false

        ]);

        // Validar si existe el metodo
        if( method_exists($this, 'resolveAuthorization')){
            //  Capacidad de autorizar peticiones y decodificar las respuestas
            $this->resolveAuthorization($queryParams, $formParams, $headers);
        }

        // Enviar peticion desde el cliente, methodo, la url destino y parametros ( si es Json, headers y queryParams )
        $response = $client->request($method, $requestUrl, [
            $isJsonRequest ? 'json' : 'form_params' => $formParams,
            'headers' => $headers,
            'query' => $queryParams
        ]);

        // Obtener cuerpo de respuesta y unicamente el contenido
        $response = $response->getBody()->getContents();

        // Validar si existe el metodo
        if (method_exists($this, 'decodeResponse')){
            // realizar decodificar de una respuesta
            $response = $this->decodeResponse($response);
        }


        return $response;
    }
}
