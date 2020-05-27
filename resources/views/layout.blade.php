<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>QRClean</title>
    <!--STYLESHEET-->
    <!--=================================================-->
    <!--Open Sans Font [ OPTIONAL ]-->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700' rel='stylesheet' type='text/css'>
     <!--JQueryUI [OPTIONAL]-->
     <link href="{{ url('/plugins/jquery-ui/jquery-ui.css') }}" rel="stylesheet">
    <!--Bootstrap Stylesheet [ REQUIRED ]-->
    <link href="{{ url('/css/bootstrap.min.css') }}" rel="stylesheet">
    {{--  <link href="{{ url('/css/bootstrap-grid.min.css') }}" rel="stylesheet">  --}}
    <!--Nifty Stylesheet [ REQUIRED ]-->

    <link href="{{ url('/css/nifty.min.css') }}" rel="stylesheet">
    <!--Nifty Premium Icon [ DEMONSTRATION ]-->
    <link href="{{ url('/css/demo/nifty-demo-icons.min.css/') }}" rel="stylesheet">
    <!--=================================================-->
    <!--Pace - Page Load Progress Par [OPTIONAL]-->
    <link href="{{ url('/plugins/pace/pace.min.css') }}" rel="stylesheet">
    <script src="{{ url('/plugins/pace/pace.min.js') }}"></script>
    <!--Demo [ DEMONSTRATION ]-->
    <link href="{{ url('/css/demo/nifty-demo.css') }}" rel="stylesheet">
    {{-- MAterial design fonts --}}
    <link rel="stylesheet" href="{{ URL('/css/materialdesignicons.min.css') }}">
    {{--  FontAwesome  --}}
    <link href="{{ asset('/plugins/fontawesome5/css/all.min.css') }}" rel="stylesheet">


    <!--Mosaic custom CSS [ REQUIRED ]-->
    <link href="{{ url('/css/mosaic.css') }}" rel="stylesheet">
    {{--  Animate CSS  --}}
    <link rel="stylesheet" href="{{ URL('/plugins/animate-css/animate.min.css') }}">

    {{--  Datatables  --}}
    <link rel="stylesheet" href="{{ URL('/plugins/datatables/datatables.min.css') }}">

    {{-- Custom file --}}
    <link href="{{ URL('/plugins/custom_file.css') }}" rel="stylesheet">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ URL('/plugins/select2/css/select2.min.css') }}">
    {{--  Toast  --}}
    <link href="{{url('/plugins/toast-master/css/jquery.toast.css')}}" rel="stylesheet" media="all">
    {{--  Colorpicker  --}}
    <link href="{{url('/plugins/jquery-minicolors-master/jquery.minicolors.css')}}" rel="stylesheet" media="all">
    {{--  sweetAlert  --}}
    <link href="{{url('/plugins/sweetalert/dist/sweetalert2.min.css')}}" rel="stylesheet" media="all">
    {{-- Iconpicker --}}
    <link href="{{ asset('/plugins/bootstrap-iconpicker/css/bootstrap-iconpicker.min.css') }}" rel="stylesheet">



    <!--=================================================

    REQUIRED
    You must include this in your project.


    RECOMMENDED
    This category must be included but you may modify which plugins or components which should be included in your project.


    OPTIONAL
    Optional plugins. You may choose whether to include it in your project or not.


    DEMONSTRATION
    This is to be removed, used for demonstration purposes only. This category must not be included in your project.


    SAMPLE
    Some script samples which explain how to initialize plugins or components. This category should not be included in your project.


    Detailed information and more samples can be found in the document.

    =================================================-->
    @yield('styles')
