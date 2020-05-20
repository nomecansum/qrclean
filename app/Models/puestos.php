<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class puestos extends Model
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
    protected $table = 'puestos';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_puesto';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'cod_puesto',
                  'des_puesto',
                  'id_edificio',
                  'planta',
                  'id_estado',
                  'id_cliente',
                  'val_color',
                  'val_icono'
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



}
