<?php
namespace gamboamartin\js_base;


use gamboamartin\errores\errores;

class params_get{

    private errores $error;
    public function __construct(){
        $this->error = new errores();
    }

    /**
     * Obtiene la session id por GET
     * @return int
     * @version 2.12.0
     */
    final public function get_session_id(): int
    {
        $session_id = -1;
        if(isset($_GET['session_id'])){
            $session_id = (int)$_GET['session_id'];
        }
        return $session_id;
    }

    /**
     * Integra parametros via GET  a url
     * @param array $params_get Parametros a incrustar en url de java
     * @return string|array
     * @version 2.25.0
     */
    final public function params_get_html(array $params_get): string|array
    {
        $params_get_html = '';
        foreach ($params_get as $key=>$val){
            $valida = (new valida())->valida_param_get(key: $key,val:  $val);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al validar params_get',data:  $valida);
            }

            $params_get_html.="&$key='+$val";
        }
        return $params_get_html;
    }

}