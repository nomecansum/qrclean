<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class turnos extends Model
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
    protected $table = 'turnos';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id_turno';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'id_cliente',
                  'des_turno',
                  'dias_semana',
                  'fec_inicio',
                  'fec_fin',
                  'val_color',
                  'mod_semana'
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
     * Set the fec_inicio.
     *
     * @param  string  $value
     * @return void
     */
    public function setFecInicioAttribute($value)
    {
        $this->attributes['fec_inicio'] = !empty($value) ? \DateTime::createFromFormat('[% date_format %]', $value) : null;
    }

    /**
     * Set the fec_fin.
     *
     * @param  string  $value
     * @return void
     */
    public function setFecFinAttribute($value)
    {
        $this->attributes['fec_fin'] = !empty($value) ? \DateTime::createFromFormat('[% date_format %]', $value) : null;
    }

    /**
     * Get fec_inicio in array format
     *
     * @param  string  $value
     * @return array
     */
    public function getFecInicioAttribute($value)
    {
        return \DateTime::createFromFormat($this->getDateFormat(), $value)->format('j/n/Y g:i A');
    }

    /**
     * Get fec_fin in array format
     *
     * @param  string  $value
     * @return array
     */
    public function getFecFinAttribute($value)
    {
        return \DateTime::createFromFormat($this->getDateFormat(), $value)->format('j/n/Y g:i A');
    }

}
