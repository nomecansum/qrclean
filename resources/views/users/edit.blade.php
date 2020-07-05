@extends('layout')
@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{url('/')}}"><i class="fa fa-home"></i> </a></li>
        <li class="breadcrumb-item">Configuracion</li>
        <li class="breadcrumb-item"><a href="{{url('/users')}}">Usuarios</a></li>
        <li class="breadcrumb-item active">Editar usuario {{ !empty($users->name) ? $users->name : '' }}</li>
    </ol>
@endsection

@section('styles')
    <!--Bootstrap FLEX Stylesheet [ REQUIRED ]-->
    <link href="{{ url('/css/bootstrap-grid.min.css') }}" rel="stylesheet">
@endsection

@section('content')



    <div class="panel">
        <div class="panel-heading">
            <div class="panel-control">
                <button class="btn btn-default" data-panel="dismiss"><i class="demo-psi-cross"></i></button>
            </div>
            <h3 class="panel-title">Editar usuario {{ !empty($users->name) ? $users->name : '' }}</h3>
        </div>
        <div class="panel-body">

            @if ($errors->any())
                <ul class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

            <form method="POST" action="{{ route('users.users.update', $users->id) }}" id="edit_users_form" name="edit_users_form" accept-charset="UTF-8" class="form-horizontal mt-4 form-ajax" enctype="multipart/form-data">
            {{ csrf_field() }}
            {{-- <input name="_method" type="hidden" value="POST"> --}}
            @include ('users.form', [
                                        'users' => $users,
                                      ])

                <div class="form-group">
                    <div class="col-md-10">
                        <input class="btn btn-primary btn-lg" type="submit" value="Actualizar">
                    </div>
                </div>
            </form>

        </div>
    </div>
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">Plantas en las que puede reservar</h3>
        </div>
        <div class="panel-body" id="plantas_usuario">

        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $(function(){
            $('#plantas_usuario').load("{{ url('users/plantas/'.$users->id) }}")
        });
        $('.configuracion').addClass('active active-sub');
	    $('.usuarios').addClass('active-link');
    </script>
@endsection
@include('layouts.scripts_panel')