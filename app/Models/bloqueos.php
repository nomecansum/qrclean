<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class bloqueos extends Model
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
    protected $table = 'bloqueo_programado';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_bloqueo';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'des_motivo',
                  'fec_fin',
                  'fec_inicio',
                  'id_turno',
                  'usu_audit',
                  'id_cliente',
                  'list_plantas',
                  'list_edificios',
                  'list_puestos',
                  'list_tags',
                  'list_estados',
                  'list_tipos'
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
