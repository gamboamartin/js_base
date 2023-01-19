<?php
namespace gamboamartin\js_base;

use config\generales;
use gamboamartin\errores\errores;
use gamboamartin\js_base\eventos\adm_seccion;

class base{

    private errores $error;
    public function __construct(){
        $this->error = new errores();
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

    final public function change_select(string $accion, string $descripcion_default, array $keys, array $params_get, string $seccion, string $type, bool $ws){

        $params_ajax = $this->params_ajax(accion: $accion, params_get: $params_get,
            seccion: $seccion, type: $type, ws: $ws);

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener params_ajax', data: $params_ajax);
        }

        $done = $this->done(descripcion_default: $descripcion_default, entidad: $seccion, keys:  $keys);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener done', data: $done);
        }

        $fail = $this->fail();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener fail', data: $fail);
        }

        return '$.ajax({'.$params_ajax.'})'.$done.$fail.';';
    }

    private function done(string $descripcion_default, string $entidad, array $keys){

        $id_css = $entidad.'_id';
        $select_change_exe = $this->select_change_exe(descripcion_default: $descripcion_default, entidad: $entidad,
            id_css: $id_css, keys:  $keys);

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener select_change_exe', data: $select_change_exe);
        }


        return '.done(function( data ) {'.$select_change_exe.'})';
    }

    private function ejecuta_error_ajax(){
        $error_ajax = $this->error_ajax();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener error_ajax', data: $error_ajax);
        }

        return "if(!isNaN(data.error)){
            if(data.error === 1){
                $error_ajax;
            }
        }";
    }

    private function ejecuta_options(string $descripcion_default, string $entidad, string $id_css, string $key_value, array $keys){
        $options_data = $this->integra_options($entidad, $id_css, $key_value,$keys);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener options_data', data: $options_data);
        }
        $option_default = $this->option_default($descripcion_default, $id_css);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener option_default', data: $option_default);
        }

        return $option_default.$options_data;
    }


    private function error_ajax(): string
    {
        return "let msj = data.mensaje_limpio+' '+url;
                alert(msj);
                console.log(data);
                return false;";
    }

    /**
     * Integra el evento a ejecutar via java
     * @param string $event Evento a ejecutar
     * @param string $key_parent_id Key id a integrar
     * @return string
     */
    private function exe_change(string $event, string $key_parent_id): string
    {
        return "$key_parent_id = $(this).val();
        $event($key_parent_id);";

    }

    private function exe_error(): string
    {
        return "
        alert('Error al ejecutar');
        console.log('The following error occured: '+ textStatus +' '+ errorThrown);";
    }

    private function fail(){
        $exe_error = $this->exe_error();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener exe_error', data: $exe_error);
        }
        return '.fail(function (jqXHR, textStatus, errorThrown){'.$exe_error.'})';
    }

    private function genera_options(string $option, string $entidad): string
    {
        return "$.each(data.registros, function( index, $entidad ) {
            $option;
        });";
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

    private function integra_new_option(string $id_css): string
    {
        return '$(new_option).appendTo("#'.$id_css.'");';
    }

    private function integra_options(string $entidad,string $id_css, string $key_value, array $keys){
        $option = $this->option($key_value, $keys, $id_css);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener option', data: $option);
        }

        $options = $this->genera_options($option, $entidad);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener options', data: $options);
        }
        return $options;
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

    /**
     * Limpia un elemento via id de css
     * @param string $id_css Identificador css a limpiar
     * @return string
     */
    private function limpia_select(string $id_css): string
    {
        $identificador = "$('#$id_css')";
        return "$identificador.empty();";
    }



    private function new_option(string $value_option, string $keys_descripcion_option): string
    {
        return "let new_option = `<option $value_option >$keys_descripcion_option</option>`;";
    }

    private function option(string $key_value, array $keys, string $id_css): string
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

    private function options(string $descripcion_default, string $entidad, string $id_css, array $keys){

        $limpia = $this->limpia_select($id_css);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener limpia', data: $limpia);
        }

        $key_value = "$entidad.$id_css";
        $ejecuta_options = $this->ejecuta_options($descripcion_default, $entidad, $id_css, $key_value, $keys);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener ejecuta_options', data: $ejecuta_options);
        }
        $refresca_select = $this->refresca_select($id_css);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener refresca_select', data: $refresca_select);
        }

        $options = $limpia.$ejecuta_options.$refresca_select;
        return $options;
    }

    private function option_default(string $descripcion, string $id_css){
        $new_option = $this->new_option(-1, $descripcion);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener new_option', data: $new_option);
        }
        $integra_new_option = $this->integra_new_option($id_css);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener integra_new_option', data: $integra_new_option);
        }
        return $new_option.$integra_new_option;

    }

    private function params_ajax(string $accion, array $params_get, string $seccion, string $type, bool $ws){
        $url  = $this->url(accion: $accion, params_get: $params_get,seccion:  $seccion,ws:  $ws);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener url', data: $url);
        }
        return "type: '$type',
                url: $url,";
    }

    private function params_get_html(array $params_get): string
    {
        $params_get_html = '';
        foreach ($params_get as $key=>$val){
            $params_get_html.="&$key='+$val";
        }
        return $params_get_html;
    }

    private function refresca_select(string $id_css): string
    {
        $identificador = "$('#$id_css')";
        $js = "$identificador.val($id_css);";
        $js.= "$identificador.selectpicker('refresh');";
        return $js;
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
    private function select_change_exe(string $descripcion_default, string $entidad, string $id_css, array $keys){
        $limpia_select = $this->limpia_select($id_css);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener limpia_select', data: $limpia_select);
        }
        $ejecuta_error_ajax = $this->ejecuta_error_ajax();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener ejecuta_error_ajax', data: $ejecuta_error_ajax);
        }
        $options = $this->options(descripcion_default: $descripcion_default, entidad: $entidad,
            id_css: $id_css, keys:  $keys);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener options', data: $options);
        }

        return $options;

    }

    private function sl_exe_change(string $event, string $key_parent_id){
        $exe_change = $this->exe_change(event: $event, key_parent_id: $key_parent_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener exe_change', data: $exe_change);
        }
        $identificador = "$('#$key_parent_id')";

        return "$identificador.change(function(){".$exe_change."});";
    }

    final public function sl_exe_change_ajax(string $event, string $key_parent_id, bool $con_tag = true){

        $adm_asigna_secciones = $this->$event();

        $sl_exe_change = $this->sl_exe_change(event: $event, key_parent_id: $key_parent_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener sl_exe_change', data: $sl_exe_change);
        }

        $js = $sl_exe_change.$adm_asigna_secciones;

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

    private function url(string $accion, array $params_get, string $seccion, bool $ws){
        $session_id = $this->get_session_id();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener session_id', data: $session_id);
        }
        $ws_exe=$this->ws_exe(ws: $ws);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener ws_exe', data: $ws_exe);
        }

        $params_get_html = $this->params_get_html(params_get: $params_get);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener params_get_html', data: $params_get_html);
        }

        return "'index.php?seccion=$seccion&accion=$accion&session_id=$session_id$ws_exe$params_get_html";
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


        $url  = $this->url(accion: $accion, params_get: $params_get,seccion:  $seccion,ws:  $ws);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener url', data: $url);
        }

        $js = "var url = '$url";
        if($con_tag){
            $js = "<script>$js</script>";
        }
        return $js;
    }

    private function value_option(string $key_value): string
    {
        return "value = $key_value";
    }

    /**
     * Integra la variable por GET ws
     * @param bool $ws si ws integra  var GET
     * @return string
     */
    private function ws_exe(bool $ws): string
    {
        $ws_exe = '';
        if($ws){
            $ws_exe = "&ws=1";
        }
        return $ws_exe;
    }





}