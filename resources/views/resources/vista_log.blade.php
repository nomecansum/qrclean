
<style>
    td {padding: 1px 5px 1px 5px}
</style>
<div class="table-responsive rounded">
    <table class="table table-condensed">
        @foreach($log as $l)
            @php
                switch($l->tip_mensaje){
                    case ("error"):
                        $color="#f25c5a";
                    break;
                    case "warning":
                    $color="#ffe066";
                    break;
                    case "debug":
                        $color="#8e9aaf";
                    break;
                    case "info":
                        $color="#70c2b4";
                    break;
                    case "critical":
                        $color="#ff0000";
                    break;
                    case "notice":
                        $color="#447a9c";
                    break;
                    case "alert":
                        $color="#ff9f1a";
                    break;

                }
                $des_log= $l->txt_log;
                $des_log=str_replace('Inicio de la tarea','<span style="color: #4169E1; font-weight: bold">Inicio de la tarea</span>',$des_log);  
                $des_log=str_replace('Fin de la tarea','<span style="color: #3CB371; font-weight: bold">Fin de la tarea</span>',$des_log);
                $des_log=str_replace('clientes','<span style="color: #BA55D3; font-weight: bold">clientes</span>',$des_log); 
                $des_log=str_replace('informe','<span style="color: #000080; font-weight: bold">informe</span>',$des_log);    
            @endphp
            <tr style="font-size: 10px; "><td style="width: 5%"><b>{{ Carbon\Carbon::parse($l->fec_log)->format('H:i')}}</b></td><td class="font-bold" style="color: {{ $color }}">{{ $l->tip_mensaje }}</td><td> {!! nl2br($des_log) !!}</td></tr>
        @endforeach
    </table>
</div>