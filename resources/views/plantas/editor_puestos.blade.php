

<style type="text/css">
.container {
    border: 1px solid #DDDDDD;
    width: 100%;
    position: relative;
    padding: 0px 0px 0px 0px !important;
    margin: 0px 0px 0px 0px !important;
    --bs-gutter-x: 0 !important;
    --bs-gutter-y: 0 !important;
}
.flpuesto {
    float: left;
    position: absolute;
    z-index: 1000;
    color: #FFFFFF;
    font-weight: bold;
    font-size: 9px;
    width: 40px;
    height: 40px;
    overflow: hidden;
    cursor: move;
}

.card_plano{
    --bs-gutter-x: 0;
    --bs-gutter-y: 0;
    padding: 0px 0px 0px 0px;
    margin: 0px 0px 0px 0px;
}

</style>

    <div class="card editor mb-5">

        <div class="card-header toolbar">
            <div class="toolbar-start">
                <h5 class="m-0">Ubicacion de puestos en planta {{ $plantas->des_planta }}</h5>
            </div>
            <div class="toolbar-end">
                <button type="button" class="btn-close btn-close-card">
                    <span class="visually-hidden">Close the card</span>
                </button>
            </div>
        </div>
        
        

        <div class="card-body">

            @if ($errors->any())
                <ul class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
            
            
            {{-- <div class="col-md-2" class="row b-all"  style="margin-top: 30px">
                <label class="control-label float-left mr-2">zoom</label>
                <i class="fa fa-minus-square float-left mt-1 mr-1" onclick="zoom(-1)"></i>
                <span id="zoom_level" class="float-left">100%</span>
                <i class="fa fa-plus-square float-left mt-1 ml-1"  onclick="zoom(+1)"></i>
            </div> --}}
            <div class="row">
                <div class="form-group col-md-4 mb-3">
                    <label>Zoom: </label> <span id="puesto-zoom-def-val"></span>
                    <div id="puesto-zoom-def"></div>	
                </div>
                <div class="col-md-5">
    
                </div>
                <div class="form-group col-md-2 mb-3">
                    <label>Alpha fondo: </label> <span id="puesto-transp-def-val"></span>
                    <div id="puesto-transp-def"></div>	
                </div>
            </div>
            
            <form method="POST" action="{{ url('/plantas/puestos/') }}" id="edit_plantas_form" name="edit_plantas_form" accept-charset="UTF-8" class="form-horizontal form-ajax"  enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="json" id="json">
                <input type="hidden" name="id_planta" value="{{ $plantas->id_planta }}">
                <input type="hidden" name="factor_puestow" id="factor_puestow" value="{{ $plantas->factor_puestow }}">
                <input type="hidden" name="factor_puestoh" id="factor_puestoh" value="{{ $plantas->factor_puestoh }}">
                <input type="hidden" name="factor_puestob" id="factor_puestob" value="{{ $plantas->factor_puestob }}">
                <input type="hidden" name="factor_puestor" id="factor_puestor" value="{{ $plantas->factor_puestor }}">
                <input type="hidden" name="factor_letra" id="factor_letra"  value="{{ $plantas->factor_letra }}">
                <input type="hidden" name="factor_grid" id="factor_grid"  value="{{ $plantas->factor_grid }}">
                <input type="hidden" name="factor_transp" id="factor_transp"  value="{{ $plantas->factor_transp }}">
                <div class="card ">
                    <div class="card-body overflow-auto card_plano">
                        @if(isset($plantas->img_plano))
                        {{--  style="background-image: url('{{ url('img/plantas/'.$plantas->img_plano) }}'); background-repeat: no-repeat; background-size: contain;"  --}}
                            <div class="container" id="plano" >
                                <img src="{{ Storage::disk(config('app.img_disk'))->url('img/plantas/'.$plantas->img_plano) }}" style="width: 100%;" id="img_fondo">
                                @php
                                    $left=0;
                                    $top=0;
                                @endphp
                                @foreach($puestos as $puesto)
                                
                                @php
                                 $asignado_usuario=null;
                                 $asignado_miperfil=null;
                                 $asignado_otroperfil=null;
                                 $reserva=null;
                                 $cuadradito=\App\Classes\colorPuesto::colores($reserva, $asignado_usuario, $asignado_miperfil,$asignado_otroperfil,$puesto);
                                 //$borde="border: 5px solid ".$puesto->val_color??"#fff".";";   
                                 if($puesto->top==null && $puesto->left==null){
                                    $puesto->color_estado="secondary";
                                 }

                                @endphp
                                    <div class="text-center font-bold  add-tooltip bg-{{ $puesto->color_estado }} align-middle flpuesto puesto_parent draggable add-tooltip" title="{{ $puesto->des_puesto }}" id="puesto{{ $puesto->id_puesto }}" data-id="{{ $puesto->id_puesto }}" data-puesto="{{ $puesto->cod_puesto }}" data-factorh="{{ $puesto->factor_puestoh }}" data-factorw="{{$puesto->factor_puestow}}" data-factorr="{{ $puesto->factor_puestor }}" style="height: {{ $puesto->factor_puestoh }}% ; width: {{ $puesto->factor_puestow }}%;top: {{ $top }}px; left: {{ $left }}px; {{ $cuadradito['borde'] }};  opacity: {{ $cuadradito['transp']}}; border-radius: {{ $cuadradito['border-radius'] }}">
                                        <span class="h-100 align-middle texto_puesto puesto_child" style="font-size: {{ $puesto->factor_letra }}vw;">{{ nombrepuesto($puesto) }}</span>
                                    </div>
                                @php
                                    $left+=50;
                                    if($left==500){
                                        $left=0;
                                        $top+=50;
                                    }
                                 @endphp
                                @endforeach
        
        
                            </div>
                        @endif
                    </div>
                </div>
                
                @if(checkPermissions(['Plantas'],['C']))
                <div class="row mt-3">
                    <div class="form-group col-md-2">
                        <label>Ancho puestos: </label> <span id="puesto-rangew-def-val"></span>
                        <div id="puesto-rangew-def"></div>	
                    </div>
                    <div class="form-group col-md-2">
                        <label>Alto puestos: </label> <span id="puesto-rangeh-def-val"></span>
                        <div id="puesto-rangeh-def"></div>	
                    </div>
                    <div class="form-group col-md-2">
                        <label>Borde: </label> <span id="puesto-rangeb-def-val"></span>
                        <div id="puesto-rangeb-def"></div>	
                    </div>
                    <div class="form-group col-md-2">
                        <label>Redondeo: </label> <span id="puesto-ranger-def-val"></span>
                        <div id="puesto-ranger-def"></div>	
                    </div>
                    <div class="form-group col-md-2">
                        <label>Tamaño letra: </label> <span id="letra-range-def-val"></span>
                        <div id="letra-range-def"></div>	
                    </div>
                    <div class="form-group col-md-2">
                        <label>Tamaño grid: </label> <span id="grid-range-def-val"></span>
                        <div id="grid-range-def"></div>	
                    </div>
                    <div class="col-md-12 text-end pt-3">
                        <input class="btn btn-primary" id="btn_guardar" type="submit" value="Guardar">
                    </div>
                </div>
                @endif
            </form>

        </div>
    </div>

