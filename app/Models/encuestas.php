<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class encuestas extends Model
{
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'encuestas';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_encuesta';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'id_cliente',
                  'titulo',
                  'pregunta',
                  'id_tipo_encuesta',
                  'fec_inicio',
                  'fec_fin',
                  'mca_activa',
                  'mca_anonima',
                  'list_perfiles',
                  'val_icono',
                  'val_color',
                  'token',
                  'list_puestos',
                  'list_plantas',
                  'list_edificios',
                  'val_momento',
                  'list_tags',
                  'val_periodo_minimo',
                  'list_tipos'
              ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [];
    
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];
    
    /**
     * Get the Cliente for this model.
     *
     * @return App\Models\Cliente
     */
    public function Cliente()
    {
        return $this->belongsTo('App\Models\Cliente','id_cliente','id_cliente');
    }

    /**
     * Get the EncuestasTipo for this model.
     *
     * @return App\Models\EncuestasTipo
     */
    public function EncuestasTipo()
    {
        return $this->belongsTo('App\Models\EncuestasTipo','id_tipo_encuesta','id_tipo_encuesta');
    }



}
