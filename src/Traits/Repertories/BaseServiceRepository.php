<?php

namespace Hxc\HxcLaravelTool\Traits\Repertories;

trait BaseServiceRepository
{
    //模型
    public $model;

    /**
     * 查
     * @param object|int|string $sql
     * @param array $field 要显示的字段名
     * @return mixed
     */
    public function find($sql,array $field = ['*'])
    {
        if(is_object($sql)){
            $model = ($this->model)::where([]);
            if($sql) $model = $sql($model);
        }else{
            $model = ($this->model)::where('id',$sql);
        }
        return $model->first($field);
    }

    /**
     * @param array $where
     * @param array $createData
     * @return mixed
     */
    public function firstOrCreate(array $where, array $createData = [])
    {
        return ($this->model)::firstOrCreate($where,$createData);
    }

    /**
     * @param array $where
     * @param array $updateData
     * @return mixed
     */
    public function updateOrCreate(array $where, array $updateData)
    {
        return ($this->model)::updateOrCreate($where,$updateData);
    }

    /**
     * 查询全部
     * @param $sql
     * @param array $field
     * @return mixed
     */
    public function all($sql = '', array $field = ['*'])
    {
        $model = ($this->model)::where([]);
        if($sql) $model = $sql($model);
        return $model->get($field);
    }

    /**
     * 查询数量
     * @param string $sql
     * @return mixed
     */
    public function count($sql = '')
    {
        $model = ($this->model)::where([]);
        if($sql) $model = $sql($model);
        return $model->count();
    }

    /**
     * 分页查询
     * @param $sql
     * @param string $limit
     * @param array $filed
     * @return mixed
     */
    public function pagination($sql = '',$filed = ['*'],$limit = '')
    {
        $model = ($this->model)::where([]);
        if($sql) $model = $sql($model);
        return $model->paginate($limit?:env('PAGE_LIMIT',10),$filed);
    }

    /**
     * 简单分页
     * @param string $sql
     * @param string[] $filed
     * @param string $limit
     * @return mixed
     */
    public function simplePaginate($sql = '',$filed = ['*'],$limit = '')
    {
        $model = ($this->model)::where([]);
        if($sql) $model = $sql($model);
        return $model->simplePaginate($limit?:env('PAGE_LIMIT',10),$filed);
    }

    /**
     * 增
     * @param $data
     * @return mixed
     */
    public function create($data)
    {
        return ($this->model)::create($data);
    }

    /**
     * 改
     * @param object|int|string $sql
     * @param array $data 要更新的数据
     * @return mixed
     */
    public function update($sql,array $data)
    {
        if(is_object($sql)){
            $model = ($this->model)::where([]);
            $model = $sql($model);
        }else{
            $model = ($this->model)::where('id',$sql);
        }
        return $model->update($data);
    }

    /**
     * 关联更新
     * @param $id
     * @param $updateData
     * @param $with
     * @return bool
     */
    public function updateByRelevance($id,$updateData,$with)
    {
        $model = ($this->model)::find($id);
        $fillable = (new $this->model())->getFillable();
        foreach ($updateData as $k => $v){
            if(in_array($k,$fillable)){
                $model->{$k} = $v;
            }
        }
        DB::transaction(function()use($model,$with) {
            $model->save();
            $with($model);
        }, 5);
        return true;
    }

    /**
     * 删
     * @param object|int|string $sql
     * @return mixed
     */
    public function delete($sql)
    {
        if(is_object($sql)){
            $model = ($this->model)::where([]);
            $model = $sql($model);
        }else{
            $model = ($this->model)::where('id',$sql);
        }
        return $model->first()->delete();
    }

    /**
     * 执行sql
     * @param $sql
     * @return mixed
     */
    public function exe($sql)
    {
        $model = ($this->model)::where([]);
        return $sql($model);
    }

    /**
     * 不同条件的批量更新
     * @param string $tableName
     * @param array $multipleData
     * @return bool|int
     */
    public function updateBatch($tableName = "", $multipleData = array()){
        if( $tableName && !empty($multipleData) ) {
            $updateColumn = array_keys($multipleData[0]);
            $referenceColumn = $updateColumn[0]; //e.g id
            unset($updateColumn[0]);
            $whereIn = "";
            $q = "UPDATE ".$tableName." SET ";
            foreach ( $updateColumn as $uColumn ) {
                $q .=  $uColumn." = CASE ";
                foreach( $multipleData as $data ) {
                    $q .= "WHEN ".$referenceColumn." = ".$data[$referenceColumn]." THEN '".$data[$uColumn]."' ";
                }
                $q .= "ELSE ".$uColumn." END, ";
            }
            foreach( $multipleData as $data ) {
                $whereIn .= "'".$data[$referenceColumn]."', ";
            }
            $q = rtrim($q, ", ")." WHERE ".$referenceColumn." IN (".  rtrim($whereIn, ', ').")";
            return DB::update(DB::raw($q));
        } else {
            return false;
        }
    }
}
