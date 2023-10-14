<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class incidencias_postprocesado extends Model
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
    protected $table = 'incidencias_postprocesado';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_proceso';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'id_tipo_incidencia',
                  'tip_metodo',
                  'val_url',
                  'param_url',
                  'val_body',
                  'val_header',
                  'val_respuesta',
                  'txt_destinos',
                  'mca_api',
                  'mca_web',
                  'mca_salas',
                  'mca_scan',
                  'val_momento',
                  'mca_abriente',
                  'mca_implicados'
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
     * Get the IncidenciasTipo for this model.
     *
     * @return App\Models\IncidenciasTipo
     */
    public function IncidenciasTipo()
    {
        return $this->belongsTo('App\Models\IncidenciasTipo','id_tipo_incidencia','id_tipo_incidencia');
    }



}
