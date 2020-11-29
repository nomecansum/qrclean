@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Resultados de la encuesta</h1>
@endsection

@section('styles')

@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{url('/')}}"><i class="fa fa-home"></i> </a></li>
        <li class="breadcrumb-item">Encuestas</li>
        <li class="breadcrumb-item"><a href="{{url('/users')}}">resultados</a></li>
        <li class="breadcrumb-item active">{{ !empty($encuesta->titulo) ? $encuesta->titulo : '' }}</li>
    </ol>
@endsection

@section('content')
<div class="row botones_accion mb-2">
    <br><br>
</div>
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title">$encuesta->titulo</h3>
    </div>
    <div class="panel-body">

    </div>
</div>

@endsection


@section('scripts')
    <script>
        $('.parametrizacion').addClass('active active-sub');
        $('.encuestas').addClass('active-link');
    </script>
@endsection
