@extends('layout')
@section('title')
<h1 class="page-header text-overflow pad-no">Usuarios</h1>
@endsection
@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{url('/')}}"><i class="fa fa-home"></i> </a></li>
        <li class="breadcrumb-item">Configuracion</li>
        <li class="breadcrumb-item"><a href="{{url('/users')}}">Usuarios</a></li>
        <li class="breadcrumb-item active">Editar usuario {{ !empty($users->name) ? $users->name : '' }}</li>
    </ol>
@endsection

@section('content')

    <div class="panel panel-default">
        <div class="panel-body">

            @if ($errors->any())
                <ul class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

            <form method="POST" action="{{ route('users.users.store') }}" accept-charset="UTF-8" id="create_users_form" name="create_users_form" class="form-horizontal form-ajax"  enctype="multipart/form-data">
            {{ csrf_field() }}
            @include ('users.form', [
                                        'users' => null,
                                      ])

                <div class="form-group">
                    <div class="col-md-10">
                        <input class="btn btn-primary btn-lg" type="submit" value="Guardar">
                    </div>
                </div>

            </form>

        </div>
    </div>

@endsection


