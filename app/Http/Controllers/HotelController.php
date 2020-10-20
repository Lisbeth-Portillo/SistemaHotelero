<?php

namespace App\Http\Controllers;
use Illuminate\Contracts\View\View;

use App\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class HotelController extends Controller
{
    public function compose(View $view)
    {
        $hotel = DB::table('hotel')->get();
        $view->with('hotel', $hotel);
    }

      /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Hotel  $hotel
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $id = 1;
        //Se busca la habitacion basado en el parametro id mandado para su edicion
        $hotel = Hotel::findOrFail($id);

        //Cuando se encuentra el registro se compactan los datos y se redireciona a la pagina de edicion
        return view('hotel.edit', compact('hotel'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Hotel  $hotel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //Validacion de los campos a registrar
        $campos=[
            'nombre' => 'required|string',
            'direccion' => 'required|max:60',
            'email' => 'required|email',
            'telefono' => 'required',

        ];
        //La constante mensaje guarda el texto que se mostrará cuando no se cumpla la validacion
        $Mensaje =[
            "required"=>'El :attribute es requerido',
            "email.email"=>'El correo es invalido',
            "direccion.max"=>'La direccion no debe exceder de 60 caracteres',
        ];

        /**
         * Se guardan los datos que se envian del formulario de edicion del registro
         * se pasan los parametros a validar de la varible campos y el mensaje con el texto
         * a mostrar en caso de que no se cumplan la validacion
         */
        $this->validate($request,$campos,$Mensaje);

        //Excepcion del token y del metodo
        $datos=request()->except(['_token','_method']);


        //Condicion para guardar correctamente la foto
        if($request->hasFile('foto')){

            //Busca el empleado del id mandado para el dato antiguo
            $hotel = Hotel::findOrFail(1);

            //Elimina la imagen antigua
            Storage::delete('/public/'.$hotel->foto);

            //Ruta para almacenar la fotografia del empleado
            $datos['foto']= $request->file('foto')->store('uploads','public');
        }

            //Actualiza de acuerdo al id recibido
            Hotel::where('idHotel', '=', 1)->update($datos);

             //Se busca el registro del hotel y se guarda en una variable
             $hotel=Hotel::findOrFail(1);

       //Se redirecciona a la pagina de con el mensaje de confirmacion
       return redirect('hotel/1/edit')->with('Mensaje','Los datos del hotel ha sido actualizados');
    }
}
