<div class="table-responsive rounded">
    <table class="table">
        @foreach($log as $l)
            @php
                $des_log= $l->txt_log;
                $des_log=str_replace('Iniciando proceso','<span style="color: #4169E1; font-weight: bold">Iniciando proceso</span>',$des_log);
                $des_log=str_replace('Finalizado el procesado','<span style="color: #3CB371; font-weight: bold">Finalizado el procesado</span>',$des_log);
                $des_log=str_replace('ID a procesar','<span style="color: #BA55D3; font-weight: bold">ID a procesar</span>',$des_log);
                $des_log=str_replace('Accion','<span style="color: #000080; font-weight: bold">Accion</span>',$des_log);
                $des_log=str_replace('Iteracion','<span style="color: #000080; font-weight: bold">Superado</span>',$des_log);
                $des_log=str_replace('Superado el maximo de iteraciones','<span style="color: #f08080; font-weight: bold">Superado el maximo de iteraciones</span>',$des_log);
                $des_log=str_replace('Añadido no molestar','<span style="color: #20b2aa; font-weight: bold">Añadido no molestar</span>',$des_log);

                $color_tipo='';
                if($l->tip_mensaje=='info'){
                    $color_tipo='#4169E1';
                }elseif($l->tip_mensaje=='error'){
                    $color_tipo='#FF0000';
                }elseif($l->tip_mensaje=='warning'){
                    $color_tipo='#FFA500';
                }elseif($l->tip_mensaje=='debug'){
                    $color_tipo='#a9bcd0';
                }

            @endphp
            <tr style="font-size: 10px"><td style="width: 5%"><b>{{ Carbon\Carbon::parse($l->fec_log)->format('H:i')}}</b></td><td style="font-size: 10px"><span style="font-size: 10px; color: {{ $color_tipo }}">[{{ $l->tip_mensaje }}]</span> {!! nl2br($des_log) !!}</td></tr>
        @endforeach
    </table>
</div>