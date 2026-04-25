#!/usr/bin/env python3
from __future__ import annotations

import re
from collections import OrderedDict
from datetime import datetime
from pathlib import Path


ROOT_10 = Path("/Users/jasonzhuang/dev/exam_10_on_lnzsbm.com")
ROOT_11 = Path("/Users/jasonzhuang/dev/phpems11")
SQL_10 = ROOT_10 / "phpems10structure.sql"
SQL_11 = ROOT_11 / "phpems11.sql"
OUT_DIR = ROOT_11 / "migration"
OUT_SQL = OUT_DIR / "phpems10_to_phpems11_data_migration_template.sql"
OUT_MD = OUT_DIR / "phpems10_to_phpems11_data_migration_report.md"


TABLE_GROUPS = OrderedDict(
    [
        (
            "基础字典",
            [
                "x2_config",
                "x2_user_group",
                "x2_subject",
                "x2_sections",
                "x2_knows",
                "x2_questype",
                "x2_area",
                "x2_province",
                "x2_city",
                "x2_cityarea",
            ],
        ),
        (
            "用户与内容",
            [
                "x2_user",
                "x2_content",
                "x2_category",
                "x2_navs",
                "x2_docs",
                "x2_docfloder",
                "x2_dochistory",
                "x2_course",
                "x2_coursesubject",
            ],
        ),
        (
            "考试核心",
            [
                "x2_questions",
                "x2_questionrows",
                "x2_quest2knows",
                "x2_exams",
                "x2_basic",
                "x2_openbasics",
                "x2_opencourse",
                "x2_questionanalysis",
            ],
        ),
        (
            "业务记录",
            [
                "x2_examhistory",
                "x2_favor",
                "x2_record",
                "x2_recorddata",
                "x2_progress",
                "x2_orders",
                "x2_coupon",
                "x2_consumelog",
                "x2_feedback",
                "x2_reply",
                "x2_comment",
                "x2_log",
            ],
        ),
        (
            "系统与扩展",
            [
                "x2_answer",
                "x2_app",
                "x2_ask",
                "x2_attach",
                "x2_attachtype",
                "x2_block",
                "x2_cequeue",
                "x2_certificate",
                "x2_cnttouser",
                "x2_document",
                "x2_enroll",
                "x2_enroll_bats",
                "x2_examsession",
                "x2_exercise",
                "x2_module",
                "x2_module_fields",
                "x2_poscontent",
                "x2_position",
                "x2_session",
                "x2_survey",
                "x2_survey_history",
                "x2_survey_node",
                "x2_survey_questions",
                "x2_wxlogin",
            ],
        ),
    ]
)


MANUAL_ONLY_NOTES = OrderedDict(
    [
        ("x2_seminar", "等待 `plugins/seminar` 插件建表后再导入"),
        ("x2_seminar_content", "等待 `plugins/seminar` 插件建表后再导入"),
        ("x2_seminar_elem", "等待 `plugins/seminar` 插件建表后再导入"),
        ("x2_seminar_layout", "等待 `plugins/seminar` 插件建表后再导入"),
        ("x2_seminar_tpls", "等待 `plugins/seminar` 插件建表后再导入"),
        ("x2_app_20230926", "历史备份表，不建议导入 PHP11"),
        ("x2_plugins", "PHP11 插件表，应由插件系统自行维护"),
        ("x2_autoform_sample", "PHP11 新表，无 PHP10 源数据"),
        ("x2_examhistory_log", "PHP11 新表，可后续按业务补写"),
        ("x2_user_log", "PHP11 新表，可不迁移"),
    ]
)


def read(path: Path) -> str:
    return path.read_text(encoding="utf-8", errors="ignore")


def parse_tables(sql_text: str) -> OrderedDict[str, list[str]]:
    pattern = re.compile(
        r"CREATE TABLE(?: IF NOT EXISTS)? `(?P<name>[^`]+)`\s*\((?P<body>.*?)\)\s*ENGINE=",
        re.I | re.S,
    )
    tables: OrderedDict[str, list[str]] = OrderedDict()
    for match in pattern.finditer(sql_text):
        name = match.group("name")
        body = match.group("body")
        columns: list[str] = []
        for line in body.splitlines():
            line = line.strip()
            if not line.startswith("`"):
                continue
            col_match = re.match(r"`([^`]+)`", line)
            if col_match:
                columns.append(col_match.group(1))
        tables[name] = columns
    return tables


def grouped_tables(common_tables: list[str]) -> OrderedDict[str, list[str]]:
    grouped: OrderedDict[str, list[str]] = OrderedDict()
    assigned = set()
    for title, tables in TABLE_GROUPS.items():
        items = [table for table in tables if table in common_tables]
        if items:
            grouped[title] = items
            assigned.update(items)
    remain = [table for table in common_tables if table not in assigned]
    if remain:
        grouped["其他公共表"] = remain
    return grouped


def sql_identifier_list(columns: list[str]) -> str:
    return ", ".join(f"`{column}`" for column in columns)


