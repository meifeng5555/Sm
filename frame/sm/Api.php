<?php
/**
 * @desc    RestApi基类
 * @date    2018-09-27
 * @author  meijinfeng
 */
namespace frame\sm;
use frame\sm\Hook;

class Api
{
    /**
     * RestApi合规请求类型
     * @var string
     */
    protected static $requestMethods = "Post|Get|Put|Patch|Delete";

    /**
     * 当前请求类型
     * @var string
     */
    protected $curMethod = "";

    /**
     * 当前指定的函数
     * @var string
     */
    protected $curFuc = "";

    public function __construct()
    {
        $this->checkMethods();
    }

    public function checkMethods()
    {
        $methods = $_SERVER['REQUEST_METHOD'];
        $methods = ucfirst(strtolower($methods));

        if (strstr(self::$requestMethods, $methods) === false) {
            $this->response("请求方式有误，合规请求方式：" . self::$requestMethods, 400);
        }

        $this->curMethod = $methods;
    }

    public function response($data, $code = 200, $type = "html")
    {
        if ("json" == $type) {
            $data = json_encode($data);
            header("Content-Type:application/json");
        } elseif ("html" == $type) {
            if (is_array($data) || is_object($data)) {
                $data = json_encode($data);
            }
            header("Content-Type:text/html;charset=utf-8");
        }

        http_response_code($code);

        echo $data;
        exit(0);
    }

    public function __call($name, $arguments)
    {
        $this->response("找不到Action：{$name}", 400);
    }
}