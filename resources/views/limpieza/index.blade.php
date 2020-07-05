@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Rondas de {{ $entidades['tipo'] }}</h1>
@endsection

@section('styles')

@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{url('/')}}"><i class="fa fa-home"></i> </a></li>
        <li class="breadcrumb-item">Configuracion</li>
        <li class="breadcrumb-item">{{ $entidades['tipo'] }}</li>
        <li class="breadcrumb-item active">rondas de {{ $entidades['tipo'] }}</li>
    </ol>
@endsection

@section('content')
<div class="row botones_accion mb-2">
    <br><br>
</div>
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title">Rondas de {{ $entidades['tipo'] }}</h3>
    </div>
    <div class="panel-body">
        <div id="all_toolbar">
            <div class="input-group">
                <input type="text" class="form-control pull-left" id="fechas" name="fechas" style="height: 40px; width: 200px" value="{{ $f1->format('d/m/Y').' - '.$f2->format('d/m/Y') }}">
                <span class="btn input-group-text btn-mint"  style="height: 40px"><i class="fas fa-calendar mt-1"></i></span>
            </div>
        </div>
        <table id="tablarondas"  data-toggle="table"
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
                    <th class="no-sort text-center w-2">Fecha</th>
                    <th style="width: 30%" class="no-sort">Descripcion</th>
                    <th data-sortable="true" class="text-center"  style="width: 50px"><i class="fad fa-building"></i> Edificios</th>
                    <th data-sortable="true" class="text-center" style="width: 50px"><i class="fad fa-layer-group"></i> Plantas</th>
                    <th data-sortable="true" class="text-center" style="width: 50px"><i class="fad fa-desktop-alt"></i> Puestos</th>
                    <th data-sortable="true">{{ $entidades['plural'] }}</th>
                    <th data-sortable="true"class="text-center" style="width: 100px">Completado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($rondas as $r)
                @php
                    $cnt_edificios=$detalles->where('id_ronda',$r->id_ronda)->pluck('id_edificio')->unique()->count();
                    $cnt_plantas=$detalles->where('id_ronda',$r->id_ronda)->pluck('id_planta')->unique()->count();
                    $cnt_puestos=$detalles->where('id_ronda',$r->id_ronda)->pluck('id_puesto')->unique()->count();
                    $puestos_si=$detalles->where('id_ronda',$r->id_ronda)->wherenotnull('fec_fin')->count();
                    $puestos_no=$detalles->where('id_ronda',$r->id_ronda)->wherenull('fec_fin')->count();
                    try{
                        $pct_completado=(100*$puestos_si/$cnt_puestos);
                    } catch(\Exception $e){
                        $pct_completado=0;
                    }
                    
                @endphp
                <tr class="hover-this" data-id="{{ $r->id_ronda }}">
                    <td class="text-center">
                        {!! beauty_fecha($r->fec_ronda) !!}
                    </td>
                    <td class="text-center" data-id="" >
                        {{ $r->des_ronda }}
                    </td>
                    
                    <td class="td text-center text-2x" data-id="">{{ $cnt_edificios }}</td>
                    <td class="td text-center text-2x" data-id="">{{ $cnt_plantas }}</td>
                    <td class="td text-center text-2x" data-id="">{{ $cnt_puestos }}</td>
                    <td class="td" data-id="">
                        @foreach(explode('#',$r->user_asignado) as $u)
                            <li>{{ $u }}</li>
                        @endforeach
                    </td>
                    <td class="text-3x font-bold text-center text-{{ color_porcentaje($pct_completado) }}">
                        {{ round($pct_completado) }} %
                    </td>
                    {{-- onclick="hoverdiv($(this),event,'toolbutton',{{ $puesto->id_puesto }},'{{ $puesto->cod_puesto }}','{{ $puesto->token }}');" --}}
                    <td class="text-center opts">
                        <a href="javascript:void(0)" ><i class="fa fa-bars add-tooltip opts" title="Acciones"></i></a>
                    </td>
                </tr>
                {{-- <tr id="detalle_ronda_{{ $r->id_ronda }}" data-id="{{ $r->id_ronda }}" style="display: none:">
                    <td colspan="8"></td>
                </tr> --}}
                @endforeach
            </tbody>
        </table> 
    </div>
</div>
<div class="modal fade" id="ronda-limpieza" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span></button>
                <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                <span class="float-right" id="loading" style="display: none"><img src="{{ url('/img/loading.gif') }}" style="height: 25px;">LOADING</span><h1 class="modal-title">Ronda de {{ $entidades['tipo'] }} <span class="idronda">#</span></h1>
            </div>
            <div class="modal-body" id="detalle_modal">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-id_ronda=0 id="btn_completar"><i class="fad fa-check-double"></i> Completar ronda</button>
                <a  class="btn btn-info" id="btn_print" href=""><i class="fad fa-print"></i> Imprimir</a>
                <button type="button" data-dismiss="modal" class="btn btn-warning">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection



@section('scripts')
    <script>


        $('{{ $entidades['menu1'] }}').addClass('active active-sub');
        $('{{ $entidades['menu2'] }}').addClass('active-link');
       

        $('#tablarondas').on('click-cell.bs.table', function(e, value, row, $element){
           //console.log($element._data.id);
           @if(Auth::user()->nivel_acceso==10)
                window.location.href = '{{ url('/rondas/detallelimp/') }}/'+$element._data.id;
           @else
            $('#loading').show();
            $('#ronda-limpieza').modal('show');
            $('#detalle_modal').load("{{ url('/rondas/view/') }}/"+$element._data.id+"/0",function(){
                $('#loading').hide();
            })
            $('#btn_print').attr('href',"{{ url('/rondas/view/')}}/"+$element._data.id+"/1");
            $('.idronda').html('#'+$element._data.id);
            $('#btn_completar').data('id_ronda',$element._data.id)
           @endif
           //animateCSS('#detalle_ronda'+$element._data.id,'bounceInRight');
        });

        $('.add-tooltip').tooltip({container:'body'});

        //Date range picker
        $('#fechas').daterangepicker({
            autoUpdateInput: false,
            locale: {
                format: '{{trans("general.date_format")}}',
                applyLabel: "OK",
                cancelLabel: "Cancelar",
                daysOfWeek:["{{trans('general.domingo2')}}","{{trans('general.lunes2')}}","{{trans('general.martes2')}}","{{trans('general.miercoles2')}}","{{trans('general.jueves2')}}","{{trans('general.viernes2')}}","{{trans('general.sabado2')}}"],
                monthNames: ["{{trans('general.enero')}}","{{trans('general.febrero')}}","{{trans('general.marzo')}}","{{trans('general.abril')}}","{{trans('general.mayo')}}","{{trans('general.junio')}}","{{trans('general.julio')}}","{{trans('general.agosto')}}","{{trans('general.septiembre')}}","{{trans('general.octubre')}}","{{trans('general.noviembre')}}","{{trans('general.diciembre')}}"],
                firstDay: {{trans("general.firstDayofWeek")}}
            },
            opens: 'right',
        }, function(start_date, end_date) {
            $('#fechas').val(start_date.format('DD/MM/YYYY')+' - '+end_date.format('DD/MM/YYYY'));
            window.location.href = '{{ url('/rondas/index/') }}/'+start_date.format('YYYY-MM-DD')+'/'+end_date.format('YYYY-MM-DD');
        });

        $('#btn_completar').click(function(){
            get_ajax("{{ url('/rondas/completar_ronda/') }}/"+$(this).data('id_ronda')+'/'+id_user,'loading',function(){
                console.log('ok0');
            });
        })

    </script>
@endsection
