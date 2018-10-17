<?php
namespace frame\sm;
use frame\sm\Sm;
use frame\sm\Config;

class Route
{
    /**
     * 请求的根目录
     * @var string
     */
    public static $requestDomain = "";

    /**
     * 请求的Url
     * @var string
     */
    public static $requestUrl = "";

    /**
     * 请求的模块
     * @var string
     */
    public static $requestModule = "";

    /**
     * 请求的控制器
     * @var string
     */
    public static $requestController = "";

    /**
     * 请求的方法
     * @var string
     */
    public static $requestAction = "";

    /**
     * 请求的参数
     * @var array
     */
    public static $requestParam = [];

    public function __construct()
    {
        $this->setDomain();
        $this->setUrl();
        $this->parseUrl();
        $this->preDispatch();
        $this->dispatch();
    }

    public function setDomain()
    {
        $domain = $_SERVER['SCRIPT_NAME'];
        $domain = substr($domain, 0, strripos($domain, "/"));

        self::$requestDomain = $domain;
    }

    public function setUrl()
    {
        $url = $_SERVER['REQUEST_URI'];
        $url = str_replace(self::$requestDomain, "", $url);
        $url = str_replace("/index.php", "", $url);
        $url = substr($url, 0, strpos($url, "?") ?: strlen($url));

        self::$requestUrl = $url;
    }

    public function parseUrl()
    {
        $config = (new Config(APP_PATH . "config.ini"))->getIni();

        if (in_array(self::$requestUrl, ["/", "//", ""])) {
            self::$requestModule = $config[APP_ENV]['default']['module'];
            self::$requestController = $config[APP_ENV]['default']['controller'];
            self::$requestAction = $config[APP_ENV]['default']['action'];
            return true;
        }

        $parseUrl = explode("/", self::$requestUrl);

        $module = $parseUrl[0] ?: $parseUrl[1];
        $module = $this->checkModule($module);

        $controller = str_replace($module, "", self::$requestUrl);
        $controller = str_replace("//", "/", $controller);

        if (in_array($controller, ["/", "//", ""])) {
            self::$requestModule = $module;
            self::$requestController = $config[APP_ENV]['default']['controller'];
            self::$requestAction = $config[APP_ENV]['default']['action'];
            return true;
        }

        $controllerAndAction = $this->checkController($module, $controller);

        $controller = $controllerAndAction['controller'];

        $action = $controllerAndAction['action'] ?: $config[APP_ENV]['default']['action'];

        self::$requestModule = $module;
        self::$requestController = $controller;
        self::$requestAction = $action;

    }

    public function checkModule($module)
    {
        if (in_array($module, Sm::getModuleArr()) === false) {
            exit("模块：{$module}不存在");
        }

        return $module;
    }

    public function checkController($module, $controller)
    {
        $reportCode = $module . $controller;
        $controllerArr = Sm::getControllerArr($module);

        if (!empty($controller)) {
            Sm::setControllerArr($module);
            $controllerArr = Sm::getControllerArr($module);
        }

        $controller = (substr($controller, -1) == "/") ? substr($controller, 0, -1) : $controller;
        $controller = (substr($controller, 0, 1) == "/") ? substr($controller, 1) : $controller;

        // 不带Action的url
        $controller = $controller . ".php";

        if (in_array($controller, $controllerArr)) {
            $controller = str_replace(".php", "", $controller);
            return [
                'controller' => $controller,
                'action'     => ''
            ];
        } else {
            // 带Action的url
            $tempArr = explode("/", $controller);
            $action = $tempArr[count($tempArr) - 1] ?: $tempArr[count($tempArr) - 2];
            $action = str_replace(".php", "", $action);
            $controller = str_replace("/" . $action, "", $controller);

            if (in_array($controller, $controllerArr)) {
                $controller = str_replace(".php", "", $controller);
                return [
                    'controller' => $controller,
                    'action'     => $action
                ];
            } else {
                exit("控制器：{$reportCode}不存在");
            }
        }
    }

    public function preDispatch()
    {

    }

    public function dispatch()
    {
        $class = "app\\" . self::$requestModule . "\\controller\\" . self::$requestController;
        (new $class)->{self::$requestAction}();
    }
}