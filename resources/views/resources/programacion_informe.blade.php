{{-- Programacion del informe --}}
{{-- style="display: none" --}}
<br>
<div class="row" id="div_programar_informe" style="display: none">
    <span class="ml-4"><i class="mdi mdi-lightbulb-on text-warning mdi-24px"></i>¿Quiere recibir periodicamente este informe en su e-mail?</span>
    <div class="col-md-12">
        <form action="{{url('programar_informe')}}" method="POST" class="prog-informe" id="frm_programar_informe">
            {{csrf_field()}}
            <input type="hidden" name="controller" id="controller" value="@isset($controller){{$controller}}@endisset">
            <input type="hidden" name="request_orig" id="request_orig" value="@if(isset($r)){!! json_encode($r->all()) !!} @endif">
            <input type="hidden" name="action_orig" id="action_orig">
            <input type="hidden" name="url_orig" id="url_orig" value="{{url($_SERVER['REQUEST_URI'])}}">
            <input type="hidden" name="cod_informe_programado" id="cod_informe_programado"  value=0>
            <input type="hidden" value="{{isset($inf->id_cliente)?$inf->id_cliente:Auth::user()->id_cliente}}" name="id_cliente">
            <div class="card border-primary border">
                <div class="card-header bg-transparent text-primary" style="font-size: 24px">
                    <i class="mdi mdi-calendar-clock"></i> Programar este informe</h2>
                    <button type="button" class="btn btn-primary mt-0 btn-xs p-0 pl-2 pr-2" onclick="showbody();">
                        <i class="mdi mdi-arrow-down-drop-circle mdi-18px"></i>
                    </button>
                </div>
                <div class="card-body mt-0" style="display:none" id="body_programar" id="btn_det_programar">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Descripcion</label>
                                <input required type="text" name="des_informe_programado" class="form-control" value="{{ $inf->des_informe_programado??'' }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Periodo</label>
                                @php

                                @endphp
                                @if(isset($periodo_programado) && $periodo_programado=='FW')
                                    <select name="fechas_prog" class="form-control fechas_prog" id="fechas_prog" onchange="prog_personalizada();">
                                        <option value="1" {{ (isset($inf->val_periodo) && $inf->val_periodo==1)?'selected':'' }}>Mañana</option>
                                        <option value="2" {{ (isset($inf->val_periodo) && $inf->val_periodo==2)?'selected':'' }}>La proxima semana</option>
                                        <option value="3" {{ isset($inf->val_periodo) && $inf->val_periodo==3?'selected':'' }}>L-V de la proxima semana</option>
                                        <option value="4" {{ isset($inf->val_periodo) && $inf->val_periodo==4?'selected':'' }}>Los proximos 10 dias</option>
                                        <option value="5" {{ isset($inf->val_periodo) && $inf->val_periodo==5?'selected':'' }}>La proxima quincena</option>
                                        <option value="6" {{ isset($inf->val_periodo) && $inf->val_periodo==6?'selected':'' }}>El proximo mes</option>
                                        <option value="7" {{ isset($inf->val_periodo) && $inf->val_periodo==7?'selected':'' }}>El proximo trimestre</option>
                                        <option value="8" {{ isset($inf->val_periodo) && $inf->val_periodo==8?'selected':'' }}>El proximo semestre</option>
                                        <option value="9" {{ isset($inf->val_periodo) && $inf->val_periodo==9?'selected':'' }}>El proximo año</option>
                                        <option value="P" {{ isset($inf->val_periodo) && $inf->val_periodo==10?'selected':'' }}>Personalizar periodo</option>
                                    </select>
                                @else
                                    <select name="fechas_prog" class="form-control fechas_prog" id="fechas_prog" onchange="prog_personalizada();">
                                        <option value="-1" {{ (isset($inf->val_periodo) && $inf->val_periodo==1)?'selected':'' }}>Ayer</option>
                                        <option value="-2" {{ (isset($inf->val_periodo) && $inf->val_periodo==2)?'selected':'' }}>La semana pasada</option>
                                        <option value="-3" {{ isset($inf->val_periodo) && $inf->val_periodo==3?'selected':'' }}>L-V de la semana pasada</option>
                                        <option value="-4" {{ isset($inf->val_periodo) && $inf->val_periodo==4?'selected':'' }}>Los ultimos 10 dias</option>
                                        <option value="-5" {{ isset($inf->val_periodo) && $inf->val_periodo==5?'selected':'' }}>La ultima quincena</option>
                                        <option value="-6" {{ isset($inf->val_periodo) && $inf->val_periodo==6?'selected':'' }}>El ultimo mes</option>
                                        <option value="-7" {{ isset($inf->val_periodo) && $inf->val_periodo==7?'selected':'' }}>El ultimo trimestre</option>
                                        <option value="-8" {{ isset($inf->val_periodo) && $inf->val_periodo==8?'selected':'' }}>El ultimo semestre</option>
                                        <option value="-9" {{ isset($inf->val_periodo) && $inf->val_periodo==9?'selected':'' }}>El ultimo año</option>
                                        <option value="P" {{ isset($inf->val_periodo) && $inf->val_periodo==10?'selected':'' }}>Personalizar periodo</option>
                                    </select>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-1 personalizado" style="display:none">
                            <div class="form-group">
                                <label>Desde</label>
                                <input type="number" name="dias_desde" class="form-control" min="1" max="365" value="{{ $inf->dia_desde??'' }}">
                            </div>
                        </div>
                        <div class="col-md-1 personalizado" style="display:none">
                            <div class="form-group">
                                <label>Hasta</label>
                                <input type="number" name="dias_hasta" class="form-control" min="1" max="365"  value="{{ $inf->dia_hasta??'' }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Intervalo</label>
                                <select class="form-control" name="val_intervalo" id="val_intervalo">
                                    <option  value="1" {{ (isset($inf->val_intervalo) && $inf->val_intervalo==1)?'selected':'' }}>Diario</option>
                                    <option  value="7" {{ (isset($inf->val_intervalo) && $inf->val_intervalo==7)?'selected':'' }}>Semanal</option>
                                    <option  value="14" {{ (isset($inf->val_intervalo) && $inf->val_intervalo==14)?'selected':'' }}>Quincenal</option>
                                    <option  value="1M" {{ (isset($inf->val_intervalo) && $inf->val_intervalo=='1M') || (!isset($inf->val_intervalo))?'selected':'' }}>Mensual</option>
                                    <option  value="2M" {{ (isset($inf->val_intervalo) && $inf->val_intervalo=='2M')?'selected':'' }}>Bimensual</option>
                                    <option  value="3M" {{ (isset($inf->val_intervalo) && $inf->val_intervalo=='3M')?'selected':'' }}>Trimestral</option>
                                    <option  value="6M" {{ (isset($inf->val_intervalo) && $inf->val_intervalo=='6M')?'selected':'' }}>Semestral</option>
                                    <option  value="1Y" {{ (isset($inf->val_intervalo) && $inf->val_intervalo=='1Y')?'selected':'' }}>Anual</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="">Comenzando en</label>
                            <div class="input-group mb-3 ">
                                <input required type="text" name="val_fecha" class="form-control singledate" value="{{ isset($inf->fec_inicio)?Carbon\Carbon::parse($inf->fec_inicio)->format('d/m/Y'):'' }}">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fa fas fa-calendar-alt"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                @php
                                    $usuarios=DB::table('users')
                                        ->join('clientes','clientes.id_cliente','users.id_cliente')
                                        ->orWhereIn('clientes.id_cliente',clientes())
                                        ->whereNull('clientes.fec_borrado')
                                        ->where(function($q){
                                            if (session('id_cliente')){
                                                $q->orWhere('clientes.id_cliente',session('id_cliente'));
                                            }
                                        })
                                        ->orderBy('name')
                                        ->get();  

                                    $clientes=DB::table('clientes')
                                        ->where(function($q){
                                            $q->orWhereIn('id_cliente',clientes());
                                        })
                                        ->whereNull('clientes.fec_borrado')
                                        ->where(function($q){
                                            if (session('id_cliente')){
                                                $q->orWhere('clientes.id_cliente',session('id_cliente'));
                                            }
                                        })->get();   
                                         
                                @endphp
                                <label>Destinatarios</label>
                                <select required class="select2 select2-filtro mb-2 col-md-11 select2-multiple form-control" multiple="multiple" name="list_usuarios[]" id="multi-usuarios">
                                    @foreach($clientes as $cl)
                                    @php 
                                        $usuarios_cliente=$usuarios->where('id_cliente',$cl->id_cliente);
                                        if(isset($inf)){
                                            $list_usuarios=explode(",",$inf->list_usuarios);
                                        } else {
                                            $list_usuarios=[];
                                        }
                                    @endphp
                                        <optgroup label="{{$cl->nom_cliente}}">
                                            @foreach ($usuarios_cliente as $u) 
                                                <option value="{{$u->id}}"  {{ in_array($u->id,$list_usuarios)?'selected':'' }}>{{$u->name}}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 pt-4">
							<button id="btn_programar" class="btn btn-primary float-right mt-2"><i class="mdi mdi-calendar-clock"></i> Programar</button>
						</div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-1"></div>
