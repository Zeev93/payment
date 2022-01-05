<label for="" class="mt-3">Card Details: </label>
<div class="row">
    <div class="col-5">
        <input type="number" class="form-control mb-3" id="cardNumber" data-checkout="cardNumber" placeholder="Card Number">
    </div>
    <div class="col-2">
        <input type="text" class="form-control mb-3" id="securityCode" data-checkout="securityCode" placeholder="CVC">
    </div>
    <div class="col-1"></div>
    <div class="col-5">
        <input type="text" class="form-control mb-3" id="cardExpirationMonth" data-checkout="cardExpirationMonth" placeholder="MM">
    </div>
    <div class="col-5">
        <input type="text" class="form-control mb-3" id="cardExpirationYear" data-checkout="cardExpirationYear" placeholder="YY">
    </div>
</div>
<div class="row">
    <div class="col-5">
        <input type="text" class="form-control mb-3" id="cardholderName" data-checkout="cardholderName" placeholder="Your Name">
    </div>
    <div class="col-5">
        <input type="email" class="form-control mb-3" id="cardholderEmail" data-checkout="cardholderEmail" placeholder="email@example.com" name="email">
    </div>
</div>
{{-- <div class="row">
    <div class="col-2">
        <select class="form-select mb-3" data-checkout="docType"></select>
    </div>
    <div class="col-3">
        <input type="text" class="form-control mb-3" data-checkout="docNumber" placeholder="Document">
    </div>
</div> --}}
<div class="row">
    <div class="col">
        <small class="form-text text-muted" role="alert">Your payment will be converted to {{ strtoupper(config('services.mercadopago.base_currency')) }}</small>
    </div>
</div>
<div class="row">
    <div class="col">
        <small class="form-text text-danger" role="alert" id="paymentErrors"></small>
    </div>
</div>
<input type="hidden" name="card_network" id="cardNetwork">
<input type="hidden" name="card_token" id="cardToken">


@push('scripts')
{{-- https://www.mercadopago.com.co/developers/es/guides/online-payments/checkout-api/v1/receiving-payment-by-card --}}
<script src="https://secure.mlstatic.com/sdk/javascript/v1/mercadopago.js"></script>

<script>
    // Inicializar mercado Pago y obtener los
    const mercadoPago = window.Mercadopago
    mercadoPago.setPublishableKey('{{ config('services.mercadopago.key') }}');
</script>

<script>
    function setCardNetwork(){
        let cardNumber = document.getElementById("cardNumber").value;
        cardNumber = cardNumber.replace(/ /g, '')
        if(cardNumber.length >= 6){
            mercadoPago.getPaymentMethod(
             {"bin" : cardNumber.substring(0,6)},
             function (status, response) {
                document.getElementById("cardNetwork").value = response[0].id;
                mercardoPagoForm.submit();
             }
        );
        }
     }
</script>

<script>
    const mercardoPagoForm = document.getElementById("paymentForm");
    mercardoPagoForm.addEventListener('submit', function(e) {
        if(form.elements.payment_platform.value === "{{ $platform->id }}"){
                e.preventDefault();
                mercadoPago.createToken(mercardoPagoForm, function(status, response){
                    if(status != 200 && status!= 201 ){
                        const errors = document.getElementById("paymentErrors");
                        errors.textContent = response.cause[0].description;
                    }else {
                        const cardToken = document.getElementById("cardToken")
                        setCardNetwork()
                        cardToken.value = response.id;
                    }
                })
        }
    });
</script>
@endpush


