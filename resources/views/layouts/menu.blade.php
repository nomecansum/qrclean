


<!--OPTIONAL : ADD YOUR LOGO TO THE NAVIGATION-->
<!--It will only appear on small screen devices.-->
<!--================================
<div class="mainnav-brand">
    <a href="index.html" class="brand">
        <img src="img/logo.png" alt="Nifty Logo" class="brand-icon">
        <span class="brand-text">Nifty</span>
    </a>
    <a href="#" class="mainnav-toggle"><i class="pci-cross pci-circle icon-lg"></i></a>
</div>
-->



<!--Menu-->
<!--================================-->
<div id="mainnav-menu-wrap">
    <div class="nano">
        <div class="nano-content">
                        <!--Profile Widget-->
            <!--================================-->
            <div id="mainnav-profile" class="mainnav-profile">
                <div class="profile-wrap text-center">
                    <div class="pad-btm">
                        @if(Auth::user()->img_usuario!="" && file_exists( public_path().'/img/users/'.Auth::user()->img_usuario))
                        <img class="img-circle img-md" src="{{Storage::disk(config('app.img_disk'))->url('img/users/'.Auth::user()->img_usuario)}}" alt="Profile Picture">
                        @else
                        {!! icono_nombre(Auth::user()->name) !!}
                        @endif
                    </div>
                    <a href="#profile-nav" class="box-block" data-toggle="collapse" aria-expanded="false">
                        <span class="pull-right dropdown-toggle">
                            <i class="dropdown-caret"></i>
                        </span>
                        <p class="mnp-name">{{ Auth::user()->name }}</p>
                        <span class="mnp-desc">{{Auth::user()->email}}</span>
                    </a>
                </div>
                <div id="profile-nav" class="collapse list-group bg-trans">
                    <a href="{{ url('/users/'.Auth::user()->id.'/edit') }}" class="list-group-item"><i class="fad fa-user"></i> Mi Perfil</a>
                    {{-- @if(checkPermissions(['Configuracion'],['R']))<a href="{{ url('/config') }}" class="list-group-item"><i class="fad fa-cogs"></i> Configuración @endif --}}
                    <a href="{{url('/logout')}}" class="list-group-item"><i class="fad fa-sign-out-alt"></i> Logout</a>
                    
                    {{-- <a href="#" class="list-group-item">
                        <a href="{{ url('/users/'.Auth::user()->id.'/edit') }}"><i class="fad fa-user"></i> Mi Perfil</a>
                    </a>
                    
                        
                    </a>
                    <a href="{{url('/logout')}}" class="list-group-item">
                        
                    </a> --}}
                </div>
            </div>


            <!--Shortcut buttons-->
            <!--================================-->
            <div id="mainnav-shortcut" class="hidden">
                <ul class="list-unstyled shortcut-wrap">
                    <li class="col-xs-3" data-content="My Profile">
                        <a class="shortcut-grid" href="#">
                            <div class="icon-wrap icon-wrap-sm icon-circle bg-mint">
                            <i class="demo-pli-male"></i>
                            </div>
                        </a>
                    </li>
                        <li class="col-xs-3" data-content="Messages">
                            <a class="shortcut-grid" href="#">
                                <div class="icon-wrap icon-wrap-sm icon-circle bg-warning">
                                <i class="demo-pli-speech-bubble-3"></i>
                                </div>
                            </a>
                        </li>
                        <li class="col-xs-3" data-content="Activity">
                            <a class="shortcut-grid" href="#">
                                <div class="icon-wrap icon-wrap-sm icon-circle bg-success">
                                <i class="demo-pli-thunder"></i>
                                </div>
                            </a>
                        </li>
                        <li class="col-xs-3" data-content="Lock Screen">
                            <a class="shortcut-grid" href="#">
                                <div class="icon-wrap icon-wrap-sm icon-circle bg-purple">
                                <i class="demo-pli-lock-2"></i>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
                <!--================================-->
                <!--End shortcut buttons-->
               

                <ul id="mainnav-menu" class="list-group">

                    <!--Category name-->
                    <li class="list-header">Navegación</li>

                    <!--Menu list item-->
                    {{-- <li class="active-sub">


                        <!--Submenu-->
                        <ul class="collapse in">
                            <li class="active-link"><a href="index.html">Dashboard 1</a></li>
                            <li><a href="dashboard-2.html">Dashboard 2</a></li>
                            <li><a href="dashboard-3.html">Dashboard 3</a></li>

                        </ul>
                    </li> --}}

                    <!--Menu list item-->
                    <a href="{{ url('/') }}">
                        <i class="fa fa-home"></i>
                        <span class="menu-title">Dashboard</span>
                        <i class="arrow"></i>
                    </a>
                    {{-- <a href="{{ url('/puestos') }}">
                        <i class="fad fa-browser"></i>
                        <span class="menu-title">Puestos</span>
                        <i class="arrow"></i>
                    </a> --}}
                    @if(checkPermissions(['Scan acceso'],['R']))<li class="main_scan"><a href="/scan_usuario" class="text-nowrap"><i class="fad fa-qrcode"></i> <span class="menu-title">Scan</span></a></li> @endif
                    @if(checkPermissions(['Reservas'],['R']))<li class="reservas"><a href="/reservas" class="text-nowrap"><i class="fad fa-calendar-alt"></i></i> <span class="menu-title">Reservar</span></a></li> @endif
                    @if(checkPermissions(['Parametrizacion'],['R']))
                    <li class="parametrizacion">
                        <a href="#">
                            <i class="fad fa-browser"></i>
                            @if(checkPermissions(['Parametrizacion'],['R']))<span class="menu-title">Parametrizacion</span> @endif
                            <i class="arrow"></i>
                        </a>
                        
                        <!--Submenu-->
                        <ul class="collapse">
                            @if(checkPermissions(['Edificios'],['R']))<li class="edificios"><a href="/edificios"><i class="fad fa-building"></i> Edificios</a></li> @endif
                            @if(checkPermissions(['Plantas'],['R']))<li class="plantas"><a href="/plantas"> <i class="fad fa-layer-group"></i> Plantas</a></li> @endif
                            @if(checkPermissions(['Puestos'],['R']))<li class="puestos"><a href="/puestos"> <i class="fad fa-desktop-alt"></i> Puestos</a></li> @endif
                            @if(checkPermissions(['Puestos'],['R']))<li class="mapa"><a href="/puestos/mapa"><i class="fad fa-th"></i> Mapa</a></li> @endif
                            @if(checkPermissions(['Encuestas'],['R']))<li class="encuestas"><a href="/encuestas"><i class="fad fa-poll-h"></i> Encuestas</a></li> @endif
                            
                            @if(checkPermissions(['Importar datos'],['W']))<li class="importar"> <a href="/import"><i class="fad fa-upload"></i> Importar datos</a></li> @endif
                        </ul>
                    </li>
                    @endif

                    @if(checkPermissions(['Limpieza'],['R']) && session('CL')['mca_limpieza']=='S')
                    <li class="limpieza">
                        <a href="#">
                            <i class="fad fa-broom"></i>
                            @if(checkPermissions(['Parametrizacion'],['R']))<span class="menu-title">Limpieza</span> @endif
                            <i class="arrow"></i>
                        </a>

                        <!--Submenu-->
                        <ul class="collapse">
                            @if(checkPermissions(['Rondas de limpieza'],['R']))<li class="rondas text-nowrap"><a href="/rondas/index/L/" class="text-nowrap"><i class="fad fa-broom"></i> Rondas limpieza</a></li> @endif
                            @if(checkPermissions(['Scan limpieza'],['R']))<li class="scan_ronda"><a href="/rondas/scan" class="text-nowrap"><i class="fad fa-qrcode"></i> Scan</a></li> @endif
                        </ul>
                    </li>
                    @endif

                    @if(checkPermissions(['Mantenimiento'],['R']))
                    <li class="mantenimiento">
                        <a href="#">
                            <i class="fad fa-tools"></i>
                            @if(checkPermissions(['Mantenimiento'],['R']))<span class="menu-title">Mantenimiento</span> @endif
                            <i class="arrow"></i>
                        </a>
                        <!--Submenu-->
                        <ul class="collapse">
                            @if(checkPermissions(['Rondas de mantenimiento'],['R']))<li class="rondas_mant text-nowrap"><a href="/rondas/index/M/" class="text-nowrap"><i class="fad fa-tools"></i> Rondas <br>mantenimiento</a></li> @endif
                            @if(checkPermissions(['Incidencias'],['R']))<li class="incidencias"><a href="/incidencias/" class="text-nowrap"><i class="fad fa-exclamation-triangle"></i> Incidencias</a></li> @endif
                            @if(checkPermissions(['Scan mantenimiento'],['R']))<li class="scan_mant"><a href="/scan_mantenimiento" class="text-nowrap"><i class="fad fa-qrcode"></i> Scan</a></li> @endif
                        </ul>
                    </li>
                    @endif

                    @if(checkPermissions(['Configuracion'],['R']))
                    <li class="configuracion">
                        <a href="#">
                            <i class="fa fa-cog"></i>
                                <span class="menu-title">Configuracion</span> 
                            <i class="arrow"></i>
                        </a>
                        <!--Submenu-->
                        <ul class="collapse">
                            @if(checkPermissions(['Bitacora'],['R']))<li class="bitacora"><a href="/bitacoras"><i class="fad fa-clipboard-list"></i> Bitacora</a></li> @endif
                            @if(checkPermissions(['Clientes'],['R']))<li class="clientes"><a href="/clientes"><i class="fad fa-user-tie"></i> Clientes</a></li> @endif
                            @if(checkPermissions(['Usuarios'],['R']))<li class="usuarios"><a href="/users"><i class="fad fa-user"></i> Usuarios</a></li> @endif
                            @if(checkPermissions(['Perfiles'],['R']))<li class="perfiles"><a href="/profiles"><i class="fad fa-users"></i> Perfiles</a></li> @endif
                            @if(checkPermissions(['Secciones'],['R']))<li class="secciones"><a href="/sections"> <i class="fad fa-browser"></i> Secciones</a></li> @endif
                            @if(checkPermissions(['Permisos'],['R']))<li class="permisos"><a href="/profile-permissions"><i class="fad fa-lock-alt"></i> Permisos</a></li> @endif
                            @if(checkPermissions(['Tipos de incidencia'],['R']))
                            <li class="tipos_incidencia">
                                <a href="#">
                                    <i class="fad fa-exclamation-triangle"></i>
                                    <span class="menu-title">  Incidencias</span>
                                    <i class="arrow"></i>
                                </a>
                                <ul class="collapse">
                                    @if(checkPermissions(['Tipos de incidencia'],['R']))<li class="incidencias_tipos text-nowrap"><a href="/incidencias/tipos"> <i class="fad fa-exclamation-triangle"></i> Tipos de incidencia</a></li> @endif
                                    @if(checkPermissions(['Causas de cierre'],['R']))<li class="incidencias_causas"><a href="/incidencias/causas"> <i class="fad fa-times-hexagon"></i> Causas de cierre</a></li> @endif
                                    @if(checkPermissions(['Estados de incidencia'],['R']))<li class="incidencias_estados"><a href="/incidencias/estados"> <i class="fad fa-sign"></i> Estados</a></li> @endif
                                </ul>
                            </li>
                            @endif
                           
                            @if(checkPermissions(['Tareas programadas'],['R']))<li class="tareas_programadas"><a href="/tasks" class="text-nowrap"> <i class="mdi mdi-camera-timer"></i>Tareas programadas</a></li> @endif
                        </ul>
                    </li>
                    @endif
                </ul>

                @if(session('DIS') && isset(session('DIS')['img_logo']))
                    <div class="text-center">
                        <img src="{{ url('/img/distribuidores/'.session('DIS')['img_logo']) }}" title="{{ session('DIS')['nom_distribuidor'] }}" style="width:50%">
                    </div> 
                @endif


                <!--Widget-->
                <!--================================-->
                {{-- <div class="mainnav-widget">
                    <!-- Show the button on collapsed navigation -->
                    <div class="show-small">
                        <a href="#" data-toggle="menu-widget" data-target="#demo-wg-server">
                            <i class="demo-pli-monitor-2"></i>
                        </a>
                    </div>
                </div> --}}
                <!--================================-->
                <!--End widget-->
            </div>
        </div>
</div>

