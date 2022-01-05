# Prueba de plataforma de pasarela de pagos
Prueba de pasarela de pagos configuración básica de un formulario para probar los diferentes métodos de pago, integrados.
    
1. [Paypal](https://www.paypal.com/us/home).
    - [Documentación Paypal](https://developer.paypal.com/api/rest)
2. [Stripe](https://stripe.com/es)
    - [Documentación Stripe](https://stripe.com/docs/api)
3. [MercadoPago](https://www.mercadopago.com.mx)
    - [Documentación MercadoPago](https://www.mercadopago.com.mx/developers/es/reference)
4. [CurrencyConverter](https://www.currencyconverterapi.com/)
    - [Documentación CurrencyConverter](https://www.currencyconverterapi.com/docs)

Referencia para futuros proyectos y configuración básica, reutilizable.


## Archivos Utilizados
- .env
- App\Services
- App\Resolvers
- App\Traits
- App\Http\Controllers\PaymentController.php
- Config\services.php
- Database
- Resources\Views



### Traits
Servicio de consumo externo reutilizable para las diferentes plataformas.

### Resolver
Selecciona la plataforma definida por el usuario para realizar su pago y manda a llamar el servicio requerido

### Services 
Servicios para enviar el pago por plataforma, con sus respectivos requerimentos e información de funcionalidad.






