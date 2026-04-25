<?php

// 加载 .env 文件到 $_ENV（安全优先：敏感配置不应硬编码在代码中）
if (defined('PEPATH') && file_exists(PEPATH . '/.env')) {
    $lines = file(PEPATH . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        // 去除首尾引号
        $len = strlen($value);
        if ($len >= 2 && (($value[0] === '"' && $value[$len - 1] === '"') || ($value[0] === "'" && $value[$len - 1] === "'"))) {
            $value = substr($value, 1, -1);
        }
        $_ENV[$key] = $value;
        putenv("{$key}={$value}");
    }
}

// 辅助函数：从环境变量读取配置
if (!function_exists('phpems_env')) {
    function phpems_env($key, $default = '') {
        if (isset($_ENV[$key])) return $_ENV[$key];
        $val = getenv($key);
        if ($val !== false) return $val;
        if (isset($_SERVER[$key])) return $_SERVER[$key];
        return $default;
    }
}

// 安全反序列化辅助函数（要求 PHP >= 7.1）
if (!function_exists('phpems_safe_unserialize')) {
    function phpems_safe_unserialize($data) {
        if ($data === '' || $data === null) return '';
        if (PHP_VERSION_ID < 70100) {
            trigger_error('PHP 版本必须 >= 7.1 才能安全地进行反序列化。当前版本：' . PHP_VERSION, E_USER_WARNING);
            return '';
        }
        return @unserialize($data, ['allowed_classes' => false]);
    }
}

/** 常规常量设置 */
define('DOMAINTYPE','off');
define('CH','exam_');
define('CDO','');
define('CP','/');
define('CRT',180);
// Session 密钥优先从环境变量读取
$env_cskey = phpems_env('CSKEY', '1hqfx6ticwRxtfviTp940vng!yC^QK^6');
$env_csiv  = phpems_env('CSIV', '1234567812345678');
define('CSKEY', $env_cskey);//请随机生成32位字符串修改此处值
define('CSIV', $env_csiv);
define('PN',10);
define('TIME',time());
if(dirname($_SERVER['SCRIPT_NAME']))
{
	define('WP','http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['SCRIPT_NAME']).'/');
}
else
{
	define('WP','http://'.$_SERVER['SERVER_NAME'].'/');
}
define('OPENOSS',false);

/**composer开关**/
define('COMPOSER',1);
/** 数据库设置 */
define('SQLDEBUG',0);
// 数据库配置优先从环境变量读取
$env_db = phpems_env('DB_NAME', 'phpems11');
$env_dh = phpems_env('DB_HOST', '127.0.0.1');
$env_du = phpems_env('DB_USER', 'root');
$env_dp = phpems_env('DB_PASS', 'Zdr5NSqnyjAPwNvL');
define('DB', $env_db);//MYSQL数据库名
define('DH', $env_dh);//MYSQL主机名，不用改
define('DU', $env_du);//MYSQL数据库用户名
define('DP', $env_dp);//MYSQL数据库用户密码
define('DTH','x2_');//系统表前缀，不用改

/** 微信相关设置 */
define('USEWX',true);//微信使用开关，绑定用户，false时不启用
define('WXAUTOREG',true);//微信开启自动注册,设置为false时转向登录和注册页面，绑定openid
define('WXPAY',true);
define('EP','@phpems.net');//微信开启自动注册时注册邮箱后缀
$env_openappid  = phpems_env('OPENAPPID', 'wx7703aa61284598ea');
$env_openappsecret = phpems_env('OPENAPPSECRET', 'wx6967d8319bfeea19');
$env_mpappid    = phpems_env('MPAPPID', 'wx7703aa61284598ea');
$env_mpappsecret = phpems_env('MPAPPSECRET', 'wx6967d8319bfeea19');
$env_wxappid    = phpems_env('WXAPPID', 'wx6967d8319bfeea19');
$env_wxappsecret = phpems_env('WXAPPSECRET', 'wx6967d8319bfeea19');
$env_wxmchid    = phpems_env('WXMCHID', '1414206302');
$env_wxkey      = phpems_env('WXKEY', '1414206302');
define('OPENAPPID', $env_openappid);//开放平台账号
define('OPENAPPSECRET', $env_openappsecret);
define('MPAPPID', $env_mpappid);//小程序账号
define('MPAPPSECRET', $env_mpappsecret);
define('WXAPPID', $env_wxappid);//公众号账号
define('WXAPPSECRET', $env_wxappsecret);
define('WXMCHID', $env_wxmchid);//MCHID
define('WXKEY', $env_wxkey);

/** 支付宝相关设置 */
define('ALIPAY',true);
/**RAS2**/
$env_aliappid  = phpems_env('ALIAPPID', '2016092200000000');
$env_aliprikey = phpems_env('ALIPRIKEY', 'ALIPRIKEY');
$env_alipubkey = phpems_env('ALIPUBKEY', 'ALIPRIKEY');
define('ALIAPPID', $env_aliappid);
define('ALIPRIKEY', $env_aliprikey);
define('ALIPUBKEY', $env_alipubkey);

/**APP相关设置**/
define("APPHASH",0);
$env_appkey = phpems_env('APPKEY', '12345678123456781234567812345678');
$env_appiv  = phpems_env('APPIV', '1234567812345678');
define("APPKEY", $env_appkey);//请随机生成32位字符串修改此处值
define("APPIV", $env_appiv);


?>
