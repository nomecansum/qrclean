
<link href="{{url('/plugins/gridstack')}}/dist/gridstack.css" rel="stylesheet"/>
<!-- HTML5 drag&drop (70k) -->
<script src="{{url('/plugins/gridstack')}}/dist/gridstack-h5.js"></script>

@php
//  Dimensiones del fondo


$filename=storage_path()."/app/temp/".$plantas->img_plano;
try{
    if(file_exists($filename))
    {
        $data = getimagesize($filename);
        $width = $data[0];
        $height = $data[1];
        $bg_gridstack='';
    } else {
        $width = 1920;
        $height = 1080;  
        $bg_gridstack=' lightgoldenrodyellow';
    }
} catch (\Exception $e){
    $width = 1920;
    $height = 1080;  
    $bg_gridstack=' lightgoldenrodyellow';
}

@endphp

<style type="text/css">


    .layout{
        margin: 30px;
        width: {{ $width }}px; 
        height: {{ $height }}px;
        background-image: url('{{ Storage::disk(config('app.img_disk'))->url('img/plantas/'.$plantas->img_plano) }}');
        /* zoom: 40%; */
    }
    .grid-stack {
        background: {{ $bg_gridstack  }};
        width: {{ $width }}px; 
        height: {{ $height }}px;
    }

    .grid-stack-item-content {
        color: #fff;
        text-align: center;
        background-color: #a3ffd6 ;
        glyph-orientation-vertical: middle;
        font-size: 1vw;
        font-weight: bolder;
        overflow-y: hidden !important;
        overflow-x: hidden !important;
        opacity: 0.7;
    }

    .icono_borrar{
        cursor: pointer; 
    }

    .nombre_zona{
        font-size: 3em;
        color: #fff; 
        -webkit-text-stroke: 1px #999;
        opacity: 1 !important;
    }

    .num_zona{
        border-radius: 50%;
        width: 34px;
        height: 34px;
        line-height: 20px !important;
        font-weight: bolder;
        padding: 4px;
        background: #fff;
        border: 3px solid rgba(0, 0, 0, 0.397);
        color: rgba(0, 0, 0, 0.397);
        text-align: center;
        font: 24px Arial, sans-serif;
    }

    
    </style>
    
        <div class="panel editor">
            <div class="panel-heading">
                <div class="panel-control">
                    <button class="btn btn-default" data-panel="dismiss" data-dismiss="panel"><i class="demo-psi-cross"></i></button>
                </div>
                <h3 class="panel-title">Distribucion de zonas en planta {{ $plantas->des_planta }}</h3>
                
            </div>
            @if(isset($error))
            <div class="alert alert-danger">
                {{-- <button class="close" data-dismiss="alert"><i class="pci-cross pci-circle"></i></button> --}}
                <strong>ERROR!</strong> {{ $error }}
            </div>
            @else
            <div class="panel-body">
                <form method="POST" action="{{ url('/plantas/save_zonas') }}" id="edit_plantas_form" name="edit_plantas_form" accept-charset="UTF-8" class="form-horizontal form-ajax"  enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="json_zonas" id="json_zonas" value="{{ $plantas->json_zonas??"" }}"> 
                    <input type="hidden" name="id_planta" value="{{ $plantas->id_planta }}">
                    <input type="hidden" name="width" value="{{ $width }}">
                    <input type="hidden" name="height" value="{{ $height }}">
                    <div class="row">
                        <div class="col-md-1 text-right">
                            <div>
                                <div class="btn-group btn-group-sm" role="group" style="margin-top: 23px">
                                    <a href="#" id="pre-add-widget"  class="btn btn-success" title="Añadir zona">
                                        <span  class="fa fa-plus-square pt-1" style="font-size: 20px" aria-hidden="true"></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-3" style="display: none" id="editor_nombre">
                            <label for="">Nombre</label>
                            <div class="input-group float-right" id="div_fechas">
                                <input type="text" class="form-control pull-left" id="nombre" name="nombre" style="width: 120px">
                                <span class="btn input-group-text btn-success" style="height: 40px" id="add-widget"><i class="fa-solid fa-square-arrow-down"></i> Añadir</span>
                            </div>
                        </div>
                        
                        <div class="col-md-9"></div>
                    </div>
                    @if(isset($plantas->img_plano))
                        <div class="row">
                            <div class="col-md-12">
                                <div class="layout">
                                    <div class="grid-stack" id="gridCont">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="form-group">
                        <div class="col-md-12 text-right">
                            <input class="btn btn-primary" id="btn_guardar" type="button" value="Guardar">
                        </div>
                    </div>
                </form>
            </div>
            
            @endif
    
            
        </div>
    
    <script>
        function stringToColor(string, saturation = 100, lightness = 75){
            hash = 0;
            for (let i = 0; i < string.length; i++) {
                hash = string.charCodeAt(i) + ((hash << 5) - hash);
                hash = hash & hash;
            }
            return `hsl(${(hash % 360)}, ${saturation}%, ${lightness}%)`;
        }
        
        serializedFull=[];

        zonas=0;
        function addNewWidget() {
            
            $( ".grid-stack-item-content" ).each(function() {
                if ($(this).attr("idzona")>zonas){zonas=$(this).attr("idzona")}
            });
            let n = items[count] || {
                w: 20,
                h: 12,
                autoPosition : true
            };
            zonas ++;
            id="zona"+zonas;
            n.text=$("#nombre").val();
            n.id="zona"+(Math.random() + 1).toString(36).substring(7);
            n.content = "<i class='fa-solid fa-trash-can text-danger icono_borrar fa-2z' onClick='grid.removeWidget(this.parentNode.parentNode)'></i><div class='num_zona'>"+zonas+"</div><div class='nombre_zona'>"+n.text+"</div>";
            grid.addWidget(n);
            //item=grid.addWidget($('<div><div class="grid-stack-item-content"  onclick="seleccionada('+zonas+')" idzona='+zonas+' >'+$("#nombre").val()+'</div></div>'), 0, 0, Math.floor(1 + 3 * Math.random()), Math.floor(4 + 3 * Math.random()), true,null,null,null,null,id);
            
        }

        $("#pre-add-widget").click(function() {
            $("#editor_nombre").val();
            $("#editor_nombre").show();
            animateCSS('#editor_nombre', 'bounceIn');
        });

        $('#add-widget').click(function() {
            addNewWidget();
            $("#editor_nombre").hide();
        });
        
        $('.form-ajax').submit(form_ajax_submit);

        var options = {
            alwaysShowResizeHandle: /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent),
            margin : 1,
            removeTimeout:500,
            disableOneColumnMode:true,
            animate:true,
            removable: false,
            resizable: { handles: 'all'},
            float: true,
            cellHeight: 10,
            column :100,
            acceptWidgets: true,
            dragIn: '.newWidget',  // class that can be dragged from outside
            dragInOptions: { revert: 'invalid', scroll: false, appendTo: 'body', helper: 'clone' }, // clone or can be your function
            removable: '#trash', // drag-out delete class
        };
        grid = GridStack.init(options);

        grid.on('added', function(event, items) {
            zonas=1;
            items.forEach(function(item){
                item.el.style.backgroundColor = stringToColor(item.text);
                item.el.style.opacity=0.7;
                item.id="zona"+(Math.random() + 1).toString(36).substring(7);
                zonas++;
            })
        });



        saveGrid = function() {
            delete serializedFull;
            serializedData = grid.save();
            document.querySelector('#json_zonas').value = JSON.stringify(serializedData, null, '  ');
        }

        loadGrid = function() {
            grid.load(serializedData, true); // update things
        }

        @if(isset($plantas->zonas))
            serializedData ={!! $plantas->zonas !!}
            loadGrid();
        @endif

        items=[];
        count = 0;
    
        $('#btn_guardar').click(function(){
            event.preventDefault();
            saveGrid();
            $('#edit_plantas_form').submit();
           
            
        });

    
        $('.demo-psi-cross').click(function(){
                $('.editor').hide();
            });
    </script>