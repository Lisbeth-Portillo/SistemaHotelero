<?php

namespace App\Http\Controllers;

use App\Cliente;
use App\Persona;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade as PDF;
use Facade\FlareClient\Http\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller
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
            $datos = DB::table('cliente')
                ->join('persona', 'persona.idPersona', '=', 'cliente.persona_id')
                ->join('identificacion', 'identificacion.idIdentificacion', '=', 'persona.identificacion_id')
                ->select('persona.*', 'identificacion.*', 'cliente.*')
                ->orWhere('identificacion.tipoIdentificacion', 'LIKE', "%$keyword%")
                ->orWhere('persona.nombres', 'LIKE', "%$keyword%")
                ->orWhere('persona.apellidos', 'LIKE', "%$keyword%")
                ->orWhere('persona.identificacion', 'LIKE', "%$keyword%")
                ->paginate($paginacion);
            /*Redireccion a la vista principal
             * Se envia a traves de la variable la consulta retornadas y
             * el valor buscado para la siguiente vista
             */
            return view('cliente.index', ['cliente' => $datos, 'search' => $keyword]);
        }
        $datos2 = DB::table('cliente')
            ->join('persona', 'persona.idPersona', '=', 'cliente.persona_id')
            ->join('identificacion', 'identificacion.idIdentificacion', '=', 'persona.identificacion_id')
            ->select('persona.*', 'identificacion.*', 'cliente.*')
            ->Where('cliente.estado', '=', '0')
            ->paginate(5);

        return view('cliente.index', ['cliente' => $datos2]);
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

        $dato2 = DB::table('identificacion')->get();

        /**
         * Redirecciona ala vista para la creacion y envia los de las
         * tablas foraneas a traves de la variable
         */
        return view('cliente.create', ['persona' => $dato1, 'identificacion' => $dato2]);
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
            'tipoIdentificacion' => 'required',
        ];
        //La variable mensaje guarda el texto que se mostrará cuando no se cumpla la validacion
        $Mensaje = [
            "required" => 'El campo :attribute es requerido',
            "identificacion.unique" => 'El DPI ingresado ya existe',
        ];

        /**
         * Se guardan los datos que se envian del formulario de creacion de registros
         * se pasan los parametros a validar de la varible campos y el mensaje con el texto
         * a mostrar en caso de que no se cumplan la validacion
         */
        $this->validate($request, $campos, $Mensaje);

        //Excepcion del campo para la validacion y seguridad de los datos
        $token = request()->except('_token');

        //Ingreso de los datos correspodiente de la tabla persona y almacenamiento del id creado
        $idpersona = DB::table('persona')->insertGetId([
            'nombres' => $request['nombres'],
            'apellidos' => $request['apellidos'],
            'identificacion' => $request['identificacion'],
            'identificacion_id' => $request['tipoIdentificacion'],
        ]);

        //Se inserta en la tabla, se exceptua el token
        Cliente::insert(['persona_id' => $idpersona], $token);

        //Se redirecciona a la vista principal de usuarios con el mensaje correspondiente
        return redirect('cliente')->with('Mensaje', 'El cliente se ha creado exitosamente');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function show(Cliente $cliente)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function edit($idCliente)
    {
        //Se busca el usuario basado en el parametro id mandado para su edicion
        $cliente = Cliente::findOrFail($idCliente);

        //Se guardan los datos obtenidos de la tabla de la llave foranea para luego mandarlos
        $dato = DB::table('persona')
            ->Where('idPersona', '=', $cliente->persona_id)
            ->get();

        $dato2 = DB::table('identificacion')->get();

        //Cuando se encuentra el registro se compactan los datos y se redireciona a la pagina de edicion
        return view('cliente.edit', ['person' => $dato, 'identificacion' => $dato2], compact('cliente'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idCliente)
    {
        //Se busca el usuario basado en el parametro id
        $cliente = Cliente::findOrFail($idCliente);
        $idpersona = $cliente->persona_id;

        //Validacion de los campos
        $campos = [
            'nombres' => 'required',
            'apellidos' => 'required',
            'identificacion' => 'required|unique:persona,identificacion,' . $idpersona . ',idPersona',
            'tipoIdentificacion' => 'required',
        ];
        //La variable mensaje guarda el texto que se mostrará cuando no se cumpla la validacion
        $Mensaje = [
            "required" => 'El campo :attribute es requerido',
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
                'identificacion_id' => $request['tipoIdentificacion'],
            ));

        //Se redirecciona a la pagina de principal con el mensaje de confirmacion
        return redirect('cliente')->with('Mensaje', 'El cliente ha sido actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function destroy($idCliente)
    {
        //Solo tendra acceso el usuario administrador
        if (Auth::user()->rol_id == 1) {

            Cliente::where('idCliente', '=', $idCliente)
            ->update(
                ['estado'=> '1']
            );

            return redirect('cliente')->with('Mensaje', 'El cliente ha sido eliminado exitosamente');
        }
        return redirect('cliente')->with('Mensaje', 'No tiene la autorizacion para eliminar');;
    }

    public function pdf()
    {
        //Datos a mostrar
        $cliente = DB::table('cliente')
            ->join('persona', 'persona.idPersona', '=', 'cliente.persona_id')
            ->join('identificacion', 'identificacion.idIdentificacion', '=', 'persona.identificacion_id')
            ->select('persona.*', 'identificacion.*', 'cliente.*')
            ->get();

        //Fecha actual
        $date = Carbon::now()->modify('-6 hours');

        $datos = compact('cliente', 'date');
        $pdf = PDF::loadView('pdf.cliente', $datos)->setPaper('carta', 'landscape');
        return $pdf->stream();
    }
}
