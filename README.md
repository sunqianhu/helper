# sunqianhu/helper

PHP 常用工具库，提供数组、字符串、时间、数据库、HTTP、文件、图片、验证码、参数校验、脱敏和文本处理等功能。

通过 Composer 安装，使用 `Sunqianhu\Helper` 命名空间和 PSR-4 自动加载。各工具类可以按需实例化使用。

## 目录

- [安装](#安装)
- [快速开始](#快速开始)
- [工具索引](#工具索引)
- [配置](#配置)
- [基础数据处理](#基础数据处理)
- [时间处理](#时间处理)
- [数据库](#数据库)
- [网络与接口](#网络与接口)
- [文件与图像](#文件与图像)
- [校验与脱敏](#校验与脱敏)
- [文本处理](#文本处理)
- [日志与异常](#日志与异常)
- [实例复用](#实例复用)
- [许可证与反馈](#许可证与反馈)

## 安装

```bash
composer require sunqianhu/helper
```

当前 `composer.json` 声明的 PHP 版本约束为 **`>7.4`**，即高于 PHP 7.4.0，例如 PHP 7.4.33、PHP 8.x。

安装时需要以下 PHP 扩展：

| 扩展 | 用途 |
| --- | --- |
| `ext-curl` | HTTP 请求 |
| `ext-pdo` | 数据库访问 |
| `ext-gd` | 图片处理与验证码 |
| `ext-zip` | ZIP 压缩 |
| `ext-json` | JSON 编解码 |

这些扩展目前均为包的必需依赖。Composer 会检查扩展，不负责安装 PHP 扩展。

数据库还需要安装所选驱动：`pdo_mysql`、`pdo_pgsql`、`pdo_sqlsrv` 或 `pdo_sqlite`。验证码需要 GD 的 FreeType 支持；图片处理需要相应的 JPEG、PNG、GIF 编解码能力。`mbstring` 可选，字符串工具在未安装时使用 UTF-8 正则回退实现。

## 快速开始

以下示例放在宿主项目根目录运行。后续 PHP 示例均假定已加载 `vendor/autoload.php`。

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use Sunqianhu\Helper\Arr;
use Sunqianhu\Helper\JsonResponse;
use Sunqianhu\Helper\Str;

$tree = (new Arr())->toTree([
    ['id' => 1, 'parent_id' => 0, 'name' => '商品'],
    ['id' => 2, 'parent_id' => 1, 'name' => '图书'],
]);

$title = (new Str())->zeroEllipsisSub('欢迎使用 PHP 助手工具库', 6);

echo (new JsonResponse())->getSuccess('操作成功', [
    'title' => $title,
    'tree' => $tree,
]);
```

`Arr`、`Str` 等纯数据工具可以直接使用。默认数据库连接、文件存储、Base64 图片保存和默认日志路径需要先完成配置。

## 工具索引

所有类均位于 `Sunqianhu\Helper` 命名空间。

| 分类 | 类 | 功能 |
| --- | --- | --- |
| 基础数据 | `Arr`、`Str`、`NumberHandler` | 数组树、排序、字符串截取、驼峰转换、数字展示 |
| 时间 | `Time`、`TimeRange` | 时间段名称、时长展示、时间范围分段 |
| 配置 | `Installer`、`Config` | 初始化配置、读取配置与实例内缓存 |
| 数据库 | `Db` | PDO 查询、参数绑定、事务与连接复用 |
| HTTP | `Curl`、`Request`、`JsonResponse` | 发起请求、读取入参、生成 JSON 响应 |
| 文件与图像 | `File`、`Image`、`Captcha`、`ZipArc` | 文件路径、图片缩放、验证码、ZIP 压缩 |
| 参数校验 | `IdValidator`、`IdsValidator`、`NumberValidator`、`TimeRangeValidator` | 数值 ID、ID 数组、数字、时间范围校验 |
| 身份信息 | `Desensitization`、`IdNumberHandler` | 手机号、身份证、姓名脱敏与身份证信息提取 |
| 文本 | `RichTextHandler`、`MarkDownHandler`、`Safety` | HTML 与文本转换、移除代码围栏、递归转义 |
| 日志与异常 | `FileLog`、`ErrorHandler`、`ExceptionHandler`、`NotFoundRecordException` | 文件日志、全局处理器、记录不存在异常 |
| 实例复用 | `SingletonFactory` | 按类名缓存实例 |

## 配置

### 初始化

在宿主项目根目录执行一次：

```bash
php vendor/bin/sunqianhu_helper_install.php
```

安装器创建缺失的目录与配置文件，保留已经存在的同名文件，并输出实际安装目录。失败时输出错误并返回非零退出码。Windows 也可以使用以上命令。自定义了 Composer 的 `bin-dir` 时，使用对应目录下的命令文件。

Composer 使用 `bin` 文件名作为命令名，所有依赖包的命令共享宿主项目的 `vendor/bin` 目录；这里使用包含包名的 `sunqianhu_helper_install.php`，减少与其他包的命令重名的风险。

命令优先加载 Composer 提供的宿主项目自动加载器，其次尝试标准安装目录中的宿主自动加载器；工具包源码目录下的 `vendor/autoload.php` 是最后的后备路径，用于本地开发时直接运行源码脚本。

Composer **只执行宿主项目 `composer.json` 中的脚本**，不会执行依赖包定义的安装脚本，也不会自动调用 `Installer::install()`。需要在每次 `composer install` 或 `composer update` 后自动初始化配置时，将以下配置合并到**宿主项目**的 `composer.json`，保留原有的其他脚本：

```json
{
    "scripts": {
        "post-install-cmd": [
            "@php vendor/bin/sunqianhu_helper_install.php"
        ],
        "post-update-cmd": [
            "@php vendor/bin/sunqianhu_helper_install.php"
        ]
    }
}
```

保存后可以运行 `composer run-script post-install-cmd` 立即初始化。使用 `--no-scripts` 时，上述自动初始化不会执行，仍可以手动运行安装命令。

这些脚本通过命令入口调用安装器，`Installer` 本身只负责复制配置文件。自定义了 Composer 的 `bin-dir` 时，同步调整脚本中的命令路径。

默认目录结构：

```text
your-project/
├── config/
│   ├── database.php
│   ├── file.php
│   └── file_log.php
├── public/
│   └── storage/
└── vendor/
    ├── autoload.php
    └── sunqianhu/helper/
```

Composer 2 环境中，`Config` 和安装命令使用 Composer 提供的宿主项目路径定位 `config/`，支持自定义 `vendor-dir` 和 path 仓库安装。旧版 Composer 回退到标准 `vendor/sunqianhu/helper/` 目录层级。

需要复制到其他目录时，将目标配置目录作为参数传入，支持递归创建目录：

```bash
php vendor/bin/sunqianhu_helper_install.php ./app/config/helper
```

也可以在加载自动加载器后直接调用：

```php
use Sunqianhu\Helper\Installer;

Installer::install(__DIR__ . '/app/config/helper');
```

指定目录只改变配置文件的复制位置，`Config` 仍从宿主项目的 `config/` 目录读取配置。自定义目录中的 `file.php` 默认按其父目录计算存储路径，请按项目实际位置调整 `root_path`。

### 数据库配置

编辑 `config/database.php`：

```php
<?php

return [
    'type' => 'mysql',
    'host' => '127.0.0.1',
    'port' => '3306',
    'dbname' => 'app',
    'username' => 'app_user',
    'password' => 'your_password',
    'charset' => 'utf8mb4',
    'break_reconnect' => false,
];
```

| 数据库 | `type` | 配置说明 |
| --- | --- | --- |
| MySQL | `mysql` | 常用端口 `3306`，编码例如 `utf8mb4` |
| PostgreSQL | `pgsql` | 常用端口 `5432`，编码例如 `UTF8`，映射为 `client_encoding` |
| SQL Server | `sqlsrv` | 常用端口 `1433`，DSN 使用主机、端口、数据库名 |
| SQLite | `sqlite` | `dbname` 为数据库文件路径或 `:memory:`，用户名和密码可设为空字符串 |

`break_reconnect` 默认关闭。开启后的重试行为见[数据库](#数据库)。

### 文件配置

编辑 `config/file.php`：

```php
<?php

return [
    'root_path' => dirname(__DIR__) . '/public/storage/',
    'access_prefix' => '/storage/',
];
```

- `root_path`：磁盘上的存储根目录，末尾保留 `/`。
- `access_prefix`：访问 URL 的前缀，末尾保留 `/`，也可以填写完整域名。

`getFullPath()` 和 `getUrl()` 直接拼接这些前缀与文件相对路径。访问前缀需要与 Web 服务器的实际映射一致；包中自带模板的访问前缀为 `/api/storage/`。

### 日志配置

编辑 `config/file_log.php`：

```php
<?php

return [
    'full_path' => dirname(__DIR__) . '/logs/app.log',
];
```

日志目录需要预先创建并可写。模板中的 `full_path` 默认为空，使用默认配置实例化 `FileLog` 前需要填写。

### Config

```php
use Sunqianhu\Helper\Config;

$config = new Config();

$rootPath = $config->getFullRootPath(); // 宿主项目的 config 目录，路径以 / 结尾
$database = $config->get('database');
$host = $config->get('database.host');
$timeout = $config->get('database.timeout', 10);
```

`get($name, $default = null)` 使用点号读取嵌套配置。配置文件不存在时抛出异常；文件内的键不存在或值为 `null` 时返回默认值。

配置缓存只在当前 `Config` 实例内有效。`getFileCacheConfig($file)` 读取文件缓存，`setFileCacheConfig($file)` 重新加载对应配置文件。

## 基础数据处理

### Arr

```php
use Sunqianhu\Helper\Arr;

$arr = new Arr();
$rows = [
    ['id' => 1, 'parent_id' => 0, 'sort' => 2],
    ['id' => 2, 'parent_id' => 1, 'sort' => 1],
];

$tree = $arr->toTree($rows);
$arr->sortByColumnAsc($rows, 'sort'); // 原数组排序，id 顺序变为 2、1

$selected = $arr->getDesignatedKeysList(
    ['name' => '小明', 'remark' => null, 'extra' => '忽略'],
    ['name', 'remark']
); // ['name' => '小明', 'remark' => null]

$ids = $arr->getColumnsString([2 => ['id' => 7], 5 => ['id' => 8]], 'id'); // '7,8'
```

| 方法 | 行为 |
| --- | --- |
| `toTree($data, $id = 'id', $parentId = 'parent_id', $children = 'children')` | 返回树数组；找不到父节点的记录作为根节点 |
| `sortByColumnAsc(&$array, $field)` | 原地升序排序，重建数字索引，返回排序结果布尔值 |
| `sortByColumnDesc(&$array, $field)` | 原地降序排序，重建数字索引，返回排序结果布尔值 |
| `getDesignatedKeysList($data, $keys)` | 按字段列表筛选，保留已存在的 `null` 值，忽略不存在的键 |
| `getColumnsString($array, $column, $separator = ',')` | 提取一列并拼接，支持非连续索引；非数组返回空字符串 |

排序时，数字及数字字符串按数值比较；其他值按字符串比较。`null` 或缺失字段在升序时排前面、降序时排后面。构树数据应具有唯一 ID，并避免循环父子关系。

### Str

```php
use Sunqianhu\Helper\Str;

$str = new Str();

$str->length("中\n文");                         // 3
$str->sub('你好，世界', 0, 2);                   // '你好'
$str->zeroEllipsisSub('你好，世界', 2);          // '你好...'
$str->toSmallPascal('user_name');               // 'userName'
$str->toSmallPascal('user/name');               // 'userName'
$str->toSmallPascal('user-name/path', '-/');     // 'userNamePath'
$str->delimiterSort('3,1,2');                   // '1,2,3'
$str->delimiterSort('3|1|2', 'desc', '|', ',');   // '3,2,1'
```

`length()` 和 `sub()` 按 UTF-8 字符处理，支持换行。`zeroEllipsisSub()` 仅在超出指定字符数时追加 `...`，省略号不计入截取长度。

`toSmallPascal()` 输出小驼峰；下划线和空格作为分隔符，第二个参数指定其他分隔字符，默认 `/`。

### NumberHandler

```php
use Sunqianhu\Helper\NumberHandler;

$number = new NumberHandler();

$number->getReadableNumber(9999);             // 9999，保持原值
$number->getReadableNumber(12000);            // '1.2万'
$number->getReadableNumber(100000000);        // '1.0亿'
$number->getReadableNumber(1000000000000);    // '1.0万亿'
$number->getPercentage(1, 3);                 // 25
$number->getPercentage(0, 0);                 // 0
```

`getPercentage($number1, $number2, $precision = 2)` 计算的是 `number1 / (number1 + number2) × 100`，返回数值，不附带 `%`。总和为零时返回 `0`。

## 时间处理

时间参数均使用秒级 Unix 时间戳。日期展示和分段边界采用 PHP 当前默认时区；应用入口可以通过 `date_default_timezone_set('Asia/Shanghai')` 设置时区。

### Time

```php
use Sunqianhu\Helper\Time;

$time = new Time();

$time->getFragmentName(strtotime('2026-11-20 09:00:00')); // '上午'
$time->getSecondToReadableTime(3661);                    // '1小时1分钟1秒'
```

`getFragmentName()` 的小时区间如下：

| 小时 | 名称 |
| --- | --- |
| 0–4 | 凌晨 |
| 5–7 | 早上 |
| 8–10 | 上午 |
| 11–12 | 中午 |
| 13–16 | 下午 |
| 17–18 | 傍晚 |
| 19–22 | 晚上 |
| 23 | 子夜 |

`getSecondToReadableTime()` 按一年 365 天、一个月 30 天换算时长，省略数值为零的单位；传入 `0` 返回空字符串。`getFragmentName(0)` 同样返回空字符串。

### TimeRange

```php
use Sunqianhu\Helper\TimeRange;

$range = new TimeRange();
$start = strtotime('2026-11-20 10:30:00');
$end = strtotime('2026-11-20 12:00:00');

$type = $range->getSegmentType($start, $end); // 'hour'
$segments = $range->getSegments($start, $end); // 10时、11时、12时三个片段
```

自动分段类型由结束时间与开始时间的差值决定：

| 时长 | 类型 |
| --- | --- |
| 小于 1 天 | `hour` |
| 1 天至不足 31 天 | `day` |
| 31 天至不足 93 天 | `week` |
| 93 天至不足 1095 天 | `month` |
| 1095 天及以上 | `year` |

也可以显式调用 `getHourSegments()`、`getDaySegments()`、`getWeekSegments()`、`getMonthSegments()`、`getYearSegments()`，它们均接收 `$startTime, $endTime`。

每个片段包含：

| 字段 | 含义 |
| --- | --- |
| `key` | 片段实际开始时间戳 |
| `name` | 简短名称，例如 `10时`、`11月20日`、`第47周` |
| `full_name` | 完整日期或范围名称 |
| `start_time` | 片段实际开始时间戳 |
| `end_time` | 片段实际结束时间戳 |

片段范围包含起止时间；相邻片段按秒衔接，首尾限制在传入的范围内。周分段从周一开始。开始时间大于结束时间时抛出异常。

## 数据库

### 创建连接

```php
use Sunqianhu\Helper\Db;

$db = new Db(); // 读取 config/database.php，并立即建立或复用连接
```

也可以直接传入完整配置：

```php
use Sunqianhu\Helper\Db;

$db = new Db([
    'type' => 'sqlite',
    'dbname' => ':memory:',
    'username' => '',
    'password' => '',
    'break_reconnect' => false,
]);
```

相同配置的 `Db` 实例在当前进程内共享 PDO 连接，也共享该连接的事务状态。

### 查询与参数绑定

以下查询示例假定 `$db` 已建立连接，且数据库中存在 `users` 表。

```php
$user = $db->queryRow(
    'SELECT * FROM users WHERE id = :id',
    [':id' => 42]
);

$users = $db->queryAll(
    'SELECT * FROM users WHERE status = ?',
    [1 => 1] // 使用 ? 占位符时，位置参数下标从 1 开始
);

$total = $db->queryColumn('SELECT COUNT(*) FROM users');

$statement = $db->execute(
    'UPDATE users SET nickname = :nickname WHERE id = :id',
    [':nickname' => null, ':id' => 42]
);
$affected = $db->getRowCount($statement);
```

建议使用命名参数。当前实现将位置参数的键直接传给 `PDOStatement::bindValue()`，因此使用 `?` 时请显式从 `1` 编号。

参数可直接传值，也可写为 `[参数值, PDO类型]`，例如 `['42', PDO::PARAM_INT]`。直接传入 `int`、`bool`、`null` 时分别推断为对应 PDO 类型，其他值按字符串绑定；需要 SQL `NULL` 时直接传入 `null`。

| 方法 | 返回值或用途 |
| --- | --- |
| `execute($sql, $params = [])` | 执行 SQL，返回 `PDOStatement` |
| `queryRow($sql, $params = [], $mode = PDO::FETCH_ASSOC)` | 一条记录，默认关联数组；无记录返回 `null` |
| `queryAll($sql, $params = [], $mode = PDO::FETCH_ASSOC)` | 全部记录；无记录返回空数组 |
| `queryColumn($sql, $params = [], $columnNumber = 0)` | 第一行中的单个字段值；无记录返回 `false` |
| `fetch($statement, $mode = PDO::FETCH_ASSOC)` | 提取下一行，无记录返回 `null` |
| `fetchAll($statement, $mode = PDO::FETCH_ASSOC)` | 提取剩余全部记录 |
| `fetchColumn($statement, $columnNumber = 0)` | 提取下一行的一个字段值，无记录返回 `false` |
| `getRowCount($statement)` | 返回 PDO 报告的影响行数 |
| `getLastInsertId($fieldName = null)` | 返回转换为 `int` 的新增 ID；空结果抛出异常 |
| `getPdo()` | 获取共享的 PDO 对象 |
| `getDsn()` | 获取当前配置生成的 DSN |
| `connect()`、`reconnect()` | 建立或重新建立连接 |
| `getParamType($value)` | 推断 PDO 参数类型 |
| `getPdoStatementErrorInfo($statement)` | 将语句错误信息组合为字符串 |

`queryColumn()` 返回一个值；要提取多行中的某列，可对 `queryAll()` 的结果使用 `array_column()`。如果新增 ID 需要保留超出 PHP 整数范围的值，可直接调用 PDO 的 `lastInsertId()` 获取原始字符串。

### 事务与断线重连

```php
$db->beginTransaction();

try {
    $db->execute('UPDATE users SET status = :status WHERE id = :id', [
        ':status' => 1,
        ':id' => 42,
    ]);
    $db->commit();
} catch (Throwable $exception) {
    try {
        if ($db->getPdo()->inTransaction()) {
            $db->rollBack();
        }
    } catch (Throwable $rollbackException) {
        // 连接已断开时，保留原始异常交给业务处理。
    }
    throw $exception;
}
```

`beginTransaction()`、`commit()`、`rollBack()` 转发对应 PDO 操作，返回布尔值。

开启 `break_reconnect` 后，非事务内被识别为断线的执行异常会触发一次重连和重试。事务内执行失败时直接抛出原异常，不自动重放 SQL，也不恢复已经丢失的事务。整个事务是否适合重试，由业务根据操作的幂等性决定。

## 网络与接口

### Curl

```php
use Sunqianhu\Helper\Curl;

$curl = (new Curl())->setOptions([
    CURLOPT_TIMEOUT => 10,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_SSL_VERIFYHOST => 2,
]);

$body = $curl->get('https://example.com');
$status = $curl->getHttpCode();
```

发送 JSON：

```php
$curl->setOption(CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$body = $curl->post(
    'https://example.com/api/items',
    json_encode(['name' => '示例'], JSON_THROW_ON_ERROR)
);
```

| 方法 | 说明 |
| --- | --- |
| `get(string $url)` | 发起 GET，默认返回响应正文 |
| `post(string $url, $fields = [])` | 发起 POST；表单或 JSON 由传入内容和请求头决定 |
| `setOptions($options)` | 按选项键合并配置，保留已有的其他选项 |
| `setOption($key, $value)` | 设置单个选项 |
| `deleteOption($key)` | 删除选项；不存在时抛出异常 |
| `setThrowHttpException(bool $throw)` | 控制 HTTP 状态码大于等于 400 时是否抛出异常 |
| `getHttpCode()` | 最近一次取得的 HTTP 状态码 |

设置方法支持链式调用。默认超时 30 秒、返回正文、不返回响应头；传输错误始终抛出异常。调用 `setThrowHttpException(false)` 后可自行处理 HTTP 错误响应。

当前默认配置关闭了 TLS 证书校验，上述 HTTPS 示例显式开启校验；运行环境需要配置可用的 CA 证书。

### Request

```php
use Sunqianhu\Helper\Request;

$request = new Request();

$query = $request->get(['page', 'keyword']);
$form = $request->post(['name', 'email']);
$json = $request->input(['name', 'remark']);
```

`get()` 读取 `$_GET`，`post()` 读取 `$_POST`，`input()` 读取 `php://input` 并按 JSON 解码。参数是允许保留的字段名数组；省略或传空数组时返回全部数据。字段筛选保留 `null`。

JSON 请求应传入对象或数组。空输入、空解码结果或 JSON 解码失败返回空数组；该方法不提供独立的解码错误信息，也不执行参数校验。

### JsonResponse

```php
use Sunqianhu\Helper\JsonResponse;

$response = new JsonResponse();

$success = $response->getSuccess('操作成功', ['id' => 1]);
$error = $response->getError('参数错误', ['field' => 'name'], 1001);

header('Content-Type: application/json; charset=utf-8');
echo $success;
```

成功响应结构：

```json
{"code":0,"message":"操作成功","data":{"id":1}}
```

`getSuccess($message, $data = null)` 的 `code` 为 `0`；`getError($message, $data = null, $code = 1)` 的默认错误码为 `1`。

这两个方法返回 JSON 字符串，不自动输出、不设置 HTTP 状态码或响应头。编码失败时抛出 `JsonException`，例如数据包含非法 UTF-8、`INF` 或循环引用。

## 文件与图像

### File

```php
use Sunqianhu\Helper\File;

$file = new File();

$path = $file->makeModuleFilePath('exports', 'txt');
$fullPath = $file->getFullPath($path);
$url = $file->getUrl($path);

// 安全保存：独占创建文件，重名时重新生成路径
$savedPath = $file->saveModuleContentToFile('exports', '文件内容', 'txt');
```

生成的相对路径结构为 `模块名/年/月/日/32位随机名.扩展名`，例如 `exports/YYYY/MM/DD/{random}.txt`。文件名通过 `md5(time() . bin2hex(random_bytes(16)))` 生成；是否覆盖已有文件由实际写入方式决定。

| 方法 | 说明 |
| --- | --- |
| `getUrl($path)` | 访问前缀加文件相对路径 |
| `getFullPath($path)` | 存储根目录加文件相对路径 |
| `makeModuleDirPath($module = '')` | 创建模块下的日期目录，返回带尾部 `/` 的相对目录 |
| `makeModuleFilePath($module = '', $ext = '')` | 创建日期目录并生成相对文件路径，不创建文件本身 |
| `saveModuleContentToFile($module, $content, $ext = '')` | 独占创建并写入文件，返回相对路径；重名最多尝试 10 次，写入失败时清理本次创建的文件并抛出异常 |
| `unlinkFile($path)` | 删除相对路径对应的文件；路径为空、目标不存在或不是文件时抛出异常 |

需要避免覆盖已有文件时，使用 `saveModuleContentToFile()`。它通过 `fopen($fullPath, 'xb')` 独占创建，重名时重新生成路径，支持二进制和空内容。单独调用 `makeModuleFilePath()` 只生成路径，不会占用文件名。

路径、模块名和扩展名应由业务控制；该类直接拼接路径，不负责目录穿越校验。

### Image

```php
use Sunqianhu\Helper\Image;

$image = new Image();
$image->generateThumbnail(
    __DIR__ . '/source.jpg',
    __DIR__ . '/thumbnail.png',
    300,
    90
);
```

`generateThumbnail($sourcePath, $thumbnailPath, $maxSize, $quality = 100)` 将最长边限制为 `$maxSize`，保持比例，不放大小图。支持 JPEG、PNG、GIF；输出格式优先按目标扩展名决定，无法识别时采用原格式。`quality` 用于 JPEG 输出。PNG 输出保留透明度。

目标目录需要预先存在且可写。生成失败会抛出异常，成功后返回 `null`。GIF 经 GD 处理时不会保留动画帧。

保存包含 Data URI 前缀的 Base64 图片：

```php
$dataUri = 'data:image/png;base64,' . base64_encode(
    file_get_contents(__DIR__ . '/source.png')
);

$path = $image->saveBase64ContentToFile('avatars', $dataUri);
```

该方法依赖 `config/file.php`，返回保存后的文件相对路径。支持的声明类型为 `image/jpg`、`image/jpeg`、`image/png`、`image/gif`。

当前实现按 Data URI 中的类型声明选择扩展名，再解码保存；真实图片内容与上传大小限制需要由业务校验。保存操作调用 `File::saveModuleContentToFile()`，通过独占创建避免覆盖已有文件，重名时自动重试。

### Captcha

```php
use Sunqianhu\Helper\Captcha;

$captcha = new Captcha();
$dataUri = $captcha->createBase64Image();
$code = $captcha->getCode(); // 获取刚生成的验证码，由业务保存用于校验
```

| 方法 | 说明 |
| --- | --- |
| `createImage()` | 生成默认 4 位验证码，返回 PNG 二进制内容 |
| `createBase64Image()` | 生成验证码，返回 `data:image/png;base64,...` |
| `getCode()` | 获取最近生成的验证码；生成前为空字符串 |
| `getRandomCode($length = 4)` | 单独生成随机字符，不更新 `getCode()` 的结果 |

默认图片为 110 × 35 像素，使用包内字体。输出二进制图片时，由调用方设置 `Content-Type: image/png`。验证码的保存、比对、有效期和使用次数由业务负责。

### ZipArc

```php
use Sunqianhu\Helper\ZipArc;

$zip = new ZipArc();
$zip->createZip(__DIR__ . '/export.zip', [
    ['full_path' => __DIR__ . '/report.csv', 'name' => 'reports/report.csv'],
    ['full_path' => __DIR__ . '/cover.png', 'name' => 'images/cover.png'],
]);
```

`full_path` 是源文件路径，`name` 是压缩包内的名称。源文件必须存在且可读，目标目录必须可写。已有同名 ZIP 会被覆盖；文件列表为空、源文件不可用、打开或最终保存失败时抛出异常。

成功后返回已经关闭的 `ZipArchive` 对象，压缩包位于传入的目标路径。

## 校验与脱敏

### 参数校验

```php
use Sunqianhu\Helper\IdValidator;
use Sunqianhu\Helper\IdsValidator;
use Sunqianhu\Helper\NumberValidator;
use Sunqianhu\Helper\TimeRangeValidator;

IdValidator::check('用户ID', 42);
(new IdsValidator())->check('用户ID列表', [1, 2, 3]);
NumberValidator::check('金额', '12.50');
(new TimeRangeValidator())->check('查询时间', [
    strtotime('2026-11-01'),
    strtotime('2026-11-20'),
]);
```

| 方法 | 规则 |
| --- | --- |
| `IdValidator::check($fieldName, $id)` | 必须为 PHP 可识别的非负数 |
| `IdsValidator->check($fieldName, $ids)` | 必须为数组，每项均为 PHP 可识别的非负数 |
| `NumberValidator::check($fieldName, $number)` | 必须通过 `is_numeric()` |
| `TimeRangeValidator->check($fieldName, $times)` | 必须是下标为 `0`、`1` 的两项数值数组，开始值不能大于结束值 |

校验成功返回 `true`，失败抛出包含字段名称的 `Exception`。ID 校验允许 `0`，当前规则不限制为整数；`IdsValidator` 允许空数组。时间范围校验仅检查数值和先后顺序，不验证日期字符串。

### Desensitization

```php
use Sunqianhu\Helper\Desensitization;

$mask = new Desensitization();

$mask->getPhoneAddStar('13800138000');             // '138*****000'
$mask->getIdNumberAddStar('12345619900101123X');   // '123456199001******'
$mask->getIdNumberAddStar('12345619900101123x');   // '123456199001******'
$mask->getNameAddStar('张三丰');                  // '张*丰'
```

手机号保留前 3 位和后 3 位，中间替换为 5 个星号。身份证要求完整字符串为前 17 位数字加一位数字或 `X/x`，保留前 12 位、遮盖后 6 位；格式不匹配时原样返回。姓名方法将第二个字符替换为星号。

这些方法负责展示脱敏，不校验手机号归属、身份证校验码或身份真实性。

### IdNumberHandler

```php
use Sunqianhu\Helper\IdNumberHandler;

$id = new IdNumberHandler();
$gender = $id->getGender('12345619900101123X'); // 0
$birthday = $id->getBirthday('12345619900101123X');
```

`getGender()` 使用倒数第二位的奇偶性，当前编码为 **`0` 表示男、`1` 表示女**；长度不是 18 位也返回 `0`。

`getBirthday()` 从号码提取年月日并调用 `strtotime()`，返回时间戳或解析失败时的 `false`；长度不是 18 位时抛出异常。该类不执行完整的身份证有效性校验。

## 文本处理

### RichTextHandler

```php
use Sunqianhu\Helper\RichTextHandler;

$text = new RichTextHandler();

$plain = $text->toMaintainFormatPlainText('<p>第一段</p><p>第二段<br>下一行</p>');
// "第一段\n第二段\n下一行"

$html = $text->toRichText("你好\n世界");
// '<p>你好<br/>世界</p>'
```

`toMaintainFormatPlainText()` 删除脚本、样式、注释和 HTML 标签，将常见块级结束标签及 `<br>` 转为换行，并解码 HTML 实体。

`toRichText()` 先转义 HTML 特殊字符，再将空行分段、段内换行转为 `<br/>`。输出由 `<p>` 包裹的 HTML。当前实现将空值和字符串 `'0'` 视为空文本。

### MarkDownHandler

```php
use Sunqianhu\Helper\MarkDownHandler;

$markdown = new MarkDownHandler();
$code = $markdown->removeCodeFlag("```php\necho 1;\n```", 'php');
// 'echo 1;'
```

`removeCodeFlag($code, $flag)` 移除指定语言的三反引号代码围栏，保留内部内容。`$flag` 应使用已知语言标记，例如 `php`、`json`；该类不负责将 Markdown 渲染为 HTML。

### Safety

```php
use Sunqianhu\Helper\Safety;

$safety = new Safety();
$escaped = $safety->htmlspecialcharsRecursive([
    'title' => '<b>标题</b>',
    'nested' => ['value' => '<script>example</script>'],
]);

$quoted = $safety->addslashesRecursive(["O'Reilly"]);
```

`addslashesRecursive($data)` 对数组中的值递归调用 `addslashes()`。`htmlspecialcharsRecursive($data, $excludeKey = '')` 递归进行 HTML 实体转义；可用逗号分隔需要跳过的键名，例如 `'content,html'`。

SQL 参数请使用数据库参数绑定。HTML 实体转义用于文本输出，不提供富文本标签白名单过滤。

## 日志与异常

### FileLog

```php
use Sunqianhu\Helper\FileLog;

$log = new FileLog(); // 读取 config/file_log.php

$log->info('任务开始');
$log->warning('队列积压');
$log->error('任务失败');
$log->success('任务完成');
```

也可通过 `new FileLog(['full_path' => '/absolute/path/app.log'])` 传入日志文件路径。四种方法按行追加字符串，格式为 `[级别] 年-月-日 时:分:秒 内容`，不自动创建父目录或轮转日志文件。

### ErrorHandler 与 ExceptionHandler

适合需要统一输出 JSON 的接口入口，按需注册：

```php
use Sunqianhu\Helper\ErrorHandler;
use Sunqianhu\Helper\ExceptionHandler;

(new ErrorHandler())->register();
(new ExceptionHandler())->register();
```

`ErrorHandler` 通过 `set_error_handler()` 处理 PHP 可交给用户处理器的普通错误，遵守 `error_reporting()` 掩码和 `@` 抑制，直接输出错误 JSON。它不会在普通警告后自动终止业务，也不能捕获 PHP 所有类型的致命错误。

`ExceptionHandler` 通过 `set_exception_handler()` 处理未捕获的 `Throwable`，将异常消息放入错误 JSON。两个处理器均不设置响应头或 HTTP 状态码；对外接口需要自行决定公开的错误信息。

### NotFoundRecordException

`NotFoundRecordException` 继承 PHP 的 `Exception`，用于表达记录不存在：

```php
use Sunqianhu\Helper\NotFoundRecordException;

throw new NotFoundRecordException('记录不存在');
```

## 实例复用

### SingletonFactory

```php
use Sunqianhu\Helper\SingletonFactory;
use Sunqianhu\Helper\Str;

$first = SingletonFactory::getInstance(Str::class);
$second = SingletonFactory::getInstance(Str::class);

$same = $first === $second; // true
```

`getInstance($class, ...$args)` 首次调用时使用参数创建对象，随后按类名返回同一个实例。后续构造参数不会重新生效；需要不同配置实例时直接 `new` 对应类。类不存在时抛出异常，缓存仅在当前进程内有效。

## 许可证与反馈

本项目声明使用 [Apache License 2.0](https://www.apache.org/licenses/LICENSE-2.0)。

- 源码：[sunqianhu/helper](https://github.com/sunqianhu/helper)
- 问题反馈：[GitHub Issues](https://github.com/sunqianhu/helper/issues)
- 作者：sun qian hu
