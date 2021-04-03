@extends('layout')
@php
    use Carbon\Carbon;
@endphp
@section('title')
    <h1 class="page-header text-overflow pad-no">Gestión de salas de reuniones</h1>
@endsection

@section('styles')
<link href="{{ url('/css/bootstrap-grid.min.css') }}" rel="stylesheet">
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{url('/')}}"><i class="fa fa-home"></i> </a></li>
        <li class="breadcrumb-item">Parametrizacion</li>
        <li class="breadcrumb-item active"><a href="{{url('/users')}}">Estado de salas de reunion</a></li>
@endsection

@section('content')
<div class="row botones_accion mb-2">
    <br><br>
</div>
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title">Salas de reunion {!!beauty_fecha(Carbon::now(),0) !!}</h3>
    </div>
    <div class="panel-body">
       @foreach($salas as $sala)
            @php
                $reserva_sala=$reservas->where('id_puesto',$sala->id_puesto);
            @endphp
            @include('salas.fill_sala')
       @endforeach
    </div>
</div>
@endsection


@section('scripts')
    <script>
        $('.parametrizacion').addClass('active active-sub');
        $('.salas').addClass('active-link');
    </script>
@endsection
