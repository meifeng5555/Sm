<?php

require_once "./frame/Autoload.php";
require_once "./frame/sm/Sm.php";
Autoload::register();

define("ROOT_PATH", str_replace(
    ["\\", "\\\\"],
    "/",
    Autoload::getRootPath())
);
define("APP_PATH", ROOT_PATH . "app/");
define("FRAME_PATH", ROOT_PATH . "frame/sm/");
define("APP_ENV", "development");

$app = new \frame\sm\Sm;
$app->run();



