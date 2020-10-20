<?php

namespace App\Http\Controllers;

use App\Nivel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class NivelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Muestra de la informacion del empleado con paginacion
        $datos = DB::table('nivel')->paginate(5);
        //Redireccion a la vista principal y envio de los datos compactados
        return view('nivel.index', ['nivel' => $datos]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //Redireccion a la vista para crear
        return view('nivel.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Validacion de los campos
        $campos = [
            'nombre' => 'required|unique:nivel',
            'numeroNivel' => 'required|unique:nivel',
            'estado' => 'required',
        ];
        //La variable mensaje guarda el texto que se mostrará cuando no se cumpla la validacion
        $Mensaje = [
            "required" => 'El campo :attribute es requerida',
            "nombre.unique" => 'El nombre de nivel ingresado ya existe',
            "numeroNivel.unique" => 'El numero de nivel ingresado ya existe',
        ];
        /**
         * Se guardan los datos que se envian del formulario de creacion de registros
         * se pasan los parametros a validar de la varible campos y el mensaje con el texto
         * a mostrar en caso de que no se cumplan la validacion
         */
        $this->validate($request, $campos, $Mensaje);


        //Excepcion del campo para la validacion y seguridad de los datos
        $token = request()->except('_token');

        //Se guardan los datos obtenidos
        $datos = $request->all();

        //Insersion de los datos hacia la base de datos
        Nivel::insert($token, $datos);

        //Redireccion a la vista principal y se envia el mensaje de confirmacion
        return redirect('nivel')->with('Mensaje', 'El nivel ha sido creado exitosamente');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Nivel  $nivel
     * @return \Illuminate\Http\Response
     */
    public function show(Nivel $nivel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Nivel  $nivel
     * @return \Illuminate\Http\Response
     */
    public function edit($idNivel)
    {
        //Busca el nivel del id mandado para su edicion
        $nivel = Nivel::findOrFail($idNivel);

        //Cuando se encuentra el registro se compactan los datos y se redireciona a la pagina de edicion
        return view('nivel.edit', compact('nivel'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Nivel  $nivel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idNivel)
    {
        //Validacion de los campos a registrar
        $campos = [
            'nombre' => 'required|unique:nivel,nombre,' . $idNivel . ',idNivel',
            'numeroNivel' => 'required|unique:nivel,numeroNivel,' . $idNivel . ',idNivel',
            'estado' => 'required',
        ];
        //La variable mensaje guarda el texto que se mostrará cuando no se cumpla la validacion
        $Mensaje = [
            "required" => 'El campo :attribute es requerida',
            "nombre.unique" => 'El nombre de nivel ingresado ya existe',
            "numeroNivel.unique" => 'El numero de nivel ingresado ya existe',
        ];
        /**
         * Se guardan los datos que se envian del formulario de edicion del registro
         * se pasan los parametros a validar de la varible campos y el mensaje con el texto
         * a mostrar en caso de que no se cumplan la validacion
         */
        $this->validate($request, $campos, $Mensaje);

        //Excepcion del token y del metodo
        $datos = request()->except(['_token', '_method']);

        //Actualizacion del registro mediante el parametro id recibido
        Nivel::where('idNivel', '=', $idNivel)->update($datos);

        //Se redirecciona a la pagina de principal con el mensaje de confirmacion
        return redirect('nivel')->with('Mensaje', 'La nivel ha sido actualizada exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Nivel  $nivel
     * @return \Illuminate\Http\Response
     */
    public function destroy($idNivel)
    {
        //Solo tendra acceso el usuario administrador
        if (Auth::user()->rol_id == 1) {

            Nivel::destroy($idNivel);

            return redirect('nivel')->with('Mensaje', 'El nivel ha sido eliminado');
        }
        return redirect('nivel')->with('Mensaje', 'No esta autorizado para eliminar');
    }
}
