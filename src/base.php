<?php
namespace gamboamartin\js_base;

use config\generales;
use gamboamartin\errores\errores;

class base{

    private errores $error;
    public function __construct(){
        $this->error = new errores();
    }



    /**
     * Genera una funcion de tipo java para obtener la url base de ejecucion
     * @param bool $con_tag Integra tag script inicio
     * @return string
     * @version 2.5.0
     */
    final public function get_absolute_path(bool $con_tag = true): string
    {
        $js = "function get_absolute_path() {";
        $js .= "var loc = window.location;";
        $js.= "var pathName = loc.pathname.substring(0, loc.pathname.lastIndexOf('/') + 1);";
        $js .= "loc.href.substring(0, loc.href.length - ((loc.pathname + loc.search + loc.hash).length - pathName.length));";
        $js .= "}";
        if($con_tag){
            $js = "<script>$js</script>";
        }
        return $js;
    }

    /**
     * Obtiene la session id por GET
     * @return int
     * @version 2.12.0
     */
    private function get_session_id(): int
    {
        $session_id = -1;
        if(isset($_GET['session_id'])){
            $session_id = (int)$_GET['session_id'];
        }
        return $session_id;
    }

    /**
     * Asigna el valor de una variable de un selector
     * @param string $name_var Nombre de variable a asignar valor
     * @param string $selector identificador del selector proveniente de selector_id
     * @param bool $con_tag Integra tag script inicio
     * @return string
     * @version 
     */
    final public function get_val_selector_id(string $name_var, string $selector, bool $con_tag = true): string
    {

        $js= "var $name_var = $selector.val()";

        if($con_tag){
            $js = "<script>$js</script>";
        }

        return $js;
    }

    private function keys_descripcion_option(array $keys): string
    {
        $keys_js = '';
        foreach ($keys as $key){

            $keys_js = trim($keys_js);
            if($keys_js!==''){
                $keys_js.="+' '+";
            }
            $keys_js.=$key;
        }
        $keys_js.='';
        return '${'.$keys_js.'}';
    }
    private function integra_new_option(string $id_css): string
    {
        return '$(new_option).appendTo("#'.$id_css.'");';
    }


    private function new_option(string $value_option, string $keys_descripcion_option): string
    {
        return "let new_option = `<option $value_option >$keys_descripcion_option</option>`;";
    }

    final public function option(string $key_value, array $keys, string $id_css): string
    {
        $value_option = $this->value_option($key_value);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener value_option', data: $value_option);
        }

        $keys_descripcion_option = $this->keys_descripcion_option($keys);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener keys_descripcion_option', data: $keys_descripcion_option);
        }

        $new_option = $this->new_option($value_option, $keys_descripcion_option);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener new_option', data: $new_option);
        }
        $integra_new_option = $this->integra_new_option($id_css);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener integra_new_option', data: $integra_new_option);
        }
        return $new_option.$integra_new_option;
    }

    /**
     * Integra var registro id java
     * @param bool $con_tag
     * @return string
     */
    final public function registro_id(bool $con_tag = true): string
    {
        $registro_id = -1;
        if(isset($_GET['registro_id'])){
            $registro_id = $_GET['registro_id'];
        }

        $js = "var REGISTRO_ID = '$registro_id';";

        if($con_tag){
            $js = "<script>$js</script>";
        }

        return $js;
    }

    final public function selector_id(string $id_css, bool $con_tag = true): string
    {
        $name_selector = "sl_$id_css";
        $selector = "let $name_selector = $('#$id_css');";
        $js = $selector;
        if($con_tag){
            $js = "<script>$js</script>";
        }

        return $js;
    }

    final public function session_id(bool $con_tag = true): string|array
    {

        $session_id = $this->get_session_id();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener session_id', data: $session_id);
        }

        $js = "var SESSION_ID = '$session_id';";

        if($con_tag){
            $js = "<script>$js</script>";
        }

        return $js;
    }

    /**
     * Genera como var la URL definida en config
     * @param bool $con_tag
     * @return string
     */
    final public function url_base(bool $con_tag = true): string
    {
        $url = (new generales())->url_base;

        $js = "var URL = '$url';";

        if($con_tag){
            $js = "<script>$js</script>";
        }

        return $js;
    }

    final public function url_para_ajax(string $accion, array $params_get, string $seccion, bool $ws, bool $con_tag = true){
        $ws_exe = '';
        if($ws){
            $ws_exe = "&ws=1";
        }
        $session_id = $this->get_session_id();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener session_id', data: $session_id);
        }

        $params_get_html = '';
        foreach ($params_get as $key=>$val){
            $params_get_html.="&$key='+$val";
        }
        $js = "var url = 'index.php?seccion=$seccion&accion=$accion&session_id=$session_id$ws_exe$params_get_html";
        if($con_tag){
            $js = "<script>$js</script>";
        }
        return $js;
    }

    private function value_option(string $key_value): string
    {
        return "value = $key_value";
    }





}