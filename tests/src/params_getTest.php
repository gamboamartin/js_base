<?php
namespace gamboamartin\js_base\tests\src;

use gamboamartin\errores\errores;
use gamboamartin\js_base\base;
use gamboamartin\js_base\params_get;
use gamboamartin\test\liberator;
use gamboamartin\test\test;
use JetBrains\PhpStorm\NoReturn;
use stdClass;



class params_getTest extends test {
    public errores $errores;
    private stdClass $paths_conf;
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->errores = new errores();
        $this->paths_conf = new stdClass();
        $this->paths_conf->generales = '/var/www/html/cat_sat/config/generales.php';
        $this->paths_conf->database = '/var/www/html/cat_sat/config/database.php';
        $this->paths_conf->views = '/var/www/html/cat_sat/config/views.php';
    }

    #[NoReturn] public function test_get_session_id(): void
    {
        errores::$error = false;

        $pg = new params_get();
        //$base = new liberator($base);
        unset($_GET['session_id']);

        $resultado = $pg->get_session_id();

        $this->assertIsInt($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(-1, $resultado);

        errores::$error = false;

        $_GET['session_id'] = 10;

        $resultado = $pg->get_session_id();
        $this->assertIsInt($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(10, $resultado);

        errores::$error = false;


    }

    #[NoReturn] public function test_params_get_html(): void
    {
        errores::$error = false;

        $pg = new params_get();
        //$base = new liberator($base);

        $params_get = array();
        $params_get['a'] = 'x';
        $params_get['b'] = 'd';

        $resultado = $pg->params_get_html($params_get);
        $this->assertIsString($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("&a='+x&b='+d", $resultado);

        errores::$error = false;
    }

}

