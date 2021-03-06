<?php

namespace Hxc\HxcLaravelTool\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class Curd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hxc:curd {model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '创建后台的增删改查';

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
        $model = $this->argument('model');
        $controller_path = config('hxc.controller_path','Api/V1');
        $path_arr = [
            app_path("{$model}.php") => "模型：{$model} 已存在，避免重复",
            app_path("Repertories/Eloquent/{$model}ServiceRepository.php") => "仓库服务：{$model}ServiceRepository 已经在，避免重复",
            app_path("Http/Requests/{$model}Request.php") => "请求类：{$model}Request 已存在，避免重复",
            app_path("Http/Controllers/{$controller_path}/{$model}Controller.php") => "控制器：{$model}Controller 已存在，避免重复",
        ];
        $models = Str::lower(Str::plural($model));//将字符转为小写和复数
        if(count(glob(database_path("migrations/*create_{$models}_table.php"))) > 0){
            $this->error("数据库迁移文件：create_{$models}_table 已存在，避免重复");
            die;
        }
        foreach ($path_arr as $k => $v){
            if(file_exists($k)){
                $this->error($v);
                die;
            }
        }
        $requests_path = app_path('Http/Requests');
        if(!is_dir($requests_path)){
            mkdir($requests_path);
        }
        Artisan::call("make:model {$model} -m");
        Artisan::call("make:repository {$model}");
        file_put_contents("{$requests_path}/{$model}Request.php",$this->getRequestCode($model));
        file_put_contents(app_path("Http/Controllers/{$controller_path}/{$model}Controller.php"),$this->getControllerCode($model));
        $this->info('curd相关创建成功');
    }

    /**
     * 获取请求类代码
     * @param $name
     * @return string
     */
    protected function getRequestCode($name)
    {
        return <<<CODE
<?php
namespace App\Http\Requests;
use App\Http\Traits\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;
/**
 * Class UserRequest
 * @package App\Http\Requests
 */
class {$name}Request extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }
}
CODE;
    }

    /**
     * 获取控制器代码
     * @param $name
     * @return string
     */
    protected function getControllerCode($name)
    {
        $controller_path = str_replace('/','\\',config('hxc.controller_path','Api/V1'));
        return <<<CODE
<?php
namespace App\Http\Controllers\\{$controller_path};

use App\Http\Controllers\Controller;
use App\Http\Requests\\{$name}Request;
use App\Models\\{$name};
use Hxc\HxcLaravelTool\Traits\Curd\Server;
use App\Repertories\\{$name}ServiceRepository;
use Illuminate\Http\Request;

class {$name}Controller extends Controller
{
    use Server;
    protected \$indexField = ['*'];//列表查询要展示的字段
    protected \$showField = ['*'];//详情查询要展示的字段
    /**
     * ProductController constructor.
     * @param {$name}ServiceRepository \${$name}ServiceRepository
     * @param {$name}Request \${$name}Request
     */
    public function __construct({$name}ServiceRepository \${$name}ServiceRepository,{$name}Request \${$name}Request)
    {
        \$this->repository = \${$name}ServiceRepository;
        \$this->request = \${$name}Request;
        \$this->repository->model = {$name}::class;
        \$this->indexField = ['*'];//列表查询要展示的字段
        \$this->showField = ['*'];//详情查询要展示的字段
        \$this->orderBy = ['id' => 'desc'];//排序，应用于列表和详情查询
        \$this->with = [];//关联查询
        //搜索相关，hxc为内容占位符，规范：
        //1、'接受的参数名/搜索的字段名' => ['逻辑','内容']
        //2、'接受的参数名/搜索的字段名' => ['逻辑','内容','关联查询的表名']
        //3、'接受的参数名/搜索的字段名' => 'time' 时间查询
        \$this->search = [
            //'phone' => ['=',"hxc",'user'],
            //'id' => ['=',"hxc",'address.regional'],
            //'order_number' => ['=',"hxc"],
            //'status' => ['=',"hxc"],
            //'created_at' => 'time',
        ];
    }
}
CODE;

    }
}
