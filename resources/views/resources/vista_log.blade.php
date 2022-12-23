@foreach($log as $l)
    @php
        $bgcolor = null;
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
                $color="#00a4eb";
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
            case "user":
                $color="#060452";
                $bgcolor="#7cc0d0";
            break;

        }
        $des_log= $l->txt_log;
        $des_log=str_replace('Inicio de la tarea','<span style="color: #4169E1; font-weight: bold">Inicio de la tarea</span>',$des_log);
        $des_log=str_replace('Fin de la tarea','<span style="color: #3CB371; font-weight: bold">Fin de la tarea</span>',$des_log);
        $des_log=str_replace('clientes','<span style="color: #BA55D3; font-weight: bold">clientes</span>',$des_log);
        $des_log=str_replace('informe','<span style="color: #000080; font-weight: bold">informe</span>',$des_log);
        $des_log=str_replace('RunTask','<i class="fa-solid fa-play text-success"></i> <span class="font-bold text-success"> RunTask</span>',$des_log);
        $des_log=str_replace('ENDTask','<i class="fa-solid fa-stop text-danger"></i><span class="font-bold text-danger">  ENDTask</span>',$des_log);
        $des_log=str_replace('Schedule start','<i class="fa-sharp fa-solid fa-stopwatch" style="color:#f4a462"></i><span class="font-bold" style="color:#f4a462">  Schedule start</span>',$des_log);
        $des_log=str_replace('Fin schedule','<i class="fa-thin fa-stopwatch"  style="color:#3d5a7f"></i><span class="font-bold" style="color: #3d5a7f">  Fin schedule</span>',$des_log);
    @endphp
    <tr style="font-size: 10px; " @isset($bgcolor) background-color: {{ $bgcolor }}; color: {{ txt_blanco($bgcolor) }} @endisset><td style="width: 5%" class="td_log"><b>{{ Carbon\Carbon::parse($l->fec_log)->setTimezone(session('timezone'))->format('H:i')}}</b></td><td class="font-bold td_log" style="color: {{ $color }}">{{ $l->tip_mensaje }}</td><td class="td_log"> {!! nl2br($des_log) !!}</td></tr>
@endforeach
   