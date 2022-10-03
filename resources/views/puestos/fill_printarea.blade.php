@php
    $color_qr=hexToRgb($r->sel_color??'#000');
    $col=1;
    $primera_vuelta=true;
    $columnas_mostrar=$r->col??0;

    //Aumentamos el tiempo maximo de timeout, porque los informes pueden tardar
    ini_set('max_execution_time', 500);
    set_time_limit(500);

    //Y la memoria disponibie para ejecucion
    ini_set('memory_limit', '4095M');

@endphp

<div class="contenedor" style="margin-top: {{ $r->margen_top??4 }}px; margin-left: {{ $r->margen_left??4 }}px">
@foreach($puestos as $puesto)
    @php
        if(isset($r->color_texto)){
            switch ((int)$r->color_texto) {
                case '1':
                    $color_texto='black';
                    break;
                case '2':
                    $color_texto=$puesto->color_tipo;
                    break;
                case '3':
                    $color_texto=$r->sel_color;
                    break;
                default: 
                    $color_texto='black';
                    break;
            }
        } else {
            $color_texto='black';
        }
    @endphp

    @if($columnas_mostrar>0 && $primera_vuelta)
        <div class="d-flex flex-row prim">
           
    @endif
    <div class="text-center cont_qr" style="display: inline-block;  margin: {{ $r->espacio_h }}px {{ $r->espacio_v }}px {{ $r->espacio_h }}px {{ $r->espacio_v }}px">
        @if(nombrepuesto($puesto)!=$puesto->cod_puesto)
        <div class="w-100 bg-white text-center font-bold mt-0 texto_qr" style="color: {{ $color_texto}}; background-color: #fff; font-size: {{ $r->font_size??14 }}px;">
            {{ $puesto->cod_puesto }}
        </div>
        @endif
        <div class="img_qr">
            <img class="qr" src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->color($color_qr["r"],$color_qr["g"],$color_qr["b"])->size($r->tam_qr??230)->generate(config('app.url_base_scan').$puesto->token)) !!} ">
        </div>
        <div class="w-100 bg-white text-center font-bold texto_qr" style="color: {{$color_texto}}; background-color: #fff; font-size: {{ $r->font_size??14 }}px">
            @if(isset($r->mca_icono)&&(int)$r->mca_icono==2)<i class="{{$puesto->icono_tipo}}"></i>@endif  {{ nombrepuesto($puesto) }}
        </div>
        @if(isset($r->footer) && (int)$r->footer>1)
    
        <div class="w-100 d-flex flex-row ">
            <div class="bg-white font-bold texto_qr col-md-10 text-start" style="color: {{$color_texto}}; background-color: #fff; font-size: {{ $r->font_size??14 }}px;">
                @if($r->footer==3) {{ $puesto->nom_cliente }} @endif 
            </div>
            <div class="col-md-2 text-end mr-1">
                <img src="{{ Storage::disk(config('app.img_disk'))->url('img/clientes/images/'.$puesto->img_logo) }}" style="width: 30px;" alt="" class="logo rounded">
            </div>
        </div>
        @endif
    </div>
    @php
        $col++;
        $primera_vuelta=false;
    @endphp
    @if($columnas_mostrar>0 && $col>$columnas_mostrar)
        </div>
        <div class="d-flex flex-row otra">
        @php
            $col=1;
        @endphp
    @endif
@endforeach
</div>