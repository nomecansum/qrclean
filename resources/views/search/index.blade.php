@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Resultados de busqueda</h1>
@endsection

@section('styles')

@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
        <li class="breadcrumb-item active">buscar "{{ !empty($r->txt_buscar) ? $r->txt_buscar : '' }}"</li>
    </ol>
@endsection

@section('content')
<div class="row botones_accion mb-2">
    <br><br>
</div>
<div class="row">
@if($clientes->count()>0 && checkPermissions(['Clientes'],["R"]))
<div class="card mt-3 col-md-4">
    <div class="card-header">

    </div>
    <div class="card-body">
        <div class="card-body">
            <h4 onclick="$('#bloque_clientes').toggle();"><i class="fa-solid fa-user-tie"></i> Empresas [{{ $clientes->count() }}]</h4>
            <ul id="bloque_clientes">
            @foreach($clientes as $cliente)
                <li><a href="{{ url('/clientes/edit/'.$cliente->id_cliente) }}">
                    @if(isset($cliente->img_logo) && $cliente->img_logo<>'')
                        <img src="{{Storage::disk(config('app.img_disk'))->url('img/clientes/images/'.$cliente->img_logo)}}" width="40px" alt="">
                    @endif
                    {{ $cliente->nom_cliente }}</a></li>
            @endforeach
            </ul>
        </div>
    </div>
</div>
@endif
@if($usuarios->count()>0 && checkPermissions(['Usuarios'],["R"]))
<div class="card mt-3 col-md-4">
    <div class="card-header">

    </div>
    <div class="card-body">
        <div class="card-body">
            <h4 onclick="$('#bloque_empleados').toggle();"><i class="fa-solid fa-user"></i> Usuarios [{{ $usuarios->count() }}]</h4>
            <ul id="bloque_empleados">
            @foreach($usuarios as $usuario)
                <li style="margin: 5 0 5 0"><a href="{{ url('/users/edit/'.$usuario->id) }}">
                    @if (isset($users->img_usuario ) && $users->img_usuario!='')
                        <img src="{{ Storage::disk(config('app.img_disk'))->url('img/users/'.$usuario->img_usuario) }}" class="img-md rounded-circle" style="height:40px; width:40px; object-fit: cover;">
                    @else
                        {!! icono_nombre($usuario->name,40,14) !!}
                    @endif
                    {{ $usuario->name }} <span class="font-14 text-muted">{{ count(clientes())>1?"[".$usuario->nom_cliente."]":"" }}</a></span></li>
            @endforeach
            </ul>
        </div>
    </div>
</div>
@endif
@if($edificios->count()>0 && checkPermissions(['Edificios'],["R"]))
<div class="card mt-3 col-md-4">
    <div class="card-header">

    </div>
    <div class="card-body">
        <div class="card-body">
            <h4 onclick="$('#bloque_edificios').toggle();"><i class="fa-solid fa-building"></i> Edificios [{{ $edificios->count() }}]</h4>
            <ul id="bloque_edificios">
            @foreach($edificios as $edificio)
                <li style="margin: 5 0 5 0"><a href="{{ url('/edificios/edit/'.$edificio->id_edificio) }}">
                    {{ $edificio->des_edificio }} <span class="font-14 text-muted">{{ count(clientes())>1?"[".$edificio->nom_cliente."]":"" }}</a></span></li>
            @endforeach
            </ul>
        </div>
    </div>
</div>
@endif
</div>
@endsection


@section('scripts')
    <script>
        $('.SECCION_MENU').addClass('active active-sub');
        $('.ITEM_MENU').addClass('active');
    </script>
@endsection
