<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class avisos extends Model
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
    protected $table = 'avisos';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_aviso';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'fec_fin',
                  'fec_inicio',
                  'id_cliente',
                  'mca_activo',
                  'txt_aviso',
                  'val_color',
                  'val_icono',
                  'val_perfiles',
                  'val_turnos',
                  'val_edificios',
                  'val_plantas',
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
    



}
