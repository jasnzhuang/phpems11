# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

PHPEMS is an online exam/education management system (PHP Exam Management System) running on PHP 7.0-8.3 and MySQL 5.6+. This is version 11, which adds a plugin system with hooks/filters, folder-based course content organization, user login logging/disable, exam regrading, and PHP 8 support.

## Environment & Dependencies

- **Runtime**: PHP 7.0 - 8.3, MySQL 5.6+
- **Web Server**: Apache/nginx serving `index.php` as the single entry point
- **Database**: Single MySQL database with `x2_` table prefix
- **No build step, no linter, no test framework** — edit PHP files and reload
- **Vendor directory**: `vendor/` contains a `vendor/autoload.php` if Composer is used (`COMPOSER` constant in `lib/config.inc.php` enables it)
- **Cache**: Template compilation cache in `data/compile/` and system cache in `data/cache/system/`. Clear via `php clear_cache.php`

## Architecture

### Entry Point & Routing

`index.php` → defines `PEPATH`, loads `lib/system.func.php` → calls `ginkgo::run()`.

All requests go through `index.php`. The URL format is `index.php?{app}-{module}-{method}` (e.g., `index.php?exam-app-basics`). The `ev` class (`lib/ev.cls.php`) parses this into `url[0]=app, url[1]=module, url[2]=method`. If no URL is present, the system defaults to the `core` app, `app` module, `index` method.

When a mobile user agent is detected and the module is `app`, it auto-switches to `phone` module.

### Core Dispatcher: `ginkgo` class (`lib/init.cls.php`)

`ginkgo` is the central controller singleton. Key roles:
- **`run()`**: Parses the URL, includes `app/{app}/{module}.php` (entry constructor) then `app/{app}/controller/{method}.{module}.php` (action), instantiates the `action` class, and calls `display()`. For the `plugin` app, it routes through `plugin()` instead.
- **`make($class, $app, $param)`**: Factory method with singleton caching. Creates business-logic class instances from `app/{app}/cls/{class}.cls.php` or `lib/{class}.cls.php`. Accessed via the global `M()` function.
- **`R($message)`**: Unified error/redirect handler. Returns JSON for API requests (those with `userhash`), or renders an error page / redirects for browser requests.
- **`autoLoadClass()`**: PSR-0-like autoloader, maps `PHPEMS\{app}\{class}` to `app/{app}/cls/{class}.cls.php` and `PHPEMS\{class}` to `lib/{class}.cls.php`. Plugin classes map to `plugins/{plugin}/cls/{class}.cls.php`.

### Request/Input: `ev` class (`lib/ev.cls.php`)

The `ev` singleton (`M('ev')`) provides all request data:
- `$ev->url($n)` — URL segment at position n
- `$ev->get($key)` / `$ev->post($key)` — GET/POST parameters (whitelist-validated keys)
- `$ev->getClientIp()` — client IP
- `$ev->isMobile()` / `$ev->isWeixin()` — device/platform detection
- `$ev->validate($rules)` — input validation with rules like `min:6|max:20|email`

### App Structure

Each app in `app/{appname}/` follows this convention:

| File/Dir | Purpose |
|---|---|
| `app.php` | Entry constructor. Sets up auth (`$this->session`, `$this->user`), loads settings, assigns template vars. Runs before every request to this app. |
| `master.php` | Main (desktop) template entry. Same structure as app.php but for the master/management interface. |
| `phone.php` | Mobile template entry |
| `teach.php` | Teacher-facing template entry |
| `api.php` | API entry point |
| `controller/{method}.{module}.php` | Action class. Contains the `action` class with a `display()` method that handles the actual request logic. |
| `cls/*.cls.php` | Business logic classes. Accessed via `M('classname','appname')`. |
| `tpls/` | Template files (custom `.tpl` format) |

### Database Layer: `pepdo` (`lib/pepdo.cls.php`)

