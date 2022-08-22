
<div id="mainnav-menu-wrap">
    
    

    
    <div class="nano">
        <div class="nano-content">
                        <!--Profile Widget-->
            <!--================================--

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
                    
                    {{-- @if(checkPermissions(['Reservas'],['R']))<li class="reservas"><a href="/reservas" class="text-nowrap"><i class="fad fa-calendar-alt"></i></i> <span class="menu-title">Reservar</span></a></li> @endif --}}

                    <!-- Link with submenu -->
                    
                    <!-- END : Link with submenu -->

                    

                    
                    

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
                           
                            
                            
                            
                           
                            @if(checkPermissions(['Parametrizacion'],['R']))
                                <li class="menu_parametrizacion">
                                    <a href="#">
                                        <i class="fad fa-browser"></i>
                                        <span class="menu-title">  Parametrizacion</span>
                                        <i class="arrow"></i>
                                    </a>
                                    <ul class="collapse">
                                        <li class="espacios">
                                            <a href="#">
                                                <i class="fa-duotone fa-city"></i>
                                                <span class="menu-title">  Espacios</span>
                                                <i class="arrow"></i>
                                            </a>
                                            <ul class="collapse">
                                                @if(checkPermissions(['Clientes'],['R']))<li class="clientes text-nowrap ml-2"><a href="/clientes"><i class="fad fa-user-tie"></i> Clientes</a></li> @endif
                                                @if(checkPermissions(['Edificios'],['R']))<li class="edificios text-nowrap ml-2"><a href="/edificios"><i class="fad fa-building"></i> Edificios</a></li> @endif
                                                @if(checkPermissions(['Plantas'],['R']))<li class="plantas text-nowrap ml-2"><a href="/plantas"> <i class="fad fa-layer-group"></i> Plantas</a></li> @endif
                                                @if(checkPermissions(['Puestos'],['R']))<li class="puestos text-nowrap ml-2"><a href="/puestos"> <i class="fad fa-desktop-alt"></i> Puestos</a></li> @endif
                                                @if(checkPermissions(['Salas'],['R']) && session('CL')['mca_salas']=='S')<li class="salas text-nowrap ml-2"><a href="/salas" class="text-nowrap"><i class="fad fa-users-class"></i> Salas reunion</a></li> @endif
                                                @if(checkPermissions(['Tipos de puesto'],['R']))<li class="puestostipos  text-nowrap ml-2"><a href="/puestos/tipos"> <i class="fal fa-desktop-alt"></i> Tipos de puesto</a></li> @endif
                                                @if(checkPermissions(['Tags'],['R']))<li class="tags  text-nowrap ml-2"><a href="/tags"> <i class="fad fa-tags"></i> Tags</a></li> @endif
                                                @if(checkPermissions(['Encuestas'],['R']))<li class="encuestas text-nowrap ml-2"><a href="/encuestas"><i class="fad fa-poll-h"></i> Encuestas</a></li> @endif
                                            </ul>
                                        </li>
                                        @if(checkPermissions(['Usuarios'],['R']))
                                        <li class="menu_usuarios">
                                            <a href="#">
                                                <i class="fad fa-user"></i>
                                                <span class="menu-title">  Personas</span>
                                                <i class="arrow"></i>
                                            </a>
                                            <ul class="collapse">
                                                @if(checkPermissions(['Usuarios'],['R'])) <li class="usuarios text-nowrap ml-2"><a href="/users"><i class="fad fa-user"></i>Usuarios</a></li>  @endif
                                                @if(checkPermissions(['Plantas usuarios'],['R']))<li class="plantas_usuarios text-nowrap ml-2"><a href="/users/plantas_usuarios"> <i class="fas fa-layer-plus"></i> Asignar plantas</a></li> @endif
                                                @if(checkPermissions(['Puestos supervisores'],['R']))<li class="puestos_supervisores text-nowrap ml-2"><a href="/users/puestos_supervisores"> <i class="fa-duotone fa-magnifying-glass-location"></i> Supervision puestos</a></li> @endif
                                                @if(checkPermissions(['Departamentos'],['R']))<li class="departamentos text-nowrap ml-2"><a href="/departments"><i class="fa-solid fa-sitemap"></i> Departamentos</a></li> @endif
                                                @if(checkPermissions(['Colectivos'],['R']))<li class="colectivos text-nowrap ml-2"><a href="/collective"><i class="fa-solid fa-user-tag"></i> Colectivos</a></li> @endif
                                                @if(checkPermissions(['Turnos'],['R']))<li class="turnos text-nowrap ml-2"><a href="/turnos"><i class="fa-solid fa-repeat-1"></i> Turnos</a></li> @endif
                                                @if(checkPermissions(['Festivos'],['R']))<li class="festivos text-nowrap ml-2"><a href="/festives"><i class="fa-solid fa-calendar-range"></i> Festivos</a></li> @endif
                                            </ul>
                                        </li>
                                        @endif
                                        @if(checkPermissions(['Tipos de incidencia'],['R']))
                                        <li class="tipos_incidencia">
                                            <a href="#">
                                                <i class="fad fa-exclamation-triangle"></i>
                                                <span class="menu-title">  Incidencias</span>
                                                <i class="arrow"></i>
                                            </a>
                                            <ul class="collapse">
                                                @if(checkPermissions(['Tipos de incidencia'],['R']))<li class="incidencias_tipos text-nowrap ml-2"><a href="/incidencias/tipos"> <i class="fad fa-exclamation-triangle"></i> Tipos de incidencia</a></li> @endif
                                                @if(checkPermissions(['Causas de cierre'],['R']))<li class="incidencias_causas text-nowrap ml-2"><a href="/incidencias/causas"> <i class="fad fa-times-hexagon"></i> Causas de cierre</a></li> @endif
                                                @if(checkPermissions(['Estados de incidencia'],['R']))<li class="incidencias_estados text-nowrap ml-2"><a href="/incidencias/estados"> <i class="fad fa-sign"></i> Estados</a></li> @endif
                                            </ul>
                                        </li>
                                        @endif
                                    </ul>
                                </li>
                            @endif


                            @if(checkPermissions(['Permisos'],['R']))
                                <li class="menu_permisos">
                                    <a href="#">
                                        <i class="fad fa-users"></i>
                                        <span class="menu-title">  Permisos</span>
                                        <i class="arrow"></i>
                                    </a>
                                    <ul class="collapse">
                                        @if(checkPermissions(['Perfiles'],['R']))<li class="perfiles"><a href="/profiles"><i class="fad fa-users"></i> Perfiles</a></li> @endif
                                        @if(checkPermissions(['Secciones'],['R']))<li class="secciones"><a href="/sections"> <i class="fad fa-browser"></i> Secciones</a></li> @endif
                                        @if(checkPermissions(['Permisos'],['R']))<li class="permisos"><a href="/profile-permissions"><i class="fad fa-lock-alt"></i> Permisos</a></li> @endif
                                    </ul>
                                </li>
                            @endif
                            <li class="menu_utilidades">
                                <a href="#">
                                    <i class="fa-solid fa-screwdriver-wrench"></i>
                                    <span class="menu-title">  Utilidades</span>
                                    <i class="arrow"></i>
                                </a>
                                <ul class="collapse">
                                    @if(checkPermissions(['Tareas programadas'],['R']))<li class="tareas_programadas"><a href="/tasks" class="text-nowrap"> <i class="mdi mdi-camera-timer"></i>Tareas programadas</a></li> @endif
                                    @if (checkPermissions(['Eventos'],["R"]))<li class="eventos"><a href="{{url('/events')}}"><i class="mdi mdi-engine"></i> Eventos</a></li>@endif
                                    @if(checkPermissions(['Señaletica'],['R']))<li class="mkd"><a href="/MKD"><i class="fad fa-sign"></i> Señaletica</a></li> @endif
                        
                                    @if(checkPermissions(['Importar datos'],['W']))<li class="importar"> <a href="/import"><i class="fad fa-upload"></i> Importar datos</a></li> @endif
                                </ul>
                            </li>
                            
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

