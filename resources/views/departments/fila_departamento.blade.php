<tr class="hover-this" @if (checkPermissions(['Departamentos'],["W"]))data-href="{{url('departments/edit',$d->cod_departamento)}}"@endif>
    @if(isAdmin())<td>{{$d->cod_departamento}}</td>@endif
    <td style="padding-left: {{ $d->num_nivel>1 ? (($d->num_nivel-1)*40) : "" }}px">{{$d->nom_departamento}}</td>
    <td style="position: relative; padding-left: 40px;">{{ $d->empleados}}
       
        <div class="pull-right floating-like-gmail mt-3" style="width: 400px;">
            <div class="btn-group btn-group pull-right ml-1" role="group">
                {{-- <label style="cursor: pointer" data-toggle="modal" data-target="#employs-{{$d->cod_departamento}}" class="label label-info" title="Empleados"><i class="mdi mdi-account"></i> ({{$d->empleados}})</label> --}}
                @if (checkPermissions(['Departamentos'],["W"]))<a href="#" data-id="{{$d->cod_departamento}}" class="btn btn-xs btn-info btn_editar add-tooltip" onclick="editar({{$d->cod_departamento}})"><span class="fa fa-pencil pt-1" aria-hidden="true" ></span> Edit</a></a>@endif
                @if (checkPermissions(['Departamentos'],["D"]))<a href="#eliminar-usuario-{{$d->cod_departamento}}" data-toggle="modal" onclick="del({{$d->cod_departamento}})" class="btn btn-xs btn-danger"><span class="fa fa-trash" aria-hidden="true"></span> Del</a>@endif
            </div>
        </div>
        
        <div class="modal fade" id="eliminar-usuario-{{$d->cod_departamento}}">
            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-header">
                        <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                        <h1 class="modal-title text-nowrap">Borrar departamento </h1>
                        <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
                            <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
                        </button>
                    </div>    
                    <div class="modal-body">
                        ¿Borrar departamento {{ $d->nom_departamento }}?
                    </div>

                    <div class="modal-footer">
                        <a class="btn btn-info" href="{{url('departments/delete',$d->cod_departamento)}}">{{trans('strings.yes')}}</a>
                        <button type="button" data-dismiss="modal" class="btn btn-warning close" onclick="cerrar_modal()">{{trans('strings.cancel')}}</button>
                    </div>
                </div>
            </div>
        </div>
    </td>
</tr>