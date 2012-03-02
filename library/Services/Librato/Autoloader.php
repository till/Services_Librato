<?php
namespace Services\Librato;

class Autoloader
{
    public function register()
    {
        spl_autoload_register(array('\Services\Librato\Autoloader', 'load'));
    }

    public static function load($className)
    {
        if (strpos($className, 'Services\Librato\\') !== 0) {
            return false;
        }
        static $base;
        if ($base === null) {
            $base = dirname(dirname(__DIR__));
        }
        $file = str_replace('\\', '/', $className) . '.php';
        return include $base . '/' . $file;
    }
}
