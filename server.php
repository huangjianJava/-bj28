#!/usr/bin/env php
<?php
define('APP_PATH', __DIR__ . '/app/');
define('BIND_MODULE','push/Worker');
// 定义配置文件目录和应用目录同级
define('CONF_PATH', __DIR__.'./config/');
// 加载框架引导文件
require __DIR__ . '/thinkphp/start.php';