

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

    <div class="panel">
        <div class="panel-heading">
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
                @if(isset($plantas->img_plano))
                {{--  style="background-image: url('{{ url('img/plantas/'.$plantas->img_plano) }}'); background-repeat: no-repeat; background-size: contain;"  --}}
                    <div class="row container" id="plano" >
                        <img src="{{ url('img/plantas/'.$plantas->img_plano) }}" style="width: 100%" id="img_fondo" class="container">
                        @php
                            $left=0;
                            $top=0;
                        @endphp
                        @foreach($puestos as $puesto)
                            <div class="text-center font-bold rounded add-tooltip bg-{{ $puesto->val_color }} align-middle flpuesto draggable" id="puesto{{ $puesto->id_puesto }}" title="{{ $puesto->des_puesto }}" data-id="{{ $puesto->id_puesto }}" data-puesto="{{ $puesto->cod_puesto }}" style="top: {{ $top }}px; left: {{ $left }}px">
                                <span class="h-100 align-middle" style="font-size: 10px;">{{ $puesto->cod_puesto }}</span>
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
                <div class="form-group mt-3">
                    <div class="col-md-offset-10 col-md-12">
                        <input class="btn btn-primary" id="btn_guardar" type="submit" value="Guardar">
                    </div>
                </div>
            </form>

        </div>
    </div>

<script>
    $('.form-ajax').submit(form_ajax_submit);

    try{
        posiciones={!! json_encode($plantas->posiciones)??'[]' !!};
        posiciones=$.parseJSON(posiciones); 
    } catch($err){
        posiciones=[];
    }
    //console.log(posiciones);
    function recolocar_puestos(){
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
        recolocar_puestos();
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
</script>