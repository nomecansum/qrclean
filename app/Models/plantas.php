<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class plantas extends Model
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
    protected $table = 'plantas';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_planta';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'id_edificio',
                  'id_cliente',
                  'des_planta',
                  'img_plano',
                  'posiciones',
                  'num_orden',
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
     * Get the Edificio for this model.
     *
     * @return App\Models\Edificio
     */
    public function Edificio()
    {
        return $this->belongsTo('App\Models\Edificio','id_edificio','id_edificio');
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

    /**
     * Get the puestos for this model.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function puestos()
    {
        return $this->hasMany('App\Models\Puesto','id_planta','id_planta');
    }

    /**
     * Get the plantasUsuario for this model.
     *
     * @return App\Models\PlantasUsuario
     */
    public function plantasUsuario()
    {
        return $this->hasOne('App\Models\PlantasUsuario','id_planta','id_planta');
    }

    /**
     * Set the posiciones.
     *
     * @param  string  $value
     * @return void
     */
    public function setPosicionesAttribute($value)
    {
        $this->attributes['posiciones'] = json_encode($value);
    }

    /**
     * Get posiciones in array format
     *
     * @param  string  $value
     * @return array
     */
    public function getPosicionesAttribute($value)
    {
        return json_decode($value) ?: [];
    }

}
