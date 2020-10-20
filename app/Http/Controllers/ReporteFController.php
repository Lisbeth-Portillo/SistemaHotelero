<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ReporteFController extends Controller
{
    public function index()
    {
        return view('reporte.indexF');
    }

    public function reporte(Request $request)
    {
        $fechai = $request["fechainicio"];
        $fechaf = $request["fechafin"];

        $datos = DB::table('reserva')
                ->join('users', 'users.id', '=', 'reserva.usuario_id')
                ->join('rol', 'rol.idRol', '=', 'users.id')
                ->join('cliente', 'cliente.idCliente', '=', 'reserva.cliente_id')
                ->join('persona', 'persona.idPersona', '=', 'cliente.persona_id')
                ->join('habitacion', 'habitacion.idHabitacion', '=', 'reserva.habitacion_id')
                ->join('tipohabitacion', 'tipohabitacion.idTipoHabitacion', '=', 'habitacion.tipoHabitacion_id')
                ->select('habitacion.*', 'persona.*', 'reserva.*', 'tipohabitacion.*', 'rol.*')
                ->whereBetween('reserva.fregistro', [$fechai, $fechaf])
                ->whereBetween('reserva.fsalida', [$fechai, $fechaf])
                ->get();

        return view('reporte.reporteF', ['reserva' => $datos, 'fechai'=>$fechai, 'fechaf'=>$fechaf]);
    }

    public function gastos()
    {
         return view('reporte.indexG');
    }

    public function reporteG(Request $request)
    {
        $fechai = $request->post("fechainicio");
        $fechaf = $request->post("fechafin");

        $datos = DB::table('gastos')
        ->join('users', 'users.id', '=', 'gastos.usuario_id')
        ->select('gastos.*', 'users.*')
                ->whereBetween('gastos.fecha', [$fechai, $fechaf])
                ->get();

        return view('reporte.reporteG', ['reserva' => $datos, 'fechai'=>$fechai, 'fechaf'=>$fechaf]);
    }
}