<script>
    $('.form-ajax').submit(form_ajax_submit);
    zoom_actual=100;
    var hacer_zoom;

    function zoom(){

        $('.container').animate({ 'zoom': zoom_actual/100 }, 400);
        $('.container').animate({ 'width': zoom_actual+'%' }, 400);
        recolocar_puestos();
    }

    var tooltip = $('.add-tooltip');
    if (tooltip.length)tooltip.tooltip();

    try{
        posiciones={!! json_encode($plantas->posiciones)??'[]' !!};
        document.getElementById('plano').setAttribute('data-posiciones',posiciones);
        posiciones=$.parseJSON(posiciones); 
       
    } catch($err){
        posiciones=[];
    }


    $( function() {
        $( ".draggable" ).draggable({
            containment: "parent",
            grid: [ 5, 5 ],
            stop: function() {
                //console.log($(this).data('id')+' '+$(this).data('puesto')+' '+$(this).position().top+ ' '+$(this).position().left);
                offsettop=100*$(this).position().top/$('#plano').height();
                offsetleft=100*$(this).position().left/$('#plano').width();
                $.get("{{ url('/puestos/save_pos') }}/"+$(this).data('id')+"/"+$(this).position().top+"/"+$(this).position().left+"/"+offsettop+"/"+offsetleft, function(data){
                    console.log(data);
                });
            }
        });
        //setTimeout(recolocar_puestos, 800);

        pw_def.noUiSlider.on('update', function( values, handle ) {
            pw_def_value.innerHTML = values[handle];
            //$('.flpuesto').css('width',values[handle]+'vw');
            console.log('w: '+parseInt($('#plano').css('width')));
            $('.flpuesto').css('width',parseInt($('#plano').css('width'))*(values[handle]/100)+'px');
            $('#factor_puestow').val(values[handle]);
        });
        ph_def.noUiSlider.on('update', function( values, handle ) {
            ph_def_value.innerHTML = values[handle];
            //$('.flpuesto').css('height',values[handle]+'vh');
            console.log('h: '+$('#plano').css('height'));
            $('.flpuesto').css('height',parseInt($('#plano').css('height'))*(values[handle]/100)+'px');
            $('#factor_puestoh').val(values[handle]);
        });
        pr_def.noUiSlider.on('update', function( values, handle ) {
            pr_def_value.innerHTML = values[handle];
            $('.flpuesto').css('border-radius',values[handle]+'px');
            $('#factor_puestor').val(values[handle]);
        });
        pb_def.noUiSlider.on('update', function( values, handle ) {
            pb_def_value.innerHTML = values[handle];
             $('.flpuesto').css('border-width',values[handle]+'px');
            $('#factor_puestob').val(values[handle]);
        });
        l_def.noUiSlider.on('update', function( values, handle ) {
            l_def_value.innerHTML = values[handle];
            $('.texto_puesto').css('font-size',values[handle]+'vw');
            $('#factor_letra').val(values[handle]);
        });
        g_def.noUiSlider.on('update', function( values, handle ) {
            g_def_value.innerHTML = values[handle];
            console.log(values[handle]);
            $('.draggable').draggable("option", "grid", [values[handle],values[handle]]);
            $('#factor_grid').val(values[handle]);
        });
        z_def.noUiSlider.on('update', function( values, handle ) {
            z_def_value.innerHTML = values[handle]+' %';
            zoom_actual=values[handle];
            clearTimeout(hacer_zoom);
            hacer_zoom=setTimeout(() => {
                zoom();
            }, 500);
        });
        t_def.noUiSlider.on('update', function( values, handle ) {
            t_def_value.innerHTML = values[handle]+' %';
            $('#factor_transp').val(values[handle]);
            $('#img_fondo').css('opacity',values[handle]/100);
        });
    } );

    $('#btn_guardar').click(function(){
        //event.preventDefault();
        let jsonObj = [];
        $('.flpuesto').each(function(){
            item={};
            item.id=$(this).data('id');
            item.puesto=$(this).data('puesto');
            item.top=$(this).position().top;
            item.left=$(this).position().left;
            item.offsettop=100*$(this).position().top/$('#plano').height();
            item.offsetleft=100*$(this).position().left/$('#plano').width();
            //console.log(item);
            jsonObj.push(item);
        });
        $('#json').val(JSON.stringify(jsonObj));
    });

    $(window).resize(function(){
        recolocar_puestos();
    })

    $('.nav-toggler').click(function(){
        recolocar_puestos();
    })

    var pw_def = document.getElementById('puesto-rangew-def');
    var pw_def_value = document.getElementById('puesto-rangew-def-val');

    var ph_def= document.getElementById('puesto-rangeh-def');
    var ph_def_value = document.getElementById('puesto-rangeh-def-val');

    var pb_def = document.getElementById('puesto-rangeb-def');
    var pb_def_value = document.getElementById('puesto-rangeb-def-val');

    var pr_def = document.getElementById('puesto-ranger-def');
    var pr_def_value = document.getElementById('puesto-ranger-def-val');

    var l_def = document.getElementById('letra-range-def');
    var l_def_value = document.getElementById('letra-range-def-val');

    var g_def = document.getElementById('grid-range-def');
    var g_def_value = document.getElementById('grid-range-def-val');

    var z_def = document.getElementById('puesto-zoom-def');
    var z_def_value = document.getElementById('puesto-zoom-def-val');

    var t_def = document.getElementById('puesto-transp-def');
    var t_def_value = document.getElementById('puesto-transp-def-val');

    noUiSlider.create(pw_def,{
        start   : [ {{ $plantas->factor_puestow }} ],
        connect : 'lower',
        range   : {
            'min': [  0.5 ],
            'max': [ 20 ]
        },
        format: wNumb({
            decimals: 2
        }),
    });

    noUiSlider.create(ph_def,{
        start   : [ {{ $plantas->factor_puestoh }} ],
        connect : 'lower',
        range   : {
            'min': [  0.5 ],
            'max': [ 20 ]
        },
        format: wNumb({
            decimals: 2
        }),
    });

    noUiSlider.create(pr_def,{
        start   : [ {{ $plantas->factor_puestor }} ],
        connect : 'lower',
        range   : {
            'min': [  0 ],
            'max': [ 50 ]
        },
        format: wNumb({
            decimals: 2
        }),
    });

    noUiSlider.create(pb_def,{
        start   : [ {{ $plantas->factor_puestob }} ],
        connect : 'lower',
        range   : {
            'min': [  0 ],
            'max': [ 10 ]
        },
        format: wNumb({
            decimals: 2
        }),
    });

    noUiSlider.create(g_def,{
        start   : [  {{ $plantas->factor_grid??5 }} ],
        connect : 'lower',
        range   : {
            'min': [  0.1 ],
            'max': [ 50 ]
        },
        format: wNumb({
            decimals: 2
        }),
    });

    noUiSlider.create(l_def,{
        start   : [ {{ $plantas->factor_letra }} ],
        connect : 'lower',
        range   : {
            'min': [  0.1 ],
            'max': [ 4 ]
        },
        format: wNumb({
            decimals: 2
        }),
    });

    noUiSlider.create(z_def,{
        start   : [ 100 ],
        connect : 'lower',
        step: 10,
        range   : {
            'min': [  20 ],
            'max': [ 500 ]
        },
        format: wNumb({
            decimals: 0
        }),
    });

    noUiSlider.create(t_def,{
        start   : [ {{ $plantas->factor_transp??100 }} ],
        connect : 'lower',
        step: 1,
        range   : {
            'min': [  0 ],
            'max': [ 100 ]
        },
        format: wNumb({
            decimals: 0
        }),
    });

    

    document.querySelectorAll( ".btn-close-card" ).forEach( el => el.addEventListener( "click", (e) => el.closest( ".card" ).remove()) );
</script>