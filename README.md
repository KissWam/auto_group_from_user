# Auto Group from User（GLPI 插件）

将资产的群组字段自动同步为“资产用户”的默认分组（来自 `glpi_users.groups_id`）。适用于在保存资产（新增或更新）时，确保群组与用户默认分组保持一致。

## 功能特性
- 在资产新增或更新时自动设置 `groups_id`
- 可配置触发条件与适用资产类型
- 支持日志记录，便于审计与问题排查
- 兼容 GLPI `11.0+`

## 支持的资产类型
- `Computer`
- `Monitor`
- `Peripheral`
- `Phone`
- `Printer`
- `NetworkEquipment`

## 安装与启用
1. 将本插件目录 `autogroupfromuser` 放置到 GLPI 的 `plugins` 目录下
2. 登录 GLPI 后台，进入 `设置 -> 插件`，启用 `Auto Group from User`
3. 在插件配置页面按需设置触发规则与适用类型

## 配置项说明
在 `front/config.php` 提供以下配置项（存储于 `Config::setConfigurationValues('plugin:autogroupfromuser', ...)`）：
- `skip_when_user_empty`：当资产用户为空时跳过
- `trigger_when_group_empty`：当资产群组为空时触发设置为用户默认分组
- `trigger_when_user_changed`：更新操作中，当资产用户变更时触发
- `skip_when_both_empty`：当用户与群组均为空时跳过
- `enabled_itemtypes`：启用的资产类型列表
- `enable_logging`：启用日志记录

## 日志
- 当启用 `enable_logging` 时，插件会通过 `Toolbox::logInFile('autogroupfromuser', ...)` 写入日志
- 查看位置：GLPI 日志目录下的 `autogroupfromuser.log`

## 版本
- 当前版本：`1.0.1`
- 变更日志：见 `CHANGELOG.md`

## 许可与作者
- 许可证：`GPLv2+`
- 作者：`KISSWAM`

## 目录结构速览
- `setup.php`：插件元信息与钩子注册
- `hook.php`：核心逻辑与配置读取、日志记录
- `front/config.php`：配置页面渲染与保存
- `autogroupfromuser.php`：入口引用
- `CHANGELOG.md`：开发日志
- `README.md`：说明文档

## 原理简述
- 通过 `pre_item_add` 与 `pre_item_update` 钩子拦截资产保存流程
- 从资产 `users_id` 找到对应用户，读取其默认分组 `groups_id`
- 按配置条件，将资产的 `groups_id` 设置为该默认分组

## 常见问题
- 如果用户没有默认分组或为空：按配置可能跳过或不触发
- 如果资产已存在群组：默认不覆盖（可通过配置 `trigger_when_group_empty` 控制）
- 多资产类型选择：在配置页勾选需要的类型即可

