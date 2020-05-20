


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
                        <img class="img-circle img-md" src="{{url('/img/users/'.Auth::user()->img_usuario)}}" alt="Profile Picture">
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
                    <a href="#" class="list-group-item">
                        <i class="demo-pli-male icon-lg icon-fw"></i> View Profile
                    </a>
                    <a href="#" class="list-group-item">
                        <i class="demo-pli-gear icon-lg icon-fw"></i> Settings
                    </a>
                    <a href="#" class="list-group-item">
                        <i class="demo-pli-information icon-lg icon-fw"></i> Help
                    </a>
                    <a href="{{url('/logout')}}" class="list-group-item">
                        <i class="demo-pli-unlock icon-lg icon-fw"></i> Logout
                    </a>
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
                    <li class="list-header">Navigation</li>

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
                    <li>
                        <a href="#">
                            <i class="fad fa-browser"></i>
                            <span class="menu-title">Puestos</span>
                            <i class="arrow"></i>
                        </a>

                        <!--Submenu-->
                        <ul class="collapse">
                            <li><a href="/puestos"> <i class="fad fa-browser"></i> Puestos</a></li>
                            <li><a href="/plantas"> <i class="fad fa-layer-group"></i> Plantas</a></li>
                            <li><a href="/edificios"><i class="fad fa-building"></i> Edificios</a></li>

                        </ul>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-cog"></i>
                            <span class="menu-title">Configuracion</span>
                            <i class="arrow"></i>
                        </a>

                        <!--Submenu-->
                        <ul class="collapse">
                            <li><a href="/users">Usuarios</a></li>
                            <li><a href="/profiles">Perfiles</a></li>
                            <li><a href="/sections">Secciones</a></li>
                            <li><a href="/profile-permissions">Permisos</a></li>

                        </ul>
                    </li>
                </ul>


                <!--Widget-->
                <!--================================-->
                <div class="mainnav-widget">
                    <!-- Show the button on collapsed navigation -->
                    <div class="show-small">
                        <a href="#" data-toggle="menu-widget" data-target="#demo-wg-server">
                            <i class="demo-pli-monitor-2"></i>
                        </a>
                    </div>
                </div>
                <!--================================-->
                <!--End widget-->
            </div>
        </div>

    <!--================================-->
    <!--End menu-->
    @if(checkPermissions(['Usuarios'],['R']))

        <ul class="collapse">
            <li><a href="/users">Usuarios</a></li>
        </ul>

        @endif


        @if(checkPermissions(['Perfiles'],['R']))

            <ul class="collapse">
                <li><a href="/profiles">Perfiles</a></li>
            </ul>

        @endif

        @if(checkPermissions(['Secciones'],['R']))

            <ul class="collapse">
                <li><a href="/sections">Secciones</a></li>
            </ul>


        @endif

        @if(checkPermissions(['Permisos'],['R']))

            <ul class="collapse">
                <li><a href="/profile-permissions">Permisos</a></li>
            </ul>

        @endif

        @if(checkPermissions(['Bitacora'],['R']))

            <ul class="collapse">
                <li class="nav-item">
                    <a  class="nav-link" href="{{ url('bitacoras') }}"><i class="nav-icon fab fa-stack-exchange"></i><p><span>Bitacora</span></p></a>
                </li>
            </ul>

        @endif

        @if(checkPermissions(['Configuracion'],['R']))
            @if(checkPermissions(['Acceso'],['R']))
            <li>
                <a href="#">
                    <i class="fa fa-cog"></i>
                    <span class="menu-title">Configuracion</span>
                    <i class="arrow"></i>
                </a>

                <!--Submenu-->
                <ul class="collapse">
                    <li><a href="/users">Usuarios</a></li>
                    <li><a href="/profiles">Perfiles</a></li>
                    <li><a href="/sections">Secciones</a></li>
                    <li><a href="/profile-permissions">Permisos</a></li>

                </ul>
            </li>
            @endif

            @if(checkPermissions(['Permisos'],['R']))
                <li><a href="/profile-permissions">Permisos</a></li>
            @endif

        @endif


        @if(checkPermissions(['Camaras'],['R']))

            <a href="{{ url('/camaras') }}">
                <i class="fa fa-video-camera"></i>
                <span class="menu-title">Gestion de camaras</span>
                <i class="arrow"></i>
            </a>

            <li>
                <a href="#">
                    <i class="fa fa-cog"></i>
                    <span class="menu-title">Configuracion</span>
                    <i class="arrow"></i>
                </a>

                <!--Submenu-->
                <ul class="collapse">
                    <li><a href="/users">Usuarios</a></li>
                    <li><a href="/profiles">Perfiles</a></li>
                    <li><a href="/sections">Secciones</a></li>
                    <li><a href="/profile-permissions">Permisos</a></li>

                </ul>
            </li>

        @endif
</div>

