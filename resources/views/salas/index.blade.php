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
        <li class="breadcrumb-item">parametrizacion</li>
	    <li class="breadcrumb-item">espacios</li>
        <li class="breadcrumb-item active"><a href="{{url('/salas')}}">salas de reunion</a></li>
@endsection

@section('content')
<div class="row botones_accion mb-2">
    <form action="{{ url('/salas') }}" name="form_mapa" id="form_mapa" method="POST">
        {{ csrf_field() }}
        <div class="input-group float-right" id="div_fechas">
            <input type="text" class=" ml-3 form-control pull-left" id="fecha" name="fecha" style="width: 100px" value="{{isset($r->fecha)?$r->fecha:Carbon::now()->format('d/m/Y') }}">
            <span class="btn input-group-text btn-mint" disabled  style="height: 40px"><i class="fas fa-calendar mt-1"></i></span>
        </div>
    </form>
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
        $('.configuracion').addClass('active active-sub');
        $('.menu_parametrizacion').addClass('active active-sub');
	    $('.espacios').addClass('active active-sub');
        $('.salas').addClass('active-link');

        $('#fecha').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            autoUpdateInput : true,
            //autoApply: true,
            locale: {
                format: '{{trans("general.date_format")}}',
                applyLabel: "OK",
                cancelLabel: "Cancelar",
                daysOfWeek:["{{trans('general.domingo2')}}","{{trans('general.lunes2')}}","{{trans('general.martes2')}}","{{trans('general.miercoles2')}}","{{trans('general.jueves2')}}","{{trans('general.viernes2')}}","{{trans('general.sabado2')}}"],
                monthNames: ["{{trans('general.enero')}}","{{trans('general.febrero')}}","{{trans('general.marzo')}}","{{trans('general.abril')}}","{{trans('general.mayo')}}","{{trans('general.junio')}}","{{trans('general.julio')}}","{{trans('general.agosto')}}","{{trans('general.septiembre')}}","{{trans('general.octubre')}}","{{trans('general.noviembre')}}","{{trans('general.diciembre')}}"],
                firstDay: {{trans("general.firstDayofWeek")}}
            },
            function() {
                
            }  
        });
        $('#fecha').on('apply.daterangepicker', function(ev, picker) {
            $('#form_mapa').submit();
        });
    </script>
@endsection
