
 <!doctype html>
 <html lang="en">
 <head>
     <meta charset="utf-8">
     <meta name="viewport" content="width=device-width, initial-scale=1">
     {{-- Obtencion de los datos del hotel--}}
        @foreach ($hotel as $hotels)
     <link rel="shortcut icon" href="{{ asset('/'.$hotels->foto) }}" type="image/x-icon">

     {{--  Utilizacion del css de boostrap y del estilo del login   --}}
     <link href="{{ asset('css/app.css') }}" rel="stylesheet">
     <link href="{{ asset('css/login.css') }}" rel="stylesheet">

 </head>
 <body>
     <div class="row m-0 h-100">
         <div class="col p-0 text-center d-flex justify-content-center align-items-center display-none" id="cont1">
             <img src="{{asset('/'.$hotels->foto)}}" class="w-800">
             @endforeach
            </div>
         <div class="col p-0 bg-custom d-flex justify-content-center align-items-center flex-column w-100">
            <form class="w-75" method="POST" action="{{ route('login') }}">
              {{-- Token de verificacion del usuario autenticado para realizar la solicitud  --}}
                @csrf
                <h1 class="text-center" style="color: white">Login </h1>
                 <div class="mb-3">
                     <label for="email" class="form-label">{{ __('E-Mail') }}</label>
                     {{-- Validacion de la email, al se incorrecto los datos dejar el email escrito--}}
                     <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}"  required autocomplete="email" autofocus placeholder="E-mail">

                     {{-- Validacion del error y uso de una alerta --}}
                     @error('email')
                     <span style="color:black;" role="alert">
                         <strong>Los datos que ingreso son inválidos</strong>
                            </span>
                        @enderror
                </div>

                 <div class="mb-3">
                     <label for="password" class="form-label">{{ __('Contraseña') }}</label>
                     {{-- Validacion de la contrasena --}}
                     <input  id="password" type="password" class="form-control  @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Contraseña">

                     {{-- Validacion del error y uso de una alerta --}}
                     @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>Los datos que ingreso son inválidos</strong>
                        </span>
                     @enderror

                </div>
                 <button type="submit" class="btn btn-custom btn-lg btn-block mt-3">{{ __('Ingresar') }}</button>
             </form>
         </div>
     </div>
 </body>

 </html>
