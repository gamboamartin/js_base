<?php
namespace gamboamartin\js_base;

use config\generales;
use gamboamartin\errores\errores;
use gamboamartin\js_base\eventos\adm_accion;
use gamboamartin\js_base\eventos\adm_seccion;

class base{

    private errores $error;
    public function __construct(){
        $this->error = new errores();
    }

    private function get_adm_accion(): string
    {
        $funcion = __FUNCTION__;
        $evento = (new adm_accion())->$funcion();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener evento', data: $evento);
        }
        return $evento;
    }

    /**
     * Obtiene las secciones en base el adm_menu_id
     * @return string
     */
    private function get_adm_seccion(): string
    {
        $funcion = __FUNCTION__;
        $evento = (new adm_seccion())->$funcion();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener evento', data: $evento);
        }
        return $evento;
    }

    final public function change_select(string $accion, string $descripcion_default, array $keys,
                                        array $params_get, string $seccion, string $type, bool $ws){

        $params_ajax = (new ajax())->params_ajax(accion: $accion, params_get: $params_get,
            seccion: $seccion, type: $type, ws: $ws);

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener params_ajax', data: $params_ajax);
        }

        $done = (new ajax)->done(descripcion_default: $descripcion_default, entidad: $seccion, keys:  $keys);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener done', data: $done);
        }

        $fail = (new ajax)->fail();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener fail', data: $fail);
        }

        return '$.ajax({'.$params_ajax.'})'.$done.$fail.';';
    }

    private function debug(bool $debug_alert, bool $debug_console, string $name_var, string $file, int $line, string $type_var, string $value){
        $debug_console_js = $this->debug_console_js(debug_console: $debug_console,file:  $file, line: $line,
            name_var:  $name_var, type_var: $type_var, value: $value);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener debug_console_js', data: $debug_console_js);
        }

        $debug_alert_js = $this->debug_alert_js(debug_alert: $debug_alert,file:  $file, line: $line,
            name_var:  $name_var, type_var: $type_var, value: $value);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener debug_alert_js', data: $debug_alert_js);
        }

        return $debug_alert_js.$debug_console_js;
    }

    private function debug_alert_js(bool $debug_alert, string $file, int $line, string $name_var,
                                      string $type_var, string $value){
        $debug_debug_alert_js = '';
        if($debug_alert) {
            $mensaje = $this->mensaje_debug(file: $file,line:  $line,name_var:  $name_var,type_var:  $type_var,value:  $value);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener mensaje', data: $mensaje);
            }
            $debug_debug_alert_js = "alert($mensaje);";
        }
        return $debug_debug_alert_js;
    }

    private function debug_console_js(bool $debug_console, string $file, int $line, string $name_var,
                                      string $type_var, string $value){
        $debug_console_js = '';
        if($debug_console) {
            $mensaje = $this->mensaje_debug(file: $file,line:  $line,name_var:  $name_var,type_var:  $type_var,value:  $value);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener mensaje', data: $mensaje);
            }
            $debug_console_js = "console.log($mensaje);";
        }
        return $debug_console_js;
    }


    /**
     * Integra el evento a ejecutar via java
     * @param string $event Evento a ejecutar
     * @param string $key_parent_id Key id a integrar
     * @param bool $debug_console integra valor in console
     * @param bool $debug_alert integra alert en ejecucion
     * @return string
     */
    private function exe_change(string $event, string $key_parent_id, bool $debug_alert = false,
                                bool $debug_console = false): string
    {

        $debug = $this->debug(debug_alert: $debug_alert,debug_console:  $debug_console,name_var: 'event',
            file: __FILE__, line: __LINE__, type_var: 'PHP',value: $event);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener debug', data: $debug);
        }

        return "$key_parent_id = $(this).val();$debug
        $event($key_parent_id);";

    }



    /**
     * Genera una funcion de tipo java para obtener la url base de ejecucion
     * @param bool $con_tag Integra tag script inicio
     * @return string
     * @version 2.5.0
     */
    private function get_absolute_path(bool $con_tag = true): string
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
     * @return string|array
     * @version 2.15.0
     */
    private function get_val_selector_id(string $name_var, string $selector, bool $con_tag = true): string|array
    {
        $name_var = trim($name_var);
        if($name_var === ''){
            return $this->error->error(mensaje: 'Error name_var esta vacio', data: $name_var);
        }
        $selector = trim($selector);
        if($selector === ''){
            return $this->error->error(mensaje: 'Error selector esta vacio', data: $selector);
        }
        $js= "var $name_var = $selector.val()";

        if($con_tag){
            $js = "<script>$js</script>";
        }

        return $js;
    }


    private function mensaje_debug(string $file, int $line, string $name_var, string $type_var, string $value): string
    {
        return "'File: $file, Line: $line, name var $name_var, Type var = $type_var, value: $value'";
    }



    /**
     * Integra var registro id java
     * @param bool $con_tag
     * @return string
     */
    private function registro_id(bool $con_tag = true): string
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


    private function sl_exe_change(string $event, string $key_parent_id, bool $debug_alert = false,
                                   bool $debug_console = false){
        $exe_change = $this->exe_change(event: $event, key_parent_id: $key_parent_id,
            debug_alert: $debug_alert,debug_console: $debug_console);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener exe_change', data: $exe_change);
        }
        $identificador = "$('#$key_parent_id')";

        return "$identificador.change(function(){".$exe_change."});";
    }

    final public function sl_exe_change_ajax(string $event, string $key_parent_id, bool $con_tag = true,
                                             bool $debug_alert = false, bool $debug_console = false){


        $debug_console_js = '';
        $debug_alert_js = '';
        if($debug_console) {
            $data = "'variable php: event: '+".$event;
            $debug_console_js = "console.log($data);";
        }
        if($debug_alert) {
            $data = "'variable php: event: '+".$event;
            $debug_alert_js = "alert($data);";
        }

        $adm_asigna_secciones = $this->$event();

        $sl_exe_change = $this->sl_exe_change(event: $event, key_parent_id: $key_parent_id,
            debug_alert: $debug_alert, debug_console: $debug_console);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener sl_exe_change', data: $sl_exe_change);
        }

        $js = $sl_exe_change.$adm_asigna_secciones.$debug_console_js.$debug_alert_js;

        if($con_tag){
            $js = "<script>$js</script>";
        }
        return $js;
    }

    private function selector_id(string $id_css, bool $con_tag = true): string
    {
        $name_selector = "sl_$id_css";
        $selector = "let $name_selector = $('#$id_css');";
        $js = $selector;
        if($con_tag){
            $js = "<script>$js</script>";
        }

        return $js;
    }

    private function session_id(bool $con_tag = true): string|array
    {

        $session_id = (new params_get())->get_session_id();
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
    private function url_base(bool $con_tag = true): string
    {
        $url = (new generales())->url_base;

        $js = "var URL = '$url';";

        if($con_tag){
            $js = "<script>$js</script>";
        }

        return $js;
    }

    private function url_para_ajax(string $accion, array $params_get, string $seccion, bool $ws, bool $con_tag = true){


        $url  = (new ajax())->url(accion: $accion, params_get: $params_get,seccion:  $seccion,ws:  $ws);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener url', data: $url);
        }

        $js = "var url = '$url";
        if($con_tag){
            $js = "<script>$js</script>";
        }
        return $js;
    }




}