<?php
namespace app\admin\controller;
use think\Config;
class System extends Base
{   
    
    protected $model = '';
    
    public function _initialize(){
        
        parent::_initialize();

    }

    public function index(){
       
        $this->assign('web_config' , config('web'));
        
        return $this->fetch();
    }


    public function dict(){
       
        $this->model = model('house_dict');
        $this->allowField = true;
        
        $keyword = trim( input('keyword') );
        $start_time = input( 'start_time');
        $end_time = input( 'end_time');

        list($where, $sort, $order, $offset, $limit) = $this->buildparams( );

        $listModel = $this->model
                ->where( $where )
                ->order($sort, $order);
        if( $keyword ){
            $listModel->where( 'title|description' , 'like' , $keyword.'%');
        }

        if( $start_time  && $end_time){
            $listModel->where( 'add_time' , 'BETWEEN' , array($start_time , $end_time ) );
        }
        $list = $listModel->paginate(10 ,false, ['query'=>request()->param()]);
        // 获取分页显示
        $page = $list->render();
        $this->assign('list' , $list);
        $this->assign('page', $page);
        return $this->fetch();

        return $this->fetch();
    }


    public function edit(){
        $param = input('param.');
        $this->setconfig( $param );
        $this->success( '保存成功' , url('index') );
    }

    /**
     * 修改config的函数
     * @param $arr1 配置前缀
     * @param $arr2 数据变量
     * @return bool 返回状态
     */
    private function setconfig( $param = array() )
    {
        $pat = array_keys( $param );
        $rep = array_values( $param );
        /**
         * 原理就是 打开config配置文件 然后使用正则查找替换 然后在保存文件.
         * 传递的参数为2个数组 前面的为配置 后面的为数值.  正则的匹配为单引号  如果你的是分号 请自行修改为分号
         * $pat[0] = 参数前缀;  例:   default_return_type
           $rep[0] = 要替换的内容;    例:  json
         */
        if (is_array($pat) and is_array($rep)) {
            for ($i = 0; $i < count($pat); $i++) {
                $pats[$i] = '/\'' . $pat[$i] . '\'(.*?),/';
                $reps[$i] = "'". $pat[$i]. "'". " => " . "'".$rep[$i] ."',";
            }
            $fileurl = APP_PATH . "/extra/web.php";

            $string = file_get_contents($fileurl); //加载配置文件
            $string = preg_replace($pats, $reps, $string); // 正则查找然后替换
            file_put_contents($fileurl, $string); // 写入配置文件
            return true;
        } else {
            return flase;
        }
    }



    

}



