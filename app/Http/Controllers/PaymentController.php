<?php

namespace App\Http\Controllers;

use App\Resolvers\PaymentPlatformResolver;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $paymentPlatformResolver;
    public function __construct(PaymentPlatformResolver $paymentPlatformResolver)
    {
        $this->middleware('auth');
        $this->paymentPlatformResolver = $paymentPlatformResolver;
    }

    public function pay(Request $request){

        // Validacion de el monto, el tipo de divisa y la plataform
            $rules = [
                'value' => ['required', 'numeric', 'min:5'],
                'currency' => ['required', 'exists:currencies,iso'],
                'payment_platform' => ['required', 'exists:payment_platforms,id']
            ];
            $request->validate($rules);

            // se envia la información al resolver para trabajar con la plataforma seleccionada
            $paymentPlatform = $this->paymentPlatformResolver->resolveService($request->payment_platform);

            // se guarda en session el id de la plataforma a usar
            session()->put('paymentPlatformId', $request->payment_platform);

            // se inician el prceso de pago usando los Servicios de la plataforma seleccionada
            return $paymentPlatform->handlePayment($request);
    }

    // Si la peticion de pago por la plataforma es correcta se realizara una validación del cargo.
    public function approval(){
        // se valida si esta el ID de la plataforma usada en la session
        if(session()->has('paymentPlatformId')){
            // se vuelve a usar el Resolver para encontrar la plataforma usada que coincida con el valor de la session y se consume el servicio
            $paymentPlatform = $this->paymentPlatformResolver->resolveService(session()->get('paymentPlatformId'));
            // se manda llamar la funcion handle approval para validar el pago.
            return $paymentPlatform->handleApproval();
        }

        return redirect()->route('home')->withErrors("We cannot retrieve your payment platform. Try again, please.");
    }

    public function cancelled(){
        return redirect()->route('home')->withErrors('You cancelled the payment');
    }
}
