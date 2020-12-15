@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Acceso a puesto</h1>
@endsection

@section('styles')
    <!--Bootstrap FLEX Stylesheet [ REQUIRED ]-->
    <link href="{{ url('/css/bootstrap-grid.min.css') }}" rel="stylesheet">
    <style type="text/css">
        .container {
            border: 1px solid #DDDDDD;
            width: 100%;
            position: relative;
            padding: 0px;
        }
        .flpuesto {
            float: left;
            position: absolute;
            z-index: 1000;
            color: #FFFFFF;
            font-size: 9px;
            width: 40px;
            height: 40px;
            overflow: hidden;
        }
        
    </style>
@endsection

@section('breadcrumb')

@endsection

@section('content')
@php
    $puesto=$respuesta['puesto']??null;
    $cookie=Cookie::get('encuesta');
@endphp
    
    <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-4 text-center">
            @if(isset($puesto))
            <img src="{{ !empty($puesto->img_logo) && file_exists(public_path().'/img/clientes/images/'.$puesto->img_logo) ? url('/img/clientes/images/'.$puesto->img_logo) : url('/img/logo.png') }}" style="width: 120px"><br>
            <h2>{{ $puesto->nom_cliente }}</h2>
            @endif
        </div>
        <div class="col-md-4"></div>
    </div>
    <div class="row">
        <div class="col-md-12 text-center">
            @if(isset($puesto))
            <div class="pad-all text-center font-bold" style="color: {{ $puesto->val_color }}; font-size: 22px">
                <i class="{{ $puesto->val_icono }}"></i>[{{ $puesto->cod_puesto }}] - {{ $puesto->des_puesto }}
            </div>
            @endif
        </div>
    </div>
    <div class="row" id="div_respuesta">
        <div class="col-md-3"></div>
        <div class="col-md-6 text-3x text-center bg-{{$respuesta['color']}} rounded">
            {!!$respuesta['icono']!!} {!!$respuesta['mensaje']!!}
        </div>
        <div class="col-md-3"></div>
    </div>
    @if($respuesta['encuesta']!=0 && (!isset($cookie) || (isset($cookie) && $cookie!=$respuesta['encuesta'])))
        @php
            $encuesta=DB::table('encuestas')->where('id_encuesta',$respuesta['encuesta'])->first();  
        @endphp
        <div class="row" id="div_encuesta"  @if($encuesta->val_momento=='D') style="display: none" @endif>
            <div class="col-md-6 col-xs-offset-3" id="pregunta">
                <h4>{{ $encuesta->pregunta }}</h4>
            </div>
            <div class="col-md-12 text-center" id="selector">
                @include('encuestas.selector',['tipo'=>$encuesta->id_tipo_encuesta])
            </div>
            <div class="col-md-12 text-center"  id="respuesta" style="display: none">
                <h4><i class="fad fa-thumbs-up fa-2x text-success"></i> ¡Muchas gracias por su colaboracion!</h4>
            </div>
        </div>
        
    @endif
    <div class="row" id="div_mensaje_fin" style="display:none">
        <div class="col-md-3"></div>
        <div class="col-md-6 text-3x text-center rounded" id="div_txt_mensaje">
            
        </div>
        <div class="col-md-3"></div>
    </div>
    @if(isset($puesto))
        <div id="div_botones">
            @if(!$reserva)
                @if($puesto->id_estado<3)
                    <div class="row mt-5 mb-5">
                        <div class="col-md-12 pt-3 pb-3 text-2x text-center">
                            ¿Que quiere hacer?
                        </div>
                    </div>
                @endif
                <div class="row">
                    <div class="col-md-12 text-center">
                        @if($respuesta['operativo']==1)
                            @switch($puesto->id_estado)
                                @case(1)
                                        <button class="btn btn-lg btn-success text-bold btn_estado" data-estado="2" data-id="{{$puesto->token}}">Voy a utilizar este puesto</button>
                                    @break
                                @case(2)
                                        @if((Auth::check() && $puesto->id_usuario_usando==Auth::user()->id) || (!Auth::check()&&$puesto->id_usuario_usando==null))
                                            @if($config_cliente->mca_limpieza=='S')
                                                <button class="btn btn-lg btn-purple btn_estado" data-estado="3"  data-id="{{$puesto->token}}">Voy a dejar este puesto</button>
                                            @else
                                                <button class="btn btn-lg btn-purple btn_estado" data-estado="1"  data-id="{{$puesto->token}}">Voy a dejar este puesto</button>
                                            @endif
                                        @endif
                                    @break
                                @default
                            @endswitch
                        @endif
                        @if(isset($respuesta['hacer_login']) && $puesto->mca_acceso_anonimo=='N')
                            <button class="btn btn-lg btn-primary text-bold btn_login" data-id="{{$puesto->token}}"><i class="fad fa-user"></i> Iniciar sesion</button>
                        @endif
                    </div>
                </div>
                @if($puesto->id_estado!=6)
                    <div class="row mt-3">
                        <div class="col-md-12 text-center">
                            <button class="btn btn-lg btn-warning text-bold btn_incidencia" data-estado="6" data-id="{{$puesto->token}}"><i class="fad fa-exclamation-triangle"></i> Notificar una incidencia <br>en este puesto</button>
                        </div>
                    </div>
                @endif
            @endif
            @if((isset($respuesta['disponibles']) && Auth::check() && Auth::user()->id!=$puesto->id_usuario_usando) || $reserva)
                <div class="row">
                    <div class="col-md-12 font-18 text-center mt-5">
                        En esta misma planta tiene los siguientes puestos disponibles:
                    </div>
                </div>
                <div class="row">
                    @foreach($respuesta['disponibles'] as $disp)
                        <div class="col-md-4 pad-all font-18 text-center font-bold" style="color: {{ $disp->val_color }}">
                            <i class="{{ $disp->val_icono }}"></i>[{{ $disp->cod_puesto }}] - {{ $disp->des_puesto }}
                        </div>
                    @endforeach
                   
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-center" id="plano" style="padding-left: 8vw">
                        @if(isset($respuesta['disponibles']) && Auth::check() && Auth::user()->id!=$puesto->id_usuario_usando)
                            @php
                                $pl=App\Models\plantas::find($puesto->id_planta);  
                                $reservas=DB::table('reservas')
                                    ->join('puestos','puestos.id_puesto','reservas.id_puesto')
                                    ->join('users','reservas.id_usuario','users.id')
                                    ->where('fec_reserva',Carbon\Carbon::now()->format('Y-m-d'))
                                    ->where('reservas.id_cliente',Auth::user()->id_cliente)
                                    ->get();

                                $asignados_usuarios=DB::table('puestos_asignados')
                                    ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
                                    ->join('users','users.id','puestos_asignados.id_usuario')    
                                    ->where('id_usuario','<>',Auth::user()->id)
                                    ->get();

                                $asignados_miperfil=DB::table('puestos_asignados')
                                    ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
                                    ->join('niveles_acceso','niveles_acceso.cod_nivel','puestos_asignados.id_perfil')    
                                    ->where('id_perfil',Auth::user()->cod_nivel)
                                    ->get();
                                
                                $asignados_nomiperfil=DB::table('puestos_asignados')
                                    ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
                                    ->join('niveles_acceso','niveles_acceso.cod_nivel','puestos_asignados.id_perfil')     
                                    ->where('id_perfil','<>',Auth::user()->cod_nivel)
                                    ->get();

                            @endphp
                            @include('puestos.fill-plano')
                        @endif
                    </div>
                </div>
            @endif
            @if(Auth::check() && $puesto->id_estado!=1)
                <div class="row">
                    <div class="col-md-12 text-center mt-3">
                        <a class="btn btn-lg btn-primary text-2x rounded btn_otravez" href="{{ url('/scan_usuario/') }} "><i class="fad fa-qrcode fa-3x"></i> Escanear otra vez</a>
                    </div>
                </div>
            @endif
        </div>
    @endif

