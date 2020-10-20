<?php

namespace App\Http\Controllers;

use App\Habitacion;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HabitacionController extends Controller
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
            $datos = DB::table('habitacion')
                ->join('nivel', 'nivel.idNivel', '=', 'habitacion.nivel_id')
                ->join('tipoHabitacion', 'tipoHabitacion.idTipoHabitacion', '=', 'habitacion.tipoHabitacion_id')
                ->select('nivel.*', 'tipoHabitacion.*', 'habitacion.*')
                ->orWhere('nivel.numeroNivel', 'LIKE', "%$keyword%")
                ->orWhere('habitacion.nombre', 'LIKE', "%$keyword%")
                ->orWhere('tipoHabitacion.habitacion', 'LIKE', "%$keyword%")
                ->orWhere('tipoHabitacion.precio', 'LIKE', "%$keyword%")
                ->orWhere('tipoHabitacion.descripcion', 'LIKE', "%$keyword%")
                ->paginate($paginacion);
            /*Redireccion a la vista principal
            * Se envia a traves de la variable la consulta retornadas y
            * el valor buscado para la siguiente vista
            */
            return view('habitacion.index', ['habitaciones' => $datos, 'search' => $keyword]);
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //Obtiene los datos de las tablas relacionadas
        $dato1 = DB::table('nivel')->where('estado', '0')->get();
        $dato2 = DB::table('tipohabitacion')->get();

        /**
         * Redirecciona ala vista para la creacion y envia los de las
         * tablas foraneas a traves de la variable
         */
        return view('habitacion.create', ['nivel' => $dato1, 'tipohabitacion' => $dato2]);
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
            'nombre' => 'required|unique:habitacion|string|max:5',
            'nivel_id' => 'required',
            'tipoHabitacion_id' => 'required',
        ];
        //La variable mensaje guarda el texto que se mostrará cuando no se cumpla la validacion
        $Mensaje = [
            "required" => 'El :attribute es requerido',
            "nombre.unique" => 'La habitacion ya existe'
        ];

        /**
         * Se guardan los datos que se envian del formulario de creacion de registros
         * se pasan los parametros a validar de la varible campos y el mensaje con el texto
         * a mostrar en caso de que no se cumplan la validacion
         */
        $this->validate($request, $campos, $Mensaje);

        //Excepcion del campo para la validacion y seguridad de los datos
        $token = request()->except('_token');

        //Se guardan los datos obtenidos de la tabla de la llave foranea
        $tipohabitacion = request()->get('tipoHabitacion');
        $nivel = request()->get('nivel');

        /**
         * Se registran los datos recibidos
         * Se redirecciona la vista principal con el mensaje de confirmacion
         */
        if (DB::table('habitacion')->where('tipoHabitacion_id', '=', $tipohabitacion)
            ->where('nivel_id', '=', $nivel)
        ) {
            Habitacion::insert($token);
        }
        return redirect('habitacion')->with('Mensaje', 'La habitación se ha creado exitosamente');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Habitacion  $habitacion
     * @return \Illuminate\Http\Response
     */
    public function edit($idHabitacion)
    {
        //Se guardan los datos obtenidos de la tabla de la llave foranea para luego mandarlos
        $dato1 = DB::table('nivel')->where('estado', '0')->get();
        $dato2 = DB::table('tipohabitacion')->get();

        //Se busca la habitacion basado en el parametro id mandado para su edicion
        $habitacion = Habitacion::findOrFail($idHabitacion);

        //Cuando se encuentra el registro se compactan los datos y se redireciona a la pagina de edicion
        return view('habitacion.edit', ['nivel' => $dato1, 'tipohabitacion' => $dato2], compact('habitacion'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Habitacion  $habitacion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idHabitacion)
    {
        //Validacion de los campos a registrar
        $campos = [
            'nombre' => 'required|max:5|unique:habitacion,nombre,' . $idHabitacion . ',idHabitacion',
            'nivel_id' => 'required',
            'tipoHabitacion_id' => 'required',
        ];
        //La constante mensaje guarda el texto que se mostrará cuando no se cumpla la validacion
        $Mensaje = [
            "required" => 'El :attribute es requerido',
            "nombre.unique" => 'El nombre de la habitacion ya esta registrada'
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
        Habitacion::where('idHabitacion', '=', $idHabitacion)->update($datos);

        //Se redirecciona a la pagina de principal con el mensaje de confirmacion
        return redirect('habitacion')->with('Mensaje', 'La habitacion ha sido actualizada exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Habitacion  $habitacion
     * @return \Illuminate\Http\Response
     */
    public function destroy($idHabitacion)
    {
       //
    }

    public function show()
    {
        //
    }

    public function habilitar($idHabitacion)
    {
        //Busca la habitacion del id mandado
        $habitacion = Habitacion::findOrFail($idHabitacion);

        /**
         * Si el la mantenimiento no esta habilidado = 0 se activa
         * e igual de manera contraria
         */

        if ($habitacion->estado == 0) {
            DB::table('habitacion')->where('idHabitacion', $idHabitacion)->update(['estado' => '1']);
            $mensaje = "La habitacion ha pasado a mantenimiento";
        } else {
            DB::table('habitacion')->where('idHabitacion', $idHabitacion)->update(['estado' => '0']);
            $mensaje = "La habitacion se ha habilitado";
        }
        //Luego de actualizar los datos se redirecciona vista habitacion
        return redirect('habitacion')->with('Mensaje', $mensaje);
    }

    public function pdf()
    {
        //Datos a mostrar
        $habitacion = DB::table('habitacion')
            ->join('nivel', 'nivel.idNivel', '=', 'habitacion.nivel_id')
            ->join('tipoHabitacion', 'tipoHabitacion.idTipoHabitacion', '=', 'habitacion.tipoHabitacion_id')
            ->select('nivel.*', 'tipoHabitacion.*', 'habitacion.*')
            ->get();

        //Fecha actual
        $date = Carbon::now()->modify('-6 hours');

        $datos = compact('habitacion', 'date');
        $pdf = PDF::loadView('pdf.habitacion', $datos)->setPaper('carta', 'landscape');
        return $pdf->stream();
    }

}
