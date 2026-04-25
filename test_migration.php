<?php
/**
 * 迁移功能静态测试脚本
 * 用于验证 phpems10 → phpems11 迁移后的代码结构正确性
 */

$pass = 0;
$fail = 0;
$warn = 0;

function report($status, $msg) {
    global $pass, $fail, $warn;
    echo "[$status] $msg\n";
    if ($status === 'PASS') $pass++;
    elseif ($status === 'FAIL') $fail++;
    elseif ($status === 'WARN') $warn++;
}

// ========== 1. PHP 语法检查 ==========
$files = [
    'app/exam/controller/questions.master.php',
    'app/exam/controller/basic.master.php',
    'app/exam/controller/exams.master.php',
    'app/dataanalysis/controller/index.master.php',
    'app/dataanalysis/cls/dataanalysis.cls.php',
    'app/dataanalysis/master.php',
    'app/dataanalysis/app.php',
    'register_dataanalysis.php',
    'deepseek_config.php',
    'clear_cache.php',
];

foreach ($files as $f) {
    $output = [];
    $ret = 0;
    exec("php -l " . escapeshellarg($f) . " 2>&1", $output, $ret);
    if ($ret === 0 && strpos(implode("\n", $output), 'No syntax errors') !== false) {
        report('PASS', "$f: syntax OK");
    } else {
        report('FAIL', "$f: syntax error - " . implode(" ", $output));
    }
}

// ========== 2. 检查 questions.master.php 关键方法 ==========
$content = file_get_contents('app/exam/controller/questions.master.php');
$methods = ['aiaddquestion', 'ailogs', 'callDeepSeekAI', 'generateSystemPrompt', 'parseAIResponse', 'parseBatchAIResponse', 'index', 'addquestion', 'modifyquestion', 'delquestion'];
foreach ($methods as $m) {
    if (preg_match('/private function ' . $m . '\(/', $content)) {
        report('PASS', "questions.master.php: method $m() exists");
    } else {
        report('FAIL', "questions.master.php: method $m() MISSING");
    }
}

// 检查 phpems10 残余
if (strpos($content, 'extends \\PHPEMS\\app') !== false) {
    report('FAIL', 'questions.master.php: still has phpems10-style extends');
} else {
    report('PASS', 'questions.master.php: no phpems10-style extends');
}
if (preg_match('/\$this->_user\[/', $content)) {
    report('FAIL', 'questions.master.php: still uses $this->_user');
} else {
    report('PASS', 'questions.master.php: no $this->_user usage');
}

// ========== 3. 检查 basic.master.php ==========
$content = file_get_contents('app/exam/controller/basic.master.php');
$methods = ['examanalysis', 'formatDuration'];
foreach ($methods as $m) {
    if (preg_match('/private function ' . $m . '\(/', $content)) {
        report('PASS', "basic.master.php: method $m() exists");
    } else {
        report('FAIL', "basic.master.php: method $m() MISSING");
    }
}

// 检查未定义变量
$vars = ['$participatedCount', '$notParticipatedCount'];
foreach ($vars as $v) {
    $varName = substr($v, 1);
    if (preg_match('/\$' . preg_quote($varName, '/') . '\s*=/', $content)) {
        report('PASS', "basic.master.php: \$$varName is defined before use");
    } else {
        report('FAIL', "basic.master.php: \$$varName is NEVER DEFINED");
    }
}

// ========== 4. 检查 dataanalysis 模块 ==========
$content = file_get_contents('app/dataanalysis/controller/index.master.php');
$methods = ['index', 'analysis', 'export'];
foreach ($methods as $m) {
    if (preg_match('/private function ' . $m . '\(/', $content)) {
        report('PASS', "dataanalysis controller: method $m() exists");
    } else {
        report('FAIL', "dataanalysis controller: method $m() MISSING");
    }
}

$content = file_get_contents('app/dataanalysis/cls/dataanalysis.cls.php');
$methods = ['getUserGroups', 'getExamRooms', 'getExamAnalysis', 'getScoreDistribution', 'getQuestionAccuracy', 'getExamTrends', 'getGroupComparison'];
foreach ($methods as $m) {
    if (preg_match('/(public|private) function ' . $m . '\(/', $content)) {
        report('PASS', "dataanalysis.cls.php: method $m() exists");
    } else {
        report('FAIL', "dataanalysis.cls.php: method $m() MISSING");
    }
}

