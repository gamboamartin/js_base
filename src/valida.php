<?php
namespace gamboamartin\js_base;

use gamboamartin\validacion\validacion;

class valida extends validacion {


    final public function valida_param_get(mixed $key, mixed $val): bool|array
    {
        if(is_numeric($key)){
            return $this->error->error(mensaje: 'Error key de param debe ser un texto valido',data:  $key);
        }
        $key = trim($key);
        if($key === ''){
            return $this->error->error(mensaje: 'Error key esta vacio',data:  $key);
        }

        $val = trim($val);
        if($val === ''){
            return $this->error->error(mensaje: 'Error val esta vacio',data:  $val);
        }
        return true;

    }



}