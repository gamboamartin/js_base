<?php
namespace gamboamartin\js_base;

use config\generales;
use gamboamartin\errores\errores;
use gamboamartin\js_base\eventos\adm_seccion;

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

    final public function params_get_html(array $params_get): string
    {
        $params_get_html = '';
        foreach ($params_get as $key=>$val){
            $params_get_html.="&$key='+$val";
        }
        return $params_get_html;
    }

}