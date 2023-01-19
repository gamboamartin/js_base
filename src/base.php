<?php
namespace gamboamartin\js_base;

use config\generales;

class base{
    /**
     * Genera una funcion de tipo java para obtener la url base de ejecucion
     * @return string
     * @version 2.5.0
     */
    final public function get_absolute_path(): string
    {
        $js = "<script>";
        $js .= "function get_absolute_path() {";
        $js .= "var loc = window.location;";
        $js.= "var pathName = loc.pathname.substring(0, loc.pathname.lastIndexOf('/') + 1);";
        $js .= "loc.href.substring(0, loc.href.length - ((loc.pathname + loc.search + loc.hash).length - pathName.length));";
        $js .= "}";
        $js .= "</script>";
        return $js;
    }

    final public function registro_id(): string
    {
        $registro_id = -1;
        if(isset($_GET['registro_id'])){
            $registro_id = $_GET['registro_id'];
        }

        $js = "<script>";
        $js .= "var REGISTRO_ID = '$registro_id';";
        $js .= "</script>";
        return $js;
    }

    /**
     * Genera como var la URL definida en config
     * @return string
     */
    final public function url_base(): string
    {
        $url = (new generales())->url_base;
        $js = "<script>";
        $js .= "var URL = '$url';";
        $js .= "</script>";
        return $js;
    }

}