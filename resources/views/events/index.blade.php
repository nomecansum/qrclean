@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Gestión de cámaras</h1>
@endsection

@section('styles')

@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{url('/')}}"><i class="fa fa-home"></i> </a></li>
        <li class="breadcrumb-item">Configuracion</li>
        <li class="breadcrumb-item active">eventos</li>
    </ol>
@endsection

@section('content')
@php
    Carbon\Carbon::setLocale(session('lang'));
    setlocale(LC_TIME, 'Spanish');
    //dd($eventos);
@endphp

<div class="row botones_accion">
    <div class="col-md-4">

    </div>
    <div class="col-md-4">
        <br>
    </div>
    <div class="col-md-4 text-right">
        <div class="btn-group btn-group-sm pull-right mb-1 " role="group">
            @if (checkPermissions(['Eventos'],['C']))<a href="{{url(config('app.carpeta_asset').'/add')}}" class="btn float-right hidden-sm-down btn-success"><i class="fas fa-plus-circle"></i> Nueva regla</a>@endif
        </div>
    </div>
</div>
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title">Eventos</h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
							<h2 class="card-title float-left col">{{ __('general.eventos') }}</h2>
						</div>
                        <div class="table-responsive">
                            <table id="tabledeps" class="table table-bordered table-striped table-condensed table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 1%">Id</th>
                                        <th style="width: 30%">{{ __('general.nombre') }}</th>
                                        <th  style="width: 15%">{{ __('general.comando') }}</th>
                                        <th>{{ __('general.act') }}</th>
                                        <th>{{ __('general.acciones') }}</th>
                                        <th>{{ __('general.ejecucion') }}</th>
                                        <th  style="width: 15%">{{ __('general.clientes') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($eventos as $ev)

                                        <tr class="hover-this" @if((checkPermissions(['Eventos'],['W'])) || fullAccess()) data-href="{{url(config('app.carpeta_asset').'/edit/'.$ev->cod_regla)}}"@endif>
                                            <td>{{$ev->cod_regla}}</td>
                                            <td>{{$ev->nom_regla}}</td>
                                            <td>{{ str_replace(".php","",str_replace("_"," ",basename($ev->nom_comando))) }}</td>
                                            <td class="text-center pt-4">
                                                {{-- <div class="custom-control custom-checkbox" style="display: inline-block; margin-right: 6px">
                                                    <input {{$ev->mca_activa == "S" ? 'checked' : ''}} type="checkbox" class="check-f2f custom-control-input">
                                                    <label class="custom-control-label"></label>
                                                </div> --}}
                                                @if($ev->mca_activa == "S")
												<div class="pretty p-icon p-curve p-smooth p-0 m-0 mt-3 p-locked">
													<input type="checkbox" checked type="checkbox" class="check-f2f" />
													<div class="state p-success p-smooth" style="font-size:22px">
														<i class="icon fa fa-check"></i>
														<label></label>
													</div>
												</div>
											@endif
                                            </td>
                                            <td nowrap="nowrap">
                                                {{$ev->iteraciones}} {{ __('general.iteraciones') }}<br>
                                                {{$ev->acciones}} {{ __('general.acciones') }}
                                            </td>
                                            <td nowrap="nowrap">Prox: {!! beauty_fecha($ev->fec_prox_ejecucion)!!}<br>Ult: {!! beauty_fecha($ev->fec_ult_ejecucion)!!}</td>
                                            <td style="position: relative; padding-left: 40px;">
                                                @php
                                                $clientes=array_filter(explode(',',$ev->clientes));
                                                $clientes=DB::table('clientes')->select('nom_cliente')->whereIn('id_cliente',$clientes)->pluck('nom_cliente')->toArray();
                                                @endphp
                                                @foreach ($clientes as $c)
                                                        <li>{{$c}}</li>
                                                @endforeach
                                                <div class="pull-right floating-like-gmail mt-3" style="width: 200px">
                                                    <a href="javascript:void(0);" data-cod_regla="{{$ev->cod_regla}}"  class="btn btn-xs btn-info log_regla">{{ __('general.detalles') }}</a>
                                                    @if(checkPermissions(['Eventos'],['W']))<a href="{{url(config('app.carpeta_asset').'/edit',$ev->cod_regla)}}" class="btn btn-xs btn-success">{{trans('general.edit')}}</a>@endif
                                                    @if(checkPermissions(['Eventos'],['D']))<a href="#eliminar-regla-{{$ev->cod_regla}}" data-toggle="modal" class="btn btn-xs btn-danger">{{trans('general.delete')}}</a>@endif
                                                </div>
                                                <div class="modal fade" id="eliminar-regla-{{$ev->cod_regla}}">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title">{{ __('general.eliminar') }}</h4>
                                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                {{ __('eventos.eliminar_la_regla') }} {{ $ev->nom_regla }}?
                                                            </div>
                                                            <div class="modal-footer">
                                                                <a class="btn btn-warning" href="{{url(config('app.carpeta_asset').'/delete',$ev->cod_regla)}}">{{trans('general.yes')}}</a>
                                                                <button type="button" data-dismiss="modal" class="btn btn-info">{{trans('general.cancelar')}}</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr id="log_evento_{{$ev->cod_regla}}" style="display: none">
                                            <td colspan="8" id="detail_evento_{{$ev->cod_regla}}"></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


@section('scripts')
    <script src="{{url('/plugins/jQuery-slimScroll-1.3.8/jquery.slimscroll.min.js')}}"></script>
    <script>
        $('.configuracion').addClass('active active-sub');
        $('.eventos').addClass('active-link');

        $('.log_regla').click(function(){
            event.stopPropagation();
            $("#log_evento_"+$(this).data('cod_regla')).show();
            $("#detail_evento_"+$(this).data('cod_regla')).load("{{url(config('app.carpeta_asset').'/detalle_evento')}}/"+$(this).data('cod_regla'), function(){
                //animateCSS("#log_evento_"+$(this).data('cod_regla'),"fadeIn");
            });
            //console.log("Log "+$(this).data('cod_regla'));
        });
    </script>
    
@endsection
