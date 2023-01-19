<?php
namespace gamboamartin\js_base;

use config\generales;

class base{
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
     * Asigna el valor de una variable de un selector
     * @param string $name_var Nombre de variable a asignar valor
     * @param string $selector identificador del selector proveniente de selector_id
     * @param bool $con_tag Integra tag script inicio
     * @return string
     */
    final public function get_val_selector_id(string $name_var, string $selector, bool $con_tag = true): string
    {

        $js= "var $name_var = $selector.val()";

        if($con_tag){
            $js = "<script>$js</script>";
        }

        return $js;
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

    final public function session_id(bool $con_tag = true): string
    {
        $session_id = -1;
        if(isset($_GET['session_id'])){
            $session_id = $_GET['session_id'];
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

}