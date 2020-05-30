<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class rondas extends Model
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
    protected $table = 'rondas_limpieza';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_ronda';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'des_ronda',
                  'fec_ronda',
                  'user_asignado',
                  'user_creado'
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
        return $this->belongsTo('App\Models\User','user_creado','id');
    }

    /**
     * Get the puestosRonda for this model.
     *
     * @return App\Models\PuestosRonda
     */
    public function puestosRonda()
    {
        return $this->hasOne('App\Models\PuestosRonda','id_ronda','id_ronda');
    }



}
