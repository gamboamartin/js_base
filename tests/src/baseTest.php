<?php
namespace gamboamartin\js_base\tests\src;

use gamboamartin\errores\errores;
use gamboamartin\js_base\base;
use gamboamartin\test\liberator;
use gamboamartin\test\test;
use JetBrains\PhpStorm\NoReturn;
use stdClass;



class baseTest extends test {
    public errores $errores;
    private stdClass $paths_conf;
    public function __construct(?string $name = null)
    {
        parent::__construct($name);
        $this->errores = new errores();
        $this->paths_conf = new stdClass();
        $this->paths_conf->generales = '/var/www/html/cat_sat/config/generales.php';
        $this->paths_conf->database = '/var/www/html/cat_sat/config/database.php';
        $this->paths_conf->views = '/var/www/html/cat_sat/config/views.php';
    }

    #[NoReturn] public function test_get_absolute_path(): void
    {
        errores::$error = false;

        $base = new base();
        $base = new liberator($base);
        $resultado = $base->get_absolute_path();


        $this->assertIsString($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("<script>function get_absolute_path() {var loc = window.location;var pathName = loc.pathname.substring(0, loc.pathname.lastIndexOf('/') + 1);loc.href.substring(0, loc.href.length - ((loc.pathname + loc.search + loc.hash).length - pathName.length));}</script>", $resultado);

        errores::$error = false;


    }



    #[NoReturn] public function test_get_val_selector_id(): void
    {
        errores::$error = false;

        $base = new base();
        $base = new liberator($base);
        $name_var = 'a';
        $selector = 'b';
        $resultado = $base->get_val_selector_id($name_var, $selector);
        $this->assertIsString($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("<script>var a = b.val()</script>", $resultado);

        errores::$error = false;
    }




}

