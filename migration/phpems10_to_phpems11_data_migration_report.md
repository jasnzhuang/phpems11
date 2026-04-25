# PHPEMS10 -> PHPEMS11 数据迁移报告

生成时间：2026-03-26 20:50:36
源结构：`/Users/jasonzhuang/dev/exam_10_on_lnzsbm.com/phpems10structure.sql`
目标结构：`/Users/jasonzhuang/dev/phpems11/phpems11.sql`

## 结论

- 可直接按字段交集迁移的公共表：`62` 张
- 仅 PHP10 存在的表：`6` 张
- 仅 PHP11 存在的表：`4` 张
- 推荐做法：先建 PHP11 新库，再按公共字段交集分批导入

## 公共表分组

### 基础字典

- `x2_config`：v10=2 列，v11=2 列，公共=2 列
- `x2_user_group`：v10=7 列，v11=7 列，公共=7 列
- `x2_subject`：v10=3 列，v11=3 列，公共=3 列
- `x2_sections`：v10=5 列，v11=5 列，公共=5 列
- `x2_knows`：v10=8 列，v11=8 列，公共=8 列
- `x2_questype`：v10=4 列，v11=4 列，公共=4 列
- `x2_area`：v10=4 列，v11=4 列，公共=4 列
- `x2_province`：v10=3 列，v11=3 列，公共=3 列
- `x2_city`：v10=4 列，v11=4 列，公共=4 列
- `x2_cityarea`：v10=4 列，v11=4 列，公共=4 列

### 用户与内容

- `x2_user`：v10=25 列，v11=26 列，公共=24 列
- `x2_content`：v10=20 列，v11=20 列，公共=20 列
- `x2_category`：v10=14 列，v11=14 列，公共=14 列
- `x2_navs`：v10=5 列，v11=5 列，公共=5 列
- `x2_docs`：v10=15 列，v11=15 列，公共=15 列
- `x2_docfloder`：v10=6 列，v11=6 列，公共=6 列
- `x2_dochistory`：v10=8 列，v11=8 列，公共=8 列
- `x2_course`：v10=17 列，v11=18 列，公共=17 列
- `x2_coursesubject`：v10=14 列，v11=14 列，公共=14 列

### 考试核心

- `x2_questions`：v10=19 列，v11=19 列，公共=19 列
- `x2_questionrows`：v10=13 列，v11=13 列，公共=13 列
- `x2_quest2knows`：v10=4 列，v11=4 列，公共=4 列
- `x2_exams`：v10=13 列，v11=13 列，公共=13 列
- `x2_basic`：v10=14 列，v11=14 列，公共=14 列
- `x2_openbasics`：v10=5 列，v11=5 列，公共=5 列
- `x2_opencourse`：v10=5 列，v11=5 列，公共=5 列
- `x2_questionanalysis`：v10=12 列，v11=12 列，公共=12 列

### 业务记录

- `x2_examhistory`：v10=24 列，v11=24 列，公共=24 列
- `x2_favor`：v10=5 列，v11=5 列，公共=5 列
- `x2_record`：v10=5 列，v11=5 列，公共=5 列
- `x2_recorddata`：v10=5 列，v11=5 列，公共=5 列
- `x2_progress`：v10=9 列，v11=9 列，公共=9 列
- `x2_orders`：v10=18 列，v11=18 列，公共=18 列
- `x2_coupon`：v10=7 列，v11=7 列，公共=7 列
- `x2_consumelog`：v10=6 列，v11=6 列，公共=6 列
- `x2_feedback`：v10=9 列，v11=9 列，公共=9 列
- `x2_reply`：v10=6 列，v11=6 列，公共=6 列
- `x2_comment`：v10=6 列，v11=6 列，公共=6 列
- `x2_log`：v10=7 列，v11=7 列，公共=7 列

### 系统与扩展

- `x2_answer`：v10=6 列，v11=6 列，公共=6 列
- `x2_app`：v10=5 列，v11=5 列，公共=5 列
- `x2_ask`：v10=9 列，v11=9 列，公共=9 列
- `x2_attach`：v10=9 列，v11=9 列，公共=9 列
- `x2_attachtype`：v10=3 列，v11=3 列，公共=3 列
- `x2_block`：v10=5 列，v11=5 列，公共=5 列
- `x2_cequeue`：v10=8 列，v11=8 列，公共=8 列
- `x2_certificate`：v10=11 列，v11=11 列，公共=11 列
- `x2_cnttouser`：v10=4 列，v11=4 列，公共=4 列
- `x2_enroll`：v10=12 列，v11=12 列，公共=12 列
- `x2_enroll_bats`：v10=9 列，v11=9 列，公共=9 列
- `x2_examsession`：v10=18 列，v11=18 列，公共=18 列
- `x2_exercise`：v10=6 列，v11=7 列，公共=6 列
- `x2_module`：v10=10 列，v11=10 列，公共=10 列
- `x2_module_fields`：v10=18 列，v11=18 列，公共=18 列
- `x2_poscontent`：v10=9 列，v11=9 列，公共=9 列
- `x2_position`：v10=3 列，v11=3 列，公共=3 列
- `x2_session`：v10=13 列，v11=13 列，公共=13 列
- `x2_survey`：v10=9 列，v11=9 列，公共=9 列
- `x2_survey_history`：v10=6 列，v11=6 列，公共=6 列
- `x2_survey_node`：v10=4 列，v11=4 列，公共=4 列
- `x2_survey_questions`：v10=7 列，v11=7 列，公共=7 列
- `x2_wxlogin`：v10=4 列，v11=4 列，公共=4 列

## 仅 PHP10 存在

- `x2_app_20230926`：历史备份表，不建议导入 PHP11
- `x2_seminar`：等待 `plugins/seminar` 插件建表后再导入
- `x2_seminar_content`：等待 `plugins/seminar` 插件建表后再导入
- `x2_seminar_elem`：等待 `plugins/seminar` 插件建表后再导入
- `x2_seminar_layout`：等待 `plugins/seminar` 插件建表后再导入
- `x2_seminar_tpls`：等待 `plugins/seminar` 插件建表后再导入

## 仅 PHP11 存在

- `x2_autoform_sample`：PHP11 新表，无 PHP10 源数据
- `x2_examhistory_log`：PHP11 新表，可后续按业务补写
- `x2_plugins`：PHP11 插件表，应由插件系统自行维护
- `x2_user_log`：PHP11 新表，可不迁移

## 建议迁移步骤

1. 在 MySQL 中创建旧库镜像，例如 `phpems10_old`
2. 在 MySQL 中创建 PHP11 新库，例如 `phpems11_new`
3. 分别导入：
   - `mysql phpems10_old < /Users/jasonzhuang/dev/exam_10_on_lnzsbm.com/phpems10structure.sql`
   - `mysql phpems11_new < /Users/jasonzhuang/dev/phpems11/phpems11.sql`
4. 再把 PHP10 的业务数据导入 `phpems10_old`
5. 编辑并执行 `phpems10_to_phpems11_data_migration_template.sql`
6. 执行后重点核查：用户、题库、考场、试卷、考试记录、订单
7. 插件数据单独处理：`x2_plugins` / `seminar` / AI 配置

## 风险提示

- `x2_app`、`x2_config` 属于系统表，执行前建议先比对新旧配置值
- `x2_user`、`x2_orders`、`x2_examhistory` 是高价值数据，先在测试库验证
- `deepseek_config.php` 不建议继续直接使用，应迁入 PHP11 插件配置
