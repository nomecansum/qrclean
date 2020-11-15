<div class="row mt-2">
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">Puestos</h3>
        </div>
        <div class="panel-body w-100" id="main_panel">
            <div class="table-responsive w-100" style="position: relative">
                <form action="{{url('/puestos/print_qr')}}" method="POST"  id="frmpuestos" enctype='multipart/form-data'>
                    @csrf
                        {{-- <div class="td"><div class="loader"></div></div> --}}
                     {{-- <table class="table table-striped table-hover table-vcenter" id="tablapuestos"  style="width: 98%" data-toggle="table"  data-pagination="true" data-search="true"> --}}
                        <div id="all_toolbar" class="ml-3">
                            <input type="checkbox" class="form-control custom-control-input magic-checkbox" name="chktodos" id="chktodos"><label  class="custom-control-label"  for="chktodos">Todos</label>
                        </div>
                        <table id="tablapuestos"  data-toggle="table" onclick="tabla_click()"
                        data-locale="es-ES"
                        data-search="true"
                        data-show-columns="true"
                        data-show-toggle="true"
                        data-show-columns-toggle-all="true"
                        data-page-list="[5, 10, 20, 30, 40, 50, 75, 100]"
                        data-page-size="50"
                        data-pagination="true" 
                        data-show-pagination-switch="true"
                        data-show-button-icons="true"
                        data-toolbar="#all_toolbar"
                        data-buttons-class="secondary"
                        data-show-button-text="true"
                        >
                        <thead>
                            <tr>
                                <th style="width: 10px" class="no-sort"  data-switchable="false"></th>
                                <th style="width: 20px" class="no-sort"  data-switchable="false"></th>
                                
                                <th data-sortable="true" @if(isMobile()) data-visible="false" @endif>Edificio</th>
                                <th data-sortable="true" @if(isMobile()) data-visible="false" @endif>Planta</th>
                                <th data-sortable="true">Puesto</th>
                                <th data-sortable="true" title="Acceso anonimo permitido en el puesto" style="width: 20px"  @if(isMobile()) data-visible="false" @endif>Anonimo</th>
                                <th data-sortable="true" title="Reserva permitida en el puesto" @if(isMobile()) data-visible="false" @endif>Reserva</th>
                                <th data-sortable="true" title="Puesto con asignacion fija" @if(isMobile()) data-visible="false" @endif>Fijo</th>
                                <th data-sortable="true"class="text-center" style="width: 100px">Estado</th>
                                <th  data-switchable="false"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($puestos as $puesto)
                            <tr class="hover-this" >
                                <td class="text-center">
                                    <input type="checkbox" class="form-control chkpuesto magic-checkbox" name="lista_id[]" data-id="{{ $puesto->id_puesto }}" id="chkp{{ $puesto->id_puesto }}" value="{{ $puesto->id_puesto }}">
                                    <label class="custom-control-label"   for="chkp{{ $puesto->id_puesto }}"></label>
                                </td>
                                <td class="thumb text-center" data-id="">
                                    @isset($puesto->val_icono)
                                        <i class="{{ $puesto->val_icono }} fa-2x {{ txt_blanco($puesto->color_puesto) }}"></i>
                                    @endisset
                                </td>
                                
                                <td class="td" data-id="">{{ $puesto->des_edificio }}</td>
                                <td class="td" data-id="">{{$puesto->des_planta}}</td>
                                <td class="td" data-id="">
                                    <div class="m-0 rounded pl-1e"  style="width: 100%; heigth: 100%; @if($puesto->color_puesto) background-color: {{ $puesto->color_puesto }}@endif; color: {{ $puesto->color_puesto && txt_blanco($puesto->color_puesto)=='text-white'?'#FFF':'navy' }} ">
                                        <b>{{$puesto->cod_puesto}}</b> - {{$puesto->des_puesto}}
                                    </div>
                                </td>
                                <td class="text-center text-muted" >@if($puesto->mca_acceso_anonimo=='S') <i class="fas fa-circle"></i> @endif</td>
                                <td class="text-center text-muted" >@if($puesto->mca_reservar=='S') <i class="fas fa-circle"  style="color: #70c2b4"></i> @endif</td>
                                <td class="text-center text-muted" >@if(isset($puesto->id_usuario))<i class="fad fa-user" title="Puesto asignado al usuario {{ \App\Models\users::find($puesto->id_usuario)->name }}" style="color: #f4a462"></i> @endif @if(isset($puesto->id_perfil))<i class="fad fa-users" title="Puesto asignado a perfil {{ \App\Models\niveles_acceso::find($puesto->id_perfil)->des_nivel_acceso }}" style="color: #f4a462"></i> @endif</td>
                                <td class="td text-center" data-id="">
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
                                        @case(6)
                                            <div class="bg-warning rounded"  id="estado_{{ $puesto->id_puesto }}" style="width: 100%; height: 100%;"><i class="fad fa-exclamation-triangle"></i>
                                            @break
                                        @default
                                    @endswitch
                                    {{$puesto->des_estado}}
                                    </div>
                                </td>
                                <td class="text-center opts">
                                    <a href="javascript:void(0)" onclick="hoverdiv($(this),event,'toolbutton',{{ $puesto->id_puesto }},'{{ $puesto->cod_puesto }}','{{ $puesto->token }}');"><i class="fa fa-bars add-tooltip opts" title="Acciones"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table> 
                </form>
                <div id="toolbutton"  style="display: none;position: absolute; ">
                    <div style="display: flex; flex-direction: row;">
                        <div class="pad-all rounded bg-white" style="border: 3px solid navy; background-color: #fff;">
                            <label>Acciones<span class="font-bold ml-2" id="nombrepuesto"></span></label><br>
                            <div class="btn-group btn-group pull-right ml-1" role="group">
                                {{--  <a href="#"  class="btn btn-primary btn_editar add-tooltip toolbutton"  title="Ver puesto"  data-id=""> <span class="fa fa-eye" aria-hidden="true"></span></a>  --}}
                                @if(checkPermissions(['Puestos'],['W']))<a href="#"  class="btn btn-info btn_editar add-tooltip toolbutton ml-2" onclick="editar()" title="Editar puesto" data-id=""> <span class="fa fa-pencil pt-1" aria-hidden="true"></span> Edit</a>@endif
                                @if(checkPermissions(['Puestos'],['D']))<a href="#" data-target="#eliminar-puesto" title="Borrar puesto" data-toggle="modal" class="btn btn-danger add-tooltip btn_del toolbutton"><span class="fa fa-trash" aria-hidden="true"></span> Del</a>@endif
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
            </div>
        </div>
    </div>
</div>

<script>
    @if(isset($r))  //Solo se ejecuta cuando se pide por ajax
        $('#tablapuestos').bootstrapTable();
        $("#chktodos").click(function(){
            $('.chkpuesto').not(this).prop('checked', this.checked);
        });
        
        var tooltip = $('.add-tooltip');
        if (tooltip.length)tooltip.tooltip();

        
    }
    @endif
   function tabla_click(){
    $('#toolbutton').hide();
    //console.log('tabla_click');
   }

    
    
</script>