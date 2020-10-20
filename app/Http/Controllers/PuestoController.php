<?php

namespace App\Http\Controllers;

use App\Puesto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PuestoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Obtencion de parametro search para realizacion de busqueda y eliminacion de los espacios en blanco
        $keyword = trim($request->get('search'));
        $paginacion = 5;

        /**
         * Si se realiza una busqueda
         * Se filtran en base a la palabra la consulta
         * de lo contrario se muestran todo los datos
         */

        if ($request) {
            $datos = DB::table('puesto')
                ->orWhere('puesto', 'LIKE', "%$keyword%")
                ->orWhere('salario', 'LIKE', "%$keyword%")
                ->paginate($paginacion);
            /*Redireccion a la vista principal
            * Se envia a traves de la variable la consulta retornadas y
            * el valor buscado para la siguiente vista
            */
            return view('puesto.index', ['puesto' => $datos, 'search' => $keyword]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //Redireccion a la vista para crear
        return view('puesto.create');
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
            'puesto' => 'required|string|unique:puesto',
            'salario' => 'required|integer|min:2742',
        ];
        //La variable mensaje guarda el texto que se mostrará cuando no se cumpla la validacion
        $Mensaje = [
            "required" => 'El campo :attribute es requerida',
            "puesto.unique" => 'El puesto ya existe',
            "salario.min" => 'El salario minimo es de Q.2742',
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
        Puesto::insert($token, $datos);

        //Redireccion a la vista principal y se envia el mensaje de confirmacion
        return redirect('puesto')->with('Mensaje', 'El puesto ha sido creado exitosamente');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Puesto  $puesto
     * @return \Illuminate\Http\Response
     */
    public function show(Puesto $puesto)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Puesto  $puesto
     * @return \Illuminate\Http\Response
     */
    public function edit($idPuesto)
    {
         //Busca el empleado del id mandado para su edicion
         $puesto=Puesto::findOrFail($idPuesto);

         //Cuando se encuentra el registro se compactan los datos y se redireciona a la pagina de edicion
         return view('puesto.edit', compact('puesto'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Puesto  $puesto
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idPuesto)
    {
         //Validacion de los campos a registrar
         $campos=[
            'puesto' => 'required|string',
            'salario' => 'required|integer|min:2742',
        ];
        //La variable mensaje guarda el texto que se mostrará cuando no se cumpla la validacion
        $Mensaje = [
            "required" => 'El campo :attribute es requerida',
            "salario.min" => 'El salario minimo es de Q.2742',
        ];
        /**
         * Se guardan los datos que se envian del formulario de edicion del registro
         * se pasan los parametros a validar de la varible campos y el mensaje con el texto
         * a mostrar en caso de que no se cumplan la validacion
         */
        $this->validate($request,$campos,$Mensaje);

        //Excepcion del token y del metodo
        $datos=request()->except(['_token','_method']);

       //Actualizacion del registro mediante el parametro id recibido
       Puesto::where('idPuesto', '=', $idPuesto)->update($datos);

       //Se redirecciona a la pagina de principal con el mensaje de confirmacion
       return redirect('puesto')->with('Mensaje','La puesto ha sido actualizada exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Puesto  $puesto
     * @return \Illuminate\Http\Response
     */
    public function destroy($idPuesto)
    {
         //Solo tendra acceso el usuario administrador
         if(Auth::user()->rol_id == 1){

            Puesto::destroy($idPuesto);

            return redirect('puesto')->with('Mensaje','El puesto ha sido eliminado');
            }
           return redirect('puesto');
    }
}
