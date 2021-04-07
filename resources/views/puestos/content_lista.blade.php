
@php
if(!isset($id_check))
    $id_check="";
@endphp

@foreach ($edificios as $e)
    @php
        $plantas=$puestos->where('id_edificio',$e->id_edificio)->pluck('des_planta','id_planta')->sortby('des_planta');
        $cuenta_fila=1;
    @endphp
    <div class="panel" id="panel{{ $e->id_edificio }}" style="{{ $plantas->isempty()?'display:none':'' }}">
        <div class="panel-heading bg-gray-dark">
            <div class="row">
                <div class="col-md-5">
                    <span class="text-2x ml-2 mt-2 font-bold"><i class="fad fa-building"></i> {{ $e->des_edificio }}
                        @if(isset($checks) && $checks==1)    
                            <input type="checkbox" class="form-control chk_edificio_puestos magic-checkbox" name="lista_id[]" data-id="{{ $e->id_edificio }}" id="chkep{{ $e->id_edificio }}" value="{{ $e->id_edificio }}">
                            <label class="custom-control-label" for="chkep{{ $e->id_edificio }}"></label>
                        @endif
                    </span>
                </div>
                <div class="col-md-5"></div>
                <div class="col-md-2 text-right  sp_edificio">
                    <h4>
                        <span class="mr-2"><i class="fad fa-layer-group"></i> {{ $e->plantas }}</span>
                        <span class="mr-2"><i class="fad fa-desktop-alt"></i> {{ $e->puestos }}</span>
                    </h4>
                </div>
            </div>
        </div>

        <div class="panel-body">
            
            @if($plantas->isempty())
            
            <div class="row">
                <div class="col-md-12  alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> El usuario no tiene asignada ninguna planta en la que pueda reservar, debe asignarle plantas en los detalles de usuario o utilizando la acción de "Asignar planta"
                </div>
            </div>
            <script>
                document.getElementById('panel{{ $e->id_edificio }}').display='none';
            </script>
            @endif
            <table id="tablapuestos{{ $e->id_edificio }}" class="tabla"  data-toggle="table" onclick="tabla_click()"
                data-locale="es-ES"
                data-search="true"
                data-show-columns="true"
                data-show-toggle="true"
                data-show-columns-toggle-all="true"
                data-page-list="[5, 10, 20, 30, 40, 50, 75, 100]"
                data-page-size="50"
                data-pagination="false" 
                data-show-pagination-switch="true"
                data-show-button-icons="true"
                data-toolbar="#all_toolbar"
                data-buttons-class="secondary"
                data-show-button-text="true"
                data-group-by="true"
                data-group-by-field="shape"
                >
                <thead>
                    <tr data-fila="{{ $cuenta_fila }}">
                        @if(isset($checks) && $checks==1)<th></th>@endif
                        <th class="no-sort"></th>
                        <th data-sortable="true" >Puesto</th>
                        <th data-sortable="true" >Tipo</th>
                        <th data-sortable="true" >Estado</th>
                        <th data-sortable="true" >Ocupado por</th>
                        <th data-sortable="true" >Fecha</th>
                        <th data-sortable="true" >Reservado por</th>
                        <th data-sortable="true" >Fecha</th>
                        <th class="no-sort"></th>
                    </tr>
                </thead>
                <tbody>
                @foreach($plantas as $key=>$value)
                    @php
                        $cuenta_fila++;
                    @endphp    
                    <tr class="ocultable" data-fila="{{ $cuenta_fila }}" data-tabla="tablapuestos{{ $e->id_edificio }}">
                        <td colspan="{{ isset($checks) && $checks==1?10:9 }}">
                            <h3 class="pad-all w-100 bg-gray rounded" style="font-size: 2vh">PLANTA {{ $value }}
                                @if(isset($checks) && $checks==1)    
                                    <input type="checkbox" class="form-control chk_planta_puestos magic-checkbox" name="lista_id[]" data-id="{{ $key }}" id="chkpp{{ $key }}" value="{{ $key }}">
                                    <label class="custom-control-label" for="chkpp{{ $key }}"></label>
                                @endif
                            </h3>
                        </td>
                    </tr>
                    
                    @php
                        $cuenta_fila++;
                        $puestos_planta=$puestos->where('id_planta',$key);
                    @endphp
                    {{-- <table id="tabla" class="table table-condensed table-hover  table-responsive-lg table-vcenter"> --}}
                        <tr class="font-bold ocultable" data-fila="{{ $cuenta_fila }}" data-tabla="tablapuestos{{ $e->id_edificio }}">
                            @if(isset($checks) && $checks==1)<td></td>@endif
                            <td>{{ $cuenta_fila }}</td>
                            <td>Puesto</td>
                            <td>Tipo</td>
                            <td>Estado</td>
                            <td>Ocupado por</td>
                            <td>Fecha</td>
                            <td>Reservado por</td>
                            <td>Fecha</td>
                            <td></td>
                        </tr>

                    
                        @foreach($puestos_planta as $puesto)
                            @php
                                $cuenta_fila++;

                                $reserva=$reservas->where('id_puesto',$puesto->id_puesto)->first();  
                                $asignado_usuario=$asignados_usuarios->where('id_puesto',$puesto->id_puesto)->first();  
                                $asignado_otroperfil=$asignados_nomiperfil->where('id_puesto',$puesto->id_puesto)->first();  
                                $asignado_miperfil=$asignados_miperfil->where('id_puesto',$puesto->id_puesto)->first();  
                                $fec_res="";
                                if(isset($reserva)){
                                    $fec_res=beauty_fecha($reserva->fec_reserva);
                                    if(isset($reserva->fec_fin_reserva)){
                                        $fec_res.=' <i class="fa fa-arrow-right"></i> '.Carbon\Carbon::parse($reserva->fec_fin_reserva)->format('H:i');
                                    }
                                }
                                
                                if(isMobile()){
                                    if($puesto->factor_puesto<3.5){
                                        $puesto->factor_puesto=12;
                                        $puesto->factor_letra=2.8;
                                    } else {
                                        $puesto->factor_puesto=$puesto->factor_puesto*4;
                                        $puesto->factor_letra=$puesto->factor_letra*4;
                                    }
                                    
                                    
                                } else if($puesto->factor_puesto<3.5){
                                    $puesto->factor_puesto=4.7;
                                    $puesto->factor_letra=0.8;
                                }
                                $cuadradito=\App\Classes\colorPuesto::colores($reserva, $asignado_usuario, $asignado_miperfil,$asignado_otroperfil,$puesto);
                            @endphp
                            <tr  id="puesto{{ $puesto->id_puesto }}" title="{!! $puesto->des_puesto." \r\n ".$cuadradito['title'] !!}" data-id="{{ $puesto->id_puesto }}" data-puesto="{{ $puesto->cod_puesto }}"  data-planta="{{ $value }}" data-fila="{{ $cuenta_fila }}">
                                @if(isset($checks) && $checks==1)
                                <td>
                                    {{-- Mostrar checkbox para seleccionar los puestos --}}
                                        <div>
                                            <input type="checkbox" class="form-control chkpuesto magic-checkbox float-right" name="lista_id[]" data-id="{{ $puesto->id_puesto }}" id="chkp{{ $puesto->id_puesto }}" data-idedificio="{{ $puesto->id_edificio }}" data-idplanta="{{$puesto->id_planta}}" value="{{ $puesto->id_puesto }}" {{ isset($puestos_check) && array_search($puesto->id_puesto,$puestos_check)?'checked':'' }}>
                                            <label class="custom-control-label"   for="chkp{{ $puesto->id_puesto }}"></label>
                                        </div>
                                </td>
                                @endif
                                <td   style="color: {{ $cuadradito['font_color'] }};">
                                    @include('resources.adornos_iconos_puesto')
                                </td>
                                <td>@if(config('app.env')=='dev')[#{{ $puesto->id_puesto }}]@endif {{ $puesto->cod_puesto }}</td>
                                <td>{{ $puesto->des_tipo_puesto }}</td>
                                <td>
                                    @if($puesto->mca_incidencia=='N')
                                        @switch($puesto->id_estado)
                                            @case(1)
                                                <div class="bg-success rounded"  id="estado_{{ $puesto->id_puesto }}" style="width: 100%; height: 100%;">
                                                @break
                                            @case(2)
                                                <div class="bg-danger rounded"  id="estado_{{ $puesto->id_puesto }}" style="width: 100%; height: 100%;">
                                                @break
                                            @case(3)
                                                <div class="bg-info rounded"  id="estado_{{ $puesto->id_puesto }}" style="width: 100%; height: 100%;">
                                                @break     
                                            @case(4)
                                                <div class="bg-dark rounded"  id="estado_{{ $puesto->id_puesto }}" style="width: 100%; height: 100%;">
                                                @break
                                            @case(5)
                                                <div class="bg-dark rounded"  id="estado_{{ $puesto->id_puesto }}" style="width: 100%; height: 100%;">
                                                @break
                                            @case(7)
                                                <div class="bg-white rounded"  id="estado_{{ $puesto->id_puesto }}" style="width: 100%; height: 100%;">
                                                @break
                                            @default
                                        @endswitch
                                        {{ $puesto->des_estado }}
                                    @else
                                        <div class="bg-warning rounded"  id="estado_{{ $puesto->id_puesto }}" style="width: 100%; height: 100%;"><i class="fad fa-exclamation-triangle"></i>
                                        Incidencia
                                    @endif
                                        </div>
                                </td>
                                <td>{{ $puesto->usuario_usando }}</td>
                                <td>{!! beauty_fecha($puesto->fec_ult_estado) !!}</td>
                                <td id="nreserva_{{ $puesto->id_puesto }}">{{ $reserva->name??'' }}</td>
                                <td id="freserva_{{ $puesto->id_puesto }}">{!! $fec_res !!}</td>
                                <td>
                                    <a href="javascript:void(0)" onclick="hoverdiv($(this),event,'toolbutton',{{ $puesto->id_puesto }},'{{ $puesto->cod_puesto }}','{{ $puesto->token }}');"><i class="fa fa-bars add-tooltip opts" title="Acciones"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endforeach
<div id="toolbutton"  style="display: none;position: absolute; ">
    <div style="display: flex; flex-direction: row;">
        <div class="pad-all rounded bg-white" style="border: 3px solid navy; background-color: #fff; ">
            <label>Acciones<span class="font-bold ml-2" id="nombrepuesto"></span></label><br>
            <div class="btn-group btn-group pull-right ml-1" role="group">
                @if(isAdmin() || config('app.env')=='dev')<a href="#"  class="btn btn-warning btn_scan add-tooltip toolbutton"  title="Scan" onclick="scan()"  data-id=""> <span class="fa fa-qrcode" aria-hidden="true"></span></a>@endif
                @if(checkPermissions(['Puestos'],['W']))<a href="#"  class="btn btn-info btn_editar add-tooltip toolbutton ml-2" onclick="editar()" title="Editar puesto" data-id=""> <span class="fa fa-pencil pt-1" aria-hidden="true"></span> Edit</a>@endif
                @if(checkPermissions(['Reservas'],['D']))<a href="#"  title="Cancelar Reserva" class="btn btn-pink add-tooltip btn_del toolbutton" onclick="cancelar()"><span class="fad fa-calendar-times" aria-hidden="true"></span> Res</a>@endif
            </div>
            <div class="btn-group btn-group pull-right" role="group">
                @if(checkPermissions(['Puestos'],['W']))
                    <a href="#"  class="btn btn-success btn_estado add-tooltip toolbutton"  onclick="estado(1)" title="Disponible" data-token=""  data-estado="1" data-id=""> <span class="fad fa-thumbs-up" aria-hidden="true"></span></a>
                    <a href="#"  class="btn btn-danger btn_estado add-tooltip toolbutton"  onclick="estado(2)" title="Usado"  data-token=""  data-estado="2" data-id=""> <span class="fad fa-lock-alt" aria-hidden="true"></span></a>
                    <a href="#"  class="btn btn-info btn_estado add-tooltip toolbutton"  onclick="estado(3)" title="Limpiar"  data-token=""  data-estado="3" data-id=""> <span class="fad fa-broom" aria-hidden="true"></span></a>
                    
                @endif
            </div>
        </div>
        <div style="color: navy; padding-top:30px">
            <i class="fas fa-caret-right fa-3x"></i>
        </div>
    </div>
</div>
@section('scripts5')
    <script>
        left_toolbar=300;
        top_toolbar=216;
    </script>
    @include('puestos.scripts_lista_puestos')
@endsection
@section('scripts2')
@include('resources.leyenda_puestos')
<script>
    
$('.tabla').on('sort.bs.table', function(){
    

})


$('.chkpuesto').click(function(){
    estado=$(this).is(':checked');
    if(estado){
        $.get("{{ url($url_check??'')."/".$id_check }}/"+$(this).data('id')+"/A",function(data){
            $('#puesto'+data.id).css("background-color",'#02c59b');
        })
    }
    else  {
        $.get("{{ url($url_check??'')."/".$id_check }}/"+$(this).data('id')+"/D",function(data){
            $('#puesto'+data.id).css("background-color",'#eae3b8');
        })
    } 
})
var tooltip = $('.add-tooltip');
if (tooltip.length)tooltip.tooltip();

$('.chk_edificio_puestos').click(function(){
    estado=$(this).is(':checked');
    console.log(estado);
    $('[data-idedificio='+$(this).data('id')+']').each(function(){
        $(this).attr('checked',estado);
        if(estado){
            $.get("{{ url($url_check??'')."/".$id_check }}/"+$(this).data('id')+"/A",function(data){
                $('#puesto'+data.id).css("background-color",'#02c59b');
            })
        }
        else  {
            $.get("{{ url($url_check??'')."/".$id_check }}/"+$(this).data('id')+"/D",function(data){
                $('#puesto'+data.id).css("background-color",'#eae3b8');
            })
        } 
    })
})

$('.chk_planta_puestos').click(function(){
    estado=$(this).is(':checked');
    console.log(estado);
    $('[data-idplanta='+$(this).data('id')+']').each(function(){
        $(this).attr('checked',estado);

        if(estado){
            $.get("{{ url($url_check??'')."/".$id_check }}/"+$(this).data('id')+"/A",function(data){
                $('#puesto'+data.id).css("background-color",'#02c59b');
            })
        }
        else  {
            $.get("{{ url($url_check??'')."/".$id_check }}/"+$(this).data('id')+"/D",function(data){
                $('#puesto'+data.id).css("background-color",'#eae3b8');
            })
        } 
    })
})

$('tr').click(function(event){
       $('#toolbutton').hide();
    })
</script>
@endsection