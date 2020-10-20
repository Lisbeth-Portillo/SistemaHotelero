<?php

namespace App\Http\Controllers;

use App\Caja;
use App\Cliente;
use App\Habitacion;
use App\Reserva;
use App\Users;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $user = Users::get()->count();
        $habitacion = Habitacion::get()->count();
        $cliente = Cliente::get()->count();
        $reserva = Reserva::whereNull('fregistro')->get()->count();

        $caja = Caja::where('estado','0')->pluck('total')->first();

        $date = Carbon::now()->format('Y');
        $fechaf = $date;

        $grafica = DB::table('caja')
        ->selectRaw('MONTH(fechaA) AS mes, SUM(total) AS total')
        ->whereBetween('fechaA', ['2020-01-01', '2020-12-31'])
        ->groupBy('mes')
        ->get();


        /**
         * SELECT MONTH(fechaA) as mes, SUM(total) as Total FROM caja WHERE
         *fechaA BETWEEN '2020-01-01' AND '2020-12-31' GROUP BY  mes
         */

    /*Redireccion a la vista principal
    * Se envia a traves de la variable la consulta retornadas y
    * el valor buscado para la siguiente vista
    */
        return view('home', ['user' => $user, 'habitacion' => $habitacion, 'cliente' => $cliente, 'caja'=> $caja, 'reserva'=> $reserva]);
    }
}
