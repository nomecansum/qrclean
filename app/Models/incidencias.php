<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class incidencias extends Model
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
    protected $table = 'incidencias';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_incidencia';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'comentario_cierre',
                  'des_incidencia',
                  'fec_apertura',
                  'fec_cierre',
                  'id_causa_cierre',
                  'id_cliente',
                  'id_puesto',
                  'id_tipo_incidencia',
                  'id_usuario_apertura',
                  'id_usuario_cierre',
                  'img_attach1',
                  'img_attach2',
                  'txt_incidencia'
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
     * Get the IncidenciasTipo for this model.
     *
     * @return App\Models\IncidenciasTipo
     */
    public function IncidenciasTipo()
    {
        return $this->belongsTo('App\Models\IncidenciasTipo','id_tipo_incidencia','id_tipo_incidencia');
    }

    /**
     * Get the User for this model.
     *
     * @return App\Models\User
     */
    public function User()
    {
        return $this->belongsTo('App\Models\User','id_usuario_cierre','id');
    }



}
