<?php
namespace frame\sm;
use frame\sm\Hook;

class Error
{
    /**
     * ErrorHeader
     * @var string
     */
    public static $errorHeader = "";

    /**
     * exceptionHeader
     * @var string
     */
    public static $exceptionHeader = "";

    public function __construct()
    {
        self::register();
    }

    public static function register()
    {
        error_reporting(E_ALL);
        set_error_handler([__CLASS__, 'handleError']);
        set_exception_handler([__CLASS__, 'handleException']);
        register_shutdown_function([__CLASS__, 'handleShutdowm']);
    }

    public static function handleError($errno, $errstr, $errfile = "", $errline = 0)
    {
        if (E_WARNING == $errno) {
            $errno = "Warning";
        } elseif (E_NOTICE == $errno) {
            $errno = "Notice";
        }

        $error = [
            'error_type'    => $errno,
            'error_str'     => $errstr,
            'error_file'    => $errfile,
            'error_line'    => $errline,
        ];

        self::setErrorHeader();
        self::debugError($error);
    }

    public static function handleException()
    {
        echo "handleException";
    }

    public static function handleShutdowm()
    {
        if (!empty($error = error_get_last())) {
            print_r($error);
        }
    }

    public static function setErrorHeader()
    {
        $body = "<table border='1' style='text-align: center'>
                    <thead>
                        <th>Error_Type</th>
                        <th>Error_Str</th>
                        <th>Error_File</th>
                        <th>Error_Line</th>
                    </thead>
                {%content%}
                </table>";

        self::$errorHeader = self::$errorHeader ?: $body;
    }

    public static function debugError($data)
    {
        $body = self::$errorHeader;
        $tbody = "<tr style='background-color: #FF9912'>{%td%}</tr>";
        $tr = "";

        foreach ($data as $v) {
            $tr .= "<td>{$v}</td>";
        }

        $tbody = str_replace("{%td%}", $tr, $tbody);
        $show = str_replace("{%content%}", $tbody, $body);
        self::$errorHeader = str_replace("{%content%}", $tbody . "{%content%}", $body);

        Hook::registerClosure("appEnd", function ($show) use ($show) {
            echo $show;
        }, __METHOD__);
    }

    public static function debugException($data)
    {

    }
}