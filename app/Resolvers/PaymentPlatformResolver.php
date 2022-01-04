<?php


namespace App\Resolvers;

use App\Models\PaymentPlatform;

class PaymentPlatformResolver {

    protected $paymentPlatforms;

    // Inicializa el array de plataformas con las que tenemos registradas en nuestra base de datos.
    public function __construct(){
        $this->paymentPlatforms = PaymentPlatform::all();
    }


    public function resolveService($paymentPlatformId){

        // Busca la plataforma que enviamos en el formulario y compara con la base de datos obteniendo el nombre
        $name = strtolower($this->paymentPlatforms->firstWhere('id', $paymentPlatformId)->name);

        // configura la plataforma de pagos en base al nombre y con los servicios registrados en config/services.php
        $service = config("services.{$name}.class");

        //si existe un servicio, lo ejecuta y llama al servicio correspondiente en la carpeta App\Services\ PaypalService.php o StripeService.php
        if($service) {
            return resolve($service);
        }

        // Si el servicio esta registrado en la bd pero no en la aplicación genera un error y viceversa.
        throw new \Exception("The selected payment method plataform is not in the configuration");
    }
}
