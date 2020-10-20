<?php

namespace App\Http\Controllers;

use App\Caja;
use App\Comprobante;
use App\Habitacion;
use App\Reserva;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class RecepcionController extends Controller
{
    public function index()
    {
        $datos = DB::table('habitacion')
            ->join('nivel', 'nivel.idNivel', '=', 'habitacion.nivel_id')
            ->join('tipoHabitacion', 'tipoHabitacion.idTipoHabitacion', '=', 'habitacion.tipoHabitacion_id')
            ->select('tipoHabitacion.*', 'habitacion.*', 'nivel.numeroNivel')
            ->get();

        //Cuando se encuentra el registro se compactan los datos y se redireciona a la pagina de edicion
        return view('recepcion.index', ['habitaciones' => $datos]);
    }

    public function limpieza($idHabitacion)
    {
        // Se finaliza la limpieza
        DB::table('habitacion')->where('idHabitacion', $idHabitacion)->update(['estado' => '0']);
        //Luego de actualizar los datos se redirecciona vista habitacion
        return redirect('habitacion')->with('Mensaje', 'La habitacion ha sido habilitada');
    }

    public function disponible($idHabitacion)
    {
        // Se finaliza la limpieza
        $datos = DB::table('habitacion')
            ->join('tipohabitacion', 'tipohabitacion.idTipoHabitacion', '=', 'habitacion.tipoHabitacion_id')
            ->select('tipohabitacion.*', 'habitacion.*')
            ->where('habitacion.idHabitacion', '=', $idHabitacion)->get();

        $datos2 = DB::table('cliente')
            ->join('persona', 'persona.idPersona', '=', 'cliente.persona_id')
            ->join('identificacion', 'identificacion.idIdentificacion', '=', 'persona.identificacion_id')
            ->select('persona.*', 'identificacion.*', 'cliente.*')
            ->Where('estado', '=', '0')
            ->get();

        $datos3 = DB::table('habitacion')
            ->join('tipohabitacion', 'tipohabitacion.idTipoHabitacion', '=', 'habitacion.tipoHabitacion_id')
            ->where('habitacion.idHabitacion', $idHabitacion)
            ->value('precio');

        //Luego de actualizar los datos se redirecciona vista habitacion
        return view('recepcion.disponible', ['habitaciones' => $datos, 'cliente' => $datos2, 'precio' => $datos3]);
    }
    public function show()
    {
        //
    }

    public function store(Request $request)
    {
        $campos = [
            'cliente_id' => 'required',
            'fsalida' => 'required',
            'hsalida' => 'required',
            'estadopago' => 'required',
            'personas' => 'required',
            'cant_noche' => 'required',
        ];
        //La constante mensaje guarda el texto que se mostrará cuando no se cumpla la validacion
        $Mensaje = [
            "required" => 'El campo :attribute es requerido',
        ];

        $this->validate($request, $campos, $Mensaje);

        //id de la habitacion
        $habitacion = $request['habitacion_id'];

        $caja = DB::table('caja')
            ->min('estado');

        //Verificacion de caja
        if ($caja == '0') {
            $user = Auth::user()->id;
            $fecha = Carbon::now()->modify('-6 hours')->format('Y-m-d');
            $hora = Carbon::now()->modify('-6 hours')->format('h:i:s');

            //Estado del pago
            $estado = $request['estadopago'];

            //Total de la reservacion
            $precio = $request['precio'];
            $cantidad = $request['cant_noche'];
            $total = ($precio * $cantidad);

            Reserva::insert([
                'usuario_id' => $user,
                'habitacion_id' => $habitacion,
                'cliente_id' => $request['cliente_id'],
                'estadopago' => $estado,
                'personas' => $request['personas'],
                'fregistro' => $fecha,
                'hregistro' => $hora,
                'fsalida' => $request['fsalida'],
                'hsalida' => $request['hsalida'],
                'total' => $total,
            ]);
            return redirect('recepcion');
        } else {
            return redirect('recepcion/disponible/' . $habitacion)->with('Mensaje', 'No existe ninguna caja abierta');
        }
    }

    public function ocupada($idHabitacion)
    {
        $datos = DB::table('comprobante')
            ->join('reserva', 'reserva.idReserva', '=', 'comprobante.reserva_id')
            ->join('cliente', 'cliente.idCliente', '=', 'reserva.cliente_id')
            ->join('persona', 'persona.idPersona', '=', 'cliente.persona_id')
            ->join('habitacion', 'habitacion.idHabitacion', '=', 'reserva.habitacion_id')
            ->join('tipoHabitacion', 'tipoHabitacion.idTipoHabitacion', '=', 'habitacion.tipoHabitacion_id')
            ->join('users', 'users.id', '=', 'reserva.usuario_id')
            ->select('habitacion.*', 'tipoHabitacion.*', 'persona.*', 'reserva.*', 'comprobante.caja_id', 'comprobante.reserva_id', 'comprobante.idComprobante')
            ->where('reserva.habitacion_id', $idHabitacion)
            ->orderByDesc('idReserva')
            ->get()->take(1);

        //Fecha actual
        $fecha = Carbon::now()->modify('-6 hours')->format('Y-m-d H:i:s');

        //Cantidad de noches
        $noches = DB::table('reserva')
            ->selectRaw('DATEDIFF(fsalida,fregistro) AS cantidad')
            ->where('habitacion_id', $idHabitacion)
            ->pluck('cantidad')->last();

        //Redireccion a la vista de la hospedaje
        return view('recepcion.ocupada', ['reserva' => $datos, 'fecha' => $fecha, 'noches' => $noches]);
    }
    public function comprobante(Request $request)
    {
        $habitacion_id = $request['habitacion'];
        $mora = $request['numero2'];
        //Costo de la reservacion
        $costo = $request['numero'];
        //Costo + mora
        $total = $request['total'];
        $reserva_id = $request['reserva_id'];
        $estadopago = $request['estadopago'];
        $idComprobante = $request['idComprobante'];
        $caja_id = $request['caja_id'];

        // Se pasa a mantenimiento la habitacion
        Habitacion::where('idHabitacion', '=', $habitacion_id)->update(
            ['estado' => '3']
        );

        $fecha = Carbon::now()->modify('-6 hours')->format('Y-m-d');
        $hora = Carbon::now()->modify('-6 hours')->format('H:i:s');


        // Actualizacion de la fecha de salida
        Reserva::where('idReserva', '=', $reserva_id)->update(
            [
                'fsalida' => $fecha,
                'hsalida' => $hora,
            ]
        );
        $total2 = $costo + $mora;
        //No pagado
        if ($estadopago == '0') {
            // Se agrega el pago a caja
            Caja::where('idCaja', '=', $caja_id)->increment('ganancias', $total);
        //Pagado
        }elseif($estadopago == '1'){
            Caja::where('idCaja', '=', $caja_id)->increment('ganancias', $mora);
        }

        Comprobante::where('idComprobante', '=', $idComprobante)->update(
            ['mora' => $mora,
            'total'=>$total2]
        );

        $datos = DB::table('comprobante')
            ->join('reserva', 'reserva.idReserva', '=', 'comprobante.reserva_id')
            ->join('cliente', 'cliente.idCliente', '=', 'reserva.cliente_id')
            ->join('persona', 'persona.idPersona', '=', 'cliente.persona_id')
            ->join('habitacion', 'habitacion.idHabitacion', '=', 'reserva.habitacion_id')
            ->join('tipoHabitacion', 'tipoHabitacion.idTipoHabitacion', '=', 'habitacion.tipoHabitacion_id')
            ->join('users', 'users.id', '=', 'reserva.usuario_id')
            ->select('habitacion.*', 'tipoHabitacion.*', 'persona.*', 'reserva.*', 'comprobante.mora', 'comprobante.idComprobante', 'comprobante.total AS totall')
            ->where('reserva.habitacion_id', $habitacion_id)
            ->orderByDesc('idReserva')
            ->get()->take(1);

        $noches = DB::table('reserva')
            ->selectRaw('DATEDIFF(fsalida,fregistro) AS cantidad')
            ->where('habitacion_id', $habitacion_id)
            ->pluck('cantidad')->last();
        //Fecha actual
        $mes = Carbon::now()->modify('-6 hours')->formatLocalized('%B');
        $dia = Carbon::now()->modify('-6 hours')->formatLocalized('%e');
        $anio = Carbon::now()->modify('-6 hours')->formatLocalized('%Y');

        //Redireccion a la vista de la hospedaje
        return view('comprobante.index', ['noches' => $noches, 'reserva' => $datos, 'mes' => $mes, 'dia' => $dia, 'anio' => $anio]);
    }
}
