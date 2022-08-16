<div class="table-responsive rounded" style="width:100%">
    <table class="table">
        @foreach($log as $l)
            @php
                $des_log= $l->txt_log;
                $des_log=str_replace('Inicio de la tarea','<span style="color: #4169E1; font-weight: bold">Inicio de la tarea</span>',$des_log);
                $des_log=str_replace('Fin de la tarea','<span style="color: #3CB371; font-weight: bold">Fin de la tarea</span>',$des_log);
                $des_log=str_replace('clientes','<span style="color: #BA55D3; font-weight: bold">clientes</span>',$des_log);
                $des_log=str_replace('informe','<span style="color: #000080; font-weight: bold">informe</span>',$des_log);

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
            <tr style=""><td style="width: 5%"><b>{{ Carbon\Carbon::parse($l->fec_log)->format('H:i')}}</b></td><td style="font-size: 10px"><span style="font-size: 10px; color: {{ $color_tipo }}">[{{ $l->tip_mensaje }}]</span> {!! nl2br($des_log) !!}</td></tr>
        @endforeach
    </table>
</div>
