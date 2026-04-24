<?php
/**
 * DeepSeek AI 大语言模型配置文件
 * 博众AI添加试题功能专用配置
 * 
 * 使用说明：
 * 1. 请在下方 $deepseek_api_key 变量中填入您的DeepSeek API密钥
 * 2. DeepSeek API密钥获取地址：https://platform.deepseek.com/
 * 3. 该配置支持OpenAI兼容格式的API调用
 * 
 * 安全提示：
 * 1. 请妥善保管您的API密钥，不要泄露给他人
 * 2. 建议定期更换API密钥以确保安全
 * 3. 生产环境中请确保此文件的访问权限设置合适
 */

// DeepSeek API密钥配置
// 推荐通过环境变量注入密钥，避免把密钥提交到代码库
// 例如：export DEEPSEEK_API_KEY='sk-xxxx...'
$deepseek_api_key = getenv('DEEPSEEK_API_KEY') ?: 'YOUR_DEEPSEEK_API_KEY_HERE';

// API接口配置（一般情况下无需修改）
$deepseek_config = array(
    // API基础地址
    'api_base_url' => 'https://api.deepseek.com/v1',
    
    // 默认使用的模型
    'model' => 'deepseek-chat',
    
    // 温度参数（0-1之间，数值越高生成内容越随机）
    'temperature' => 0.7,
    
    // 最大token数量
    'max_tokens' => 4000,
    
    // 请求超时时间（秒）- 针对大模型请求设置较长的超时时间
    'timeout' => 600,
    
    // 连接超时时间（秒）
    'connect_timeout' => 300,
    
    // 是否启用SSL证书验证（生产环境建议开启）
    'ssl_verify' => true
);

// API使用限制配置
$deepseek_limits = array(
    // 每分钟最大请求次数
    'requests_per_minute' => 3,
    
    // 每小时最大请求次数
    'requests_per_hour' => 200,
    
    // 单次请求最大提示词长度
    'max_prompt_length' => 4000
);

/**
 * 验证API配置是否正确
 * @return array 验证结果
 */
function validateDeepSeekConfig() {
    global $deepseek_api_key, $deepseek_config;
    
    $result = array(
        'valid' => true,
        'messages' => array()
    );
    
    // 检查API密钥
    if (empty($deepseek_api_key) || $deepseek_api_key === 'YOUR_DEEPSEEK_API_KEY_HERE') {
        $result['valid'] = false;
        $result['messages'][] = '请配置有效的DeepSeek API密钥';
    }
    
    // 检查API密钥格式
    if (strlen($deepseek_api_key) < 10) {
        $result['valid'] = false;
        $result['messages'][] = 'API密钥格式不正确';
    }
    
    // 检查网络连接
    if (!function_exists('curl_init')) {
        $result['valid'] = false;
        $result['messages'][] = '系统未安装cURL扩展，无法调用API';
    }
    
    return $result;
}

/**
 * 记录API调用日志
 * @param string $action 操作类型
 * @param string $message 日志消息
 * @param array $data 附加数据
 */
function logDeepSeekAPI($action, $message, $data = array()) {
    $logFile = dirname(__FILE__) . '/data/deepseek_api.log';
    $logDir = dirname($logFile);
    
    // 确保日志目录存在
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }
    
    $logEntry = array(
        'timestamp' => date('Y-m-d H:i:s'),
        'action' => $action,
        'message' => $message,
        'data' => $data,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    );
    
    $logLine = json_encode($logEntry, JSON_UNESCAPED_UNICODE) . "\n";
    
    // 写入日志文件
    @file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX);
}

// 注释掉自动验证，避免重复记录错误日志
// 配置验证将在控制器中进行
/*
$config_validation = validateDeepSeekConfig();
if (!$config_validation['valid']) {
    // 记录配置错误日志
    logDeepSeekAPI('config_error', '配置验证失败', $config_validation['messages']);
}
*/

?> 