<?php
namespace PHPEMS;

class sql
{
    private $pdo;
    private $skipComments = true;
    private $useTransaction = true;
    private $skipSetAndUse = true;

    /**
     * 导入 SQL 文件
     *
     * @param string $filePath .sql 文件路径
     * @return bool 成功返回 true
     * @throws Exception
     */
    public function import($filePath)
    {
        if (!file_exists($filePath)) {
            return false;
        }


        $handle = fopen($filePath, 'r');
        if (!$handle) {
            return false;
        }
        $this->pdo = M('pepdo');
        $sql = '';
        $inTransaction = false;

        try {
            if ($this->useTransaction) {
                $this->pdo->beginTransaction();
                $inTransaction = true;
            }

            while (($line = fgets($handle)) !== false) {
                // 去除行尾换行符
                $line = rtrim($line, "\n\r");
                // 跳过空行
                if (trim($line) === '') {
                    continue;
                }

                // 跳过注释（如果启用）
                if ($this->skipComments && $this->isCommentLine($line)) {
                    continue;
                }

                // 跳过 SET / USE 语句（如果启用）
                if ($this->skipSetAndUse && $this->isSetOrUseStatement($line)) {
                    continue;
                }

                $sql .= $line;
                // 检查是否以分号结尾（且不在字符串或注释中）
                if ($this->endsWithSemicolon($sql)) {
                    // 移除末尾分号
                    $query = rtrim($sql, " \t\n\r;");
                    if ($query !== '') {
                        $this->pdo->query($query);
                    }
                    $sql = ''; // 重置
                }
            }


            // 处理文件末尾没有分号的情况（不推荐，但兼容）
            if (trim($sql) !== '') {
                echo $sql;
                $this->pdo->exec(trim($sql));
            }

            if ($inTransaction) {
                $this->pdo->commit();
            }

            fclose($handle);
            return true;

        } catch (\Exception $e) {
            if ($inTransaction) {
                $this->pdo->rollback();
            }
            fclose($handle);
            return false;
        }
    }

    /**
     * 判断是否为注释行
     */
    private function isCommentLine($line)
    {
        $trimmed = ltrim($line);
        if (strpos($trimmed, '--') === 0) {
            return true;
        }
        if (strpos($trimmed, '#') === 0) {
            return true;
        }
        if (preg_match('/^\/\*.*\*\/$/', $trimmed)) {
            return true;
        }
        // 忽略 /* ... */ 跨行注释（本实现不支持跨行，仅跳过单行 /* */）
        if (strpos($trimmed, '/*') === 0) {
            return true;
        }
        return false;
    }

    /**
     * 判断是否为 SET 或 USE 语句
     */
    private function isSetOrUseStatement($line)
    {
        $upper = strtoupper(trim($line));
        return strpos($upper, 'SET ') === 0 || strpos($upper, 'USE ') === 0;
    }

    /**
     * 安全判断是否以分号结尾（忽略字符串内的分号）
     * 简化版：仅检查末尾是否有分号，且前面不是转义或在引号中
     * 注意：此方法对复杂 SQL（如存储过程）可能不完美，但适用于大多数 dump 文件
     */
    private function endsWithSemicolon($sql)
    {
        $len = strlen($sql);
        if ($len === 0 || $sql[$len - 1] !== ';') {
            return false;
        }

        // 检查分号是否在字符串内（简单启发式）
        $inSingleQuote = false;
        $inDoubleQuote = false;
        $escaped = false;

        for ($i = 0; $i < $len; $i++) {
            $char = $sql[$i];
            if ($escaped) {
                $escaped = false;
                continue;
            }
            if ($char === '\\') {
                $escaped = true;
                continue;
            }
            if ($char === "'" && !$inDoubleQuote) {
                $inSingleQuote = !$inSingleQuote;
            } elseif ($char === '"' && !$inSingleQuote) {
                $inDoubleQuote = !$inDoubleQuote;
            }
        }

        // 如果不在字符串中，则分号有效
        return !$inSingleQuote && !$inDoubleQuote;
    }

    // ===== 配置方法 =====

    public function setSkipComments($skip = true)
    {
        $this->skipComments = $skip;
        return $this;
    }

    public function setUseTransaction($use = false)
    {
        $this->useTransaction = $use;
        return $this;
    }

    public function setSkipSetAndUse($skip = true)
    {
        $this->skipSetAndUse = $skip;
        return $this;
    }
}