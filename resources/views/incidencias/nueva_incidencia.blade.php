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
            <img src="{{ Storage::disk(config('app.img_disk'))->url('img/clientes/images/'.session('logo_cliente')) }}" style="width: 13vw" alt="" onerror="this.src='{{ url('/img/logo.png') }}';">
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

        $('input[type="file"]').change(function(e){
			var fileName = e.target.files[0].name;
			$(this).next('label').html(fileName);
			//$('.custom-file-label').html(fileName);
		});

    </script>
@endsection