PDO wrapper with a query builder. All table names are auto-prefixed with `x2_` (`DTH` constant).
- `M('pepdo')->makeSelect($data)`, `makeInsert()`, `makeUpdate()`, `makeDelete()` — build SQL from structured arrays
- `M('pepdo')->fetch($sql)`, `fetchAll($sql)`, `exec($sql)` — execute
- `M('pepdo')->listElements($page, $number, $data)` — paginated list with count
- `M('pepdo')->beginTransaction()`, `commit()`, `rollBack()` — transactions
- Array format: `array(select, table, query, group, order, limit, serial)`

### Key Libraries

| File | Class | Purpose |
|---|---|---|
| `lib/tpl.cls.php` | `tpl` | Template engine (assign vars, compile/include templates) |
| `lib/session.cls.php` | `session` | Session management (DB-backed) |
| `lib/strings.cls.php` | `strings` | String validation (keys, email, password, URL, etc.) |
| `lib/html.cls.php` | `html` | HTML generation (forms, selects, etc.) |
| `lib/files.cls.php` | `files` | File upload handling |
| `lib/sql.cls.php` | `sql` | SQL file import (for migrations/install) |
| `lib/http.cls.php` | `http` | HTTP client (cURL wrapper) |
| `lib/module.cls.php` | `module` | Dynamic model system (custom fields, CRUD for content types) |
| `lib/email.cls.php` | `email` | Email sending |
| `lib/wxpay.cls.php` | `wxpay` | WeChat Pay integration |
| `lib/alipay.cls.php` | `alipay` | Alipay integration |

### Plugin System (v11)

Plugins live in `plugins/{pluginname}/`. Access via `P('classname','pluginname')` or `M('classname','plugin')`. The `plugin` app router (`app/plugin/app.php`) handles plugin requests at `index.php?plugin-app-demo-index`.

### Configuration

`lib/config.inc.php` contains all system constants:
- **Database**: `DB`, `DH`, `DU`, `DP` (name/host/user/password), `DTH` (table prefix `x2_`)
- **Security**: `CSKEY` (32-char cipher key), `CSIV` (16-char IV), `APPKEY`, `APPIV`
- **WeChat/Alipay**: API keys and secrets for payment and auth
- **Pagination**: `PN` (page size, default 10)
- **Composer**: `COMPOSER` flag to enable vendor autoload

### Custom Features (on this branch)

- **DeepSeek AI Integration**: `deepseek_config.php` configures the DeepSeek API for AI-powered question generation. Exam controllers invoke this. Configuration validation lives in `app/exam/controller/basic.master.php`.
- **Data Analysis Module**: `app/dataanalysis/` — custom analytics module
- **Migration Tools**: `migration/` contains PHPEMS10→11 data migration SQL templates and reports. `tools/generate_phpems10_to_11_migration.py` generates migration SQL.

## Common Patterns

**Accessing a business class:**
```php
$basic = M('basic', 'exam');  // app/exam/cls/basic.cls.php
$pepdo = M('pepdo');          // lib/pepdo.cls.php
```

**Template rendering:**
```php
M('tpl')->assign('key', $value);
M('tpl')->display('template_name');  // from app/{current_app}/tpls/
```

**Error/redirect:**
```php
R(array('statusCode' => 301, 'message' => '请重新登录', 'forwardUrl' => 'index.php?user-app-login'));
```

**Session + current user:**
```php
$this->session = M('session')->getSessionUser();
$this->user = M('user', 'user')->getUserById($this->session['sessionuserid']);
```

## Database

The database uses the `x2_` prefix. The full schema is in `phpems11.sql` (322KB dump). Key tables include: `x2_user`, `x2_exam`, `x2_questions`, `x2_course`, `x2_plugins`, `x2_module`, `x2_config`, `x2_session`.

The `module` system allows dynamic table creation — fields in `x2_module_fields` define columns that can be added/removed at runtime via the admin interface.
