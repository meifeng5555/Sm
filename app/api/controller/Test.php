<?php
/**
 * @desc    RestApi测试
 * @date    2018-09-27
 * @author  meijinfeng
 */
namespace app\api\controller;
use frame\sm\Api;
use frame\sm\Hook;

class Test extends Api
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        // Hook Templetes
        // Hook::addHook("testStart");
        // Hook::registerClosureHook("testStart", function () {
        //     echo time();
        // });
        // Hook::trigger("testStart");

        $this->response([
            'time'  => time(),
            'msg'   => 'ok',
        ], 200, 'json');
    }
}