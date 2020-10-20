<?php

namespace App\Http\Controllers;

use App\Gastos;

use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GastosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Muestra de la informacion del empleado con paginacion
        $datos = DB::table('gastos')
            ->join('users', 'users.id', '=', 'gastos.usuario_id')
            ->select('gastos.*', 'users.*')
            ->orderByDesc('idGastos')
            ->paginate(5);

        $dato2 = DB::table('caja')
            ->min('estado');

        //Redireccion a la vista principal con los datos compactados
        return view('gastos.index', ['gastos' => $datos, 'estado' => $dato2]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $dato = DB::table('caja')
            ->min('estado');

        if ($dato === '0') {
            return view('gastos.create');
        }
        return redirect('gastos')->with('Mensaje', 'No existe caja abierta para ingresar un gasto');
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
            'nombre' => 'required|string',
            'descripcion' => 'required|string|max:50',
            'precio' => 'required',
        ];
        //La variable mensaje guarda el texto que se mostrará cuando no se cumpla la validacion
        $Mensaje = [
            "required" => 'El campo :attribute es requerido',
            "descripcion.max" => 'La descripcion no debe exceder de 50 caracteres',
        ];

        /**
         * Se guardan los datos que se envian del formulario de creacion de registros
         * se pasan los parametros a validar de la varible campos y el mensaje con el texto
         * a mostrar en caso de que no se cumplan la validacion
         */
        $this->validate($request, $campos, $Mensaje);

        //Usuario registrado
        $user = Auth::user()->id;

        //Fecha actual
        $date = Carbon::now()->modify('-6 hours');

        //Caja abierta
        $caja = DB::table('caja')
            ->max('idCaja');

        Gastos::insert([
            'nombre' => $request['nombre'],
            'descripcion' => $request['descripcion'],
            'precio' => $request['precio'],
            'usuario_id' => $user,
            'caja_id' => $caja,
            'fecha' => $date,
        ]);

        return redirect('gastos')->with('Mensaje', 'El gasto se ha creado exitosamente');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Gastos  $gastos
     * @return \Illuminate\Http\Response
     */
    public function show(Gastos $gastos)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Gastos  $gastos
     * @return \Illuminate\Http\Response
     */
    public function edit(Gastos $gastos)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Gastos  $gastos
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Gastos $gastos)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Gastos  $gastos
     * @return \Illuminate\Http\Response
     */
    public function destroy($idGastos)
    {
        //Solo tendra acceso el usuario administrador
        if (Auth::user()->rol_id == 1) {
            $gastos = Gastos::findOrFail($idGastos);

            $caja = DB::table('caja')
                ->where('idCaja', $gastos->caja_id)
                ->value('estado');

            if ($caja === '0') {
                Gastos::destroy($idGastos);
                return redirect('gastos')->with('Mensaje', 'El gasto ha sido eliminado exitosamente');
            }
            return redirect('gastos')->with('Mensaje', 'La caja ha sido cerrada, no puede eliminar el gasto');
        }
        return redirect('gastos')->with('Mensaje', 'No esta autorizado para eliminar');
    }

    public function pdf()
    {
        //Datos a mostrar
        $gastos = DB::table('gastos')
            ->join('users', 'users.id', '=', 'gastos.usuario_id')
            ->select('gastos.*', 'users.*')
            ->get();

        //Fecha actual
        $date = Carbon::now()->modify('-6 hours');

        $datos = compact('gastos', 'date');
        $pdf = PDF::loadView('pdf.gastos', $datos)->setPaper('carta', 'landscape');
        return $pdf->stream();
    }
}
