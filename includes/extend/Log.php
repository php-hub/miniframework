<?php
namespace extend;

/**
 * 本地化调试输出到文件
 */
class Log
{
    protected $config = [
        'time_format' => ' c ',
        'single'      => false,
        'file_size'   => 2097152,
        'path'        => '',
        'apart_level' => [],
        'max_files'   => 0,
        'is_debug'     => false,
    ];

    protected $writed = [];

    // 实例化并传入参数
    public function __construct($config = [])
    {
        if (is_array($config)) {
            $this->config = array_merge($this->config, $config);
        }

        if (empty($this->config['path'])) {
            $this->config['path'] = '';
        } elseif (substr($this->config['path'], -1) != '/') {
            $this->config['path'] .= '/';
        }
    }

    /**
     * 日志写入接口
     * @access public
     * @param  array $log 日志信息
     * @return bool
     */
    public function save($info)
    {
        if ($this->config['single']) {
            $name        = is_string($this->config['single']) ? $this->config['single'] : 'single';
            $destination = $this->config['path'] . $name . '.log';
        } else {
            $cli = PHP_SAPI == 'cli' ? '_cli' : '';

            if ($this->config['max_files']) {
                $filename = date('Ymd') . $cli . '.log';
                $files    = glob($this->config['path'] . '*.log');

                if (count($files) > $this->config['max_files']) {
                    unlink($files[0]);
                }
            } else {
                $filename = date('Ym') . '/' . date('d') . $cli . '.log';
            }

            $destination = $this->config['path'] . $filename;
        }

        $path = dirname($destination);

        if(!file_exists($path)){
          mkdir($path, 0755, true);
        }

        if ($info) {
            return $this->write($info, $destination);
        }

        return true;
    }

    /**
     * 日志写入
     * @access protected
     * @param  array     $message 日志信息
     * @param  string    $destination 日志文件
     * @param  bool      $apart 是否独立文件写入
     * @return bool
     */
    protected function write($message, $destination, $apart = false)
    {
        // 检测日志文件大小，超过配置大小则备份日志文件重新生成
        if (is_file($destination) && floor($this->config['file_size']) <= filesize($destination)) {
            try {
                rename($destination, dirname($destination) . '/' . time() . '-' . basename($destination));
            } catch (\Exception $e) {
            }

            $this->writed[$destination] = false;
        }

        if (empty($this->writed[$destination]) && PHP_SAPI != 'cli') {
            $now     = date("Y-m-d H:i:s");
            $ip      = $this->ip();
            $method  = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'CLI';
            $uri     = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
            $message = "\r\n---------------------------------------------------------------\r\n[{$now}] {$ip} {$method} {$uri}\r\n" . $message;

            $this->writed[$destination] = true;
        }

        if (PHP_SAPI == 'cli') {
            $now     = date("Y-m-d H:i:s");
            $message = "[{$now}]" . $message;
        }
        $result  =  file_put_contents($destination,$message,FILE_APPEND|LOCK_EX);
        return $result;
    }

    /**
   * 获取客户端IP地址
   * @access public
   * @param  integer   $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
   * @param  boolean   $adv 是否进行高级模式获取（有可能被伪装）
   * @return mixed
   */
    protected function ip($type = 0, $adv = true)
    {
        $type      = $type ? 1 : 0;
        static $ip = null;

        if (null !== $ip) {
            return $ip[$type];
        }

        if ($adv) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $pos = array_search('unknown', $arr);
                if (false !== $pos) {
                    unset($arr[$pos]);
                }
                $ip = trim(current($arr));
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        // IP地址合法验证
        $long = sprintf("%u", ip2long($ip));
        $ip   = $long ? [$ip, $long] : ['0.0.0.0', 0];

        return $ip[$type];
    }

}