</div>
@section('scripts3')
<script>
    $.fn.serializeObject = function() {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function() {
            if (o[this.name]) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };

    

    function prog_personalizada(){
        if($('#fechas_prog').val()=='P'){
            $('.personalizado').show();
        } else {
            $('.personalizado').hide();
        }
    }

    function showbody(){
        $('#body_programar').toggle();
        animateCSS('#body_programar','fadeIn');
    }

    $('#btn_programar').click(function(){
        json=$('.ajax-filter').serializeObject();
        request_original=JSON.stringify($('.ajax-filter').serializeObject());
        request_original=request_original.replaceAll('[]','');
        jobj=JSON.parse(request_original);
        if(!Array.isArray(jobj.clientes)){
            jobj.clientes=jobj.clientes.split(',');
        }
        if(jobj.dispositivos && !Array.isArray(jobj.dispositivos)){
            jobj.dispositivos=jobj.dispositivos.split(',');
        }
        if(jobj.departamentos && !Array.isArray(jobj.departamentos)){
            jobj.departamentos=jobj.departamentos.split(',');
        }
        if(jobj.centros && !Array.isArray(jobj.centros)){
            jobj.centros=jobj.centros.split(',');
        }
        if(jobj.colectivos && !Array.isArray(jobj.colectivos)){
            jobj.colectivos=jobj.colectivos.split(',');
        }
        if(jobj.empleados && !Array.isArray(jobj.empleados)){
            jobj.empleados=jobj.empleados.split(',');
        }
        if(jobj.posiciones && !Array.isArray(jobj.posiciones)){
            jobj.posiciones=jobj.posiciones.split(',');
        }
        if(jobj.incidencias && !Array.isArray(jobj.incidencias)){
            jobj.incidencias=jobj.incidencias.split(',');
        }
        if(jobj.ciclos && !Array.isArray(jobj.ciclos)){
            jobj.ciclos=jobj.ciclos.split(',');
        }
        console.log(jobj);
        request_original=JSON.stringify(jobj);
        $('#request_orig').val(request_original);
    });

</script>
@endsection
{{-- Fin programacion informe --}}