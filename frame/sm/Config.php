<?php
namespace frame\sm;

class Config
{
    /**
     * ini配置
     * @var array
     */
    public $ini = [];

    public function __construct($path)
    {
        $iniArr = parse_ini_file($path, true);

        if ($iniArr === false) {
            exit("配置文件解析出错");
        }

        $this->ini = $iniArr;
    }

    public function getIni()
    {
        return $this->ini;
    }
}