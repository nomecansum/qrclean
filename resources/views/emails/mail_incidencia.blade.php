@extends('layout_email')
@php
use App\Models\incidencias;
use App\Models\incidencias_tipos;
use App\Models\users;

//$inc=incidencias::find(3);
$tipo=incidencias_tipos::find($inc->id_tipo_incidencia);
$puesto=DB::table('puestos')
->join('edificios','puestos.id_edificio','edificios.id_edificio')
->join('plantas','puestos.id_planta','plantas.id_planta')
->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
->join('clientes','puestos.id_cliente','clientes.id_cliente')
->where('id_puesto',$inc->id_puesto)
->first();
$usuario=users::find($inc->id_usuario_apertura);
@endphp

<tr>
    <td style="Margin:0;padding-top:20px;padding-bottom:20px;padding-left:20px;padding-right:20px;background-color:#FFFFFF"
        bgcolor="#ffffff" align="left">
        <!--[if mso]><table style="width:560px" cellpadding="0" cellspacing="0"><tr><td style="width:390px" valign="top"><![endif]-->
        <table class="es-left" cellspacing="0" cellpadding="0" align="left"
            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left">
            <tr>
                <td class="es-m-p20b" align="left"
                    style="padding:0;Margin:0;width:390px">
                    <table width="100%" cellspacing="0" cellpadding="0"
                        role="presentation"
                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                        <tr>
                            <td style="padding:0;Margin:0;font-size:0px"
                                align="left">
                                <img
                                src="{{ Storage::disk(config('app.img_disk'))->url('img/clientes/images/'.session('logo_cliente')) }}"
                                class="adapt-img"
                                style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic"
                                width="158" alt="" onerror="this.src='{{ config('app.url_asset_mail').'/img/Mosaic_brand_300.png' }}';">
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:0;Margin:0;padding-top:20px;padding-bottom:35px;font-size:0"
                                align="left">
                                <table width="20%" height="100%" cellspacing="0"
                                    cellpadding="0" border="0" role="presentation"
                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                    <tr>
                                        <td
                                            style="padding:0;Margin:0;border-bottom:4px solid #5A83BE;background:#FFFFFF none repeat scroll 0% 0%;height:1px;width:100%;margin:0px">
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td align="left" style="padding:0;Margin:0">
                                <p
                                    style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:35px;color:#252F85;font-size:36px">
                                    Se ha notiﬁcado una incidencia</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <!--[if mso]></td><td style="width:20px"></td><td style="width:150px" valign="top"><![endif]-->
        <table class="es-right" cellspacing="0" cellpadding="0" align="right"
            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:right">
            <tr>
                <td align="left" style="padding:0;Margin:0;width:150px">
                    <table width="100%" cellspacing="0" cellpadding="0"
                        role="presentation"
                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                        <tr>
                            <td style="padding:0;Margin:0;font-size:0px"
                                align="center"><img class="adapt-img"
                                    src="https://crambo.eu/newsletters/plantillas_reservas/alerta_02.png"
                                    alt
                                    style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic"
                                    width="127"></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <!--[if mso]></td></tr></table><![endif]-->
    </td>
</tr>
<tr>
    <td align="left" style="padding:0;Margin:0">
        <table width="100%" cellspacing="0" cellpadding="0"
            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
            <tr>
                <td valign="top" align="center"
                    style="padding:0;Margin:0;width:600px">
                    <table width="100%" cellspacing="0" cellpadding="0"
                        role="presentation"
                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                        <tr>
                            <td style="padding:0;Margin:0;font-size:0px"
                                align="center"><img class="adapt-img"
                                    src="https://crambo.eu/newsletters/plantillas_reservas/sombra_01.png"
                                    alt
                                    style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic"
                                    width="600"></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </td>
</tr>
<tr>
    <td align="left"
        style="padding:0;Margin:0;padding-top:20px;padding-left:20px;padding-right:20px">
        <table width="100%" cellspacing="0" cellpadding="0"
            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
            <tr>
                <td valign="top" align="center"
                    style="padding:0;Margin:0;width:560px">
                    <table width="100%" cellspacing="0" cellpadding="0"
                        role="presentation"
                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                        <tr>
                            <td align="left" style="padding:0;Margin:0">
                                <p
                                    style="Margin:0;margin-bottom:10px;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:24px;color:#333333;font-size:16px">
                                    El usuario {{ $usuario->name }} ( <a href="mailto:{{ $usuario->email }}"> {{ $usuario->email }} </a> ) ha creado una incidencia de tipo <span style="color:#6488C0"><strong>{{ $tipo->des_tipo_incidencia }}</strong>:</span></p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </td>
