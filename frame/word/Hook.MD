#### Sm Framework
#### Hook 
>     Sm System Hook
>       1.  appStart    应用程序启动时触发
>       2.  appEnd      应用程序退出时触发
#### Use Templete
>   Hook::register($hookN, $hookV, $param, $unique)
>
>     1.add normal function
>       function test($param) {
>           var_dump($param);
>       }
>       Hook::register("appEnd", "test", [1], "test");
>     2.add class function
>       class TestClass
>       {
>           public static function testStatic() {}
>           public function test() {}
>       }
>       Hook::register("appEnd", ["TestClass", "testStatic"], [], "static");
>       Hook::register("appEnd", [new TestClass, "test"], [], "new");
>
>   Hook::registerClosure($hookN, $hookV, $unique = null)
>
>     1.add closure function
>       $a = 1;
>       Hook::registerClosure("appEnd", function ($a) use ($a) {
>           var_dump($a);
>       })