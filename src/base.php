<?php
namespace gamboamartin\js_base;

use config\generales;

class base{
    /**
     * Genera una funcion de tipo java para obtener la url base de ejecucion
     * @return string
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

    final public function url_base(): string
    {
        $url = (new generales())->url_base;
        $js = "<script>";
        $js .= "cons URL = $url;";
        $js .= "</script>";
        return $js;
    }

}