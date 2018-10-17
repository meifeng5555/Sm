<?php
namespace frame\sm;

class Hook
{
    protected static $systemHook = [
        'appStart',
        'appEnd'
    ];

    public static $hook = [
        'appStart'  => [],
        'appEnd'    => []
    ];

    public static function isLegal($hookN)
    {
        return isset(self::$hook[$hookN]);
    }

    /**
     * @desc    新增普通Hook
     * @param   $hookN      hook名
     * @param   $hookV      执行方法
     * @param   $param      执行方法参数
     * @param   $unquine    唯一标识
     * @return bool
     */
    public static function register($hookN, $hookV, $param, $unquine)
    {
        if (!self::isLegal($hookN)) {
            return false;
        }

        if (is_numeric($unquine) || empty($unquine)) {
            return false;
        }

        if ($hookV instanceof \Closure) {
            return self::registerClosure($hookN, $hookV, $unquine);
        }

        if (!is_array($param)) {
            $param = [$param];
        }

        self::$hook[$hookN][$unquine]['func'] = $hookV;
        self::$hook[$hookN][$unquine]['args'] = $param;

        return true;
    }

    /**
     * @desc    新增闭包Hook
     * @param   $hookN      hook名
     * @param   $hookV      执行闭包
     * @param   $unquine    唯一标识
     * @return  bool
     */
    public static function registerClosure($hookN, \Closure $hookV, $unquine = null)
    {
        if (!self::isLegal($hookN)) {
            return false;
        }

        if (is_numeric($unquine)) {
            return false;
        }

        if ($unquine) {
            self::$hook[$hookN][$unquine] = $hookV;
        } else {
            self::$hook[$hookN][] = $hookV;
        }

        return true;
    }

    public static function add($hookN)
    {
        if (in_array($hookN, self::$systemHook)) {
            return false;
        }

        if (!isset(self::$hook[$hookN])) {
            self::$hook[$hookN] = [];
        }

        return true;
    }

    public static function trigger($hookN)
    {
        if (!is_array(self::$hook[$hookN])) {
            return false;
        }

        foreach (self::$hook[$hookN] as $func) {
            if ($func instanceof \Closure) {
                $func();
            } else {
                call_user_func_array($func['func'], $func['args']);
            }
        }

        return true;
    }
}
