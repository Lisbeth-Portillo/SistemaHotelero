<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ReporteGController extends Controller
{
    public function index()
    {
        return view('reporte.indexG');
    }

    public function reporte(Request $request)
    {
        $fechai = $request["fechainicio"];
        $fechaf = $request["fechafin"];

        $datos = DB::table('gastos')
            ->join('users', 'users.id', '=', 'gastos.usuario_id')
            ->select('gastos.*', 'users.*')
            ->whereBetween('gastos.fecha', [$fechai, $fechaf])
            ->paginate(5);

        return view('reporte.reporteG', ['gastos' => $datos, 'fechai' => $fechai, 'fechaf' => $fechaf]);
    }
}
