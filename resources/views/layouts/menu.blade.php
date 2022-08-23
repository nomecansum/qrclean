<nav id="mainnav-container" class="mainnav">
    <div class="mainnav__inner">

        <!-- Navigation menu -->
        <div class="mainnav__top-content scrollable-content pb-5">

            <!-- Profile Widget -->
            <div class="mainnav__profile mt-3">

                <!-- Profile picture  -->
                <div class="mininav-toggle text-center py-2">
                    @if (isset(Auth::user()->img_usuario ) && Auth::user()->img_usuario!='')
                        <img src="{{ Storage::disk(config('app.img_disk'))->url('img/users/'.Auth::user()->img_usuario) }}" id="main_user_image" class="img-md rounded-circle">
                    @else
                        {!! icono_nombre(Auth::user()->name,40,16) !!}
                    @endif
                </div>
        
                <div class="mininav-content collapse d-mn-max">
                    <div class="d-grid">
        
                        <!-- User name and position -->
                        <button class="d-block btn shadow-none p-2" data-bs-toggle="collapse" data-bs-target="#usernav" aria-expanded="false" aria-controls="usernav">
                            <span class="dropdown-toggle d-flex justify-content-center align-items-center">
                                <h6 class="mb-0 me-2">{{ Auth::user()->name }}</h6>
                            </span>
                            <small class="text-muted">{{Auth::user()->email}}</small>
                        </button>
        
                        <!-- Collapsed user menu -->
                        <div id="usernav" class="nav flex-column collapse">
                            <a href="#" class="nav-link d-flex justify-content-between align-items-center">
                                <span><i class="fa-light fa-bell fs-5 me-3"></i> Notificaciones</span>
                                <span class="badge bg-danger rounded-pill cuenta_notificaciones">0</span>
                            </a>
                            <a href="{{ url('/miperfil/'.Auth::user()->id) }}" class="nav-link">
                                <i class="demo-pli-male fs-5 me-3"></i> Perfil
                            </a>
                            <a href="{{ url('/user_settings/'.Auth::user()->id) }}" class="nav-link " data-bs-toggle="offcanvas" data-bs-target="#_dm-settingsContainer" aria-controls="_dm-settingsContainer">
                                <i class="demo-pli-gear fs-5 me-3"></i> Ajustes
                            </a>
                            <a href="{{ url('/lockscreen') }}" class="nav-link">
                                <i class="demo-pli-computer-secure fs-5 me-3"></i> Bloquear pantalla
                            </a>
                            <a href="{{url('/logout')}}" class="nav-link">
                                <i class="demo-pli-unlock fs-5 me-3"></i> Logout
                            </a>
                        </div>
        
                    </div>
                </div>
            </div>
            <!-- End - Profile widget -->

            <h6 class="mainnav__caption mt-5 px-3 fw-bold">Mi espacio</h6>
            <ul class="mainnav__menu nav flex-column">

                <li class="nav-item">
                    <a href="{{ url('/') }}" class="nav-link mininav-toggle collapsed"><i class="fa-light fa-house fs-5 me-2"></i>
                        <span class="nav-label mininav-content">Home</span>
                    </a>
                </li>
                @if(checkPermissions(['Scan acceso'],['R']))
                <li class="nav-item main_scan">
                    <a href="/scan_usuario" class="nav-link mininav-toggle collapsed"><i class="fa-light fa-qrcode fs-5 me-2"></i>
                        <span class="nav-label mininav-content"> Scan</span>
                    </a>
                </li>
                @endif
                @if(checkPermissions(['Reservas'],['R']))
                <li class="nav-item has-sub reservas">
                    <a href="#" class="mininav-toggle nav-link "><i class="fa-light fa-calendar-day fs-5 me-2"></i>{{-- active --}}
                        <span class="nav-label">Reservas</span>
                    </a>
                    <!-- Dashboard submenu list -->
                    <ul class="mininav-content nav collapse">
                        @if(checkPermissions(['Reservas puestos'],['R']))<li class="reservas_puestos nav-item"><a href="/reservas" class="text-nowrap nav-link"><i class="fad fa-chair-office"></i> Puestos</a></li> @endif
                        @if(checkPermissions(['Reservas salas'],['R']) && session('CL')['mca_salas']=='S')<li class="reservas_salas nav-item"><a href="/salas/reservas" class="text-nowrap nav-link"><i class="fad fa-users-class"></i> Salas</a></li> @endif
                    </ul>
                    <!-- END : Dashboard submenu list -->
                </li>
                @endif

                @if(checkPermissions(['Mi oficina'],['R']))
                <li class="nav-item has-sub parametrizacion">
                    <a href="#" class="mininav-toggle nav-link">
                        <i class="fa-light fa-browser fs-5 me-2"></i>
                        @if(checkPermissions(['Parametrizacion'],['R']))<span class="nav-label">Mi oficina</span> @endif
                    </a>
                    
                    <!--Submenu-->
                    <ul class="mininav-content nav collapse">
                        @if(checkPermissions(['Mapa puestos'],['R']))<li class="mapa  nav-item"><a href="/puestos/mapa"  class="text-nowrap nav-link"><i class="fad fa-th"></i> Mapa</a></li> @endif
                        @if(checkPermissions(['Compañeros'],['R']))<li class="compas  nav-item"><a href="/puestos/compas"  class="text-nowrap nav-link"><i class="fa-duotone fa-users"></i> Mis compañeros</a></li> @endif
                        @if(checkPermissions(['Salas'],['R']) && session('CL')['mca_salas']=='S')<li class="salas text-nowrap nav-item"><a href="/salas" class="text-nowrap nav-link"><i class="fa-light fa-users-class"></i> Salas reunion</a></li> @endif
                    </ul>
                </li>
                @endif
            </ul>

            <h6 class="mainnav__caption mt-5 px-3 fw-bold">Servicios</h6>
            <ul class="mainnav__menu nav flex-column">

                @if(checkPermissions(['Limpieza'],['R']) && session('CL')['mca_limpieza']=='S')
                <li class="nav-item has-sub limpieza">
                    <a href="#" class="mininav-toggle nav-link "><i class="fa-light fa-broom fs-5 me-2"></i>{{-- active --}}
                        <span class="nav-label">Limpieza</span>
                    </a>
                    <!-- Dashboard submenu list -->
                    <ul class="mininav-content nav collapse">
                        @if(checkPermissions(['Rondas de limpieza'],['R']))<li class="rondas text-nowrap  nav-item"><a href="/rondas/index/L" class="text-nowrap nav-link"><i class="fa-light fa-broom"></i> Rondas limpieza</a></li> @endif
                        @if(checkPermissions(['Pendientes limpieza'],['R']))<li class="pendientes text-nowrap  nav-item"><a href="/limpieza/pendientes" class="text-nowrap nav-link"><i class="fa-light fa-broom" style="color: #f00"></i> Pendientes limpieza</a></li> @endif
                        @if(checkPermissions(['Scan limpieza'],['R']))<li class="scan_ronda  nav-item"><a href="/rondas/scan" class="text-nowrap nav-link"><i class="fa-light fa-qrcode"></i> Scan</a></li> @endif
                    </ul>
                    <!-- END : Dashboard submenu list -->
                </li>
                @endif

                @if(checkPermissions(['Mantenimiento'],['R']))
                <li class="nav-item has-sub mantenimiento">
                    <a href="#" class="mininav-toggle nav-link">
                        <i class="fa-light fa-tools fs-5 me-2"></i>
                        @if(checkPermissions(['Parametrizacion'],['R']))<span class="nav-label">Mantenimiento</span> @endif
                    </a>
                    
                    <!--Submenu-->
                    <ul class="mininav-content nav collapse">
                        @if(checkPermissions(['Rondas de mantenimiento'],['R']))<li class="rondas_mant text-nowrap  nav-item"><a href="/rondas/index/M/" class="text-nowrap nav-link"><i class="fa-light fa-tools"></i> Rondas <br>mantenimiento</a></li> @endif
                        @if(checkPermissions(['Incidencias'],['R']))<li class="incidencias  nav-item"><a href="/incidencias/" class="text-nowrap nav-link"><i class="fa-light fa-exclamation-triangle"></i> Incidencias</a></li> @endif
                        @if(checkPermissions(['Scan mantenimiento'],['R']))<li class="scan_mant  nav-item"><a href="/scan_mantenimiento" class="text-nowrap nav-link"><i class="fa-light fa-qrcode"></i> Scan</a></li> @endif
                    </ul>
                </li>
                @endif
            </ul>

            <h6 class="mainnav__caption mt-5 px-3 fw-bold">Informes</h6>
            <ul class="mainnav__menu nav flex-column">

                @if(checkPermissions(['Limpieza'],['R']) && session('CL')['mca_limpieza']=='S')
                <li class="nav-item has-sub informes">
                    <a href="#" class="mininav-toggle nav-link "><i class="fa-light fa-file-chart-pie fs-5 me-2"></i>{{-- active --}}
                        <span class="nav-label">Informes</span>
                    </a>
                    <!-- Dashboard submenu list -->
                    <ul class="mininav-content nav collapse">
                        @if(checkPermissions(['Informes > Uso de puestos'],['R']))<li class="inf_puestos text-nowrap nav-item"><a href="/reports/puestos" class="text-nowrap nav-link"><i class="fa-light fa-file-alt"></i> Uso de puestos</a></li> @endif
                            @if(checkPermissions(['Informes > Puestos por usuario'],['R']))<li class="inf_usuarios nav-item"><a href="/reports/users" class="text-nowrap nav-link"><i class="fa-light fa-file-alt"></i> Puestos por usuario</a></li> @endif
                            @if(checkPermissions(['Informes > Reservas canceladas'],['R']))<li class="inf_reservas nav-item"><a href="/reports/canceladas" class="text-nowrap nav-link"><i class="fa-light fa-file-alt"></i> Reservas canceladas</a></li> @endif
                            @if(checkPermissions(['Informes > Uso de espacio'],['R']))<li class="inf_heatmap nav-item"><a href="/reports/heatmap" class="text-nowrap nav-link"><i class="fa-light fa-file-alt"></i> Uso de espacio</a></li> @endif
                            
                            @if(checkPermissions(['Informes > Ferias'],['R']))<li class="inf_reservas nav-item"><a href="/reports/ferias" class="text-nowrap nav-link"><i class="fa-light fa-file-alt"></i> Ferias</a></li> @endif
                            @if(checkPermissions(['Informes programados'],["R"]))<li class="inf_programados nav-item"><a href="{{url('prog_report')}}" class="text-nowrap nav-link"><i class="fa-light fa-envelope"></i> Informes Programados</a></li>@endif
                    </ul>
                    <!-- END : Dashboard submenu list -->
                </li>
                @endif

                @if(checkPermissions(['Mantenimiento'],['R']))
                <li class="nav-item has-sub ferias">
                    <a href="#" class="mininav-toggle nav-link">
                        <i class="fa-light fa-sensor-on fs-5 me-2"></i>
                        @if(checkPermissions(['Parametrizacion'],['R']))<span class="nav-label">Ferias</span> @endif
                    </a>
                    
                    <!--Submenu-->
                    <ul class="mininav-content nav collapse">
                        @if(checkPermissions(['Ferias'],['R']))<li class="ferias_asistentes nav-item"><a href="/ferias/asistentes" class="text-nowrap nav-link"><i class="fa-light fa-user-tie"></i> Asistentes</a></li> @endif
                        @if(checkPermissions(['Ferias marcas'],['R']))<li class="ferias_marcas nav-item"><a href="/ferias/marcas" class="text-nowrap nav-link"><i class="fa-brands fa-bandcamp"></i> Marcas</a></li> @endif
                        @if(checkPermissions(['Scan ferias'],['R']))<li class="scan_feriat nav-item"><a href="/ferias/actividad" class="text-nowrap nav-link"><i class="fa-light fa-file-chart-column"></i> Actividad</a></li> @endif
                    </ul>
                </li>
                @endif
            </ul>


            @if(checkPermissions(['Configuracion'],['R']))
            <h6 class="mainnav__caption mt-5 px-3 fw-bold">Configuracion</h6>
            <ul class="mainnav__menu nav flex-column">
                <li class="nav-item has-sub">

                    <a href="#" class="mininav-toggle nav-link collapsed"><i class="fa-light fa-cog fs-5 me-2"></i>
                        <span class="nav-label">Parametrizacion</span>
                    </a>

                    <ul class="mininav-content nav collapse">
                        <li class="nav-item"> 
                            @if(checkPermissions(['Bitacora'],['R']))<li class="bitacora nav-item"><a href="/bitacoras" class="nav-link"><i class="fa-light fa-clipboard-list fs-5 me-2"></i> Bitacora</a></li> @endif
                        </li>
                        <li class="nav-item has-sub espacios">
                            <a href="#" class="mininav-toggle nav-link collapsed"><i class="fa-light fa-city fs-5 me-2"></i> Espacios</a>
                            <ul class="mininav-content nav collapse">
                                @if(checkPermissions(['Clientes'],['R']))<li class="clientes text-nowrap ml-2 nav-item"><a href="/clientes" class="nav-link"><i class="fa-light fa-user-tie"></i> Empresas</a></li> @endif
                                @if(checkPermissions(['Edificios'],['R']))<li class="edificios text-nowrap ml-2 nav-item"><a href="/edificios" class="nav-link"><i class="fa-light fa-building"></i> Edificios</a></li> @endif
                                @if(checkPermissions(['Plantas'],['R']))<li class="plantas text-nowrap ml-2 nav-item"><a href="/plantas" class="nav-link"> <i class="fa-light fa-layer-group"></i> Plantas</a></li> @endif
                                @if(checkPermissions(['Puestos'],['R']))<li class="puestos text-nowrap ml-2 nav-item"><a href="/puestos" class="nav-link"> <i class="fa-light fa-desktop-alt"></i> Puestos</a></li> @endif
                                
                                @if(checkPermissions(['Tipos de puesto'],['R']))<li class="puestostipos  text-nowrap ml-2 nav-item" ><a href="/puestos/tipos" class="nav-link"><i class="fa-light fa-desktop-alt"></i> Tipos de puesto</a></li> @endif
                                @if(checkPermissions(['Tags'],['R']))<li class="tags  text-nowrap ml-2 nav-item"><a href="/tags" class="nav-link"> <i class="fa-light fa-tags"></i> Tags</a></li> @endif
                                @if(checkPermissions(['Encuestas'],['R']))<li class="encuestas text-nowrap ml-2 nav-item"><a href="/encuestas" class="nav-link"><i class="fa-light fa-poll-h"></i> Encuestas</a></li> @endif
                            </ul>
                        </li>
                        <li class="nav-item has-sub menu_usuarios">
                            <a href="#" class="mininav-toggle nav-link collapsed"><i class="fa-light fa-user fs-5 me-2"></i>Personas</a>
                            <ul class="mininav-content nav collapse">
                                @if(checkPermissions(['Usuarios'],['R'])) <li class="usuarios text-nowrap ml-2 nav-item"><a href="/users" class="nav-link"><i class="fa-light fa-user"></i> Usuarios</a></li>  @endif
                                @if(checkPermissions(['Plantas usuarios'],['R']))<li class="plantas_usuarios text-nowrap ml-2 nav-item"><a href="/users/plantas_usuarios" class="nav-link"> <i class="fa-light fa-layer-plus"></i> Asignar plantas</a></li> @endif
                                @if(checkPermissions(['Puestos supervisores'],['R']))<li class="puestos_supervisores text-nowrap ml-2 nav-item"><a href="/users/puestos_supervisores" class="nav-link"> <i class="fa-light fa-magnifying-glass-location"></i> Supervision puestos</a></li> @endif
                                @if(checkPermissions(['Departamentos'],['R']))<li class="departamentos text-nowrap ml-2 nav-item"><a href="/departments" class="nav-link"><i class="fa-light fa-sitemap"></i> Departamentos</a></li> @endif
                                @if(checkPermissions(['Colectivos'],['R']))<li class="colectivos text-nowrap ml-2 nav-item"><a href="/collective" class="nav-link"><i class="fa-light fa-user-tag"></i> Colectivos</a></li> @endif
                                @if(checkPermissions(['Turnos'],['R']))<li class="turnos text-nowrap ml-2 nav-item"><a href="/turnos" class="nav-link"><i class="fa-light fa-repeat-1"></i> Turnos</a></li> @endif
                                @if(checkPermissions(['Festivos'],['R']))<li class="festivos text-nowrap ml-2 nav-item"><a href="/festives" class="nav-link"><i class="fa-light fa-calendar-range"></i> Festivos</a></li> @endif
                            </ul>
                        </li>
                        <li class="nav-item has-sub tipos_incidencia">
                            <a href="#" class="mininav-toggle nav-link collapsed"><i class="fa-light fa-exclamation-triangle fs-5 me-2"></i> Incidencias</a>
                            <ul class="mininav-content nav collapse">
                                @if(checkPermissions(['Tipos de incidencia'],['R']))<li class="incidencias_tipos text-nowrap ml-2 nav-item"><a href="/incidencias/tipos" class="nav-link"> <i class="fa-light fa-exclamation-triangle"></i> Tipos de incidencia</a></li> @endif
                                @if(checkPermissions(['Causas de cierre'],['R']))<li class="incidencias_causas text-nowrap ml-2 nav-item"><a href="/incidencias/causas" class="nav-link"> <i class="fa-light fa-times-hexagon"></i> Causas de cierre</a></li> @endif
                                @if(checkPermissions(['Estados de incidencia'],['R']))<li class="incidencias_estados text-nowrap ml-2 nav-item"><a href="/incidencias/estados" class="nav-link"> <i class="fa-light fa-sign"></i> Estados</a></li> @endif
                            </ul>
                        </li>
                        <li class="nav-item has-sub menu_permisos">
                            <a href="#" class="mininav-toggle nav-link collapsed"><i class="fa-light fa-user-lock fs-5 me-2"></i> Permisos</a>
                            <ul class="mininav-content nav collapse">
                                @if(checkPermissions(['Perfiles'],['R']))<li class="perfiles nav-item"><a href="/profiles"  class="nav-link"><i class="fa-light fa-users"></i> Perfiles</a></li> @endif
                                @if(checkPermissions(['Secciones'],['R']))<li class="secciones nav-item"><a href="/sections" class="nav-link"> <i class="fa-light fa-browser"></i> Secciones</a></li> @endif
                                @if(checkPermissions(['Permisos'],['R']))<li class="permisos nav-item"><a href="/profile-permissions" class="nav-link"><i class="fa-light fa-lock-alt"></i> Permisos</a></li> @endif
                            </ul>
                        </li>
                        <li class="nav-item has-sub menu_utilidades">
                            <a href="#" class="mininav-toggle nav-link collapsed"><i class="fa-light fa-screwdriver-wrench fs-5 me-2"></i> Utilidaes</a>
                            <ul class="mininav-content nav collapse">
                                @if(checkPermissions(['Tareas programadas'],['R']))<li class="tareas_programadas nav-item"><a href="/tasks" class="text-nowrap nav-link"> <i class="fa-light fa-timer"></i> Tareas programadas</a></li> @endif
                                @if (checkPermissions(['Eventos'],["R"]))<li class="eventos nav-item"><a href="{{url('/events')}}" class="nav-link"><i class="fa-light fa-engine"></i> Eventos</a></li>@endif
                                @if(checkPermissions(['Señaletica'],['R']))<li class="mkd nav-item"><a href="/MKD" class="nav-link"><i class="fa-light fa-sign"></i> Señaletica</a></li> @endif
                    
                                @if(checkPermissions(['Importar datos'],['W']))<li class="importar nav-item"> <a href="/import" class="nav-link"><i class="fa-light fa-upload"></i> Importar datos</a></li> @endif
                            </ul>
                        </li>
                    </ul>

                </li>
            </ul>
            @endif
            @if(session('DIS') && isset(session('DIS')['img_logo']))
                <div class="text-center mt-5">
                    <img src="{{ url('/img/distribuidores/'.session('DIS')['img_logo']) }}" title="{{ session('DIS')['nom_distribuidor'] }}" style="width:50%">
                </div> 
            @endif
            {{-- <!-- Widget -->
            <div class="mainnav__profile">

                <!-- Widget buttton form small navigation -->
                <div class="mininav-toggle text-center py-2 d-mn-min">
                    <i class="pli-monitor-2"></i>
                </div>

                <div class="d-mn-max mt-5"></div>

                <!-- Widget content -->
                <div class="mininav-content collapse d-mn-max">
                    <h6 class="mainnav__caption px-3 fw-bold">Server Status</h6>
                    <ul class="list-group list-group-borderless">
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <p class="mb-2 me-auto">CPU Usage</p>
                                <span class="badge bg-info rounded">35%</span>
                            </div>
                            <div class="progress progress-md">
                                <div class="progress-bar bg-info" role="progressbar" style="width: 35%" aria-valuenow="35" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <p class="mb-2 me-auto">Bandwidth</p>
                                <span class="badge bg-warning rounded">73%</span>
                            </div>
                            <div class="progress progress-md">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: 73%" aria-valuenow="73" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </li>
                    </ul>
                    <div class="d-grid px-3 mt-3">
                        <a href="#" class="btn btn-sm btn-success">View Details</a>
                    </div>
                </div>
            </div> --}}
            <!-- End - Profile widget -->

        </div>
        <!-- End - Navigation menu -->

        <!-- Bottom navigation menu -->
        <div class="mainnav__bottom-content border-top pb-2">
            <ul id="mainnav" class="mainnav__menu nav flex-column">
                <li class="nav-item has-sub">
                    <a href="#" class="nav-link mininav-toggle collapsed" aria-expanded="false">
                        <i class="pli-unlock fs-5 me-2"></i>
                        <span class="nav-label">Logout</span>
                    </a>
                    <ul class="mininav-content nav flex-column collapse">
                        <li class="nav-item">
                            <a href="{{url('/logout')}}" class="nav-link">Este dispositivo</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{url('/logout')}}" class="nav-link">Todos mis dispositivos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/lockscreen') }}" tabindex="-1" aria-disabled="true">Bloquear pantalla</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
        <!-- End - Bottom navigation menu -->

    </div>
</nav>