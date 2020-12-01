@php
$month = isset( $backMonth ) ? $backMonth : date( 'Y-n' );
$carbon = new Carbon\Carbon;
$week = 1;
for ( $i=1;$i<=date( 't', strtotime( $month ) );$i++ ) {

	$day_week = date( 'N', strtotime( $month.'-'.$i )  );

	$calendar[ $week ][ $day_week ] = $i;
	if ( $day_week == 7 )
		$week++;
}

$leyenda = [];
$color = "silver";
$meses = ["enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre"];

@endphp
<style>
	.calendar-badge {
		position: absolute; font-size: 8px; font-weight: 700; color: #fff;display: block; width: 14px; height: 14px; text-align: center; border-radius: 64px; top: 5px; right:5px; cursor: pointer;
	}
	.popover-content, .popover-title {
		font-size: 12px;
	}
	.popover-content ul {padding: 0;padding-left:15px; margin-bottom:0;}
	/* .popover-content ul li{font-size: 10px; margin-bottom: 3px; border-bottom: 1px solid #eee} */
	.bloque{
		left: 40px;
	}
	.background {
		position: absolute;
		top: 0;
		left: 0;
		bottom: 0;
		right: 0;
		z-index: -1;
		overflow: hidden;
		}
</style>
{{-- <div class="panel-heading">
	<table class="table table-calendar mb0">


	</table>
</div> --}}

<div class="panel-body">
	<table class="table table-calendar mb0 rounded w-100" style="border: 1px solid #f2f7f8;">
		<thead>
			<tr>
				<th>
					<a data-month="{{ $month }}" data-action="sub" class="changeMonth" style="float:left; font-size: 18px; cursor: pointer;"> <i class="fas fa-arrow-left"></i> </a>
				</th>
				<th class="text-center" colspan="5" width="80%" style="font-size: 24px">
					{{trans('strings.'.$meses[$carbon->parse($month)->format('n')-1]).' '. ucwords($carbon->parse($month)->format('Y')) }}
				</th>
				<th>
					<a data-month="{{ $month }}" data-action="add" class="changeMonth" style="float:right; font-size: 18px; cursor: pointer;"> <i class="fas fa-arrow-right"></i> </a>
				</th>
			</tr>
			<tr style="background-color: #ffad76; color: #737887; border: 1px solid #f2f7f8; border-radius: 3px 3px 0 0; text-align: center; font-size: 12px">
				<th style="width: 14.28%" class="text-center">{{ trans('strings.lunes') }}</th>
				<th style="width: 14.28%" class="text-center">{{ trans('strings.martes') }}</th>
				<th style="width: 14.28%" class="text-center">{{ trans('strings.miercoles') }}</th>
				<th style="width: 14.28%" class="text-center">{{ trans('strings.jueves') }}</th>
				<th style="width: 14.28%" class="text-center">{{ trans('strings.viernes') }}</th>
				<th style="width: 14.28%" class="text-center">{{ trans('strings.sabado') }}</th>
				<th style="width: 14.28%" class="text-center">{{ trans('strings.domingo') }}</th>
			</tr>
		</thead>
		<tbody>
			@foreach ($calendar as $days)
				<tr>
					
					@for ($i=1;$i<=7;$i++)
						@if (isset($days[$i]))
							@php
								$dias=$reservas->where('fec_reserva',Carbon\Carbon::parse($month.'-'.$days[$i])->format('Y-m-d'))->all();
							@endphp
					
							
							@php
								if(count($dias)>0){
									$color="#b3dbbf";
									$borde="";
									$title="";
									$estado="ocupado";
								} else {
									$color = '#fff';
									$borde="border: 2px solid #f2f7f8";
									$title="";
									$estado="vacio";
								}
								if(Carbon\Carbon::parse($month.'-'.$days[$i])->format('Y-m-d')==Carbon\Carbon::now()->format('Y-m-d')){
									$borde="border: 3px solid #1e90ff";
								}

								$dia_pasado=Carbon\Carbon::parse($month.'-'.$days[$i]) < Carbon\Carbon::now()->format('Y-m-d');	
							@endphp
							{{--  data-tooltip-content="#tooltip_content{{$carbon->parse($actual->fecha)->format('d-m-Y')}}"  --}}
							
							<td style="background-color: {{$dia_pasado?'#dedede':$color}}; height: 10vw; width: 15vw;  color: #999; border-radius: 8px; {{ $borde }}"  class="add-tooltip  pt-3 @if(!$dia_pasado)td_calendar @endif {{ $estado }}" @if($dia_pasado) data-past="1" @else data-past="0" @endif data-fecha="{{ Carbon\Carbon::parse($month.'-'.$days[$i])->format('Y-m-d') }}" data-fechaID="{{ Carbon\Carbon::parse($month.'-'.$days[$i])->format('Ymd') }}" id="TD{{ Carbon\Carbon::parse($month.'-'.$days[$i])->format('Ymd') }}" data-toggle="tooltip" data-container="body" data-placement="top" data-original-title="{!!$title!!}" >
								
                                <span class="font-bold" style="font-size: 2.5vw; font-weigth: bolder" >{{ isset($days[$i]) ? $days[$i] : '' }}</span><br>
								<span style="color: #fff; cursor: pointer">
									@foreach($dias as $dia)
										@php
											$icono=$dia->val_icono;
											$ic_color=$dia->val_color;
											$descrip=$dia->cod_puesto;
											//$title=Carbon\Carbon::parse($dia->fec_reserva)->format('d/m/Y').chr(13)." Puesto: ".$descrip." - Edificio: ".$dia->des_edificio." - Planta: ".$dia->des_planta;	
										@endphp
									@if($dia)
									<b class="text-white" style="font-size: 1vw">@if($icono!="") <i class="{{ $icono }}  {{ txt_blanco($ic_color) }}"></i> @endif{!! $descrip !!}</b><br>
									@endif
									@endforeach
								</span>
							</td>
						@else
							<td></td>
						@endif
					@endfor
				</tr>
			@endforeach
		</tbody>
	</table>
	<div id="detalle_horario" style="position: absolute; width: 250px; height: 442px;display: flex; flex-direction: row; ">
		<div class="text-dark" style="padding-top:5px; margin-right: 0px">
			<i class="fas fa-caret-left fa-3x"></i>
		</div>
		<div  class="bg-light w-100" id="contenido_detalle_horario" style="border: 2px solid #333; margin-left: 0px; border-radius: 6px">
			
		</div>
		
	</div>
</div>

<script>

	$(function(){
		$('#detalle_horario').hide();
	})

	var tooltip = $('.add-tooltip');
    if (tooltip.length)tooltip.tooltip();

	fechacal="{{ $month }}";

	$('.td_calendar').click(function(){
		spshow('spin');
		if($(this).data('past')==0){
			$('#editorCAM').load("{{ url('/reservas/create/') }}/"+$(this).data('fecha'), function(){
				animateCSS('#editorCAM','bounceInRight');
				sphide('spin');
				$('body, html').animate({scrollTop : 0}, 500);
			});
		} else {
			toast_warning('Reservas','No se puede reservar en una fecha pasada');
			sphide('spin');
		}
		
	})


	
</script>
