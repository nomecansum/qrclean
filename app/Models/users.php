<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class users extends Model
{
    

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = true;


    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'cod_nivel',
                  'collapse',
                  'def_camera',
                  'email',
                  'email_expire_at',
                  'email_verified_at',
                  'id_cliente',
                  'id_edificio',
                  'id_externo',
                  'id_usuario_supervisor',
                  'img_usuario',
                  'last_login',
                  'list_puestos_preferidos',
                  'name',
                  'nivel_acceso',
                  'password',
                  'remember_token',
                  'theme',
                  'tipos_puesto_admitidos',
                  'token_acceso',
                  'token_expires',
                  'val_timezone',
                  'val_vista_puestos'
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
     * Get the incidenciasAcciones for this model.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function incidenciasAcciones()
    {
        return $this->hasMany('App\Models\IncidenciasAccione','id_usuario','id');
    }

    /**
     * Get the informesProgramados for this model.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function informesProgramados()
    {
        return $this->hasMany('App\Models\InformesProgramado','cod_usuario','id');
    }

    /**
     * Get the incidencias for this model.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function incidencias()
    {
        return $this->hasMany('App\Models\Incidencia','id_usuario_apertura','id');
    }

    /**
     * Get the plantasUsuarios for this model.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function plantasUsuarios()
    {
        return $this->hasMany('App\Models\PlantasUsuario','id_usuario','id');
    }

    /**
     * Get the puestos for this model.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function puestos()
    {
        return $this->hasMany('App\Models\Puesto','id_usuario_usando','id');
    }

    /**
     * Get the puestosAsignados for this model.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function puestosAsignados()
    {
        return $this->hasMany('App\Models\PuestosAsignado','id_usuario','id');
    }

    /**
     * Get the turnosUsuarios for this model.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function turnosUsuarios()
    {
        return $this->hasMany('App\Models\TurnosUsuario','id_usuario','id');
    }


    /**
     * Get created_at in array format
     *
     * @param  string  $value
     * @return array
     */
    public function getCreatedAtAttribute($value)
    {
        return \DateTime::createFromFormat($this->getDateFormat(), $value)->format('j/n/Y g:i A');
    }

    /**
     * Get updated_at in array format
     *
     * @param  string  $value
     * @return array
     */
    public function getUpdatedAtAttribute($value)
    {
        return \DateTime::createFromFormat($this->getDateFormat(), $value)->format('j/n/Y g:i A');
    }

}
