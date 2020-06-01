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



    <div class="panel">
        <div class="panel-heading">
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

@endsection
