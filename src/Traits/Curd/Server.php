<?php

namespace Hxc\HxcLaravelTool\Traits\Curd;

trait Server
{
    protected $request;
    protected $repository;
    protected $indexField = ['*'];
    protected $showField = ['*'];
    protected $orderBy = ['id' => 'desc'];
    protected $with = [];
    protected $withCount = [];
    protected $search = [];


    /**
     * 列表查询的条件，可以直接追加查询条件
     * @param $sql
     * @return mixed
     */
    protected function indexWhere($sql)
    {
        return $sql;
    }

    /**
     * 创建数据的处理，入库前
     * @param $params
     * @return mixed
     */
    protected function storeParams($params)
    {
        return $params;
    }

    /**
     * 更新数据的处理，入库前
     * @param $params
     * @param
     * @return mixed
     */
    protected function updateParams($params,$id)
    {
        return $params;
    }

    /**
     * 编辑数据查询结果处理，查询完成后，需自己判断是否为空结果
     * @param $res
     * @return mixed
     */
    protected function showData($res)
    {
        return $res;
    }

    /**
     * 列表数据查询结果处理，查询完成后，需自己判断是否为空结果
     * @param $res
     * @return mixed
     */
    protected function indexData($res)
    {
        return $res;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $params = $request->all();
        $res = $this->repository->pagination(function ($sql)use($params){
            $sql = $this->indexWhere($sql);
            if(is_array($this->orderBy) && count($this->orderBy) > 0){
                foreach ($this->orderBy as $key => $value){
                    $sql = $sql->orderBy($key,$value);
                }
            }
            if(is_array($this->search) && count($this->search) > 0){
                foreach ($this->search as $key => $value){
                    $key_field = $key;
                    $key_field_arr = explode(' as ',$key);
                    if(count($key_field_arr) === 2){
                        $key_field = trim($key_field_arr[1]);
                        $key = trim($key_field_arr[0]);
                    }
                    if(is_array($value)){
                        $value_count = count($value);
                        if($value_count == 2){
                            if(isset($params[$key])) {
                                if($value[0] == 'in'){
                                    $sql = $sql->whereIn($key_field,$params[$key]);
                                }else{
                                    $sql = $sql->where($key_field,$value[0],str_replace('hxc',$params[$key],$value[1]));
                                }
                            }
                        }elseif ($value_count == 3){
                            if(isset($params[$key])){
                                if($value[0] == 'in'){
                                    $sql = $sql->whereHas($value[2],function (Builder $query)use ($key,$value,$params,$key_field){
                                        $query->whereIn($key_field,$params[$key]);
                                    });
                                }else{
                                    $sql = $sql->whereHas($value[2],function (Builder $query)use ($key,$value,$params,$key_field){
                                        $query->where($key_field,$value[0],str_replace('hxc',$params[$key],$value[1]));
                                    });
                                }

                            }
                        }
                    }elseif (is_string($value)){
                        if($value === 'time' && isset($params[$key]) && is_array($params[$key])) {
                            if($params[$key][0] && $params[$key][1]) {
                                if($params[$key][0] === $params[$key][1]){
                                    $sql = $sql->where($key_field, $params[$key][0]);
                                }else{
                                    $sql = $sql->whereBetween($key_field, [$params[$key][0],$params[$key][1]]);
                                }
                            }
                        }
                    }
                }
            }
            if(is_array($this->with) && count($this->with) > 0){
                $sql = $sql->with($this->with);
            }
            if(is_array($this->withCount) && count($this->withCount) > 0){
                $sql = $sql->withCount($this->withCount);
            }
            return $sql;
        },$this->indexField);
        $res = $this->indexData($res);
        if(isset($res['code']) && $res['code'] === 0 && isset($res['status']) && $res['status'] === 'fail'){
            return $res;
        }
        return commonReturn($res->toArray()['data'],'查询错误',$res);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $params = $this->request->validated();
        $params = $this->storeParams($params);
        if(isset($params['code']) && $params['code'] === 0 && isset($params['status']) && $params['status'] === 'fail'){
            return $params;
        }
        $res = $this->repository->create($params);
        return commonReturn($res,'创建失败');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $res = $this->repository->find(function ($sql)use($id){
            if(is_array($this->with) && count($this->with) > 0){
                $sql = $sql->with($this->with);
            }
            return $sql->where('id',$id);
        },$this->showField);
        $res = $this->showData($res);
        if(isset($res['code']) && $res['code'] === 0 && isset($res['status']) && $res['status'] === 'fail'){
            return $res;
        }
        return commonReturn($res,'查询失败',$res);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $params = $this->request->validated();
        $params = $this->updateParams($params,$id);
        if(isset($params['code']) && $params['code'] === 0 && isset($params['status']) && $params['status'] === 'fail'){
            return $params;
        }
        $res = $this->repository->update($id,$params);
        return commonReturn($res,'修改失败');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $res = $this->repository->delete($id);
        return commonReturn($res,'删除失败');
    }
}
