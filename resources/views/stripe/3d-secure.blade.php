@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Complete the security steps</div>

                <div class="card-body">
                   <p>You need to follow some steps with your bank to complete your payment. Let's do it. {{$clientSecret}}</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>

<script>
// Para clientes con 3D secure se realizara una validación extra
// Se configura el servicio de Stripe
// Se realiza una validación que provee Stripe y eventualmente realiza el la redicreccion para la aprovación
    const stripe = Stripe("{{ config('services.stripe.key') }}");
    stripe.handleCardAction("{{$clientSecret}}")
          .then( function(result){
                if(result.error){
                    window.location.replace("{{ route('payments.cancelled') }}");
                }else{
                    window.location.replace("{{ route('payments.approval') }}");
                }
          })
</script>

@endpush

@endsection
