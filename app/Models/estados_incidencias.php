<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class estados_incidencias extends Model
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
    protected $table = 'estados_incidencias';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_estado';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'des_estado',
                  'id_cliente',
                  'mca_cierre',
                  'mca_fijo',
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
     * Get the incidencias for this model.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function incidencias()
    {
        return $this->hasMany('App\Models\Incidencia','id_estado','id_estado');
    }



}
