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
                        <table id="tablapuestos"  data-toggle="table"
                        data-locale="es-ES"
                        data-search="true"
                        data-show-columns="true"
                        data-show-columns-toggle-all="true"
                        data-page-list="[5, 10, 20, 30, 40, 50]"
                        data-page-size="50"
                        data-pagination="true" 
                        data-show-pagination-switch="true"
                        data-show-button-icons="true"
                        data-toolbar="#all_toolbar"
                        >
                        <thead>
                            <tr>
                                <th style="width: 10px" class="no-sort"></th>
                                <th style="width: 20px" class="no-sort"></th>
                                
                                <th data-sortable="true">Edificio</th>
                                <th data-sortable="true">Planta</th>
                                <th data-sortable="true">Puesto</th>
                                <th data-sortable="true"class="text-center" style="width: 100px">Estado</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($puestos as $puesto)
                            <tr class="hover-this">
                                <td class="text-center">
                                    <input type="checkbox" class="form-control chkpuesto magic-checkbox" name="lista_id[]" data-id="{{ $puesto->id_puesto }}" id="chkp{{ $puesto->id_puesto }}" value="{{ $puesto->id_puesto }}">
                                    <label class="custom-control-label"   for="chkp{{ $puesto->id_puesto }}"></label>
                                </td>
                                <td class="thumb text-center" data-id="" >
                                    @isset($puesto->val_icono)
                                        <i class="{{ $puesto->val_icono }} fa-2x" style="color:{{ $puesto->val_color }}"></i>
                                    @endisset
                                </td>
                                
                                <td class="td" data-id="">{{ $puesto->des_edificio }}</td>
                                <td class="td" data-id="">{{$puesto->des_planta}}</td>
                                <td class="td" data-id=""><b>{{$puesto->cod_puesto}}</b> - {{$puesto->des_puesto}}</td>
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
                                            <div class="bg-secondary rounded"  id="estado_{{ $puesto->id_puesto }}" style="width: 100%; height: 100%;">
                                            @break
                                        @case(5)
                                            <div class="bg-danger rounded"  id="estado_{{ $puesto->id_puesto }}" style="width: 100%; height: 100%;">
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
                            <div class="btn-group btn-group pull-right" role="group">
                                <a href="#"  class="btn btn-primary btn_editar add-tooltip toolbutton"  title="Ver puesto"  data-id=""> <span class="fa fa-eye" aria-hidden="true"></span></a>
                                @if(checkPermissions(['Puestos'],['W']))<a href="#"  class="btn btn-info btn_editar add-tooltip toolbutton" onclick="editar()" title="Editar puesto" data-id=""> <span class="fa fa-pencil pt-1" aria-hidden="true"></span></a>@endif
                                @if(checkPermissions(['Puestos'],['D']))<a href="#" data-target="#eliminar-puesto" title="Borrar puesto" data-toggle="modal" class="btn btn-danger add-tooltip btn_del toolbutton"><span class="fa fa-trash" aria-hidden="true"></span></a>@endif
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
    @endif
    
</script>