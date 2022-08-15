

@foreach($usersObjects as $users)
    <tr class="hover-this" data-id="{{ $users->id }}" data-href="{{ route('users.users.edit', $users->id ) }}">
        <td class="text-center">
            <input type="checkbox" class="form-control chkuser magic-checkbox" name="lista_id[]" data-id="{{ $users->id }}" id="chku{{ $users->id }}" value="{{ $users->id }}">
            <label class="custom-control-label"   for="chku{{ $users->id }}"></label>
        </td>
        <td class="center">
            @if (isset($users->img_usuario ) && $users->img_usuario!='')
                <img src="{{ Storage::disk(config('app.img_disk'))->url('img/users/'.$users->img_usuario) }}" class="img-circle" style="height:50px; width:50px; object-fit: cover;">
            @else
                {!! icono_nombre($users->name) !!}
            @endif
            @if(config('app.env')=='local')[#{{ $users->id }}]@endif
        </td>
        <td class="pt-3">{{ $users->name }}</td>
        <td>
            @if(isset($users->cod_nivel))
                {{$users->des_nivel_acceso}}
            @else
                <div>
                    <i class="fa fa-warning" style="color:orange">Pendiente</i>
                </div>
            @endif
        </td>

        <td>{!! beauty_fecha($users->last_login) !!}</td>
        <td style="vertical-align: middle">
            {{ $users->email }}
            <form method="POST" action="{!! route('users.users.destroy', $users->id) !!}" accept-charset="UTF-8">
            <input name="_method" value="DELETE" type="hidden">
            {{ csrf_field() }}
                <div class="pull-right floating-like-gmail" role="group" style="width: 170px; position: relative">
                    @if (checkPermissions(['ReLogin'],["R"]))<a href="{{url('relogin',$users->id)}}" class="btn btn-xs btn-warning"><i class="fa fa-user" ></i> Suplantar</a>@endif
                    <a href="{{ route('users.users.edit', $users->id ) }}" class="btn btn-xs btn-info  add-tooltip" title="Editar Usuario"  style="float: left"><span class="fa fa-pencil pt-1" ></span> Edit</a>
                    <a class="btn btn-xs btn-danger add-tooltip ml-1 btn_eliminar"  data-target="#eliminar-usuario" data-toggle="modal" style="float: left" title="Borrar usuario" onclick="$('#txt_borrar').html('{{ $users->name }}'); $('#id_usuario_borrar').val({{ $users->id }})" data-name="{{ $users->name }}"   style="float: right">
                        <span class="fa fa-trash"></span> Del
                    </a>
                </div>
            </form>
        </td>
    </tr>
@endforeach
