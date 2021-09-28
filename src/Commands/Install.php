<?php

namespace Hxc\HxcLaravelTool\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hxc:install {package?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '拓展安装命令';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $package = $this->argument('package');
        $package_arr = ['sms','pay'];
        if(!$package){
            $this->anticipate("需要安装那些拓展?",$package_arr);
        }else{
            switch ($package){
                case 'sms':
                    $base_path = base_path();
                    $this->line(exec("cd {$base_path} & composer require overtrue/easy-sms "));
                    copy(__DIR__."/../Migrations/2021_09_28_000000_create_sms_table.php",database_path('migrations'));
                    $this->info('拓展安装完成');
                    break;
                case 'pay':
                    break;
                default:
                    $this->error("当前仅支持".implode(',',$package_arr)."安装");

            }
        }
    }
}