@endsection


@section('scripts')
    <script>
        $('.btn_estado').click(function(){
            $.get("{{url('/puesto/estado/')}}/"+$(this).data('id')+"/"+$(this).data('estado'), function(data){
                $('#div_botones').hide();
                $('#div_respuesta').hide();
                $('#div_mensaje_fin').show();

                animateCSS('#div_mensaje_fin','bounceInright');
                if(data.tipo=='OK'){
                    $('#div_txt_mensaje').addClass('bg-info');
                    $('#div_txt_mensaje').removeClass('bg-danger');
                    $('#div_txt_mensaje').html('<i class="fad fa-check-circle"></i> '+data.mensaje);
                    @if(isset($encuesta->val_momento) && $encuesta->val_momento=='D')
                        $('#div_encuesta').show();
                    @endif
                } else {
                    $('#div_txt_mensaje').removeClass('bg-info');
                    $('#div_txt_mensaje').addClass('bg-danger');
                    $('#div_txt_mensaje').html('<i class="fad fa-exclamation-square"></i> '+data.mensaje);
                }
                console.log(data);
            })
        })

        $('.btn_incidencia').click(function(){
            window.location.replace("{{url('/incidencias/create')}}/"+$(this).data('id'));
        })

        $('.btn_login').click(function(){
            window.location.replace("{{url('/login')}}");
        })

        $(function(){
            $('#footer').hide();
        })

        @if(isset($respuesta['disponibles']) && Auth::user())
            function recolocar_puestos(posiciones){
                $('.container').each(function(){
                    plano=$(this);
                    //console.log(plano.data('posiciones'));
                    
                    $.each(plano.data('posiciones'), function(i, item) {//console.log(item);
                        puesto=$('#puesto'+item.id);
                        puesto.css('top',plano.height()*item.offsettop/100);
                        puesto.css('left',plano.width()*item.offsetleft/100);
                    });

                }) 
            }

            $(window).resize(function(){
                recolocar_puestos();
            })

            $('.mainnav-toggle').click(function(){
                recolocar_puestos();
            })
        @endif

        @if($respuesta['encuesta']!=0 && (!isset($cookie) || (isset($cookie) && $cookie!=$respuesta['encuesta'])))
            //Scripts para manejar la encuesta
            id_encuesta='{{ $encuesta->token }}';
            mca_anonima='{{ $encuesta->mca_anonima }}';
            $('.valor').click(function(){
                $(this).css('background-color','#7fff00')
                console.log($(this).data('value'));
                $.post('{{url('/encuestas/save_data')}}', {_token:'{{csrf_token()}}',val: $(this).data('value'), id_encuesta: id_encuesta, mca_anonima: mca_anonima}, function(data, textStatus, xhr) {
                    console.log(data);
                    $('#selector').hide();
                    $('#pregunta').hide();
                    $('#respuesta').show();
                    animateCSS('#respuesta','bounceInRight');
                });
            })
            $('.valor').css('cursor', 'pointer');
        @endif
    </script>
@endsection
