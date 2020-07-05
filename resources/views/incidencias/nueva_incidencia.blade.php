@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Crear nueva incidencia</h1>
@endsection

@section('styles')

@endsection

@section('breadcrumb')

@endsection

@section('content')
@php

@endphp

    <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-4 text-center">
            @if(isset($puesto))
            <img src="{{ !empty($puesto->img_logo) && file_exists(public_path().'/img/clientes/images/'.$puesto->img_logo) ? url('/img/clientes/images/'.$puesto->img_logo) : url('/img/logo.png') }}" style="width: 120px"><br>
            <h2>{{ $puesto->nom_cliente }}</h2>
            @endif
        </div>
        <div class="col-md-4"></div>
    </div>
    <div class="row" id="div_respuesta">
        <div class="col-md-3"></div>
        <div class="col-md-6 text-3x text-center rounded">
            
        </div>
        <div class="col-md-3"></div>
    </div>
    
    @include('incidencias.fill_frm_incidencia')

    
   

@endsection


@section('scripts')
    <script>
        

    </script>
@endsection
