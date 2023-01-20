<?php
namespace gamboamartin\js_base;

use gamboamartin\errores\errores;

class ajax{

    private errores $error;
    public function __construct(){
        $this->error = new errores();
    }

    final public function done(string $descripcion_default, string $entidad, array $keys){

        $id_css = $entidad.'_id';
        $select_change_exe = (new select())->select_change_exe(descripcion_default: $descripcion_default, entidad: $entidad,
            id_css: $id_css, keys:  $keys);

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener select_change_exe', data: $select_change_exe);
        }


        return '.done(function( data ) {'.$select_change_exe.'})';
    }

    final public function ejecuta_error_ajax(){
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

    private function exe_error(): string
    {
        return "
        alert('Error al ejecutar');
        console.log('The following error occured: '+ textStatus +' '+ errorThrown);";
    }

    private function error_ajax(): string
    {
        return "let msj = data.mensaje_limpio+' '+url;
                alert(msj);
                console.log(data);
                return false;";
    }

    final public function fail(){
        $exe_error = $this->exe_error();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener exe_error', data: $exe_error);
        }
        return '.fail(function (jqXHR, textStatus, errorThrown){'.$exe_error.'})';
    }

    final public function params_ajax(string $accion, array $params_get, string $seccion, string $type, bool $ws){
        $url  = $this->url(accion: $accion, params_get: $params_get,seccion:  $seccion,ws:  $ws);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener url', data: $url);
        }
        return "type: '$type',
                url: $url,";
    }

    /**
     * Integra una url para ajax
     * @param string $accion Accion a ejecutar
     * @param array $params_get Parametros extra para get
     * @param string $seccion Seccion en ejecucion
     * @param bool $ws Si true da salida json
     * @return array|string
     */
    final public function url(string $accion, array $params_get, string $seccion, bool $ws): array|string
    {
        $session_id = (new params_get())->get_session_id();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener session_id', data: $session_id);
        }
        $ws_exe=$this->ws_exe(ws: $ws);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener ws_exe', data: $ws_exe);
        }

        $params_get_html = (new params_get())->params_get_html(params_get: $params_get);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener params_get_html', data: $params_get_html);
        }

        return "'index.php?seccion=$seccion&accion=$accion&session_id=$session_id$ws_exe$params_get_html";
    }

    /**
     * Integra la variable por GET ws
     * @param bool $ws si ws integra  var GET
     * @return string
     * @version 2.22.0
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