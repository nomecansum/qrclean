
<!--Brand logo & name-->
<!--================================-->
<div class="navbar-header">
    <a href="{{url('/')}}" class="navbar-brand">
        @if(session('logo_cliente_menu'))
        <img src="{{ Storage::disk(config('app.img_disk'))->url('img/clientes/images/'.session('logo_cliente_menu')) }}" style="width: 55px; height: 55px" alt="" class="brand-icon">
        @else   
        <img src="/img/logo.png" alt="Spotlinker" class="brand-icon">
        @endif
        <div class="brand-title">
            <span class="brand-text"> <img src="/img/logo_menu_arriba.png" alt="Spotlinker" class="brand-text"></span>
        </div>
    </a>
</div>
<!--================================-->
<!--End brand logo & name-->


<!--Navbar Dropdown-->
<!--================================-->
<div class="navbar-content">
    <ul class="nav navbar-top-links">

        <!--Navigation toogle button-->
        <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
        <li class="tgl-menu-btn">
            <a class="mainnav-toggle" href="#">
                <i class="demo-pli-list-view"></i>
            </a>
        </li>
        <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
        <!--End Navigation toogle button-->



        <!--Search-->
        <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
        <li>
            <div class="custom-search-form">
                {{--  <label class="btn btn-trans" for="search-input" data-toggle="collapse" data-target="#nav-searchbox">
                    <i class="demo-pli-magnifi-glass"></i>
                </label>  --}}
                {{--  <form>
                    <div class="search-container collapse" id="nav-searchbox">
                        <input id="search-input" type="text" class="form-control" placeholder="Type for search...">
                    </div>
                </form>  --}}
            </div>
        </li>
        <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
        <!--End Search-->

    </ul>
    <ul class="nav navbar-top-links">
        <!--Notification dropdown-->
        <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
        {{--  <li class="dropdown">
            <a href="#" data-toggle="dropdown" class="dropdown-toggle">
                <i class="fa fa-bell"></i>
                <span class="badge badge-header badge-danger"></span>
            </a>


            <!--Notification dropdown menu-->
            <div class="dropdown-menu dropdown-menu-md dropdown-menu-right">
                <div class="nano scrollable">
                    <div class="nano-content">
                        <ul class="head-list">
                            <li>
                                <a href="#" class="media add-tooltip" data-title="Used space : 95%" data-container="body" data-placement="bottom">
                                    <div class="media-left">
                                        <i class="demo-pli-data-settings icon-2x text-main"></i>
                                    </div>
                                    <div class="media-body">
                                        <p class="text-nowrap text-main text-semibold">HDD is full</p>
                                        <div class="progress progress-sm mar-no">
                                            <div style="width: 95%;" class="progress-bar progress-bar-danger">
                                                <span class="sr-only">95% Complete</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a class="media" href="#">
                                    <div class="media-left">
                                        <i class="demo-pli-file-edit icon-2x"></i>
                                    </div>
                                    <div class="media-body">
                                        <p class="mar-no text-nowrap text-main text-semibold">Write a news article</p>
                                        <small>Last Update 8 hours ago</small>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a class="media" href="#">
                                    <span class="label label-info pull-right">New</span>
                                    <div class="media-left">
                                        <i class="demo-pli-speech-bubble-7 icon-2x"></i>
                                    </div>
                                    <div class="media-body">
                                        <p class="mar-no text-nowrap text-main text-semibold">Comment Sorting</p>
                                        <small>Last Update 8 hours ago</small>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a class="media" href="#">
                                    <div class="media-left">
                                        <i class="demo-pli-add-user-star icon-2x"></i>
                                    </div>
                                    <div class="media-body">
                                        <p class="mar-no text-nowrap text-main text-semibold">New User Registered</p>
                                        <small>4 minutes ago</small>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!--Dropdown footer-->
                <div class="pad-all bord-top">
                    <a href="#" class="btn-link text-main box-block">
                        <i class="pci-chevron chevron-right pull-right"></i>Show All Notifications
                    </a>
                </div>
            </div>
        </li>  --}}
        <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
        <!--End notifications dropdown-->
        <!--User dropdown-->
        <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
        <li id="dropdown-user" class="dropdown">
            <a href="#" data-toggle="dropdown" class="dropdown-toggle text-end">
                <span class="ic-user pull-right">
                    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                    <!--You can use an image instead of an icon.-->
                    <!--<img class="img-circle img-user media-object" src="img/profile-photos/1.png" alt="Profile Picture">-->
                    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                    <i class="fa fa-user"></i>
                </span>
                <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                <!--You can also display a user name in the navbar.-->
                <!--<div class="username hidden-xs">Aaron Chavez</div>-->
                <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
            </a>


            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right panel-default">
                <div class="font-bold text-center w100 mt-2">{{ Auth::user()->name }}</div>
                <ul class="head-list">
                    
                    <li>
                        <a href="{{ url('/miperfil/'.Auth::user()->id) }}"><i class="fad fa-user"></i> Mi Perfil</a>
                    </li>
                    {{-- <li>
                        <a href="#"><span class="badge badge-danger pull-right">9</span><i class="demo-pli-mail icon-lg icon-fw"></i> Messages</a>
                    </li> --}}
                    {{-- <li>
                        <a href="#"><span class="label label-success pull-right">New</span><i class="demo-pli-gear icon-lg icon-fw"></i> Settings</a>
                    </li> --}}
                    <li>
                        <a href="{{ url('/lockscreen') }}"><i class="fad fa-user-lock"></i> Bloquear pantalla</a>
                    </li>
                   
                    <li>
                        <a href="{{url('/logout')}}"><i class="fad fa-sign-out-alt"></i> Logout</a>
                    </li>
                    @if(session('back_id') && session('back_id')!=Auth::user()->id)
                        <li role="separator" class="divider"></li>
                        <button class="btn btn-warning ml-5 mb-2" onclick="document.location='{{url('reback')}}'"> <i class="mdi mdi-format-rotate-90"></i> Volver a mi sesion</button>
                    @endif
                </ul>
            </div>
        </li>
        <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
        <!--End user dropdown-->


        {{-- <li>
            <a href="#" class="aside-toggle">
                <i class="demo-pli-dot-vertical"></i>
            </a>
        </li> --}}
    </ul>
</div>
<!--================================-->
<!--End Navbar Dropdown-->