def generate_sql(v10_tables: OrderedDict[str, list[str]], v11_tables: OrderedDict[str, list[str]]) -> str:
    common_tables = sorted(set(v10_tables) & set(v11_tables))
    grouped = grouped_tables(common_tables)
    lines: list[str] = []
    now = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    lines.extend(
        [
            "-- PHPEMS10 -> PHPEMS11 数据迁移 SQL 模板",
            f"-- 生成时间: {now}",
            "-- 说明:",
            "-- 1. 执行前请把 `phpems10_old` 与 `phpems11_new` 替换为你的实际数据库名",
            "-- 2. 建议先完整备份旧库和新库",
            "-- 3. 建议先在测试库执行，再上线",
            "-- 4. 本模板仅处理 v10/v11 公共表的公共字段交集",
            "-- 5. seminar / plugins 等非公共表见文档中的手工迁移说明",
            "",
            "SET NAMES utf8mb4;",
            "SET FOREIGN_KEY_CHECKS = 0;",
            "",
        ]
    )
    for group_name, tables in grouped.items():
        lines.append(f"-- ==================== {group_name} ====================")
        for table in tables:
            source_columns = v10_tables[table]
            target_columns = v11_tables[table]
            common_columns = [column for column in target_columns if column in source_columns]
            if not common_columns:
                lines.append(f"-- {table}: 无公共字段，跳过")
                lines.append("")
                continue
            column_list = sql_identifier_list(common_columns)
            update_list = ", ".join(f"`{column}` = VALUES(`{column}`)" for column in common_columns)
            lines.extend(
                [
                    f"-- {table}",
                    f"INSERT INTO `phpems11_new`.`{table}` ({column_list})",
                    f"SELECT {column_list}",
                    f"FROM `phpems10_old`.`{table}`",
                    f"ON DUPLICATE KEY UPDATE {update_list};",
                    "",
                ]
            )
    lines.extend(
        [
            "-- ==================== 手工处理提示 ====================",
            "-- x2_plugins: 不要从 v10 直接导，待 PHP11 插件安装后再补 pluginsetting",
            "-- x2_seminar*: 待 `plugins/seminar` 建表后再导",
            "-- deepseek_config.php: 不再保留根文件，改写入 x2_plugins.pluginsetting",
            "",
            "SET FOREIGN_KEY_CHECKS = 1;",
            "",
        ]
    )
    return "\n".join(lines)


def generate_report(v10_tables: OrderedDict[str, list[str]], v11_tables: OrderedDict[str, list[str]]) -> str:
    common_tables = sorted(set(v10_tables) & set(v11_tables))
    only_v10 = sorted(set(v10_tables) - set(v11_tables))
    only_v11 = sorted(set(v11_tables) - set(v10_tables))
    grouped = grouped_tables(common_tables)
    now = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    lines: list[str] = []
    lines.extend(
        [
            "# PHPEMS10 -> PHPEMS11 数据迁移报告",
            "",
            f"生成时间：{now}",
            f"源结构：`{SQL_10}`",
            f"目标结构：`{SQL_11}`",
            "",
            "## 结论",
            "",
            "- 可直接按字段交集迁移的公共表：`62` 张",
            "- 仅 PHP10 存在的表：`6` 张",
            "- 仅 PHP11 存在的表：`4` 张",
            "- 推荐做法：先建 PHP11 新库，再按公共字段交集分批导入",
            "",
            "## 公共表分组",
            "",
        ]
    )
    for group_name, tables in grouped.items():
        lines.append(f"### {group_name}")
        lines.append("")
        for table in tables:
            source_columns = v10_tables[table]
            target_columns = v11_tables[table]
            common_columns = [column for column in target_columns if column in source_columns]
            lines.append(
                f"- `{table}`：v10={len(source_columns)} 列，v11={len(target_columns)} 列，公共={len(common_columns)} 列"
            )
        lines.append("")
    lines.extend(
        [
            "## 仅 PHP10 存在",
            "",
        ]
    )
    for table in only_v10:
        lines.append(f"- `{table}`：{MANUAL_ONLY_NOTES.get(table, '需单独评估')}")
    lines.extend(
        [
            "",
            "## 仅 PHP11 存在",
            "",
        ]
    )
    for table in only_v11:
        lines.append(f"- `{table}`：{MANUAL_ONLY_NOTES.get(table, 'PHP11 新增表，不从 PHP10 直接导入')}")
    lines.extend(
        [
            "",
            "## 建议迁移步骤",
            "",
            "1. 在 MySQL 中创建旧库镜像，例如 `phpems10_old`",
            "2. 在 MySQL 中创建 PHP11 新库，例如 `phpems11_new`",
            "3. 分别导入：",
            f"   - `mysql phpems10_old < {SQL_10}`",
            f"   - `mysql phpems11_new < {SQL_11}`",
            "4. 再把 PHP10 的业务数据导入 `phpems10_old`",
            "5. 编辑并执行 `phpems10_to_phpems11_data_migration_template.sql`",
            "6. 执行后重点核查：用户、题库、考场、试卷、考试记录、订单",
            "7. 插件数据单独处理：`x2_plugins` / `seminar` / AI 配置",
            "",
            "## 风险提示",
            "",
            "- `x2_app`、`x2_config` 属于系统表，执行前建议先比对新旧配置值",
            "- `x2_user`、`x2_orders`、`x2_examhistory` 是高价值数据，先在测试库验证",
            "- `deepseek_config.php` 不建议继续直接使用，应迁入 PHP11 插件配置",
            "",
        ]
    )
    return "\n".join(lines)


def main() -> None:
    OUT_DIR.mkdir(parents=True, exist_ok=True)
    v10_tables = parse_tables(read(SQL_10))
    v11_tables = parse_tables(read(SQL_11))
    OUT_SQL.write_text(generate_sql(v10_tables, v11_tables), encoding="utf-8")
    OUT_MD.write_text(generate_report(v10_tables, v11_tables), encoding="utf-8")
    print(f"generated: {OUT_SQL}")
    print(f"generated: {OUT_MD}")


if __name__ == "__main__":
    main()
