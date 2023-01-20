<?php
namespace gamboamartin\js_base\eventos;

use gamboamartin\errores\errores;
use gamboamartin\js_base\base;

class adm_accion{
    private errores $error;
    private string $seccion = 'adm_accion';
    private string $name_base = 'Accion';
    public function __construct(){
        $this->error = new errores();
    }
    final public  function get_adm_accion(): string
    {
        $funcion = __FUNCTION__;
        $keys = array();
        $keys[] = $this->seccion.'.adm_menu_descripcion';
        $keys[] = $this->seccion.'.adm_seccion_descripcion';
        $keys[] = $this->seccion.'.adm_accion_descripcion';

        $change_select = (new base())->change_select(accion: $funcion, descripcion_default: "Selecciona una $this->name_base",
            keys: $keys, params_get: array('adm_seccion_id'=>'adm_seccion_id'), seccion: $this->seccion, type: 'GET', ws: true);

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener change_select', data: $change_select);
        }

        return "function $funcion(adm_seccion_id = ''){".$change_select."}";
    }
}
