<?php

namespace Sunqianhu\Helper\thinkphp;

use Exception;

class Crud
{
    /**
     * 模型类
     * @var string
     */
    protected string $modelClass;

    /**
     * 构造函数
     * @param $modelClass
     * @throws Exception
     */
    public function __construct($modelClass)
    {
        if (!class_exists($modelClass)) {
            throw new Exception("模型类不存在：{$modelClass}");
        }
        $this->modelClass = $modelClass;
    }

    /**
     * 插入
     * @param $data
     * @return int
     */
    public function insert($data)
    {
        $model = new $this->modelClass();
        $model->save($data);
        return $model->id;
    }

    /**
     * 批量插入
     * @param $data
     * @return array
     */
    public function inserts($data)
    {
        $model = new $this->modelClass();
        $models = $model->saveAll($data);
        $ids = [];
        foreach ($models as $model){
            $ids[] = $model->id;
        }
        return $ids;
    }

    /**
     * 插入并设置添加时间为当前时间
     * @param $data
     * @return int
     */
    public function insertWithAddTimeNow($data)
    {
        $data['add_time'] = time();
        return $this->insert($data);
    }

    /**
     * 插入并设置修改时间为当前时间
     * @param $data
     * @return int
     */
    public function insertWithEditTimeNow($data)
    {
        $data['edit_time'] = time();
        return $this->insert($data);
    }

    /**
     * 删除
     * @param $id
     * @return boolean
     * @throws Exception
     */
    public function delete($id)
    {
        $model = $this->modelClass::find($id);
        if (empty($model)) {
            throw new Exception('没有找到记录：'.$id);
        }
        return $model->delete();
    }

    /**
     * 通过字段删除
     * @param $field
     * @param $value
     * @return boolean
     */
    public function deleteByField($field, $value)
    {
        return $this->deleteByWhere([$field=>$value]);
    }

    /**
     * 通过条件删除
     * @param $where
     * @return boolean
     */
    public function deleteByWhere($where)
    {
        $models = $this->modelClass::where($where)
            ->select();
        if($models->isEmpty()){
            return true;
        }
        return $models->delete();
    }

    /**
     * 更新
     * @param $id
     * @param $data
     * @return boolean
     * @throws Exception
     */
    public function update($id, $data)
    {
        if(isset($data['id'])){
            unset($data['id']);
        }
        if(empty($data)){
            throw new Exception('更新的数据不能为空');
        }
        $model = $this->modelClass::find($id);
        if (empty($model)) {
            throw new Exception('没有找到记录：'.$id);
        }
        return $model->save($data);
    }

    /**
     * 通过字段更新
     * @param $field
     * @param $value
     * @param $data
     * @return boolean
     */
    public function updateByField($field, $value, $data)
    {
        return $this->updateByWhere([$field=>$value], $data);
    }

    /**
     * 通过条件更新
     * @param $where
     * @param $data
     * @return boolean
     */
    public function updateByWhere($where, $data)
    {
        $models = $this->modelClass::where($where)
            ->select();
        if($models->isEmpty()){
            return true;
        }
        return $models->update($data);
    }

    /**
     * 更新或插入
     * @param $id
     * @param $data
     * @return int
     */
    public function upsert($id, $data)
    {
        return $this->upsertByWhere(['id'=>$id], $data);
    }

    /**
     * 通过字段更新或插入
     * @param $field
     * @param $value
     * @param $data
     * @return int
     */
    public function upsertByField($field, $value, $data)
    {
        return $this->upsertByWhere([$field=>$value], $data);
    }

    /**
     * 通过条件更新或插入
     * @param $where
     * @param $data
     * @return mixed
     */
    public function upsertByWhere($where, $data)
    {
        $model = $this->modelClass::where($where)
            ->findOrEmpty();
        $model->save($data);
        return $model->id;
    }

    /**
     * 查询一条记录
     * @param $id
     * @param $field
     * @return array
     */
    public function selectRow($id, $field = '*')
    {
        $model = $this->modelClass::field($field)
            ->findOrEmpty($id);
        return $model->toArray();
    }

    /**
     * 通过字段查询一行
     * @param $whereField
     * @param $whereValue
     * @param $selectField
     * @return array
     */
    public function selectRowByField($whereField, $whereValue, $selectField = '*')
    {
        return $this->selectByWhere([$whereField=>$whereValue], $selectField);
    }

    /**
     * 通过条件查询一行
     * @param $where
     * @param $selectField
     * @return mixed
     */
    public function selectByWhere($where, $selectField = '*')
    {
        $model = $this->modelClass::field($selectField)
            ->where($where)
            ->findOrEmpty();
        return $model->toArray();
    }

    /**
     * 查询一个字段的值
     * @param $id
     * @param $field
     * @return mixed
     * @throws Exception
     */
    public function selectValue($id, $field)
    {
        return $this->selectValueByWhere(['id'=>$id], $field);
    }

