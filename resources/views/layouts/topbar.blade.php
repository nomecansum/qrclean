<div class="header__inner">

    <!-- Brand -->
    <div class="header__brand">
        <div class="brand-wrap">

            <!-- Brand logo -->
            <a href="{{url('/')}}" class="brand-img stretched-link">
                @if(session('logo_cliente_menu'))
                <img src="{{ Storage::disk(config('app.img_disk'))->url('img/clientes/images/'.session('logo_cliente_menu')) }}" style="width: 45px; height: 45px" alt="" class="Nifty logo rounded">
                @else   
                <img src="/img/logo.png" alt="Spotlinker" class="Nifty logo">
                @endif
                
            </a>

           

            <!-- Brand title -->
            <div class="brand-title">
                <span class="brand-text"> Spotlinker</span>
            </div>

            <!-- You can also use IMG or SVG instead of a text element. -->

        </div>
    </div>
    <!-- End - Brand -->

    <div class="header__content">

        <!-- Content Header - Left Side: -->
        <div class="header__content-start">

            <!-- Navigation Toggler -->
            <button type="button" class="nav-toggler header__btn btn btn-icon btn-sm">
                <i class="demo-psi-view-list"></i>
            </button>

            <!-- Searchbox -->
            <div class="header-searchbox">

                <!-- Searchbox toggler for small devices -->
                <label for="header-search-input" class="header__btn d-md-none btn btn-icon rounded-pill shadow-none border-0 btn-sm" type="button">
                    <i class="demo-psi-magnifi-glass"></i>
                </label>

                <!-- Searchbox input -->
                <form class="searchbox searchbox--auto-expand searchbox--hide-btn input-group">
                    <input id="header-search-input" class="searchbox__input form-control bg-transparent" type="search" placeholder="Type for search . . ." aria-label="Search">
                    <div class="searchbox__backdrop">
                        <button class="searchbox__btn header__btn btn btn-icon rounded shadow-none border-0 btn-sm" type="button" id="button-addon2">
                            <i class="demo-pli-magnifi-glass"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <!-- End - Content Header - Left Side -->

        <!-- Content Header - Right Side: -->
        <div class="header__content-end">

            <!-- Notification Dropdown -->
            {{-- <div class="dropdown">

                <!-- Toggler -->
                <button class="header__btn btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="d-block position-relative">
                        <i class="demo-psi-bell"></i>
                        <span class="badge badge-super rounded bg-danger p-1">

                            <span class="visually-hidden">unread messages</span>
                        </span>
                    </span>
                </button>

                <!-- Notification dropdown menu -->
                <div class="dropdown-menu dropdown-menu-end w-md-300px">
                    <div class="border-bottom px-3 py-3 mb-3">
                        <h5>Notifications</h5>
                    </div>

                    <div class="list-group list-group-borderless">

                        <!-- List item -->
                        <div class="list-group-item list-group-item-action d-flex align-items-start mb-3">
                            <div class="flex-shrink-0 me-3">
                                <i class="demo-pli-data-settings fs-2"></i>
                            </div>
                            <div class="flex-grow-1 ">
                                <a href="#" class="h6 d-block mb-0 stretched-link text-decoration-none">Your storage is full</a>
                                <small class="text-muted">Local storage is nearly full.</small>
                            </div>
                        </div>

                        <!-- List item -->
                        <div class="list-group-item list-group-item-action d-flex align-items-start mb-3">
                            <div class="flex-shrink-0 me-3">
                                <i class="demo-pli-file-edit fs-2"></i>
                            </div>
                            <div class="flex-grow-1 ">
                                <a href="#" class="h6 d-block mb-0 stretched-link text-decoration-none">Writing a New Article</a>
                                <small class="text-muted">Wrote a news article for the John Mike</small>
                            </div>
                        </div>

                        <!-- List item -->
                        <div class="list-group-item list-group-item-action d-flex align-items-start mb-3">
                            <div class="flex-shrink-0 me-3">
                                <i class="demo-pli-speech-bubble-7 fs-2"></i>
                            </div>
                            <div class="flex-grow-1 ">
                                <div class="d-flex justify-content-between align-items-start">
                                    <a href="#" class="h6 mb-0 stretched-link text-decoration-none">Comment sorting</a>
                                    <span class="badge bg-info rounded ms-auto">NEW</span>
                                </div>
                                <small class="text-muted">You have 1,256 unsorted comments.</small>
                            </div>
                        </div>

                        <!-- List item -->
                        <div class="list-group-item list-group-item-action d-flex align-items-start mb-3">
                            <div class="flex-shrink-0 me-3">
                                <img class="img-xs rounded-circle" src="./assets/img/profile-photos/7.png" alt="Profile Picture" loading="lazy">
                            </div>
                            <div class="flex-grow-1 ">
                                <a href="#" class="h6 d-block mb-0 stretched-link text-decoration-none">Lucy Sent you a message</a>
                                <small class="text-muted">30 minutes ago</small>
                            </div>
                        </div>

                        <!-- List item -->
                        <div class="list-group-item list-group-item-action d-flex align-items-start mb-3">
                            <div class="flex-shrink-0 me-3">
                                <img class="img-xs rounded-circle" src="./assets/img/profile-photos/3.png" alt="Profile Picture" loading="lazy">
                            </div>
                            <div class="flex-grow-1 ">
                                <a href="#" class="h6 d-block mb-0 stretched-link text-decoration-none">Jackson Sent you a message</a>
                                <small class="text-muted">1 hours ago</small>
                            </div>
                        </div>

                        <a href="#" class="btn btn-link shadow-none">Show all Notifications</a>

                    </div>
                </div>
            </div> --}}
            <!-- End - Notification dropdown -->

           <!-- User dropdown -->
            <div class="dropdown">

                <!-- Toggler -->
                <button class="header__btn btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-user"></i>
                </button>

                <!-- User dropdown menu -->
                <div class="dropdown-menu dropdown-menu-end w-md-450px">

                    <!-- User dropdown header -->
                    <div class="d-flex align-items-center border-bottom p-3">
                        <div class="flex-shrink-0">
                            @if(Auth::user()->img_usuario!="" && file_exists( public_path().'/img/users/'.Auth::user()->img_usuario))
                            <img class="img-sm rounded-circle"  src="{{Storage::disk(config('app.img_disk'))->url('img/users/'.Auth::user()->img_usuario)}}" alt="Profile Picture" loading="lazy">
                            @else
                            {!! icono_nombre(Auth::user()->name,50,18) !!}
                            @endif
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-0">{{ Auth::user()->name }}</h5>
                            <span class="text-muted fst-italic">{{Auth::user()->email}}</span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-7">

                            <!-- Simple widget and reports -->
                            <div class="list-group list-group-borderless mb-3">
                                <div class="list-group-item text-center border-bottom mb-3">
                                    <p class="display-2 text-warning cuenta_reservas">{{ session('reservas')!==null?count(session('reservas')):0 }}</p>
                                    <p class="h6 mb-0"><i class="fa-light fa-calendar-circle-user"></i> Reservas</p>
                                    <small class="text-muted">Reservas para hoy {!! beauty_fecha(Carbon\Carbon::now(),0) !!}</small>
                                </div>
                                @if(session('reservas')!==null)
                                    @foreach(session('reservas') as $reserva)
                                        <div class="list-group-item py-0 d-flex justify-content-between align-items-center">
                                            <i class="{{ $reserva->icono_tipo }}"></i> {{ $reserva->des_tipo_puesto }} 
                                            <small class="fw-bolder"> {{ $reserva->cod_puesto }}</small>
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                        </div>
                        <div class="col-md-5">

                            <!-- User menu link -->
                            <div class="list-group list-group-borderless h-100 py-3">
                                <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <span><i class="fa-light fa-bell fs-5 me-3"></i> Avisos</span>
                                    <span class="badge bg-danger rounded-pill cuenta_notificaciones">0</span>
                                </a>
                                <a href="{{ url('/miperfil/'.Auth::user()->id) }}" class="nav-link">
                                    <i class="demo-pli-male fs-5 me-3"></i> Perfil
                                </a>
                                <a class="list-group-item list-group-item-action" id="_dm-settingsToggler" class="_dm-btn-settings list-group-item list-group-item-action" data-bs-toggle="offcanvas" data-bs-target="#_dm-settingsContainer" aria-controls="_dm-settingsContainer">
                                    <i class="demo-pli-gear fs-5 me-3"></i> Ajustes
                                </a>

                                <a href="{{ url('/lockscreen') }}" class="nav-link">
                                    <i class="demo-pli-computer-secure fs-5 me-3"></i> Bloquear
                                </a>
                                <a href="{{url('/logout')}}" class="nav-link">
                                    <i class="demo-pli-unlock fs-5 me-3"></i> Logout
                                </a>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
            <!-- End - User dropdown -->

            <!-- Sidebar Toggler -->
            {{-- <button class="sidebar-toggler header__btn btn btn-icon btn-sm" type="button">
                <i class="demo-psi-dot-vertical"></i>
            </button> --}}

        </div>
    </div>
</div>