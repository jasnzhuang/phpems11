<?php
/**
 * 缓存清理脚本
 * 用于清理PHPEMS系统的模板缓存和编译文件
 */

echo "正在清理缓存...\n";

// 清理系统缓存
$cacheDir = __DIR__ . '/data/cache/system/';
if (is_dir($cacheDir)) {
    $files = glob($cacheDir . '*.cache');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    echo "✅ 系统缓存已清理\n";
} else {
    echo "⚠️  系统缓存目录不存在\n";
}

// 清理编译文件
$compileDir = __DIR__ . '/data/compile/';
if (is_dir($compileDir)) {
    $directories = glob($compileDir . '*', GLOB_ONLYDIR);
    foreach ($directories as $dir) {
        if (is_dir($dir)) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );
            
            foreach ($files as $file) {
                if ($file->isDir()) {
                    rmdir($file->getRealPath());
                } else {
                    unlink($file->getRealPath());
                }
            }
            rmdir($dir);
        }
    }
    echo "✅ 编译文件已清理\n";
} else {
    echo "⚠️  编译目录不存在\n";
}

echo "🎉 缓存清理完成！\n";
echo "现在可以正常访问数据分析页面了。\n";
?>