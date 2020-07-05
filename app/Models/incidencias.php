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
    protected $primaryKey = 'id_incicencia';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'des_incidencia',
                  'txt_incidencia',
                  'id_usuario_apertura',
                  'id_usuario_cierre',
                  'fec_apertura',
                  'fec_cierre',
                  'id_tipo_incidencia',
                  'img_attach1',
                  'img_attach2',
                  'id_cliente',
                  'id_puesto',
                  'id_causa_cierre'
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
     * Get the User for this model.
     *
     * @return App\Models\User
     */
    public function User()
    {
        return $this->belongsTo('App\Models\User','id_usuario_cierre','id');
    }

    /**
     * Get the Cliente for this model.
     *
     * @return App\Models\Cliente
     */
    public function Cliente()
    {
        return $this->belongsTo('App\Models\Cliente','id_cliente','id_cliente');
    }



}