$tables = ['x2_user_group', 'x2_exams', 'x2_examhistory', 'x2_exam_questions', 'x2_questions', 'x2_user'];
foreach ($tables as $t) {
    if (strpos($content, $t) !== false) {
        report('PASS', "dataanalysis.cls.php: references table $t");
    } else {
        report('WARN', "dataanalysis.cls.php: does not reference $t");
    }
}

$open = substr_count($content, '{');
$close = substr_count($content, '}');
if ($open == $close) {
    report('PASS', "dataanalysis.cls.php: brace balance OK ($open / $close)");
} else {
    report('FAIL', "dataanalysis.cls.php: brace mismatch ($open vs $close)");
}

// ========== 5. 检查 SQL 文件 ==========
$sql = file_get_contents('phpems11.sql');
if (strpos($sql, '`questiontag`') !== false) {
    report('PASS', 'phpems11.sql: questiontag column added to x2_questions');
} else {
    report('FAIL', 'phpems11.sql: questiontag column MISSING');
}

// ========== 6. 检查模板变量一致性 ==========
$ctrl = file_get_contents('app/exam/controller/basic.master.php');
$tpl = file_get_contents('app/exam/tpls/master/basic_examanalysis.tpl');
preg_match_all('/M\("tpl"\)->assign\(([^,]+)/', $ctrl, $m);
$assigned = [];
foreach ($m[1] as $v) {
    $v = trim($v, "'\"");
    $assigned[] = $v;
}
preg_match_all('/\{x2;\\\$([a-zA-Z_][a-zA-Z0-9_]*)\}/', $tpl, $m2);
$used = array_unique($m2[1]);
$builtin = ['_user', '_app', 'apps', 'search'];
$missing = [];
foreach ($used as $u) {
    if (!in_array($u, $assigned) && !in_array($u, $builtin)) {
        $missing[] = $u;
    }
}
if (empty($missing)) {
    report('PASS', 'basic_examanalysis.tpl: all template variables are assigned');
} else {
    report('WARN', 'basic_examanalysis.tpl: potentially unassigned: ' . implode(', ', $missing));
}

// ========== 7. 检查 exams.master.php 修改 ==========
$content = file_get_contents('app/exam/controller/exams.master.php');
$krsortCount = substr_count($content, 'krsort($subjects)');
if ($krsortCount >= 5) {
    report('PASS', "exams.master.php: krsort found $krsortCount times (expected 5+)");
} else {
    report('FAIL', "exams.master.php: krsort found only $krsortCount times (expected 5+)");
}
if (strpos($content, "'examid'") !== false) {
    report('PASS', 'exams.master.php: examid search condition present');
} else {
    report('FAIL', 'exams.master.php: examid search condition MISSING');
}

// ========== 8. 检查 register_dataanalysis.php ==========
$content = file_get_contents('register_dataanalysis.php');
if (strpos($content, 'dataanalysis') !== false && (strpos($content, 'x2_app') !== false || strpos($content, 'DTH') !== false)) {
    report('PASS', 'register_dataanalysis.php: contains module registration logic');
} else {
    report('FAIL', 'register_dataanalysis.php: registration logic MISSING');
}

// ========== 9. 检查 deepseek_config.php ==========
$content = file_get_contents('deepseek_config.php');
if (strpos($content, 'deepseek_api_key') !== false) {
    report('PASS', 'deepseek_config.php: API key config present');
} else {
    report('FAIL', 'deepseek_config.php: API key config MISSING');
}

// ========== 汇总 ==========
echo "\n========================================\n";
echo "测试结果汇总: PASS=$pass, FAIL=$fail, WARN=$warn\n";
echo "========================================\n";

if ($fail > 0) {
    echo "\n注意: 存在 $fail 个失败项，请检查并修复。\n";
    exit(1);
} elseif ($warn > 0) {
    echo "\n提示: 存在 $warn 个警告项，建议关注。\n";
    exit(0);
} else {
    echo "\n全部通过!\n";
    exit(0);
}