</tr>
<tr>
    <td align="left"
        style="padding:0;Margin:0;padding-top:20px;padding-left:20px;padding-right:20px">
        <!--[if mso]><table style="width:560px" cellpadding="0" cellspacing="0"><tr><td style="width:40px" valign="top"><![endif]-->
        <table class="es-left" cellspacing="0" cellpadding="0" align="left"
            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left">
            <tr>
                <td class="es-m-p20b" align="left"
                    style="padding:0;Margin:0;width:40px">
                    <table width="100%" cellspacing="0" cellpadding="0"
                        role="presentation"
                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                        <tr>
                            <td style="padding:0;Margin:0;font-size:0px"
                                align="center"><img class="adapt-img"
                                    src="https://crambo.eu/newsletters/plantillas_reservas/ico_01.png"
                                    alt
                                    style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic"
                                    width="37"></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <!--[if mso]></td><td style="width:20px"></td><td style="width:500px" valign="top"><![endif]-->
        <table class="es-right" cellspacing="0" cellpadding="0" align="right"
            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:right">
            <tr>
                <td align="left" style="padding:0;Margin:0;width:500px">
                    <table width="100%" cellspacing="0" cellpadding="0"
                        role="presentation"
                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                        <tr>
                            <td style="padding:0;Margin:0;padding-top:5px;padding-bottom:5px;font-size:0"
                                align="center">
                                <table width="100%" height="100%" cellspacing="0"
                                    cellpadding="0" border="0" role="presentation"
                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                    <tr>
                                        <td
                                            style="padding:0;Margin:0;border-bottom:0px solid #CCCCCC;background:#FFFFFF none repeat scroll 0% 0%;height:1px;width:100%;margin:0px">
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td align="left" style="padding:0;Margin:0">
                                <p
                                    style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#333333;font-size:14px">
                                    <strong>Fecha:</strong> {!! beauty_fecha($inc->fec_apertura)!!}</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <!--[if mso]></td></tr></table><![endif]-->
    </td>
</tr>
<tr>
    <td align="left"
        style="padding:0;Margin:0;padding-top:20px;padding-left:20px;padding-right:20px">
        <!--[if mso]><table style="width:560px" cellpadding="0" cellspacing="0"><tr><td style="width:40px" valign="top"><![endif]-->
        <table class="es-left" cellspacing="0" cellpadding="0" align="left"
            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left">
            <tr>
                <td class="es-m-p20b" align="left"
                    style="padding:0;Margin:0;width:40px">
                    <table width="100%" cellspacing="0" cellpadding="0"
                        role="presentation"
                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                        <tr>
                            <td style="padding:0;Margin:0;font-size:0px"
                                align="center"><img class="adapt-img"
                                    src="https://crambo.eu/newsletters/plantillas_reservas/ico_02.png"
                                    alt
                                    style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic"
                                    width="37"></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <!--[if mso]></td><td style="width:20px"></td><td style="width:500px" valign="top"><![endif]-->
        <table class="es-right" cellspacing="0" cellpadding="0" align="right"
            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:right">
            <tr>
                <td align="left" style="padding:0;Margin:0;width:500px">
                    <table width="100%" cellspacing="0" cellpadding="0"
                        role="presentation"
                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                        <tr>
                            <td style="padding:0;Margin:0;padding-top:5px;padding-bottom:5px;font-size:0"
                                align="center">
                                <table width="100%" height="100%" cellspacing="0"
                                    cellpadding="0" border="0" role="presentation"
                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                    <tr>
                                        <td
                                            style="padding:0;Margin:0;border-bottom:0px solid #CCCCCC;background:#FFFFFF none repeat scroll 0% 0%;height:1px;width:100%;margin:0px">
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td align="left" style="padding:0;Margin:0">
                                <p
                                    style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#333333;font-size:14px">
                                    <strong>Puesto:</strong> {{ $puesto->des_puesto }}, edificio {{ $puesto->des_edificio  }} | {{ $puesto->des_planta }}</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <!--[if mso]></td></tr></table><![endif]-->
    </td>
</tr>
<tr>
    <td align="left"
        style="padding:0;Margin:0;padding-top:20px;padding-left:20px;padding-right:20px">
        <!--[if mso]><table style="width:560px" cellpadding="0" cellspacing="0"><tr><td style="width:40px" valign="top"><![endif]-->
        <table class="es-left" cellspacing="0" cellpadding="0" align="left"
            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left">
            <tr>
                <td class="es-m-p20b" align="left"
                    style="padding:0;Margin:0;width:40px">
                    <table width="100%" cellspacing="0" cellpadding="0"
                        role="presentation"
                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                        <tr>
                            <td style="padding:0;Margin:0;font-size:0px"
                                align="center"><img class="adapt-img"
                                    src="https://crambo.eu/newsletters/plantillas_reservas/ico_03.png"
                                    alt
                                    style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic"
                                    width="37"></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <!--[if mso]></td><td style="width:20px"></td><td style="width:500px" valign="top"><![endif]-->
        <table class="es-right" cellspacing="0" cellpadding="0" align="right"
            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:right">
            <tr>
                <td align="left" style="padding:0;Margin:0;width:500px">
                    <table width="100%" cellspacing="0" cellpadding="0"
                        role="presentation"
                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                        <tr>
                            <td style="padding:0;Margin:0;padding-top:5px;padding-bottom:5px;font-size:0"
                                align="center">
                                <table width="100%" height="100%" cellspacing="0"
                                    cellpadding="0" border="0" role="presentation"
                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                    <tr>
                                        <td
                                            style="padding:0;Margin:0;border-bottom:0px solid #CCCCCC;background:#FFFFFF none repeat scroll 0% 0%;height:1px;width:100%;margin:0px">
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td align="left" style="padding:0;Margin:0">
                                <p
                                    style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#333333;font-size:14px">
                                    <strong>Descripción:</strong>  {{ $inc->des_incidencia }} {{ $inc->txt_incidencia }}</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <!--[if mso]></td></tr></table><![endif]-->
    </td>
</tr>