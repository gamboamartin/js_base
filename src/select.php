<?php
namespace gamboamartin\js_base;

use config\generales;
use gamboamartin\errores\errores;
use gamboamartin\js_base\eventos\adm_seccion;

class select{

    private errores $error;
    public function __construct(){
        $this->error = new errores();
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
    private function genera_options(string $option, string $entidad): string
    {
        return "$.each(data.registros, function( index, $entidad ) {
            $option;
        });";
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
        $value_option = $this->value_option(key_value: $key_value);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener value_option', data: $value_option);
        }

        $keys_descripcion_option = $this->keys_descripcion_option(keys: $keys);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener keys_descripcion_option', data: $keys_descripcion_option);
        }

        $new_option = $this->new_option(value_option: $value_option,keys_descripcion_option:  $keys_descripcion_option);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener new_option', data: $new_option);
        }
        $integra_new_option = $this->integra_new_option(id_css: $id_css);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener integra_new_option', data: $integra_new_option);
        }
        return $new_option.$integra_new_option;
    }

    private function option_default(string $descripcion, string $id_css){
        $new_option = $this->new_option(-1, $descripcion);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener new_option', data: $new_option);
        }
        $integra_new_option =$this->integra_new_option($id_css);
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

    private function refresca_select(string $id_css): string
    {
        $identificador = "$('#$id_css')";
        $js = "$identificador.val($id_css);";
        $js.= "$identificador.selectpicker('refresh');";
        return $js;
    }

    final public function select_change_exe(string $descripcion_default, string $entidad, string $id_css, array $keys){
        $limpia_select = $this->limpia_select($id_css);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener limpia_select', data: $limpia_select);
        }
        $ejecuta_error_ajax = (new ajax())->ejecuta_error_ajax();
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

    /**
     * Integra un value para select
     * @param string $key_value variable java a integrar
     * @return string
     *
     */
    private function value_option(string $key_value): string
    {
        return 'value = ${'.$key_value.'}';
    }

}