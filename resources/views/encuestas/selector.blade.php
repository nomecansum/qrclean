@switch($tipo)
    @case(1)
        <i class="fas fa-tired  fa-5x text-danger valor" data-value="1"></i>
        <i class="fas fa-frown  fa-5x text-warning valor" data-value="2"></i>
        <i class="fas fa-meh-rolling-eyes  fa-5x text-primary valor" data-value="3"></i>
        <i class="fas fa-smile  fa-5x text-mint valor" data-value="4"></i>
        <i class="fas fa-grin-alt fa-5x text-success valor" data-value="5"></i>
        
        @break
    @case(2)
        <i class="fas fa-frown  fa-5x text-danger valor" data-value="1"></i>
        <i class="fas fa-meh  fa-5x text-warning valor" data-value="2"></i>
        <i class="fas fa-smile fa-5x text-success valor" data-value="3"></i>
        @break
    @case(3)
        <i class="fal fa-star fa-5x valor" style="color: #ffd700" data-value="1"></i>
        <i class="fal fa-star fa-5x valor" style="color: #ffd700" data-value="2"></i>
        <i class="fal fa-star fa-5x valor" style="color: #ffd700" data-value="3"></i>
        <i class="fal fa-star fa-5x valor" style="color: #ffd700" data-value="4"></i>
        <i class="fal fa-star fa-5x valor" style="color: #ffd700" data-value="5"></i>
        @break
    @case(4)
        <table style="margin-left: auto; margin-right: auto;">
            <tr>
                @for($n=1;$n<=5;$n++)
                    <td><div class=" valor inline" style="font-size: 54px; padding: 10px; font-weight: bold" data-value="{{ $n }}">{{ $n }}</div></td>
                @endfor
            </tr>
        </table>
        @break
    @case(5)
            <table style="margin-left: auto; margin-right: auto;">
                <tr>
                    @for($n=1;$n<=10;$n++)
                        <td><div class=" valor inline" style="font-size: 44px; padding: 10px; font-weight: bold" data-value="{{ $n }}">{{ $n }}</div></td>
                    @endfor
                </tr>
            </table>
        @break
    
    @default
@endswitch
@if(isset($comentarios) && $comentarios=='S')
<div class="row">
    <div class="form-group col-md-12">
        <label for="comentario" class="control-label text-left">Comentarios</label>
        <textarea  class="textarea_editor form-control" name="comentario" id="comentario" rows="8" maxlength="5000" placeholder="Enter text ..."></textarea>
            
    </div>
</div>

@endif

{{--  <i class="fas fa-grin-alt fa-5x text-success"></i>
        <i class="fas fa-smile  fa-5x text-mint"></i>
        <i class="fas fa-meh-rolling-eyes  fa-5x text-primary"></i>
        <i class="fas fa-frown  fa-5x text-warning"></i>
        <i class="fas fa-tired  fa-5x text-danger"></i>

        <i class="fas fa-smile fa-5x text-success"></i>
        <i class="fas fa-meh  fa-5x text-warning"></i>
        <i class="fas fa-frown  fa-5x text-danger"></i>

        <i class="fas fa-star fa-5x" style="color: #ffd700"></i>
        <i class="fas fa-star fa-5x" style="color: #ffd700"></i>
        <i class="fas fa-star fa-5x" style="color: #ffd700"></i>
        <i class="fas fa-star fa-5x" style="color: #ffd700"></i>
        <i class="fas fa-star fa-5x" style="color: #ffd700"></i>  --}}