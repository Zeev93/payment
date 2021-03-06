<label for="" class="mt-3">Card Details: </label>
<div id="cardElement">
</div>
<small class="form-text text-muted" id="cardErrors" role="alert"></small>
<input type="hidden" name="payment_method" id="paymentMethod">

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>

<script>
    const appearance = {
            theme: 'night',
            labels: 'floating'
        };

    // Se Inicializa el Servicio Stripe via Javascript y se Configura
    const stripe = Stripe("{{ config('services.stripe.key') }}");
    const elements = stripe.elements({ locale: 'en', appearance});
    const cardElement = elements.create('card');

    // Se genera el formato de tarjeta con Nombre, Fecha de vencimiento y CP o ZIP Code
    cardElement.mount('#cardElement');


    //Formulario

    const form = document.getElementById('paymentForm');
    const payButton = document.getElementById('payButton');

    payButton.addEventListener('click', async(e) => {
        if(form.elements.payment_platform.value === "{{ $platform->id }}"){
                e.preventDefault();

                // se crea el metodo de pago con los valores que stripe te facilita y los datos del usuario activo con la session de Laravel
                const { paymentMethod, error } = await stripe.createPaymentMethod(
                    'card', cardElement, {
                        billing_details: {
                            "name": "{{ auth()->user()->name }}",
                            "email": "{{ auth()->user()->email }}"
                        }
                    }
                );

                if(error){
                    // Si existe un error con los datos te sobresalta errores
                    const displayError = document.getElementById('cardErrors');
                    displayError.textContext = text.message;
                }else{
                    // sino muestra ningun error se genera el token y se envia el formulario al controller
                    const tokenInput = document.getElementById('paymentMethod');
                    tokenInput.value = paymentMethod.id
                    form.submit()
                }
        }
    });
</script>
@endpush


