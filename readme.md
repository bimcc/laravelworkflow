# Laravel Workflow 3.0 正式版

**本项目从 [Tpflow](https://gitee.com/ntdgg/tpflow "Tpflow")  移植而来 感谢作者 [逆天的蝈蝈](https://gitee.com/ntdgg/tpflow "ntdgg")  的辛勤付出**

**欢迎使用 `Laravel Workflow` 工作流引擎**

### 有问题请提交ISSUE 48小时内解决~~

### 主要特性

+ 基于  `<jsPlumb>` 可视化设计流程图
    + 支持可视化界面设计
    + 支持拖拽式流程绘制
    + 三布局便捷调整
    + 基于`workflow.3.0.js` `workflow.3.0.js ` 引擎
+ 超级强大的API 对接功能
    + `flowApi` 可支持工作流设计开发管理
    + `ProcessApi` 步骤管理API，可以对步骤进行管理、读取
    + `SuperApi ` 超级管理接口，对流程进行终止，代审
+ 完善的流引擎机制
    + 规范的命名空间，可拓展的集成化开发
    + 支持 直线式、会签式、转出式、同步审批式等多格式的工作流格式
+ 提供基于 `Laravel 5.5.39` 的样例Demo

---

### 开发方向

1. 本项目将持续跟进[Tpflow](https://gitee.com/ntdgg/tpflow "Tpflow")的版本更新 , bug修复等
2. 提交至本项目的bug修复及建议等也将反馈给[Tpflow](https://gitee.com/ntdgg/tpflow "Tpflow")(框架差异问题除外)
3. 将在近期修改成composer包引入的形式 , 请关注

### 使用方式

1. 克隆本项目
```bash
git clone https://github.com/bimcc/laravelworkflow
```
2. 安装 composer 依赖
```bash
composer install
```
3. 将根目录下的 `laravelflow3.0.sql` 导入数据库
4. 复制 .env.example 为 .env 并填上你的数据库信息
5. 生成secret key 并开启服务
```bash
php artisan key:generate
php artisan serve
```
6. 访问 localhost:8000/index/index 即可


## 版权信息

Laravel Workflow 是从 [Tpflow](https://gitee.com/ntdgg/tpflow "Tpflow") 移植而来 遵循 MIT 开源协议重新发布，并提供免费使用。

本项目包含的第三方源码和二进制文件之版权信息另行标注。

版权所有Copyright © 2018-2020 by Laravel Workflow

All rights reserved。
