{{-- Extension para la utilizacion de la plantilla layout --}}
@extends('layouts.app')
@section('content')
    {{-- Contenido Wrapper / contenido de la pagina--}}
    <div class="content-wrapper">

        {{-- Columnas de la navegacion / breadcrum --}}
        <ol class="breadcrumb ">
            <li class="breadcrumb-item"><a href="/home" style="text-decoration: none">
                    <i class="fas fa-home"></i> Inicio</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
        {{-- Fin columnas de navegacion --}}

        {{-- Contenido del titulo / header --}}
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-4">
                        <h3 class="m-0 text-dark">DASHBOARD</h3>
                    </div>
                    {{-- Fin de la fila --}}
                </div>
                <br>
                {{-- Fin container-fluid --}}

                {{-- CONTENIDO --}}
                <div class="row">
                    {{-- Columna Reservaciones--}}
                    <div class="col-lg-3 col-sm-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ $reserva }}</h3>
                                <p>Reservaciones</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-home"></i>
                            </div>
                            <a href="{{ url('/reserva') }}" class="small-box-footer">Mas información <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    {{-- Fin columna Reservaciones--}}

                    {{-- Columna Ingresos--}}
                    <div class="col-lg-3  col-sm-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>Q.{{ $caja }}</h3>
                                <p>Caja</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-home"></i>
                            </div>
                            <a href="{{ url('/caja') }}" class="small-box-footer">Mas información <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    {{-- Fin columna Ingresos--}}

                    {{-- Columna Habitaciones--}}
                    <div class="col-lg-3 col-sm-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ $habitacion }}</h3>
                                <p>Habitaciones</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-home"></i>
                            </div>
                            <a href="{{ url('/habitacion') }}" class="small-box-footer">Mas información<i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    {{-- Fin columna Habitaciones--}}

                    {{-- Columna Clientes--}}
                    <div class="col-lg-3 col-sm-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>{{ $cliente }}</h3>
                                <p>Clientes</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-home"></i>
                            </div>
                            <a href="{{ url('/cliente') }}" class="small-box-footer">Mas información<i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                       {{-- @foreach($caja as $c)
                              <h1>{{ $caja->total }}</h1>
                        @endforeach*  --}}

                    </div>
                    {{-- Fin columna Clientes--}}
                    {{-- Grafica --}}
                    <div class="container col-8">
                        <canvas id="myChart" width="400" height="250"></canvas>
                    </div>

                    {{-- Fin grafica --}}
                </div>
                {{-- Fin contenido --}}
            </div>
        </div>
    </div>

@endsection
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>

@section('script')

var ctx = document.getElementById('myChart').getContext('2d');
var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        datasets: [{
            label: 'Ventas por mes',
            data: [2, 1, 1, 1, 1, 1],
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true
                }
            }]
        }
    }
});
@endsection