</head>
<!--TIPS-->
<!--You may remove all ID or Class names which contain "demo-", they are only used for demonstration. -->
<body>
    <div id="container" class="effect aside-float aside-bright mainnav-lg">

        <!--NAVBAR-->
        <!--===================================================-->
        <header id="navbar">
            @if(Auth::check())
                <div id="navbar-container" class="boxed">
                    @include('layouts.topbar')
                </div>
            @endif
            @include('flash::message')
        </header>

        <!--=====================   ==============================-->
        <!--END NAVBAR-->

        <div class="boxed">

            <!--CONTENT CONTAINER-->
            <!--===================================================-->
            @if(Auth::check())
            <div id="content-container">
                <div id="page-head">

                    <!--Page Title-->
                    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                    <div id="page-title">
                        {{--  <h1 class="page-header text-overflow pad-no">Helper Classes</h1>  --}}
                        @yield('title')
                    </div>
                    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                    <!--End page title-->


                    <!--Breadcrumb-->
                    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                    @yield('breadcrumb')
                    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                    <!--End breadcrumb-->

                </div>
                {{--  <div id="page-head">
                    <div class="pad-all text-center">
                        <img src="{{url('/img/Mosaic_brand.png')}}" style="width:300px">
                    </div>
                </div>  --}}
                <!--Page content-->
                <!--===================================================-->
                <div id="page-content" style="padding-top:0px">
                    @yield('content')
                </div>
                <!--===================================================-->
                <!--End page content-->
            </div>
            @else
                @yield('content')
            @endif
            <!--===================================================-->
            <!--END CONTENT CONTAINER-->

            <!--ASIDE-->
            <!--===================================================-->
            <aside id="aside-container">
                <div id="aside">
                    @include('layouts.aside')
                </div>
            </aside>
            <!--===================================================-->
            <!--END ASIDE-->


            <!--MAIN NAVIGATION-->
            <!--===================================================-->
            <nav id="mainnav-container">
                @if(Auth::check())
                    <div id="mainnav">
                        @include('layouts.menu')
                    </div>
                @endif
            </nav>
            <!--===================================================-->
            <!--END MAIN NAVIGATION-->

        </div>



        <!-- FOOTER -->
        <!--===================================================-->
        <footer id="footer">
            <!-- Visible when footer positions are fixed -->
            <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
            <div class="show-fixed pad-rgt pull-right">
                You have <a href="#" class="text-main"><span class="badge badge-danger">3</span> pending action.</a>
            </div>
            <!-- Visible when footer positions are static -->
            <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
            <div class="hide-fixed pull-right pad-rgt">
                14GB of <strong>512GB</strong> Free.
            </div>
            <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
            <!-- Remove the class "show-fixed" and "hide-fixed" to make the content always appears. -->
            <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
            <p class="pad-lft">&#0169; 2020 <img src="{{url('/img/Mosaic_brand_20.png')}}"></p>
        </footer>
        <!--===================================================-->
        <!-- END FOOTER -->

        <!-- SCROLL PAGE BUTTON -->
        <!--===================================================-->
        <button class="scroll-top btn">
            <i class="pci-chevron chevron-up"></i>
        </button>
        <!--===================================================-->
    </div>
    <!--===================================================-->
    <!-- END OF CONTAINER -->





    <!--JAVASCRIPT-->
    <!--=================================================-->
    <!--jQuery [ REQUIRED ]-->
    <script src="{{ url('js/jquery.min.js') }}"></script>
    <!--jQueryUI [ REQUIRED ]-->
    <script src="{{ url('/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <!--BootstrapJS [ RECOMMENDED ]-->
    <script src="{{ url('js/bootstrap.min.js') }}"></script>
    <!--NiftyJS [ RECOMMENDED ]-->
    <script src="{{ url('js/nifty.min.js') }}"></script>
    <!--=================================================-->
    <!--Demo script [ DEMONSTRATION ]-->
    <script src="{{ url('js/demo/nifty-demo.js') }}"></script>
     
    <!--Specify page [ SAMPLE ]-->
    {{--  <script src="{{ url('js/demo/dashboard.js') }}"></script>  --}}
    <!-- Select2 -->
    <script src="{{ url('/plugins/select2/js/select2.full.min.js') }}"></script>
    {{--  Toast  --}}
    <script src="{{url('/plugins/toast-master/js/jquery.toast.js')}}"></script>
    {{--  Colorpicker  --}}
    <script src="{{url('/plugins/jquery-minicolors-master/jquery.minicolors.min.js')}}"></script>
   
    {{--  SweetAlert  --}}
    <script src="{{url('/plugins/sweetalert/dist/sweetalert2.all.min.js')}}"></script>
    {{-- Iconpicker --}}
    <script src="{{ asset('/plugins/bootstrap-iconpicker/js/bootstrap-iconpicker.bundle.min.js') }}"></script>
    {{-- Plugin print --}}
    <script src="{{asset('/plugins/printThis-master/printThis.js')}}"></script>

    {{-- Datatables --}}
    <script src="{{asset('/plugins/datatables/datatables.min.js')}}"></script>

    @include('layouts.main_scripts')
    @yield('scripts')
    @yield('scripts2')
    @yield('scripts3')

</body>
</html>
