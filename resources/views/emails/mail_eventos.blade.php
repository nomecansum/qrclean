@extends('layout_email')
@php
use App\Models\incidencias;
use App\Models\incidencias_tipos;
use App\Models\users;


@endphp


@section('logo_cliente')
<img align="left" border="0" src="{{ Storage::disk(config('app.img_disk'))->url('img/clientes/images/'.session('logo_cliente')) }}" alt="Illustration" title="Illustration" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: inline-block !important;border: none;height: auto;float: none;width: 25%;max-width: 160px;" width="160" class="v-src-width v-src-max-width"/>
@endsection

@section('saludo')
    Hola 
@endsection

@section('cuerpo')
    <p style="font-size: 14px; line-height: 160%;"> </p>
    <p style="font-size: 14px; line-height: 160%;">BLA BLA BLA BLA BLA BLA BLA BLA BLA BLA BLA BLA</strong>:</span></p></p>
    <br>
    <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#333333;font-size:18px"><strong>Puesto:</strong> BLA BLA BLA BLA BLA BLA BLA BLA BLA BLA BLA BLA</p>
    <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#333333;font-size:14px"><strong>Fecha:</strong> BLA BLA BLA BLA BLA BLA BLA BLA BLA BLA BLA BLA</p>
    <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#333333;font-size:14px"><strong>Descripción:</strong>  BLA BLA BLA BLA BLA BLA BLA BLA BLA BLA BLA BLA</p>
    <p style="font-size: 14px; line-height: 160%;"><br />Le enviamos esta notificacion como comprobante de la apertura de dicha incidencia </p>
@endsection

@section('enlace')
    @if(isset($inc->url_detalle_incidencia))
        <div class="v-text-align" align="left">
              <a href="#" target="_blank" class="v-size-width" style="box-sizing: border-box;display: inline-block;font-family:'Rubik',sans-serif;text-decoration: none;-webkit-text-size-adjust: none;text-align: center;color: #ffffff; background-color: #805997; border-radius: 4px;-webkit-border-radius: 4px; -moz-border-radius: 4px; width:39%; max-width:100%; overflow-wrap: break-word; word-break: break-word; word-wrap:break-word; mso-border-alt: none;">
                <span style="display:block;padding:10px 20px;line-height:120%;"><strong><span style="font-size: 14px; line-height: 16.8px;">Ver detalle</span></strong></span>
              </a>
        </div>
    @endif
@endsection