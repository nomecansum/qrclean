<div class="row">
    <div class="col-sm-3">
        <div class="panel panel-body panel-bordered-danger rounded_panel">
            <div class="row">
                <div class="col pr-0">
                    <h1 class="text-muted font-bold">{{ $countFestNac }}</h1>
                    <h5 class="text-muted" id="titAus">Fiestas nacionales</h5>
                </div>
                @php
                    try{
                        $pct_nacionales=ceil((100*$countFestNac/$countFest)/5)*5;
                    }catch(Throwable  $e){
                        $pct_nacionales=0;
                    }
                @endphp
                <div class="progress"><div style="width: {{ $pct_nacionales }}%; font-size:10px" class="progress-bar-danger text-white p-l-10" >{{ $pct_nacionales }}%</div></div>

            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="panel panel-body panel-bordered-mint rounded_panel">
            <div class="row">
                <div class="col pr-0">
                    <h1 class="text-muted font-bold">{{ $countFestReg }}</h1>
                    <h5 class="text-muted" id="titAus">Fiestas regionales</h5>
                </div>
                @php
                    try{
                        $pct_regionales= ceil((100*$countFestReg/$countFest)/5)*5;
                    }catch(Throwable  $e){
                        $pct_regionales=0;
                    }
                @endphp
                <div class="progress"><div style="width: {{ $pct_regionales }}%; font-size:10px" class="progress-bar-mint  text-white p-l-10">{{ $pct_regionales }}%</div></div>
                
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="panel panel-body panel-bordered-info rounded_panel">
            <div class="row">
                <div class="col pr-0">
                    <h1 class="text-muted font-bold">{{ $countFestProv }}</h1>
                    <h5 class="text-muted" id="titAus">Fiestas provinciales</h5>
                </div>
                @php
                    try{
                        $pct_provinciales=ceil((100*$countFestProv/$countFest)/5)*5;
                    }catch(Throwable  $e){
                        $pct_provinciales=0;
                    }
                @endphp
                <div class="progress"><div style="width: {{ $pct_provinciales }}%; font-size:10px" class="progress-bar-info  text-white p-l-10">{{ $pct_provinciales }}%</div></div>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="panel panel-body panel-bordered-pink rounded_panel">
            <div class="row">
                <div class="col pr-0">
                    <h1 class="text-muted font-bold">{{ $countFestLoc }}</h1>
                    <h5 class="text-muted" id="titAus">Fiestas locales</h5>
                </div>
                @php
                    try{
                        $pct_locales=ceil((100*$countFestLoc/$countFest)/5)*5;
                    }catch(Throwable  $e){
                        $pct_locales=0;
                    }
                @endphp
                <div class="progress"><div style="width: {{ $pct_locales }}%; font-size:10px" class="progress-bar-pink  text-white p-l-10">{{ $pct_locales }}%</div></div>
            </div>
        </div>
    </div>
</div>