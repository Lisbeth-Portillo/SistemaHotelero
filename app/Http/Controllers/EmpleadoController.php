<?php

namespace App\Http\Controllers;

use App\Empleado;
use App\Persona;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EmpleadoController extends Controller
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

        if ($keyword) {
            $datos = DB::table('empleado')
                ->join('persona', 'persona.idPersona', '=', 'empleado.persona_id')
                ->join('puesto', 'puesto.idPuesto', '=', 'empleado.puesto_id')
                ->select('persona.*', 'puesto.*', 'empleado.*')
                ->orWhere('persona.nombres', 'LIKE', "%$keyword%")
                ->orWhere('persona.apellidos', 'LIKE', "%$keyword%")
                ->orWhere('persona.identificacion', 'LIKE', "%$keyword%")
                ->orWhere('puesto.puesto', 'LIKE', "%$keyword%")
                ->orWhere('puesto.salario', 'LIKE', "%$keyword%")
                ->orWhere('empleado.profesion', 'LIKE', "%$keyword%")
                ->orWhere('empleado.direccion', 'LIKE', "%$keyword%")
                ->orWhere('empleado.fechaN', 'LIKE', "%$keyword%")
                ->paginate($paginacion);
            /*Redireccion a la vista principal
             * Se envia a traves de la variable la consulta retornadas y
             * el valor buscado para la siguiente vista
             */
            return view('empleado.index', ['empleado' => $datos, 'search' => $keyword]);
        }
        $datos2 = DB::table('empleado')
        ->join('persona', 'persona.idPersona', '=', 'empleado.persona_id')
        ->join('puesto', 'puesto.idPuesto', '=', 'empleado.puesto_id')
        ->select('persona.*', 'puesto.*', 'empleado.*')
        ->where('estado', '0')
        ->paginate($paginacion);

        return view('empleado.index', ['empleado' => $datos2]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //Obtiene los datos de las tablas relacionadas
        $dato1 = DB::table('persona')->get();
        $dato2 = DB::table('puesto')->get();

        /**
         * Redirecciona ala vista para la creacion y envia los de las
         * tablas foraneas a traves de la variable
         */
        return view('empleado.create', ['persona' => $dato1, 'puesto' => $dato2]);
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
            'nombres' => 'required',
            'apellidos' => 'required',
            'identificacion' => 'required|unique:persona',
            'puesto_id' => 'required',
            'profesion' => 'required',
            'direccion' => 'required',
            'telefono' => 'required|min:8',
            'fechaN' => 'required|date',
        ];
        //La variable mensaje guarda el texto que se mostrará cuando no se cumpla la validacion
        $Mensaje = [
            "required" => 'El campo :attribute es requerido',
            "puesto_id.required" => 'Este atributo es requerido',
            "identificacion.unique" => 'El DPI ingresado ya existe',
        ];

        /**
         * Se guardan los datos que se envian del formulario de creacion de registros
         * se pasan los parametros a validar de la varible campos y el mensaje con el texto
         * a mostrar en caso de que no se cumplan la validacion
         */
        $this->validate($request, $campos, $Mensaje);

        //Ingreso de los datos correspodiente de la tabla persona y almacenamiento del id creado
        $idpersona = DB::table('persona')->insertGetId([
            'nombres' => $request['nombres'],
            'apellidos' => $request['apellidos'],
            'identificacion' => $request['identificacion'],
            'identificacion_id' => 1,
        ]);

        //Se inserta en la tabla empleado y se exceptua el token
        Empleado::insert([
            'persona_id' => $idpersona,
            'puesto_id' => $request['puesto_id'],
            'profesion' => $request['profesion'],
            'direccion' => $request['direccion'],
            'telefono' => $request['telefono'],
            'fechaN' => $request['fechaN'],
        ]);



        //Se redirecciona a la vista principal de usuarios con el mensaje correspondiente
        return redirect('empleado')->with('Mensaje', 'El empleado se ha creado exitosamente');
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Empleado  $empleado
     * @return \Illuminate\Http\Response
     */
    public function show(Empleado $empleado)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Empleado  $empleado
     * @return \Illuminate\Http\Response
     */
    public function edit($idEmpleado)
    {
        //Se busca el usuario basado en el parametro id mandado para su edicion
        $empleado = Empleado::findOrFail($idEmpleado);

        //Se guardan los datos obtenidos de la tabla de la llave foranea para luego mandarlos
        $dato = DB::table('persona')
        ->Where('idPersona', '=', $empleado->persona_id)
        ->get();

        $dato2 = DB::table('puesto')->get();

        //Cuando se encuentra el registro se compactan los datos y se redireciona a la pagina de edicion
        return view('empleado.edit', ['person' => $dato, 'puesto' => $dato2], compact('empleado'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Empleado  $empleado
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idEmpleado)
    {
        //Se busca el usuario basado en el parametro id
        $empleado = Empleado::findOrFail($idEmpleado);
        $idpersona = $empleado->persona_id;

        //Validacion de los campos
        $campos = [
            'nombres' => 'required',
            'apellidos' => 'required',
            'identificacion' => 'required|unique:persona,identificacion,'.$idpersona.',idPersona',
            'puesto_id' => 'required',
            'profesion' => 'required',
            'direccion' => 'required',
            'telefono' => 'required|min:8',
            'fechaN' => 'required|date',
        ];
        //La variable mensaje guarda el texto que se mostrará cuando no se cumpla la validacion
        $Mensaje = [
            "required" => 'El campo :attribute es requerido',
            "puesto_id.required" => 'Este atributo es requerido',
            "identificacion.unique" => 'El DPI ingresado ya existe',
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
        Persona::where('idPersona', '=', $idpersona)
            ->update(array(
                'nombres' => $request['nombres'],
                'apellidos' => $request['apellidos'],
                'identificacion' => $request['identificacion'],
            ));

        //Actualizacion del registro mediante el parametro id recibido
        Empleado::where('idEmpleado', '=', $idEmpleado)
            ->update(array(
                'puesto_id' => $request['puesto_id'],
                'profesion' => $request['profesion'],
                'direccion' => $request['direccion'],
                'telefono' => $request['telefono'],
                'fechaN' => $request['fechaN'],
            ), $datos);

        //Se redirecciona a la pagina de principal con el mensaje de confirmacion
        return redirect('empleado')->with('Mensaje', 'El empleado ha sido actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Empleado  $empleado
     * @return \Illuminate\Http\Response
     */
    public function destroy($idEmpleado)
    {
          //Solo tendra acceso el usuario administrador
          if (Auth::user()->rol_id == 1) {

            Empleado::where('idEmpleado', '=', $idEmpleado)
            ->update(
                ['estado'=> '1']
            );

            return redirect('empleado')->with('Mensaje', 'El empleado ha sido eliminado exitosamente');
        }
        return redirect('empleado')->with('Mensaje', 'No tiene autilizacion para eliminar');;
    }

    public function pdf()
    {
        //Datos a mostrar
        $empleado =DB::table('empleado')
        ->join('persona', 'persona.idPersona', '=', 'empleado.persona_id')
        ->join('puesto', 'puesto.idPuesto', '=', 'empleado.puesto_id')
        ->select('persona.*', 'puesto.*', 'empleado.*')
        ->get();

        //Fecha actual
        $date = Carbon::now()->modify('-6 hours');

        $datos = compact('empleado', 'date');
        $pdf = PDF::loadView('pdf.empleado', $datos)->setPaper('carta', 'landscape');
        return $pdf->stream();
    }

}
