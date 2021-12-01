@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Gestión de salas de reuniones</h1>
@endsection

@section('styles')
<link href="{{ url('/css/bootstrap-grid.min.css') }}" rel="stylesheet">
<link href="{{ asset('/plugins/noUiSlider/nouislider.min.css') }}" rel="stylesheet">
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{url('/')}}"><i class="fa fa-home"></i> </a></li>
        <li class="breadcrumb-item">Parametrizacion</li>
        <li class="breadcrumb-item active"><a href="{{url('/users')}}">Reserva de salas de reunion</a></li>
@endsection

@section('content')
<div class="row botones_accion">
    <div class="col-md-4">
        <div class="input-group float-right" id="div_fechas">
            <input type="text" class="form-control pull-left ml-1" id="fecha_ver" name="fecha_ver" style="width: 100px" value="{{ Carbon\Carbon::now()->format('d/m/Y') }}">
            <span class="btn input-group-text btn-mint" disabled  style="height: 40px"><i class="fas fa-calendar mt-1"></i></span>
        </div>
    </div>
    <div class="col-md-7">
        <br>
    </div>
    <div class="col-md-1 text-right">
        <div class="btn-group btn-group-sm pull-right" role="group">
                <a href="#" id="btn_nueva_puesto" class="btn btn-success" title="Nueva reserva">
                <i class="fa fa-plus-square pt-2" style="font-size: 20px" aria-hidden="true"></i>
                <span>Nueva reserva</span>
            </a>
        </div>
    </div>
</div>
<div id="editorCAM" class="mt-2">

</div>


 



<div class="panel">
     <div class="panel-heading">
        <h3 class="panel-title" id="titulo">Estado de salas para {!! beauty_fecha(Carbon\Carbon::now(),0) !!}</h3>
    </div> 
    <div class="panel-body" id="detalles_reserva">
       @foreach($salas as $sala)
            @php
                $reserva_sala=$reservas->where('id_puesto',$sala->id_puesto);
            @endphp
            @include('salas.fill_sala')
       @endforeach
    </div>
</div>


@if(count($misreservas)>0)
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">Mis reservas</h3>
        </div>
        <div class="panel-body" id="misreservas">
            @include('salas.mis_reservas')      
        </div>
    </div>
@endif
@endsection


@section('scripts')
    <script>
        $('.reservas').addClass('active active-sub');
        $('.reservas_salas').addClass('active-link');
    </script>

    <script src="{{url('/plugins/noUiSlider/nouislider.min.js')}}"></script>
    <script src="{{url('/plugins/noUiSlider/wNumb.js')}}"></script>
    <script>
        function filter_hour(value, type) {
        return (value % 60 == 0) ? 1 : 0;
        }

        

        $('#btn_nueva_puesto').click(function(){
            spshow('spin');
            $('#div_fechas').hide();
            $('#editorCAM').load("{{ url('/salas/crear_reserva') }}", function(){
                animateCSS('#editorCAM','bounceInRight');
                sphide('spin');
                $('#titulo').html('Nueva reserva de sala');
            });
            
        });

        function editar(id){
            $('#editorCAM').load("{{ url('/reservas/edit/') }}"+"/"+id, function(){
                animateCSS('#editorCAM','bounceInRight');
            });
        }

       

        function borrar(){
            $.post('{{url('/reservas/cancelar')}}', {_token: '{{csrf_token()}}',fecha: $(this).data('fecha'),id: $(this).data('id'), des_puesto: $(this).data('des_puesto')}, function(data, textStatus, xhr) {
                
            })
            .done(function(data) {
                console.log(data);
                if(data.error){
                    mensaje_error_controlado(data);

                } else if(data.alert){
                    mensaje_warning_controlado(data);
                } else{
                    toast_ok(data.title,data.mensaje);
                    $('#misreservas').load("/salas/mis_reservas");
                }
                
            })
            .fail(function(err) {
                mensaje_error_respuesta(err);
            });
        }

        $(function(){
            $('#fecha_ver').change(function(){
                lafecha=$(this).val();
                lafecha=moment(lafecha,"DD/MM/YYYY").format('YYYY-MM-DD')
                console.log(lafecha);
                $('#detalles_reserva').load("/salas/dia/"+lafecha);
            })
        })          
         
        $('.btn_del').click(borrar);
        
        $('#fecha_ver').daterangepicker({
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
            }
        });

    </script>
@endsection