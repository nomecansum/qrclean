<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class incidencias_tipos extends Model
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
    protected $table = 'incidencias_tipos';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_tipo_incidencia';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'des_tipo_incidencia',
                  'id_cliente',
                  'id_estado_inicial',
                  'mca_fijo',
                  'param_url',
                  'tip_metodo',
                  'txt_destinos',
                  'val_apikey',
                  'val_body',
                  'val_color',
                  'val_content_type',
                  'val_icono',
                  'val_url'
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
     * Get the EstadosIncidencia for this model.
     *
     * @return App\Models\EstadosIncidencia
     */
    public function EstadosIncidencia()
    {
        return $this->belongsTo('App\Models\EstadosIncidencia','id_estado_inicial','id_estado');
    }

    /**
     * Get the incidencias for this model.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function incidencias()
    {
        return $this->hasMany('App\Models\Incidencia','id_tipo_incidencia','id_tipo_incidencia');
    }



}
