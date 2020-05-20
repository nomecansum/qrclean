@extends('layout')

@section('css')

@endsection
@section('breadcrumb')
<!-- Content Header (Page header) -->
<ol class="breadcrumb">
    <li><a href="{{url('/')}}"><i class="demo-pli-home"></i> </a></li>
    <li class="">Configuracion</li>
    <li class="active">Usuarios</li>
</ol>

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
        <div class="row">
            <div class="col-md-4">

            </div>
            <div class="col-md-7">
                <br>
            </div>
            <div class="col-md-1 text-right ">
                <div class="btn-group btn-group-sm pull-right v-middle mt-2" role="group" style="margin-right: 20px;">
                    <a href="{{ route('users.users.create') }}" class="btn btn-success" title="Nuevo dashboard">
                        <i class="fa fa-plus-square" style="font-size: 20px" aria-hidden="true"></i> Nuevo
                    </a>
                </div>
            </div>
        </div>

        @if(count($usersObjects) == 0)
            <div class="panel-body text-center">
                <h4>No Users Available.</h4>
            </div>
        @else
        <div class="panel-body panel-body-with-table">
            <div class="table-responsive">

                <table class="table table-striped table-hover ">
                    <thead>
                        <tr>
                            <th style="width:30px"></th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Perfil</th>
                            <th>Ult acceso</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($usersObjects as $users)
                        <tr class="hover-this" onclick="javascript: document.location='{{ route('users.users.edit', $users->id ) }}'">
                            <td class="center">
                                @if (isset($users->img_usuario ) && $users->img_usuario!='')
                                    <img src="{{ url('img/users/'.$users->img_usuario) }}" style="height: 30px">
                                @else
                                    {!! icono_nombre($users->name) !!}
                                @endif
                            </td>
                            <td class="pt-3">{{ $users->name }}</td>
                            <td>{{ $users->email }}</td>
                            <td>
                                @if(isset($users->cod_nivel))
                                    {{ DB::table('niveles_acceso')->where('cod_nivel',$users->cod_nivel)->value('des_nivel_acceso')}}
                                @else
                                    <div>
                                        <i class="fa fa-warning" style="color:orange">Pendiente</i>
                                    </div>
                                @endif
                            </td>
                            <td>{!! beauty_fecha($users->last_login) !!}</td>
                            <td style="vertical-align: middle">
                                <form method="POST" action="{!! route('users.users.destroy', $users->id) !!}" accept-charset="UTF-8">
                                <input name="_method" value="DELETE" type="hidden">
                                {{ csrf_field() }}
                                    <div class="btn-group btn-group-xs pull-right floating-like-gmail" role="group">
                                        <a href="{{ route('users.users.edit', $users->id ) }}" class="btn btn-info  add-tooltip" title="Editar Usuario"  style="float: left"><span class="fa fa-pencil pt-1" ></span></a>
                                        <button type="submit" class="btn btn-danger add-tooltip" style="float: left" title="Borrar usuario" onclick="if(confirm(&quot;¿Seguro que quiere borrar el usuario?.&quot;)){document.location='{{ url('users/delete/'.$users->id) }}'}"  style="float: right">
                                            <span class="fa fa-trash"></span>
                                        </button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="panel-footer">
            {!! $usersObjects->render() !!}
        </div>
        @endif
    </div>
@endsection
