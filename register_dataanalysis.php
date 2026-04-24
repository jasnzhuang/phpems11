<?php
/**
 * 数据分析模块注册脚本
 * 用于将数据分析模块注册到系统中
 */

// 设置错误报告
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>数据分析模块注册</h1>";

// 检查配置文件
if (!file_exists('lib/config.inc.php')) {
    echo "<div class='error'>❌ 配置文件不存在</div>";
    exit;
}

require_once 'lib/config.inc.php';

echo "<h2>数据库连接测试</h2>";

try {
    // 连接数据库
    $conn = new mysqli(DH, DU, DP, DB);
    
    if ($conn->connect_error) {
        echo "<div class='error'>❌ 数据库连接失败: " . $conn->connect_error . "</div>";
        exit;
    }
    
    echo "<div class='success'>✅ 数据库连接成功</div>";
    
    // 检查应用表是否存在
    $tables = $conn->query("SHOW TABLES LIKE '" . DTH . "app'");
    if ($tables->num_rows == 0) {
        echo "<div class='error'>❌ 应用表不存在: " . DTH . "app</div>";
        exit;
    }
    
    echo "<div class='success'>✅ 应用表存在</div>";
    
    // 检查数据分析模块是否已注册
    echo "<h2>模块注册检查</h2>";
    
    $checkSql = "SELECT * FROM " . DTH . "app WHERE appid = 'dataanalysis'";
    $result = $conn->query($checkSql);
    
    if ($result->num_rows > 0) {
        $app = $result->fetch_assoc();
        echo "<div class='success'>✅ 数据分析模块已注册</div>";
        echo "<div class='info'>";
        echo "<p><strong>应用ID：</strong>" . $app['appid'] . "</p>";
        echo "<p><strong>应用名称：</strong>" . $app['appname'] . "</p>";
        echo "<p><strong>应用状态：</strong>" . ($app['appstatus'] ? '启用' : '禁用') . "</p>";
        echo "</div>";
    } else {
        echo "<div class='warning'>⚠️ 数据分析模块未注册，正在注册...</div>";
        
        // 注册数据分析模块
        $insertSql = "INSERT INTO " . DTH . "app (appid, appname, appthumb, appstatus, appsetting) VALUES 
                     ('dataanalysis', '数据分析', 'files/public/images/dataanalysis.png', 1, 'a:1:{s:10:\"managemodel\";s:1:\"0\";}')";
        
        if ($conn->query($insertSql)) {
            echo "<div class='success'>✅ 数据分析模块注册成功</div>";
        } else {
            echo "<div class='error'>❌ 数据分析模块注册失败: " . $conn->error . "</div>";
        }
    }
    
    // 检查其他必要模块
    echo "<h2>其他模块检查</h2>";
    
    $modules = [
        'core' => '核心模块',
        'user' => '用户模块',
        'exam' => '考试模块'
    ];
    
    foreach ($modules as $module => $name) {
        $checkSql = "SELECT * FROM " . DTH . "app WHERE appid = '$module'";
        $result = $conn->query($checkSql);
        
        if ($result->num_rows > 0) {
            $app = $result->fetch_assoc();
            echo "<div class='success'>✅ $name - 已注册 (状态: " . ($app['appstatus'] ? '启用' : '禁用') . ")</div>";
        } else {
            echo "<div class='warning'>⚠️ $name - 未注册</div>";
        }
    }
    
    // 检查数据表
    echo "<h2>数据表检查</h2>";
    
    $tables = [
        DTH . 'user_group' => '用户组表',
        DTH . 'exams' => '考试表',
        DTH . 'examscore' => '考试成绩表',
        DTH . 'user' => '用户表'
    ];
    
    foreach ($tables as $table => $desc) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result->num_rows > 0) {
            echo "<div class='success'>✅ $desc - 存在</div>";
        } else {
            echo "<div class='error'>❌ $desc - 不存在</div>";
        }
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<div class='error'>❌ 数据库连接异常: " . $e->getMessage() . "</div>";
}

echo "<h2>文件检查</h2>";

$files = [
    'app/dataanalysis/master.php' => '应用主文件',
    'app/dataanalysis/app.php' => '应用前端文件',
    'app/dataanalysis/controller/index.master.php' => '控制器文件',
    'app/dataanalysis/cls/dataanalysis.cls.php' => '模型类文件',
    'app/dataanalysis/tpls/master/dataanalysis_index_robust.tpl' => '主页面模板',
    'app/dataanalysis/tpls/master/dataanalysis_analysis.tpl' => '分析结果模板',
    'app/dataanalysis/tpls/master/menu.tpl' => '菜单模板'
];

foreach ($files as $file => $desc) {
    if (file_exists($file)) {
        echo "<div class='success'>✅ $desc - 存在</div>";
    } else {
        echo "<div class='error'>❌ $desc - 不存在</div>";
    }
}

echo "<h2>测试链接</h2>";
echo "<div class='info'>";
echo "<p><a href='index.php?dataanalysis-master' target='_blank'>数据分析页面</a></p>";
echo "<p><a href='index.php?dataanalysis-master&debug=1' target='_blank'>调试模式</a></p>";
echo "<p><a href='test_db_detailed.php' target='_blank'>数据库详细测试</a></p>";
echo "<p><a href='clear_cache.php' target='_blank'>清空缓存</a></p>";
echo "</div>";

// 显示样式
echo "<style>
.error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 10px; margin: 5px 0; border-radius: 4px; }
.success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 10px; margin: 5px 0; border-radius: 4px; }
.warning { background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 10px; margin: 5px 0; border-radius: 4px; }
.info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; padding: 10px; margin: 5px 0; border-radius: 4px; }
</style>";

echo "<h2>注册完成</h2>";
echo "<p>如果数据分析模块已正确注册，您应该能够正常访问数据分析页面。</p>";
echo "<p>如果仍有问题，请检查数据库连接和文件权限。</p>";
?>