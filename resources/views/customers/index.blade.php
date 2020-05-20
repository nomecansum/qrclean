@extends('layout')

@section('css')

@endsection

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-6 col-8 align-self-center">
            <h3 class="text-themecolor mb-0 mt-0">{{trans('strings.business')}}</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{trans('strings.home')}}</a></li>
                <li class="breadcrumb-item active">{{trans('strings.business')}}</li>
            </ol>
        </div>
        <div class="col-md-6 col-4 align-self-center">
			@if (checkPermissions(['Empresas'],["W"]))<a href="{{url('business/create')}}" class="btn float-right hidden-sm-down btn-success"><i class="mdi mdi-plus-circle"></i> {{trans('strings.create_client')}}</a>@endif
        </div>
    </div>
    <div class="row">
        <div class="col-12">
			<div class="card">
			    <div class="card-body">
			        <h2 class="card-title">{{trans('strings.business')}}</h2>
			        <div class="table-responsive m-t-40">
			            <table id="myTable" class="table table-bordered table-condensed table-hover">
			                <thead>
			                    <tr>
			                        <!--th>{{trans('strings.id')}}</th-->
			                        <th>{{trans('strings._configuration.business.logo')}}</th>
			                        <th>{{trans('strings._configuration.business.name')}}</th>
			                        <th>{{trans('strings._configuration.business.cod_sistema')}}</th>
			                        <th>{{trans('strings._configuration.business.suprabusiness')}}</th>
			                        <th>{{trans('strings._configuration.business.information')}}</th>
			                        {{-- <th></th> --}}
			                    </tr>
			                </thead>
			                <tbody>
								@foreach ($clientes as $cus)
			                		<tr class="hover-this" @if (checkPermissions(['Empresas'],["W"]))data-href="{{url('business/edit',$cus->cod_cliente)}}"@endif>
			                			<!--td>{{$cus->cod_cliente}}</td-->
			                			<td>
			                			    @isset($cus->img_logo)
			                			      <img src="{{url('uploads/customers/images',$cus->img_logo)}}" width="30px" alt="">
			                			    @else
			                			      <img src="{{url('uploads/customers/images',$cus->img_logo)}}" width="30px" alt="">
			                			    @endif
			                			</td>
			                			<td>{{$cus->nom_cliente}}</td>
			                			<td>{{ $cus->cod_sistema}}</td>
			                			<td>{{ $cus->emp_matriz}}</td>
			                			<td style="position: relative;">{{$cus->nom_contacto}}
			                				<div class="floating-like-gmail">
												@if (checkPermissions(['Empresas'],["C"]))<a href="{{url('business/edit',$cus->cod_cliente)}}" class="btn btn-xs btn-success">{{trans('strings.edit_client')}}</a>@endif
			                					@if (checkPermissions(['Empresas'],["D"]))<a href="#eliminar-usuario-{{$cus->cod_cliente}}" data-toggle="modal" class="btn btn-xs btn-danger">{{trans('strings.delete_client')}}</a>@endif
			                					@if (checkPermissions(['Empresas'],["D"]))<a href="#eliminar-empresa-{{$cus->cod_cliente}}" data-toggle="modal" class="btn btn-xs btn-danger">¡Borrado completo!</a>@endif
			                				</div>
			                				@if (checkPermissions(['Empresas'],["D"]))
    			                				<div class="modal fade" id="eliminar-usuario-{{$cus->cod_cliente}}">
    			                					<div class="modal-dialog modal-md">
    			                						<div class="modal-content"><div><img src="/imgs/cucoweb_20.png" class="float-right"></div>
    			                							<div class="modal-header"><i class="mdi mdi-comment-question-outline text-warning mdi-48px"></i><b>
    			                								{{trans('strings._configuration.business.delete_business')}}</b>
    														</div>
    														<div class="modal-body text-left">
    															La empresa tiene:<br>
    															<ul>
    																<li>{{ $cus->empleados }} Empleados</li>
    																<li>{{ $cus->centros }} Centros</li>
    																<li>{{ $cus->departamentos }} Departamentos</li>
    																<li>{{ $cus->dispositivos }} Dispositivos</li>
    															</ul>
    														</div>
    			                							<div class="modal-footer">
    			                								<a class="btn btn-info" href="{{url('business/delete',$cus->cod_cliente)}}">{{trans('strings.yes')}}</a>
    			                								<button type="button" data-dismiss="modal" class="btn btn-warning">{{trans('strings.cancel')}}</button>
    			                							</div>
    			                						</div>
    			                					</div>
    			                				</div>
    			                				<div class="modal fade" id="eliminar-empresa-{{$cus->cod_cliente}}">
                                                    <div class="modal-dialog modal-md">
                                                        <div class="modal-content"><div><img src="/imgs/cucoweb_20.png" class="float-right"></div>
                                                            <div class="modal-header"><i class="mdi mdi-comment-question-outline text-warning mdi-48px"></i>
                                                                <b>Esta opción no se podrá deshacer! Seguro que quiere seguir?</b>
                                                            </div>
                                                            <div class="modal-body text-left">
                                                                La empresa tiene:<br>
                                                                <ul>
                                                                    <li>{{ $cus->empleados }} Empleados</li>
                                                                    <li>{{ $cus->centros }} Centros</li>
                                                                    <li>{{ $cus->departamentos }} Departamentos</li>
                                                                    <li>{{ $cus->dispositivos }} Dispositivos</li>
                                                                </ul>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <a class="btn btn-info" href="{{url('business/deleteCompleto',$cus->cod_cliente)}}">¡{{trans('strings.yes')}}!</a>
                                                                <button type="button" data-dismiss="modal" class="btn btn-warning">{{trans('strings.cancel')}}</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
    			                		   @endif
			                			</td>
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
@stop
