<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class config_clientes extends Model
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
    protected $table = 'config_clientes';

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
                  'hora_liberar_puestos',
                  'max_dias_reserva',
                  'mca_liberar_puestos_auto',
                  'mca_limpieza',
                  'mca_mostrar_nombre_usando',
                  'mca_permitir_anonimo',
                  'mca_reserva_horas',
                  'mca_restringir_usuarios_planta',
                  'modo_visualizacion_puestos',
                  'modo_visualizacion_reservas',
                  'num_imagenes_incidencias',
                  'tam_qr',
                  'theme_name',
                  'theme_type',
                  'val_campo_puesto_mostrar',
                  'val_layout_incidencias',
                  'val_metodo_notificacion'
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
     * Get the Cliente for this model.
     *
     * @return App\Models\Cliente
     */
    public function Cliente()
    {
        return $this->belongsTo('App\Models\Cliente','id_cliente','id_cliente');
    }



}
