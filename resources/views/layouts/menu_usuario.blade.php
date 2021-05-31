


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
                    <a href="{{ url('/miperfil/'.Auth::user()->id.'') }}" class="list-group-item"><i class="fad fa-user"></i> Mi Perfil</a>
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
                        <span class="menu-title">Home</span>
                        <i class="arrow"></i>
                    </a>
                    @if(checkPermissions(['Scan acceso'],['R']))<li class="main_scan"><a href="/scan_usuario" class="text-nowrap"><i class="fad fa-qrcode"></i> <span class="menu-title">Scan</span></a></li> @endif
                    @if(checkPermissions(['Reservas'],['R']))<li class="reservas"><a href="/reservas" class="text-nowrap"><i class="fad fa-calendar-alt"></i></i> <span class="menu-title">Reservar</span></a></li> @endif
                    {{-- <a href="{{ url('/puestos') }}">
                        <i class="fad fa-browser"></i>
                        <span class="menu-title">Puestos</span>
                        <i class="arrow"></i>
                    </a> --}}
                    
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

