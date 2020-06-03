@php
Use \Carbon\Carbon;
if (isset($r->fechas) && $r->fechas[0]!=null && $r->fechas[1]!=null){
    $fechas=explode(" - ",$r->fechas);
    $fechas[0]=Carbon::parse($fechas[0]);
    $fechas[1]=Carbon::parse($fechas[1]);
    //dd($fechas);
} else {
    $fechas[0]=Carbon::now()->startOfMonth();
    $fechas[1]=Carbon::now()->endOfMonth();
}

@endphp

@extends('layout')

@section('styles')
<style type="text/css">
    .select2-results__options[id*="tipo_log"] .select2-results__option:nth-child(2) {
        color: green;
    }
    .select2-results__options[id*="tipo_log"] .select2-results__option:nth-child(3) {
       
        color: red;
    }

    .select2-container{
        height: 40px;
    }
       
    .select2-selection{
        height: 40px;
    }

    .select2-selection__choice{
        height: 30px;
        padding-top: 2px;
        background-color: #25476a;
    }
  
</style>
@endsection

@section('title')
    <h1 class="page-header text-overflow pad-no">Bitácora</h1>
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{url('/')}}"><i class="fa fa-home"></i> </a></li>
        <li class="breadcrumb-item">configuración</li>
        <li class="breadcrumb-item">bitácora</li>
        {{--  <li class="breadcrumb-item active">Editar usuario {{ !empty($users->name) ? $users->name : '' }}</li>  --}}
    </ol>
@endsection


@section('content')

    @if(Session::has('success_message'))
        <div class="alert alert-success">
            <span class="glyphicon glyphicon-ok"></span>
            {!! session('success_message') !!}

            <button type="button" class="close" data-dismiss="alert" aria-label="close">
                <span aria-hidden="true">&times;</span>
            </button>

        </div>
    @endif

    <div class="panel " >
        <div class="panel-heading cursor-pointer" style="padding-top: 10px" id="headfiltro" >
            <span class="mt-3 ml-2 font-18"><i class="fad fa-filter"></i> Filtro</span>
        </div>
        <div class="panel-body" id="divfiltro" style="display:none" >
            <form name="frm_busca_bitacora" method="POST" action="{{ url('bitacoras/search') }}">
                {{ csrf_field() }}
                <div class="row">
                    
                
                    <div class="col-md-1" style="width: 110px">
                        <div class="form-group">
                            <label>Mostrar</label>
                            <select class="form-control select2" name="tipo_log" id="tipo_log" style="height: 43px">
                                <option value=""></option>
                                <option  {{ isset($r) && $r->tipo_log=="ok" ? 'selected' : '' }} value="ok" id="ok">ok</option>
                                <option {{ isset($r) && $r->tipo_log=="error" ? 'selected' : '' }} value="error" id="error">error</option>
                            </select>
                        </div>
                    </div>
                
                    
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Usuario</label>
                            <select class="form-control select2" tabindex="-1" aria-hidden="true" name="usuario"  style="height: 43px">
                                <option value=""></option>
                                @foreach($usuarios as $key=>$value)
                                <option {{ isset($r) && $r->usuario==$key ? 'selected' : '' }} value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">  
                            <label>Modulo</label>
                            <select class="form-control select2 select2-hidden-accessible" multiple="" data-placeholder="Seleccione modulo" tabindex="-1" aria-hidden="true" name="modulos[]"> 
                                @forelse($modulos as $modulo)
                                <option {{ isset($r) && in_array($modulo,$r->modulos) ? 'selected' : '' }}  value="{{ $modulo }}">{{ $modulo }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Fechas:</label>
                            <div class="input-group mar-btm">
                                <input type="text" class="form-control pull-right" id="fechas" name="fechas" value="{{ isset($r)?Carbon::parse($fechas[0])->format('d/m/Y').' - '.Carbon::parse($fechas[1])->format('d/m/Y'):'' }}">
                                <div class="input-group-btn">
                                    <span class="btn input-group-text btn-mint"  style="height: 40px"><i class="fa fa-calendar mt-1"></i></span>
                                </div>
                            </div>
                            <!-- /.input group -->
                        </div> 
                    </div>

                    
                        
                    <div class="col-md-1 form-group text-right">
                        <button type="submit" class="btn btn-primary btn-lg" style="margin-top: 24px; height: 40px;"><i class="fa fa-search"></i> Buscar</button>
                    </div>
                    
                </div>
            </form>
        </div>
    </div>
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">Bitácora</h3>
        </div>
        @if(count($bitacoras) == 0)
            <div class="panel-body text-center">
                <h4>No Bitacoras Available.</h4>
            </div>
        @else
        
            <div class="panel-body panel-body-with-table">
                <div class="table-responsive">

                    <table id="tablapuestos"  data-toggle="table"
                        data-locale="es-ES"
                        data-search="true"
                        data-show-columns="true"
                        data-show-columns-toggle-all="true"
                        data-page-list="[5, 10, 20, 30, 40, 50]"
                        data-page-size="50"
                        data-pagination="true" 
                        data-show-pagination-switch="true"
                        data-show-button-icons="true"
                        data-toolbar="#all_toolbar"
                        >
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Cliente</th>
                                <th>Modulo</th>
                                <th>Seccion</th>
                                <th style="width:30%">Accion</th>
                                <th>Status</th>
                                <th style="width: 140px">Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($bitacoras as $bitacora)
                            <tr @if($bitacora->status=="error" || strpos($bitacora->accion,"ERROR:")!==false) class="bg-red color-palette" @endif>
                                <td>{{ $bitacora->name }}</td>
                                <td>{{ $bitacora->nom_cliente }}</td>
                                <td>{{ $bitacora->id_modulo }}</td>
                                <td>{{ $bitacora->id_seccion }}</td>
                                <td style="word-break: break-all;">{{ $bitacora->accion }}</td>
                                <td ><span @if(strtoupper($bitacora->status)=="OK") class="bg-success" @else class="bg-danger" @endif style="padding: 0 5px 0 5px">{{ $bitacora->status }}</span></td>
                                <td>{!! beauty_fecha($bitacora->fecha) !!}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        @endif
        
    </div>
@endsection
@php
    //dd($r->modulos);
    if(isset($r)){
        $fechas=explode(" - ",$r->fechas);
    } else {
        $fechas[0]=date('Y-m-d');
        $fechas[1]=date('Y-m-d', strtotime(date('Y-m-d') . " + 30 day"));
    }
    
@endphp
@section('scripts')
    <script>

    $('.configuracion').addClass('active active-sub');
	$('.bitacora').addClass('active-link');

    $('#headfiltro').click(function(){
        $('#divfiltro').toggle();
    })  

    $('#tipo_log').select2({
        minimumResultsForSearch: -1
    });

     //Date range picker
     $('#fechas').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear'
            },
            opens: 'left',
        }, function(start_date, end_date) {
            $('#fechas').val(start_date.format('DD/MM/YYYY')+' - '+end_date.format('DD/MM/YYYY'));
        });

    @if(isset($fechas) && $fechas[0]!='' && $fechas[1]!='')
        $('#fechas').val('{{ $fechas[0] }} - {{ $fechas[1] }}');
    @endif;
    </script>
@endsection