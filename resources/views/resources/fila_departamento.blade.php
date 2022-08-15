<tr class="hover-this" @if (checkPermissions(['Departamentos'],["W"]))data-href="{{url('departments/edit',$d->cod_departamento)}}"@endif>
    @if(isAdmin())<td>{{$d->cod_departamento}}</td>@endif
    <td style="padding-left: {{ $d->num_nivel>1 ? (($d->num_nivel-1)*40) : "" }}px">{{$d->nom_departamento}}</td>
    <td style="position: relative; padding-left: 40px;">{{ $d->empleados}}
       
        <div class="pull-right floating-like-gmail mt-3" style="width: 300px;">
            {{-- <label style="cursor: pointer" data-toggle="modal" data-target="#employs-{{$d->cod_departamento}}" class="label label-info" title="Empleados"><i class="mdi mdi-account"></i> ({{$d->empleados}})</label> --}}
            @if (checkPermissions(['Departamentos'],["W"]))<a href="#" data-id="{{$d->cod_departamento}}" class="btn btn-xs btn-info btn_editar add-tooltip" onclick="editar({{$d->cod_departamento}})"><span class="fa fa-pencil pt-1" aria-hidden="true" ></span> Edit</a></a>@endif
            @if (checkPermissions(['Departamentos'],["D"]))<a href="#eliminar-usuario-{{$d->cod_departamento}}" data-toggle="modal" class="btn btn-xs btn-danger"><span class="fa fa-trash" aria-hidden="true"></span> Del</a>@endif
        </div>
        
        <div class="modal fade" id="eliminar-usuario-{{$d->cod_departamento}}">
            <div class="modal-dialog">
                <div class="modal-content">
                	<div class="modal-header">
		                <h4 class="modal-title">Borrar departamento</h4>
		                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		            </div>
                    <div class="modal-body">
		            	¿Borrar departamento {{ $d->nom_departamento }}?
		            </div>
                    <div class="modal-footer">
                        <a class="btn btn-info" href="{{url('departments/delete',$d->cod_departamento)}}">{{trans('strings.yes')}}</a>
                        <button type="button" data-dismiss="modal" class="btn btn-warning">{{trans('strings.cancel')}}</button>
                    </div>
                </div>
            </div>
        </div>
    </td>
</tr>