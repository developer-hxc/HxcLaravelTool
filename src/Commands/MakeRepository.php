<?php

namespace Hxc\HxcLaravelTool\Commands;
use Illuminate\Console\Command;

class MakeRepository extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository {name : 仓库名称}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成仓库服务';

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
        $name = $this->argument('name');
        $path = app_path('/Repertories');
        if(!is_dir($path)){
            mkdir($path);
        }
        $path = "{$path}/{$name}ServiceRepository.php";
        file_put_contents($path,$this->getCode($name));
        $this->info('仓库服务创建成功');

    }

    /**
     * 获取代码
     * @param $name
     * @return string
     */
    protected function getCode($name)
    {
        return <<<CODE
<?php
namespace App\Repertories;
use Hxc\HxcLaravelTool\Traits\Repertories\BaseServiceRepository;
class {$name}ServiceRepository
{
    use BaseServiceRepository;
}
CODE;
    }

}
