@extends('layout')
@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{url('/')}}"><i class="fa fa-home"></i> </a></li>
        <li class="breadcrumb-item">Configuracion</li>
        <li class="breadcrumb-item"><a href="{{url('/users')}}">Usuarios</a></li>
        <li class="breadcrumb-item active">Editar usuario {{ !empty($users->name) ? $users->name : '' }}</li>
    </ol>
@endsection

@section('content')

<div class="row" style="margin-top: 10px">
    <div class="col-md-4">

    </div>
    <div class="col-md-7">
        <br>
    </div>
    <div class="col-md-1 text-right">
        <div class="btn-group btn-group-sm pull-right" role="group" style="margin-right: 20px;">
            <a href="{{ route('users.users.index') }}" class="btn btn-primary" title="Listado">
                <span class="fa fa-list pt-2" aria-hidden="true"></span>
            </a>
            <a href="{{ route('users.users.create') }}" class="btn btn-success" title="Nuevo usuario">
                <span class="fa fa-plus-square pt-1" style="font-size: 20px" aria-hidden="true"></span>
            </a>
        </div>
    </div>
</div>

    <div class="panel panel-default">
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

@endsection
