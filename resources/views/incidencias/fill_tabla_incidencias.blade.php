@foreach ($incidencias as $inc)
    @php
        $descripcion="";
        if(isset($inc->txt_incidencia) && $inc->txt_incidencia!=''){
            $descripcion=substr($inc->txt_incidencia,0,50);
        }
        if(isset($inc->des_incidencia) && $inc->des_incidencia!=''){
            $descripcion=substr($inc->des_incidencia,0,50);
        }
    @endphp
    <tr class="hover-this" @if (checkPermissions(['Clientes'],["W"])) @endif>
        <td>{{$inc->id_incidencia}}</td>
        <td class="text-center"><i class="{{ $inc->val_icono }} fa-2x" style="color:{{ $inc->val_color }}"></i></td>
        <td>
            <div class="rounded"  style="padding: 3px; width:100%: height: 100%; background-color: {{ $inc->val_color  }}; {{ txt_blanco($inc->val_color=='text-white')?'color: #fff':'color:#222' }}">
                {{$inc->des_tipo_incidencia}}
            </div>
        </td>
        <td>{{ $inc->des_puesto}}</td>
        <td>{{ $inc->des_edificio}}</td>
        <td>{{ $inc->des_planta}}</td>
        <td>{!! beauty_fecha($inc->fec_apertura)!!}</td>
        <td>@if(isset($inc->fec_cierre)) <div class="bg-success text-xs text-white text-center rounded b-all" style="padding: 5px" id="cell{{$inc->id_incidencia}}">Cerrada</div> @else  <div class="bg-pink  text-xs text-white text-center rounded b-all"  style="padding: 5px" id="cell{{$inc->id_incidencia}}">Abierta </div>@endif</td>  
        
        <td style="position: relative; vertical-align: middle" class="pt-2">
            {{ $descripcion}}
            <div class="floating-like-gmail mt-2 w-100" style="width: 100%">
                @if (checkPermissions(['Incidencias'],["W"]))<a href="#" title="Ver incidencia " data-id="{{ $inc->id_incidencia }}" class="btn btn-xs btn-info add-tooltip btn_edit" onclick="edit({{ $inc->id_incidencia }})"><span class="fa fa-eye pt-1" aria-hidden="true"></span> Ver</a>@endif
                @if (!isset($inc->fec_cierre) && checkPermissions(['Incidencias'],["W"]))<a href="#accion-incidencia" title="Acciones incidencia" data-toggle="modal" class="btn btn-xs btn-warning add-tooltip btn-accion" data-desc="{{ $inc->des_incidencia}}" data-id="{{ $inc->id_incidencia}}" id="boton-accion{{ $inc->id_incidencia }}" onclick="accion_incidencia({{ $inc->id_incidencia}})"><span class="fad fa-plus pt-1" aria-hidden="true"></span> Accion</a>@endif
                @if (!isset($inc->fec_cierre) && checkPermissions(['Incidencias'],["W"]))<a href="#cerrar-incidencia" title="Cerrar incidencia" data-toggle="modal" class="btn btn-xs btn-success add-tooltip btn-cierre" data-desc="{{ $inc->des_incidencia}}" data-id="{{ $inc->id_incidencia}}" id="boton-cierre{{ $inc->id_incidencia }}" onclick="cierre_incidencia({{ $inc->id_incidencia}})"><span class="fad fa-thumbs-up pt-1" aria-hidden="true"></span> Cerrar</a>@endif
                @if (isset($inc->fec_cierre) && checkPermissions(['Incidencias'],["W"]))<a href="#reabrir-incidencia" title="Reabrir incidencia" data-toggle="modal" class="btn btn-xs btn-success add-tooltip btn-reabrir" data-desc="{{ $inc->des_incidencia}}" data-id="{{ $inc->id_incidencia}}" id="boton-reabrir{{ $inc->id_incidencia }}" onclick="reabrir_incidencia({{ $inc->id_incidencia}})"><i class="fad fa-external-link-square-alt"></i> Reabrir</a>@endif
                @if (checkPermissions(['Incidencias'],["D"]))<a href="#eliminar-incidencia-{{$inc->id_incidencia}}" title="Borrar incidencia" data-toggle="modal" class="btn btn-xs btn-danger add-tooltip "><span class="fa fa-trash pt-1" aria-hidden="true"></span> Del</a>@endif
                {{--  @if (checkPermissions(['Clientes'],["D"]))<a href="#eliminar-Cliente-{{$inc->id_incidencia}}" data-toggle="modal" class="btn btn-xs btn-danger">¡Borrado completo!</a>@endif  --}}
            </div>
            @if (checkPermissions(['Incidencias'],["D"]))
                <div class="modal fade" id="eliminar-incidencia-{{$inc->id_incidencia}}">
                    <div class="modal-dialog modal-md">
                        <div class="modal-content"><div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                            <div class="modal-header"><i class="mdi mdi-comment-question-outline text-warning mdi-48px"></i>
                                ¿Borrar incidencia {{ $descripcion}}?
                            </div>
                            
                            <div class="modal-footer">
                                <a class="btn btn-info" href="{{url('/incidencias/delete',$inc->id_incidencia)}}">Si</a>
                                <button type="button" data-dismiss="modal" class="btn btn-warning">Cancelar</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </td>
    </tr>
@endforeach
