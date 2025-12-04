# 变更日志

所有显著更改都记录在此文件中。

## [1.0.1] - 2025-12-02
- 更新作者为 `KISSWAM`
- 新增开发日志 `CHANGELOG.md`
- 新增中文使用说明 `README.md`
- 未更改功能逻辑，仅元数据与文档更新

## [1.0.0] - 2025-11-xx
- 首次发布：资产在新增/更新时，按用户默认分组自动设置资产的 `groups_id`
- 支持资产类型：`Computer`、`Monitor`、`Peripheral`、`Phone`、`Printer`、`NetworkEquipment`
- 提供配置页面：可配置触发规则、启用类型与是否记录日志
- 日志输出：使用 GLPI `Toolbox::logInFile('autogroupfromuser', ...)` 记录关键行为

