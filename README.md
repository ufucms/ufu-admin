<div align="center">
    <br/>
    <img src="./admin-views/public/logo.png" alt="" />
    <h1 align="center">
        UFU Admin
    </h1>
    <h4 align="center">
        快速且灵活的后台框架
    </h4> 
<br>

### 项目介绍

基于 `Laravel` 、 `amis` 开发的后台框架, 快速且灵活~

- 基于 amis 以 json 的方式在后端构建页面，减少前端开发工作量，提升开发效率。
- 在 amis 150多个组件都不满足的情况下, 可自行开发前端。
- 框架为前后端分离 (不用再因为框架而束手束脚~)。

<br>

### 内置功能

- 基础后台功能
    - 后台用户管理
    - 角色管理
    - 权限管理
    - 菜单管理
- **代码生成器**
    - 保存生成记录
    - 导入/导出生成记录
    - 可使用命令清除生成的内容
    - 无需更改代码即可生成完整功能
- `amis` 全组件封装 150+ , 无需前端开发即可完成复杂页面
- 多模块支持
- 图形化扩展管理

<br>


### 安装

> 👉 __注意: `UfuAdmin` 是 `laravel` 的扩展包, 安装前请确保你会使用 `laravel`__

##### 1. 创建 `laravel` 项目

```php
composer create-project laravel/laravel example-app
```

##### 2. 配置数据库信息

```dotenv
# .env

APP_TIMEZONE=Asia/Shanghai

APP_LOCALE=zh_CN

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ufucms
DB_USERNAME=root
DB_PASSWORD=
```

> 如果使用的是MySQL的utf8mb4字符集，需要在文件`app\Providers\AppServiceProvider.php`中增加以下代码

```shell
    public function boot(): void
    {
        \Schema::defaultStringLength(191);
    }
```


> 如果你使用的是 laravel 11 , 还需要执行: `php artisan install:api`

##### 3. 获取 `UFU Admin`

```shell
composer require ufucms/ufu-admin:dev-master
```

##### 4. 安装

```shell
# 先发布框架资源
php artisan admin:publish
# 执行安装 (可以在执行安装命令前在 config/admin.php 中修改部分配置)
php artisan admin:install
```

##### 5. 运行项目

启动服务, 访问 `/admin` 路由即可 <br>
_初始账号密码都是 `admin`_

<br>

<br>

感谢 [__Slowlyo__](https://github.com/slowlyo) 开源[__owl-admin__](https://github.com/Slowlyo/owl-admin)项目，使得[__ufu-admin__](https://github.com/ufucms/ufu-admin)可以站在巨人的肩膀上剑指苍穹。