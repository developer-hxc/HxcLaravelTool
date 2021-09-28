## 安装方式
```composer
composer require hxc/hxc-tool-laravel
```

## 初始化
```php
php artisan vendor:publish --tag=hxc-init
```
> 此命令会在config目录下生成hxc.php配置文件和app目录下生成Helpers/functions.php全局函数文件，还需要在composer.json中的autoload增加"files": ["app/Helpers/functions.php"]
