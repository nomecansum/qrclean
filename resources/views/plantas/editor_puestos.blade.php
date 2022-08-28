

<style type="text/css">
.container {
    border: 1px solid #DDDDDD;
    width: 100%;
    position: relative;
    padding: 0px;
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
            
            

            <form method="POST" action="{{ url('/plantas/puestos/') }}" id="edit_plantas_form" name="edit_plantas_form" accept-charset="UTF-8" class="form-horizontal form-ajax"  enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="json" id="json">
                <input type="hidden" name="id_planta" value="{{ $plantas->id_planta }}">
                <input type="hidden" name="factor_puestow" id="factor_puestow" value="{{ $plantas->factor_puestow }}">
                <input type="hidden" name="factor_puestoh" id="factor_puestoh" value="{{ $plantas->factor_puestoh }}">
                <input type="hidden" name="factor_puestob" id="factor_puestob" value="{{ $plantas->factor_puestob }}">
                <input type="hidden" name="factor_puestor" id="factor_puestor" value="{{ $plantas->factor_puestor }}">
                <input type="hidden" name="factor_letra" id="factor_letra"  value="{{ $plantas->factor_letra }}">
                <div class="card">
                    <div class="card-body">
                        @if(isset($plantas->img_plano))
                        {{--  style="background-image: url('{{ url('img/plantas/'.$plantas->img_plano) }}'); background-repeat: no-repeat; background-size: contain;"  --}}
                            <div class="row container" id="plano" >
                                <img src="{{ Storage::disk(config('app.img_disk'))->url('img/plantas/'.$plantas->img_plano) }}" style="width: 100%" id="img_fondo">
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
                                    <div class="text-center font-bold  add-tooltip bg-{{ $puesto->color_estado }} align-middle flpuesto draggable add-tooltip" title="{{ nombrepuesto($puesto) }}" id="puesto{{ $puesto->id_puesto }}" data-id="{{ $puesto->id_puesto }}" data-puesto="{{ $puesto->cod_puesto }}" style="height: {{ $puesto->factor_puestoh }}vw ; width: {{ $puesto->factor_puestow }}vw;top: {{ $top }}px; left: {{ $left }}px; {{ $cuadradito['borde'] }}">
                                        <span class="h-100 align-middle texto_puesto" style="font-size: {{ $puesto->factor_letra }}vw;">{{ nombrepuesto($puesto) }}</span>
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

    var tooltip = $('.add-tooltip');
    if (tooltip.length)tooltip.tooltip();

    try{
        posiciones={!! json_encode($plantas->posiciones)??'[]' !!};
        document.getElementById('plano').setAttribute('data-posiciones',posiciones);
        posiciones=$.parseJSON(posiciones); 
       
    } catch($err){
        posiciones=[];
    }
    //console.log(posiciones);
    function colocar_puestos(){
        console.log("recolocar");
        $.each(posiciones, function(i, item) {//console.log(item);
            puesto=$('#puesto'+item.id);
            puesto.css('top',$('#plano').height()*item.offsettop/100);
            puesto.css('left',$('#plano').width()*item.offsetleft/100);
        });
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
        setTimeout(colocar_puestos, 800);

        pw_def.noUiSlider.on('update', function( values, handle ) {
            pw_def_value.innerHTML = values[handle];
            $('.flpuesto').css('width',values[handle]+'vw');
            $('#factor_puestow').val(values[handle]);
        });
        ph_def.noUiSlider.on('update', function( values, handle ) {
            ph_def_value.innerHTML = values[handle];
            $('.flpuesto').css('height',values[handle]+'vh');
            $('#factor_puestoh').val(values[handle]);
        });
        pr_def.noUiSlider.on('update', function( values, handle ) {
            pr_def_value.innerHTML = values[handle];
            $('.flpuesto').css('border-radius',values[handle]+'px');
            $('#factor_puestob').val(values[handle]);
        });
        pb_def.noUiSlider.on('update', function( values, handle ) {
            pb_def_value.innerHTML = values[handle];
             $('.flpuesto').css('border-width',values[handle]+'px');
            $('#factor_puestor').val(values[handle]);
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
            // $('.texto_puesto').css('font-size',values[handle]+'vw');
            // $('#factor_letra').val(values[handle]);
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

    noUiSlider.create(pw_def,{
        start   : [ {{ $plantas->factor_puestow }} ],
        connect : 'lower',
        range   : {
            'min': [  0.5 ],
            'max': [ 10 ]
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
            'max': [ 10 ]
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
            'max': [ 10 ]
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
        start   : [ 5 ],
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

    

    document.querySelectorAll( ".btn-close-card" ).forEach( el => el.addEventListener( "click", (e) => el.closest( ".card" ).remove()) );
</script>