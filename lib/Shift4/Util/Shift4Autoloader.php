<?php

namespace Shift4\Util;

/**
 * Implementation of autoloader that only loads classes from package "Shift4"
 *
 * Usage example:
 * <code>
 * require_once 'Shift4/Util/Shift4Autoloader.php';
 * \Shift4\Util\Shift4Autoloader::register();
 * </code>
 */
class Shift4Autoloader
{
    private $classPrefix = 'Shift4\\';
    private $baseDir;

    private function __construct()
    {
        $this->baseDir = realpath(__DIR__ . '/../../') . '/';

        spl_autoload_register([$this, 'autoload']);
    }

    public function autoload($class)
    {
        if (!$this->startsWithPrefix($class)) {
            return;
        }

        $file = $this->baseDir . str_replace('\\', '/', $class) . '.php';

        if (file_exists($file)) {
            require_once $file;
        }
    }

    private function startsWithPrefix($class)
    {
        $len = strlen($this->classPrefix);
        return strncmp($this->classPrefix, $class, $len) === 0;
    }

    public static function register()
    {
        new Shift4Autoloader();
    }
}
