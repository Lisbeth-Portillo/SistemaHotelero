<?php

namespace App\Http\Controllers;

use App\TipoHabitacion;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TipoHabitacionController extends Controller
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
            $datos = DB::table('tipohabitacion')
            ->orWhere('habitacion', 'LIKE', "%$keyword%")
            ->orWhere('descripcion', 'LIKE', "%$keyword%")
            ->orWhere('precio', 'LIKE', "%$keyword%")
            ->paginate($paginacion);

             /*Redireccion a la vista principal
            * Se envia a traves de la variable la consulta retornadas y
            * el valor buscado para la siguiente vista
            */
            return view('tipohabitacion.index',['tipohabitacion' => $datos, 'search'=>$keyword]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //Se envia a la vista create para la crear una nuevo tipo de habitacion
        return view('tipohabitacion.create');
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
        $campos=[
            'habitacion' => 'required|string',
            'descripcion' => 'required|max:100',
            'precio' => 'required|numeric|min:150|max:300',
        ];
        //La variable mensaje guarda el texto que se mostrará cuando no se cumpla la validacion
        $Mensaje =[
            "required"=>'La :attribute es requerida',
            "nombre.unique"=> 'La habitacion ya existe',
            "precio.min"=>'El precio minimo es de Q.150 por día',
            "precio.max"=>'El precio maximo por habitacion es de Q.300 por día',
            "precio.required"=>'El precio es requerido',
            "descripcion.max"=> 'La descripcion no debe exceder de 100 caracteres'

        ];
        /**
         * Se guardan los datos que se envian del formulario de creacion de registros
         * se pasan los parametros a validar de la varible campos y el mensaje con el texto
         * a mostrar en caso de que no se cumplan la validacion
         */
        $this->validate($request,$campos,$Mensaje);


         //Excepcion del campo para la validacion y seguridad de los datos
         $token = request()->except('_token');

         //Se guardan los datos obtenidos
         $datos = $request->all();

       //Insersion de los datos hacia la base de datos
       TipoHabitacion::insert($token,$datos);

       //Redireccion al guardar el empleado
       return redirect('tipohabitacion')->with('Mensaje', 'El tipo habitacion se ha creado exitosamente');
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\TipoHabitacion  $tipoHabitacion
     * @return \Illuminate\Http\Response
     */
    public function show(TipoHabitacion $tipoHabitacion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TipoHabitacion  $tipoHabitacion
     * @return \Illuminate\Http\Response
     */
    public function edit($idTipoHabitacion)
    {

        //Se busca la habitacion basado en el parametro id mandado para su edicion
        $tipohabitacion = TipoHabitacion::findOrFail($idTipoHabitacion);

        //Cuando se encuentra el registro se compactan los datos y se redireciona a la pagina de edicion
        return view('tipohabitacion.edit',compact('tipohabitacion'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TipoHabitacion  $tipoHabitacion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$idTipoHabitacion)
    {
          //Validacion de los campos a registrar
          $campos=[
            'habitacion' => 'required|string',
            'descripcion' => 'required|max:100',
            'precio' => 'required|numeric|min:150|max:300',
        ];
        //La constante mensaje guarda el texto que se mostrará cuando no se cumpla la validacion
        $Mensaje =[
            "required"=>'La :attribute es requerida',
            "nombre.unique"=> 'La habitacion ya existe',
            "precio.min"=>'El precio minimo es de Q.150 por día',
            "precio.min"=>'El precio minimo es de 150',
            "precio.required"=>'El precio es requerido',
            "descripcion.max"=> 'La descripcion no debe exceder de 100 caracteres'
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
       TipoHabitacion::where('idTipoHabitacion', '=', $idTipoHabitacion)->update($datos);

       //Se redirecciona a la pagina de principal con el mensaje de confirmacion
       return redirect('tipohabitacion')->with('Mensaje','El tipo habitacion ha sido actualizada exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TipoHabitacion  $tipoHabitacion
     * @return \Illuminate\Http\Response
     */
    public function destroy($idTipoHabitacion)
    {
         //Solo tendra acceso el usuario administrador
         if(Auth::user()->rol_id == 1){

            TipoHabitacion::destroy($idTipoHabitacion);

            return redirect('tipohabitacion')->with('Mensaje','El tipo de habitacion ha sido eliminada');
            }
           return redirect('habitacion')->with('Mensaje', 'No esta autorizado para eliminar');
    }

    public function pdf()
    {
        $tipohabitacion = DB::table('tipohabitacion')->get();

        //Fecha actual
        $date = Carbon::now()->modify('-6 hours');

        $datos = compact('tipohabitacion', 'date');
        $pdf = PDF::loadView('pdf.tipohabitacion', $datos)->setPaper('carta', 'landscape');
        return $pdf->stream();
    }
}