    /**
     * 通过字段查询一个字段的值
     * @param $whereField
     * @param $whereValue
     * @param $selectField
     * @return mixed
     * @throws Exception
     */
    public function selectValueByField($whereField, $whereValue, $selectField)
    {
        return $this->selectValueByWhere([$whereField=>$whereValue], $selectField);
    }

    /**
     * 通过条件查询一个字段的值
     * @param $where
     * @param $selectField
     * @return mixed
     * @throws Exception
     */
    public function selectValueByWhere($where, $selectField)
    {
        $model = $this->modelClass::field($selectField)
            ->where($where)
            ->limit(1)
            ->find();
        if(empty($model)){
            throw new Exception('没有找到记录');
        }

        return $model->$selectField;
    }

    /**
     * 查询一列
     * @param $field
     * @param $sortField
     * @param $sortDirection
     * @return array
     */
    public function selectColumn($field, $sortField = 'id', $sortDirection = 'asc')
    {
        return $this->modelClass::field($field)
            ->order($sortField, $sortDirection)
            ->column($field);
    }

    /**
     * 通过字段查询一列
     * @param $whereField
     * @param $whereValue
     * @param $selectField
     * @param $sortField
     * @param $sortDirection
     * @return array
     */
    public function selectColumnByField($whereField, $whereValue, $selectField, $sortField = 'id', $sortDirection = 'asc')
    {
        return $this->selectColumnByWhere([$whereField=>$whereValue], $selectField, $sortField, $sortDirection);
    }

    /**
     * 通过条件查询一列
     * @param $where
     * @param $selectField
     * @param $sortField
     * @param $sortDirection
     * @return array
     */
    public function selectColumnByWhere($where, $selectField, $sortField = 'id', $sortDirection = 'asc')
    {
        return $this->modelClass::field($selectField)
            ->where($where)
            ->order($sortField, $sortDirection)
            ->column($selectField);
    }

    /**
     * 得到部分
     * @param $offset
     * @param $length
     * @param $selectField
     * @param $sortField
     * @param $sortDirection
     * @return array
     */
    public function selectLimit($offset, $length, $selectField='*', $sortField = 'id', $sortDirection = 'asc')
    {
        $models = $this->modelClass::field($selectField)
            ->order($sortField, $sortDirection)
            ->limit($offset, $length)
            ->select();
        return $models->toArray();
    }

    /**
     * 通过字段得到部分
     * @param $whereField
     * @param $whereValue
     * @param $offset
     * @param $length
     * @param $selectField
     * @param $sortField
     * @param $sortDirection
     * @return array
     */
    public function selectLimitByField($whereField, $whereValue, $offset, $length, $selectField='*', $sortField = 'id', $sortDirection = 'asc')
    {
        return $this->selectLimitByWhere([$whereField=>$whereValue], $offset, $length, $selectField, $sortField, $sortDirection);
    }

    /**
     * 通过条件得到部分
     * @param $where
     * @param $offset
     * @param $length
     * @param $selectField
     * @param $sortField
     * @param $sortDirection
     * @return array
     */
    public function selectLimitByWhere($where, $offset, $length, $selectField='*', $sortField = 'id', $sortDirection = 'asc')
    {
        $models = $this->modelClass::field($selectField)
            ->where($where)
            ->order($sortField, $sortDirection)
            ->limit($offset, $length)
            ->select();
        return $models->toArray();
    }

    /**
     * 查询全部
     * @param $selectField
     * @param $sortField
     * @param $sortDirection
     * @return array
     */
    public function selectAll($selectField='*', $sortField = 'id', $sortDirection = 'asc')
    {
        $models = $this->modelClass::field($selectField)
            ->order($sortField, $sortDirection)
            ->select();
        return $models->toArray();
    }

    /**
     * 通过字段查询列表
     * @param $whereField
     * @param $whereValue
     * @param $selectField
     * @param $sortField
     * @param $sortDirection
     * @return array
     */
    public function selectListByField($whereField, $whereValue, $selectField='*', $sortField = 'id', $sortDirection = 'asc')
    {
        return $this->selectListByWhere([$whereField=>$whereValue], $selectField, $sortField, $sortDirection);
    }

    /**
     * 通过条件得到列表
     * @param $where
     * @param $selectField
     * @param $sortField
     * @param $sortDirection
     * @return array
     */
    public function selectListByWhere($where, $selectField='*', $sortField = 'id', $sortDirection = 'asc')
    {
        $models = $this->modelClass::field($selectField)
            ->where($where)
            ->order($sortField, $sortDirection)
            ->select();
        return $models->toArray();
    }

    /**
     * 查询数量
     * @return int
     */
    public function selectCount()
    {
        return $this->modelClass::count();
    }

    /**
     * 查询数量通过字段
     * @param $field
     * @param $value
     * @return int
     */
    public function selectCountByField($field, $value)
    {
        return $this->selectCountByWhere([$field=>$value]);
    }

    /**
     * 查询数量通过条件
     * @param $where
     * @return int
     */
    public function selectCountByWhere($where)
    {
        return $this->modelClass::where($where)->count();
    }
}