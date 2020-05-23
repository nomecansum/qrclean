
@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Gestión de puestos</h1>
@endsection

@section('styles')

@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{url('/')}}"><i class="fa fa-home"></i> </a></li>
        <li class="breadcrumb-item">Imprimir etiquetas de puesto</li>
        {{--  <li class="breadcrumb-item"><a href="{{url('/users')}}">Usuarios</a></li>
        <li class="breadcrumb-item active">Editar usuario {{ !empty($users->name) ? $users->name : '' }}</li>  --}}
    </ol>
@endsection

@section('content')
    <div class="row  rounded mb-4">
        <div class="col-md-9">
           
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-warning float-right btn_print font-20 mr-0" style=""><i class="mdi mdi-printer mdi-24px"></i> Imprimir</button>
        </div>
    </div>
    <div class="row" style="background-color: #fff" id="printarea">
        @foreach($puestos as $puesto)
            <div class="float-left mb-4 mr-4" style="width: 200px; display: inline-block;">
                <div class="mb-0 pb-0">
                    <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(200)->generate(config('app.url_base_scan').$puesto->token)) !!} ">
                    {{config('app.url_base_scan').$puesto->token}}
                </div>
                <div class="w-100 bg-white text-center font-bold mt-0 pb-2" style="color: {{$puesto->val_color}}; background-color: #fff">
                    <i class="{{$puesto->val_icono}}"></i>  {{$puesto->des_puesto}}
                </div>
            </div>
    
        @endforeach
        </div>
@endsection

@section('scripts')
<script>
	

    $('.btn_print').click(function(){
            $('#printarea').printThis({
                importCSS: true,
                importStyle: true,
            });
        })



</script>
@endsection
