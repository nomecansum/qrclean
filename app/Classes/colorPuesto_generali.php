<?php
/**
 * RandomColor 1.0.4
 *
 * PHP port of David Merfield JavaScript randomColor
 * https://github.com/davidmerfield/randomColor
 *
 *
 * The MIT License (MIT)
 *
 * Copyright (c) 2014 Damien "Mistic" Sorel
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace App\Classes;

use Carbon\Carbon;

class colorPuesto_generali
{
    static function colores($reserva, $asignado_usuario, $asignado_miperfil,$asignado_otroperfil,$puesto,$origen="P"){
        $tam_borde=$puesto->border!=null?$puesto->border:(isMobile()?$puesto->factor_puestob-1:$puesto->factor_puestob);
        if ($puesto->mca_incidencia=='S'){  //Incidencia
            return [
                'color'=>"#ffb300",
                'font_color'=>"#fff",
                'clase_disp'=>"",
                'title'=>"Puesto con incidencia",
                'borde'=>"",
                "transp"=>0.4,
                "icon="=>$puesto->val_icono?$puesto->val_icono:"fa fa-exclamation-triangle",
            ];
        } else if($puesto->id_estado==5){  //Inoperativo
            return [
                'color'=>"#3a444e",
                'font_color'=>"#fff",
                'clase_disp'=>"",
                'title'=>"Puesto inoperativo",
                'borde'=>"",
                "transp"=>0.4,
                "icon="=>$puesto->val_icono?$puesto->val_icono:"",
            ];
        } else if(isset($reserva)){
            if($origen=="P"){
                $borde=$puesto->val_color?$puesto->val_color:$puesto->hex_color;
            }
            if($origen=="R"){
                $borde=$puesto->val_color?$puesto->val_color:"LightCoral";
            }
            if(isset($reserva->fec_fin_reserva)){
                $horas_reserva="de ".Carbon::parse($reserva->fec_reserva)->format('H:i')." a ".Carbon::parse($reserva->fec_fin_reserva)->format('H:i');
            } else {
                $horas_reserva="";
            }
            return [
                'color'=>"LightCoral",
                'font_color'=>"#fff",
                'clase_disp'=>"",
                'title'=>"Reservado por ".$reserva->name." para hoy ".$horas_reserva,
                'borde'=>"border: ".$tam_borde."px solid ".$borde.";",
                "transp"=>0.4,
                "icon="=>$puesto->val_icono?$puesto->val_icono:"",
            ];
        } else if(isset($asignado_usuario)){
            return [
                'color'=>"#f2cb07",
                'font_color'=>"#fff",
                'clase_disp'=>"",
                'title'=>"Puesto permanentemente asignado a ".$asignado_usuario->name,
                'borde'=>"border: 3px solid #ff9f1a; border-radius: 8px",
                "transp"=>0.4,
                "icon="=>$puesto->val_icono?$puesto->val_icono:"",
            ];
        } else if(isset($asignado_otroperfil)){
            return [
                'color'=>"#e8c468",
                'font_color'=>"#fff",
                'clase_disp'=>"",
                'title'=>"Puesto reservado para  ".$asignado_otroperfil->des_nivel_acceso,
                'borde'=>"",
                "transp"=>0.4,
                "icon="=>$puesto->val_icono?$puesto->val_icono:"",
            ];
        } else if(isset($asignado_miperfil)){
            return [
                'color'=>"#dff9d2",
                'font_color'=>"#05688f",
                'clase_disp'=>"disponible",
                'title'=>"Puesto reservado para  ".$asignado_miperfil->des_nivel_acceso,
                'borde'=>"border: 3px solid #05688f; border-radius: 6px",
                "transp"=>1,
                "icon="=>$puesto->val_icono?$puesto->val_icono:"",
            ];
        } else if($puesto->id_estado==5){  //Bloqueado
            return [
                'color'=>"#3a444e",
                'font_color'=>"#fff",
                'clase_disp'=>"",
                'title'=>"Puesto bloquedo",
                'borde'=>"",
                "transp"=>0.4,
                "icon="=>$puesto->val_icono?$puesto->val_icono:"",
            ];
        }   else {
            if($origen=="P"){
                $borde=$puesto->val_color?$puesto->val_color:$puesto->hex_color;
            }
            if($origen=="R"){
                $borde="#dff9d2";
                $borde=$puesto->val_color?$puesto->val_color:"#dff9d2";
            }
            return [
                'color'=>"#dff9d2",
                'font_color'=>"#444",
                'clase_disp'=>"disponible",
                'title'=>$origen=="P"?$puesto->des_estado:"Disponible",
                'borde'=>"border: ".$tam_borde."px solid ".$borde.";",
                "transp"=>1,
                "icon="=>$puesto->val_icono?$puesto->val_icono:"",
            ];
        } 
    }
}