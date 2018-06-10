<?php

namespace linkphp\log;

class Log
{
    //保存日志存储大小
    static private $_log_size = 1048576;

    static private $log_path = RUNTIME_PATH . 'log/';

    //设置日志保存大小
    public function setLogSize($size)
    {
        if(is_numeric($size)){
            self::$_log_size = $size;
        }
        return $this;
    }

    public function setLogPath($path)
    {
        self::$log_path = $path;
        return $this;
    }

    //存储操作日志内容
    static public function write($param=[],$path=null)
    {
        $scheme = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http';
        $uri = $scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $data = date('Y-m-d');
        $path = is_null($path) ? self::$log_path . $data : $path;
        if(!is_dir($path)){
            mkdir($path,0755,true);
        }
        $filename = $path . '/' . $data . '.json';
        if(file_exists($filename) && filesize($filename) >= self::$_log_size){
            $i = 0;
            $filename = rename($filename,$path . '/' . $data . '-' . $i++  . '.json');
        }
        $data = [$param,'url' => $uri];
        $json_string = json_encode($data);
        file_put_contents($filename, $json_string . PHP_EOL, FILE_APPEND);
    }

    //存储操作日志内容
    static public function error($message,$path=null)
    {
        $time = date('c');
        $data = date('Y-m-d');
        $log_path = is_null($path) ? self::$log_path : $path;
        if(!is_dir($log_path)){
            mkdir($log_path,0755,true);
        }
        $filename = $log_path . $data;
        if(file_exists($filename) && filesize($filename) >= self::$_log_size){
            $i = 0;
            $filename = rename($filename,$log_path  . $data . '-' . $i++  . '.log');
        }
        error_log("[{$time}] " ."\r\n{$message}\r\n", 3, $filename . '.log');
    }

}