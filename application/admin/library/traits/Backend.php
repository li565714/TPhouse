<?php

namespace app\admin\library\traits;

trait Backend
{


    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost())
        {
            $params = $this->request->post("");

            if ($params)
            {
                foreach ($params as $k => &$v)
                {
                    $v = is_array($v) ? implode(',', $v) : $v;
                }
                try
                {
                    //是否采用模型验证
                    if ($this->modelValidate)
                    {
                        $name = basename(str_replace('\\', '/', get_class($this->model)));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : true) : $this->modelValidate;
                        $this->model->validate($validate);
                    }
                    $result = $this->model->save($params);
                    if ($result !== false)
                    {
                        $this->success('添加成功');
                    }
                    else
                    {
                        $this->error($this->model->getError());
                    }
                }
                catch (\think\exception\PDOException $e)
                {
                    $this->error($e->getMessage());
                }
            }
            $this->error('Parameter %s can not be empty');
        }
        return $this->view->fetch();
    }

    /**
     * 编辑
     */
    public function edit()
    {
        $params = input('request.');
    
        $row = $this->model->get(input('ids'));
        

        if ($this->request->isPost())
        {
            if ($params)
            {
                foreach ($params as $k => &$v)
                {
                    $v = is_array($v) ? implode(',', $v) : $v;
                }
                try
                {
                    //是否采用模型验证
                    if ($this->modelValidate)
                    {
                        $name = basename(str_replace('\\', '/', get_class($this->model)));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : true) : $this->modelValidate;
                        $row->validate($validate);
                    }

                    $result = $row->allowField(true)->save($params);
                    if ($result !== false)
                    {
                        $this->success('编辑成功');
                    }
                    else
                    {
                        $this->error($row->getError());
                    }
                }
                catch (think\exception\PDOException $e)
                {
                    $this->error($e->getMessage());
                }
            }
            $this->error('Parameter %s can not be empty');
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

    /**
     * 删除
     */
    public function del()
    {
        $ids = input('ids/a');
        if ($ids)
        {
            $count = $this->model->destroy($ids);
            if ($count)
            {
                $this->success('删除成功');
            }
        }
        $this->error('Parameter ids can not be empty');
    }

    /**
     * 删除
     */
    public function dels($ids = "")
    {
        $this->del();
    }

    /**
     * 批量更新
     */
    public function multi($ids = "")
    {
        $ids = $ids ? $ids : $this->request->param("ids");

        if ($ids)
        {

            if ($this->request->has('params'))
            {
                parse_str($this->request->post("params"), $values);
                $values = array_intersect_key($values, array_flip(is_array($this->multiFields) ? $this->multiFields : explode(',', $this->multiFields)));
                if ($values)
                {
                    $count = $this->model->where($this->model->getPk(), 'in', $ids)->update($values);
                    if ($count)
                    {
                        $this->success('编辑成功');
                    }
                }
                else
                {
                    $this->error('You have no permission');
                }
            }
        }
        $this->error('Parameter ids can not be empty');
    }

}
