<?php
namespace frame\sm;
use frame\sm\Route;
use frame\sm\Error;
use frame\sm\Hook;

class Sm
{
    protected static $moduleArr = [];

    protected static $controllerArr = [];

    public function __construct()
    {
        $this->appStart = true;
        $this->setModuleArr();
    }

    public function setModuleArr()
    {
        $pack = scandir(APP_PATH);

        foreach ($pack as $path) {

            if (in_array($path, [".", ".."])) {
                continue;
            }

            if (is_dir(APP_PATH . $path)) {
                self::$moduleArr[$path] = $path;
            }

        }
    }

    public static function setControllerArr($module, $sub = null)
    {
        $prefix = $sub ? APP_PATH . $module . "/controller/" . $sub ."/" : APP_PATH . $module . "/controller/";
        $pack = scandir($prefix);

        foreach ($pack as $path) {

            if (in_array($path, [".", ".."])) {
                continue;
            }

            if (is_file($prefix . $path)
                && strstr($path, ".php")) {
                self::$controllerArr[$module][] = strtolower($sub ? $sub . "/" . $path : $path);
            }

            if (is_dir($prefix . $path)) {
                if ($sub) {
                    return self::setControllerArr($module, $sub . "/" .$path);
                } else {
                    return self::setControllerArr($module, $path);
                }
            }

        }
    }

    public static function getModuleArr()
    {
        return self::$moduleArr;
    }

    public static function getControllerArr($module)
    {
        return self::$controllerArr[$module];
    }

    public function run()
    {
        //  触发Hook
        Hook::trigger("appStart");

        // 启动异常拦截
        new Error();

        // 启动路由
        new Route();
    }

    public function __destruct()
    {
        // 触发Hook
        Hook::trigger("appEnd");
    }
}