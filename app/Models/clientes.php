<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class clientes extends Model
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
    protected $table = 'clientes';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_cliente';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'CIF',
                  'cod_tipo_cliente',
                  'fec_borrado',
                  'img_logo',
                  'img_logo_menu',
                  'locked',
                  'mca_appmovil',
                  'mca_vip',
                  'nom_cliente',
                  'nom_contacto',
                  'tel_cliente',
                  'token_1uso',
                  'val_apikey'
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
     * Get the edificios for this model.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function edificios()
    {
        return $this->hasMany('App\Models\Edificio','id_cliente','id_cliente');
    }

    /**
     * Get the plantas for this model.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function plantas()
    {
        return $this->hasMany('App\Models\Plantum','id_cliente','id_cliente');
    }

    /**
     * Get the puestos for this model.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function puestos()
    {
        return $this->hasMany('App\Models\Puesto','id_cliente','id_cliente');
    }



}
