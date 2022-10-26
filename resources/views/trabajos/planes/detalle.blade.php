@php
    use Carbon\Carbon;
@endphp

<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th rowspan="2" colspan="2" scope="col" class="text-center">Trabajos</th>
                <th  rowspan="2"  scope="col" >Fechas</th>
                <th  rowspan="2"  scope="col" >Periodicidad</th>
                @if($plantas->count()>0)<th colspan="{{ $plantas->count() }}" class="text-center bg-light"  scope="col" >PLANTAS</th>@endif
                @if($zonas->count()>0)<th colspan="{{ $zonas->count() }}" class="text-center bg-light"  scope="col" >ZONAS</th>@endif
            </tr>
            <tr>
                @foreach($plantas as $planta)
                    <th class="text-center"  scope="col" ><span class="vertical">{{$planta->des_planta}}</span></th>
                @endforeach
                @foreach($zonas as $zona)
                    <th class="text-center"  scope="col" ><span class="vertical">{{ $zona->des_planta }}<br>{{$zona->des_zona}}</span></th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($grupos as $grupo)
                @php
                    $trabajos_grupo=$trabajos->where('id_grupo',$grupo->id_grupo);
                    dump($trabajos_grupo);
                @endphp
                @foreach($trabajos_grupo as $trabajo)
                    <tr>
                        @if($loop->index==0)
                            <td rowspan="{{ $trabajos_grupo->count() }}" class="text-center align-middle" style="vertical-align: middle; padding: 10px 0px 10px 0px; background-color: {{ $grupo->val_color }}"><span class="vertical text-center ml-2 {{ txt_blanco($grupo->val_color) }}"> {{ $grupo->des_grupo }}</span></td>
                        @endif
                        <td scope="col" class="{{ txt_blanco($trabajo->val_color) }}" style="background-color:{{ $trabajo->val_color }}"><div style="margin-left: {{ 30*$trabajo->num_nivel }}px;"><i class="{{ $trabajo->val_icono }}"></i> {{ $trabajo->des_trabajo }}</td></div>
                        <td class="text-center"  scope="col" >@if(isset($trabajo->fec_inicio)){{ Carbon::parse($trabajo->fec_inicio)->format('d/M') }}->{{ Carbon::parse($trabajo->fec_fin)->format('d/M') }}@endif</td>
                        <td scope="col" class="text-center td_periodo" data-grupo="{{ $grupo->id_grupo }}" data-trabajo="{{ $trabajo->id_trabajo }}" data-desc="{{ $grupo->des_grupo.' - '.$trabajo->des_trabajo }}"></td>
                        @foreach($plantas as $planta)
                            <td scope="col" class="td_planta" data-id="{{ $planta->id_planta }}" data-tipo="P" data-grupo="{{ $grupo->id_grupo }}" data-trabajo="{{ $trabajo->id_trabajo }}" data-desc="{{ $planta->des_planta.' - '.$trabajo->des_trabajo }}">
                                @php
                                    $item=$trabajos_grupo->where('id_planta',$planta->id_planta)->where('id_trabajo',$trabajo->id_trabajo)->dump();
                                @endphp
                                @if(isset($item))
                                    @php
                                        dump($item);
                                    @endphp
                                @endif
                            </td>
                        @endforeach
                        @foreach($zonas as $zona)
                            <td scope="col" class="td_planta" data-id="{{ $zona->key_id }}" data-tipo="Z" data-grupo="{{ $grupo->id_grupo }}" data-trabajo="{{ $trabajo->id_trabajo }}"  data-desc="{{ $zona->des_zona .' - '.$trabajo->des_trabajo}}">
                                @php
                                    $item=$trabajos_grupo->where('id_zona',$zona->id_zona)->where('id_trabajo',$trabajo->id_trabajo)->first();
                                @endphp
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</div>

<div class="modal fade" id="detalle-trabajo" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            
            <div class="modal-header">
                
                <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                <span class="float-right" id="loading" style="display: none"><img src="{{ url('/img/loading.gif') }}" style="height: 25px;">LOADING</span><h3 class="modal-title text-nowrap">Detalle: <span id="desc_detalle">#</span></h3>
                <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
                    <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
                </button>
            </div>
            <div class="modal-body" id="detalle_modal">
                
            </div>
            <div class="modal-footer">
                <a class="btn btn-info" id="btn_si_detalle" href="javascript:void(0)">Si</a>
                <button type="button" id="btn_no_detalle" data-dismiss="modal" class="btn btn-warning close" onclick="cerrar_modal()">No</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detalle-periodo" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                <span class="float-right" id="loading" style="display: none"><img src="{{ url('/img/loading.gif') }}" style="height: 25px;">LOADING</span><h3 class="modal-title text-nowrap">Periodicidad: <span id="desc_periodo">#</span></h3>
                <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
                    <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
                </button>
            </div>
            <div class="modal-body" id="detalle_periodo">
                
            </div>
            <div class="modal-footer">
                <a class="btn btn-info" id="btn_si_detalle" href="javascript:void(0)">Si</a>
                <button type="button" id="btn_no_detalle" data-dismiss="modal" class="btn btn-warning close" onclick="cerrar_modal()">No</button>
            </div>
        </div>
    </div>
</div>

<script>
$('.td_planta').click(function(){
    var id=$(this).data('id');
    var tipo=$(this).data('tipo');
    var grupo=$(this).data('grupo');
    var trabajo=$(this).data('trabajo');
    var desc=$(this).data('desc');
    $.post('{{url('/trabajos/planes/detalle_trabajo')}}', {_token:'{{csrf_token()}}',id_plan:{{ $dato->id_plan }},id:id,tipo:tipo,grupo:grupo,trabajo:trabajo,contratas:$('#multi-contratas').val()}, function(data, textStatus, xhr) {
        $('#detalle_modal').html(data);
        $('#desc_detalle').html(desc);
        $('#detalle-trabajo').modal('show');
    });
    
});

$('.td_periodo').click(function(){
    var grupo=$(this).data('grupo');
    var trabajo=$(this).data('trabajo');
    var desc=$(this).data('desc');
    var url="{{ url('/trabajos/planes/periodo_trabajo') }}/{{ $dato->id_plan }}/"+grupo+"/"+trabajo;
    $('#detalle_periodo').load(url);
    $('#desc_periodo').html(desc);
    $('#detalle-periodo').modal('show');
});

$('#btn_si_detalle').click(function(){
        var url = "{{ url('/trabajos/planes/detalle_save') }}";
        var form = $('#edit_plan_detalle');
        var data = form.serialize();
        $.post(url, data, function (result) {
            if (result.error) {
                toast_error(result.title,result.error);
            } else {
                toast_ok(result.title,result.message);
                $('.modal').modal('hide');
                $('#detalle').load("{{ url('/trabajos/planes/detalle',[$dato->id_plan,$dato->grupo,$dato->trabajo]) }}");
            }
        });
    });

</script>