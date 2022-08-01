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
                  'id_distribuidor',
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
     * Get the Distribuidore for this model.
     *
     * @return App\Models\Distribuidore
     */
    public function Distribuidore()
    {
        return $this->belongsTo('App\Models\Distribuidore','id_distribuidor','id_distribuidor');
    }

    /**
     * Get the causasCierres for this model.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function causasCierres()
    {
        return $this->hasMany('App\Models\CausasCierre','id_cliente','id_cliente');
    }

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
     * Get the configCliente for this model.
     *
     * @return App\Models\ConfigCliente
     */
    public function configCliente()
    {
        return $this->hasOne('App\Models\ConfigCliente','id_cliente','id_cliente');
    }

    /**
     * Get the encuestas for this model.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function encuestas()
    {
        return $this->hasMany('App\Models\Encuesta','id_cliente','id_cliente');
    }

    /**
     * Get the estadosIncidencias for this model.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function estadosIncidencias()
    {
        return $this->hasMany('App\Models\EstadosIncidencia','id_cliente','id_cliente');
    }

    /**
     * Get the incidencias for this model.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function incidencias()
    {
        return $this->hasMany('App\Models\Incidencia','id_cliente','id_cliente');
    }

    /**
     * Get the plantas for this model.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function plantas()
    {
        return $this->hasMany('App\Models\Planta','id_cliente','id_cliente');
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

    /**
     * Get the rondasLimpiezas for this model.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function rondasLimpiezas()
    {
        return $this->hasMany('App\Models\Ronda','id_cliente','id_cliente');
    }

    /**
     * Get the tags for this model.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function tags()
    {
        return $this->hasMany('App\Models\Tag','id_cliente','id_cliente');
    }

    /**
     * Get the incidenciasTipos for this model.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function incidenciasTipos()
    {
        return $this->hasMany('App\Models\IncidenciasTipo','id_cliente','id_cliente');
    }

    /**
     * Get the puestosTipos for this model.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function puestosTipos()
    {
        return $this->hasMany('App\Models\PuestosTipo','id_cliente','id_cliente');
    }



}
