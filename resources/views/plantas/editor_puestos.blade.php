

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

    <div class="panel editor">
        <div class="panel-heading">
            <div class="panel-control">
                <button class="btn btn-default" data-panel="dismiss" data-dismiss="panel"><i class="demo-psi-cross"></i></button>
            </div>
            <h3 class="panel-title">Ubicacion de puestos en planta {{ $plantas->des_planta }}</h3>
            
        </div>
        

        <div class="panel-body">

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
                <input type="hidden" name="factor_puesto" id="factor_puesto" value="{{ $plantas->factor_puesto }}">
                <input type="hidden" name="factor_letra" id="factor_letra"  value="{{ $plantas->factor_letra }}">
                @if(isset($plantas->img_plano))
                {{--  style="background-image: url('{{ url('img/plantas/'.$plantas->img_plano) }}'); background-repeat: no-repeat; background-size: contain;"  --}}
                    <div class="row container" id="plano" >
                        <img src="{{ Storage::disk(config('app.img_disk'))->url('img/plantas/'.$plantas->img_plano) }}" style="width: 100%" id="img_fondo" class="container">
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
                        @endphp
                            <div class="text-center font-bold rounded add-tooltip bg-{{ $puesto->color_estado }} align-middle flpuesto draggable add-tooltip" title="{{ $puesto->des_puesto }}" id="puesto{{ $puesto->id_puesto }}" title="{{ $puesto->des_puesto }}" data-id="{{ $puesto->id_puesto }}" data-puesto="{{ $puesto->cod_puesto }}" style="height: {{ $puesto->factor_puesto }}vw ; width: {{ $puesto->factor_puesto }}vw;top: {{ $top }}px; left: {{ $left }}px; {{ $cuadradito['borde'] }}">
                                <span class="h-100 align-middle texto_puesto" style="font-size: 0.8vw;">{{ $puesto->cod_puesto }}</span>
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
                @if(checkPermissions(['Plantas'],['C']))
                <div class="row mt-3">
                    <div class="form-group col-md-3">
                        <label>Tamaño de puestos: </label> <span id="puesto-range-def-val"></span>
                        <div id="puesto-range-def"></div>	
                    </div>
                    <div class="col-md-2">

                    </div>
                    <div class="form-group col-md-3">
                        <label>Tamaño de letra: </label> <span id="letra-range-def-val"></span>
                        <div id="letra-range-def"></div>	
                    </div>
                    <div class="col-md-3">

                    </div>
                    <div class="col-md-1">
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
        posiciones=$.parseJSON(posiciones); 
    } catch($err){
        posiciones=[];
    }
    //console.log(posiciones);
    function recolocar_puestos(){
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
            stop: function() {
            //console.log($(this).data('id')+' '+$(this).data('puesto')+' '+$(this).position().top+ ' '+$(this).position().left);
            }
        });
        setTimeout(recolocar_puestos, 800);
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

    $('.mainnav-toggle').click(function(){
        recolocar_puestos();
    })

    var p_def = document.getElementById('puesto-range-def');
    var p_def_value = document.getElementById('puesto-range-def-val');

    var l_def = document.getElementById('letra-range-def');
    var l_def_value = document.getElementById('letra-range-def-val');

    noUiSlider.create(p_def,{
        start   : [ {{ $plantas->factor_puesto }} ],
        connect : 'lower',
        range   : {
            'min': [  0.5 ],
            'max': [ 6 ]
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
            'max': [ 1.5 ]
        },
        format: wNumb({
            decimals: 2
        }),
    });

    p_def.noUiSlider.on('update', function( values, handle ) {
        p_def_value.innerHTML = values[handle];
        $('.flpuesto').css('height',values[handle]+'vw');
        $('.flpuesto').css('width',values[handle]+'vw');
        $('#factor_puesto').val(values[handle]);
    });
    l_def.noUiSlider.on('update', function( values, handle ) {
        l_def_value.innerHTML = values[handle];
        $('.texto_puesto').css('font-size',values[handle]+'vw');
        $('#factor_letra').val(values[handle]);
    });

    $('.demo-psi-cross').click(function(){
            $('.editor').hide();
        });
</script>