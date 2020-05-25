@php
Use \Carbon\Carbon;
@endphp

@extends('layouts.web.web')

@section('camino')
<!-- Content Header (Page header) -->
<div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0 text-dark">Log Argos</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{url('/')}}">Home</a></li>
                <li class="breadcrumb-item active">Log</li>
                <li class="breadcrumb-item active">Log Argos</li>
              </ol>
            </div><!-- /.col -->
          </div><!-- /.row -->
        </div><!-- /.container-fluid -->
      </div>
      <!-- /.content-header -->
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

    <div class="panel panel-default">
        
        <div class="row"><br></div>
        <form name="frm_busca_bitacora" method="POST" action="{{ url('bitacoras/search') }}">
        <div class="row">
            
        {{ csrf_field() }}
            <div class="col-xs-1" style="width: 110px">
                <div class="form-group">
                    <label>Mostrar</label>
                    <select class="form-control" name="tipo_log">
                        <option value=""></option>
                        <option  {{ isset($r) && $r->tipo_log=="ok" ? 'selected' : '' }} value="ok">ok</option>
                        <option {{ isset($r) && $r->tipo_log=="error" ? 'selected' : '' }} value="error">error</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Fechas:</label>
    
                    <div class="input-group">
                        
                        <input type="text" class="form-control pull-right" id="fechas" name="fechas">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                        </div>
                    </div>
                    <!-- /.input group -->
                </div> 
            </div>
            
            <div class="col-md-3">
                <div class="form-group">
                    <label>Usuario</label>
                    <select class="form-control select2" style="width: 100%;" tabindex="-1" aria-hidden="true" name="usuario">
                        <option value=""></option>
                        @foreach(DB::table('usuarios')->get() as $usuario)
                        <option {{ isset($r) && $r->usuario==$usuario->nombre ? 'selected' : '' }} value="{{ $usuario->nombre }}">{{ $usuario->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">  
                    <label>Modulo</label>
                    <select class="form-control select2 select2-hidden-accessible" multiple="" data-placeholder="Seleccione modulo" style="width: 100%; color #000;" tabindex="-1" aria-hidden="true" name="modulos[]"> 
                        @foreach(DB::table('modulos')->get() as $modulo)
                        <option {{ isset($r) && array_search($modulo,$r->modulos)!=false ? 'selected' : '' }}  value="{{ $modulo->modulo }}">{{ $modulo->modulo }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-1 form-group">
                <button type="submit" class="btn btn-primary btn-lg" style="margin-top: 34px"><i class="fa fa-search"></i> Buscar</button>
            </div>
            
        </div>
        </form>
        @if(count($bitacoras) == 0)
            <div class="panel-body text-center">
                <h4>No Bitacoras Available.</h4>
            </div>
        @else
        
        <div class="panel-body panel-body-with-table">
            <div class="table-responsive">

                <table class="table table-striped" >
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Modulo</th>
                            <th>Accion</th>
                            <th>Status</th>
                            <th style="width: 140px">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($bitacoras as $bitacora)
                        <tr @if($bitacora->status=="error" || strpos($bitacora->accion,"ERROR:")!==false) class="bg-red color-palette" @endif>
                            <td>{{ $bitacora->id_usuario }}</td>
                            <td>{{ $bitacora->id_modulo }}</td>
                            @php
                                $clase="";
                                if(strpos($bitacora->id_modulo,"ATIS Maniobras")!==false && $bitacora->status!=="error"){
                                    if(strpos($bitacora->accion,"Cambiada Maniobra S")!==false){
                                        $clase="linea_titulo_pistas bg_amarillo titulo_orientacion_maniobras";
                                    } else{
                                        $clase="linea_titulo_pistas bg_azul_claro txt_blanco titulo_orientacion_maniobras";
                                    }
                                }
                            @endphp
                            <td class="{{ $clase }}" style="word-break: break-all;">{{ $bitacora->accion }}</td>
                            <td ><span @if($bitacora->status=="ok") class="bg-green-active color-palette" @endif style="padding: 0 5px 0 5px">{{ $bitacora->status }}</span></td>
                            <td>{!! beauty_fecha($bitacora->fecha) !!}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

            </div>
        </div>

        <div class="panel-footer">
            {!! $bitacoras->render() !!}
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