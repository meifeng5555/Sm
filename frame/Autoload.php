<?php
/**
 * @desc    Rest自动加载
 *          支持PSR0，PSR4
 * @date    2018-09-27
 * @author  meijinfeng
 */

class Autoload
{
    /**
     * 文件后缀
     * @var string
     */
    private static $fileExt = ".php";

    public static function register()
    {
        spl_autoload_register(['Autoload', 'loadPsr4'], true, true);
    }

    public static function getRootPath()
    {
        if ("cli" == PHP_SAPI) {
            $scriptPath = realpath($_SERVER['argv'][0]);
        } else {
            $scriptPath = realpath($_SERVER['SCRIPT_FILENAME']);
        }

        $path = realpath(dirname($scriptPath));

        return $path . "\\";
    }

    public static function loadPsr4($class)
    {
        $rootPath = self::getRootPath();
        $classPath = $rootPath . $class;
        $classPath = str_replace("\\", "/", $classPath);
        $classPath = $classPath . self::$fileExt;

        self::requireClass($classPath);
    }

    public static function requireClass($class)
    {
        require_once $class;
    }
}