<div class="table-responsive rounded" style="width:100%">
    <table class="table">
        @foreach($log as $l)
            @php
                $des_log= $l->txt_log;
                $des_log=str_replace('Inicio de la tarea','<span style="color: #4169E1; font-weight: bold">Inicio de la tarea</span>',$des_log);
                $des_log=str_replace('Fin de la tarea','<span style="color: #3CB371; font-weight: bold">Fin de la tarea</span>',$des_log);
                $des_log=str_replace('clientes','<span style="color: #BA55D3; font-weight: bold">clientes</span>',$des_log);
                $des_log=str_replace('informe','<span style="color: #000080; font-weight: bold">informe</span>',$des_log);
            @endphp
            <tr style="font-size: 10px"><td style="width: 5%"><b>{{ Carbon\Carbon::parse($l->fec_log)->format('H:i')}}</b></td><td> {!! nl2br($des_log) !!}</td></tr>
        @endforeach
    </table>
</div>
