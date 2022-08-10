<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

$descripcion = "Abre un nuevo ticket LAIA con los parametros que se especifiqen";

$icono='<i class="fa-solid fa-ticket-simple"></i>';

$params='{
    "parametros":[
        {
            "label": "Titulo del ticket",
            "name": "titulo",
            "tipo": "txt",
            "def": "",
            "required": true
        },
        {
            "label": "Cuerpo del ticket",
            "name": "cuerpo",
            "tipo": "html5",
            "def": "",
            "required": true
        },
        {
            "label": "Categoria",
            "name": "categoria",
            "tipo": "num",
            "def": "1260",
            "required": true
        },
        {
            "label": "Area escalado",
            "name": "area_escalado",
            "tipo": "num",
            "def": "12",
            "required": true
        },
        {
            "label": "Tipo ticket",
            "name": "tipo_ticket",
            "tipo": "num",
            "def": "12",
            "required": true
        },
        {
            "label": "Notificar",
            "name": "notificar",
            "tipo": "bool",
            "def": "true",
            "required": true
        },
        {
            "label": "Cliente",
            "name": "cod_cliente",
            "tipo": "num",
            "def": "",
            "required": true
        },
        {
            "label": "Usuario creador",
            "name": "usuario_creador",
            "tipo": "txt",
            "def": "addon_caixa",
            "required": true
        }
    ]
}';

$func_accion = function($accion, $resultado, $campos,$id) {

    //Parte comun de todas las acciones
    $param_accion = $accion->param_accion;
    $param_accion = json_decode(json_decode($param_accion));

    //https://laia.onthespot.com/objetos/tickets_laia.cfc?method=CrearTicketLaia_v3&usuario=addon_caixa&password=caixa&usuario_creador=addon_caixa&categoria=1260&area_escalado=19&tipo_ticket=2&cod_cliente=23671&titulo=ola ke ase&descripcion=desc1

    $resultado = collect($resultado->data); //Cogemos el bloque da datos del resultado que es donde estan los datos de los elementos afectadso
    $datos = $resultado->where('id', $id)->first();
    $response=Http::withOptions(['verify' => false])
        ->get(config('app.url_tickets_laia'), [
            'method' => 'CrearTicketLaia_v3',
            'usuario' => config('app.usuario_laia'),
            'password' => config('app.password_laia'),
            'titulo' => comodines_texto(valor($param_accion, "titulo"),$campos,$datos),
            'descripcion' => comodines_texto(valor($param_accion, "cuerpo"),$campos,$datos),
            'categoria' => valor($param_accion, "categoria"),
            'area_escalado' => valor($param_accion, "area_escalado"),
            'tipo_ticket' => valor($param_accion, "tipo_ticket"),
            'notificar' => valor($param_accion, "notificar"),
            'cod_cliente' => valor($param_accion, "cod_cliente"),
            'usuario_creador' => config('app.usuario_creador_tickets_laia'),
        ]);
    $respuesta=$response->body();
    $respuesta=str_replace("\r", '', $respuesta);
    $respuesta=str_replace("\n", '', $respuesta);
    $respuesta=str_replace("\t", '', $respuesta);
    $respuesta=str_replace("&lt;", '<', $respuesta);
    $respuesta=str_replace("&gt;", '>', $respuesta);
    $respuesta=strip_tags($respuesta);
    $this->log_evento($respuesta,$accion->cod_regla);

}
/* 


<!-- [EMAIL]
----Notificacion por ticket----=
servidor=http://localhost/laia/objetos/tickets_laia.cfc
user=addon_caixa
pass=caixa
notificar=0
categoria=1260
area_escalado=19
tipo_ticket=2
cod_cliente=23671
usuario_creador=renderonce
grupo_excluir_ack=669
----Notificacion por email----=
host_smtp=smtp.gmail.com
user_smtp=desarrolloots@gmail.com
pass_smtp=onthespot2017
port_smtp=465
lista_smtp=manuel.ferreroleonardo@telefonica.com
from_smtp=desarrolloots@gmail.com
activar_smtp=0 -->



<!-- if IniOptions.EMAILnotificar then
        begin
            tty('Creando ticket',4);
            req1:=TclHttpRequest.create(Self);

            http1.Request:=req1;
            response:=TStringList.Create();
            http1.Close;
            http1.Connection.Close(true);
            //url:=IniOptions.EMAILservidor+'?method=CrearTicketLaia_v3&usuario='+IniOptions.EMAILuser+'&password='+IniOptions.EMAILpass+'&usuario_creador='+IniOptions.EMAILuser+'&categoria='+inttostr(IniOptions.EMAILcategoria)+'&area_escalado='+inttostr(IniOptions.EMAILarea_escalado)+'&tipo_ticket='+inttostr(IniOptions.EMAILtipo_ticket)+'&cod_cliente='+inttostr(IniOptions.EMAILcod_cliente)+'&titulo='+titulo+'&descripcion='+texto;
            Http1.Request.AddFormField('method','CrearTicketLaia_v3');
            Http1.Request.AddFormField('usuario',IniOptions.EMAILuser);
            Http1.Request.AddFormField('password',IniOptions.EMAILpass);
            Http1.Request.AddFormField('usuario_creador',IniOptions.EMAILuser);
            Http1.Request.AddFormField('categoria',inttostr(IniOptions.EMAILcategoria));
            Http1.Request.AddFormField('area_escalado',inttostr(IniOptions.EMAILarea_escalado));
            Http1.Request.AddFormField('tipo_ticket',inttostr(IniOptions.EMAILtipo_ticket));
            Http1.Request.AddFormField('cod_cliente',inttostr(IniOptions.EMAILcod_cliente));
            Http1.Request.AddFormField('titulo',titulo);
            Http1.Request.AddFormField('descripcion',texto);
            http1.Post(IniOptions.EMAILservidor, response);
            //http1.Get(url,response);
            s:=StringReplace(response.Text,#$D#$A,'',[rfreplaceall]);
            s:=StringReplace(s,#9,'',[rfreplaceall]);
            s:=StringReplace(s,'&gt;','',[rfreplaceall]);
            s:=StringReplace(s,'&lt;','',[rfreplaceall]);

            tty('Respuesta creacion ticket: '+stripHTML(s),4);
        end; -->
*/

?>

