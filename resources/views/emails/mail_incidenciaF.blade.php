///PARA CIERRE
                    // //Enviamos mail al uusario abriente
                    // $des_causa=causas_cierre::find($inc->id_causa_cierre)->des_causa;
                    // $usuario_abriente=users::find($inc->id_usuario_apertura);
                    // $body="Tenemos el placer de comunicarle que la incidencia [".$inc->id_incidencia."] ".$inc->des_incidencia."Que usted abrió el  ".Carbon::parse($inc->fec_apertura)->format('d/m/Y')." ha sido cerrada por ".Auth::user()->name." Con el siguiente comentario:<br>".chr(13)." [".$des_causa."] ".$r->comentario_cierre;
                    // Mail::send('emails.mail_cerrar_incidencia', ['inc'=>$inc,'body'=>$body], function($message) use ($inc, $puesto,$body,$usuario_abriente) {
                    //     if(config('app.env')=='local'){//Para que en desarrollo solo me mande los mail a mi
                    //         $message->to(explode(';','nomecansum@gmail.com'), '')->subject('Confirmacion de cierre de incidencia en puesto '.$puesto->cod_puesto.' '.$puesto->des_edificio.' - '.$puesto->des_planta);
                    //     } else {
                    //         $message->to(explode(';',$usuario_abriente->email), '')->subject('Confirmacion de cierre de incidencia en puesto '.$puesto->cod_puesto.' '.$puesto->des_edificio.' - '.$puesto->des_planta);
                    //     }
                    //     $message->from(config('mail.from.address'),config('mail.from.name'));
                    // });