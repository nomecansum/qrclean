<?php
namespace App\Classes;

if(isset($reserva)){
    $color="LightCoral";
    $font_color="#fff";
    $clase_disp="";
    $title="Reservado por ".$reserva->name." para hoy";
} else if(isset($asignado_usuario)){
    $color="#f2cb07";
    $font_color="#fff";
    $clase_disp="";
    $title="Puesto permanentemente asignado a ".$asignado_usuario->name;
    $borde="border: 3px solid #ff9f1a; border-radius: 16px";
} else if(isset($asignado_otroperfil)){
    $color="#e8c468";
    $font_color="#fff";
    $clase_disp="";
    $title="Puesto reservado para  ".$asignado_otroperfil->des_nivel_acceso;
} else if(isset($asignado_miperfil)){
    $color="#dff9d2";
    $font_color="#05688f";
    $clase_disp="disponible";
    $title="Puesto reservado para  ".$asignado_miperfil->des_nivel_acceso;
    $borde="border: 3px solid #05688f; border-radius: 10px";
}   else {
    $color="#dff9d2";
    $font_color="#aaa";
    $clase_disp="disponible";
    $tam_borde=isMobile()?'3':'5';
    $borde="border: ".$tam_borde."px solid ".$puesto->val_color??"#fff".";";
} 