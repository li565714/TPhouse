<?php
namespace app\admin\controller;
use think\Controller;
use think\Session;
use think\Request;
use Auth\Auth;
use think\Config;

class Base extends controller{  

    

     /**
     * 无需登录的方法,同时也就不需要鉴权了
     * @var array
     */
    protected $noNeedLogin = ['index/login' , 'index/logout'];

    /**
     * 无需鉴权的方法,但需要登录
     * @var array
     */
    protected $noNeedRight = [];

    /**
     * 布局模板
     * @var string
     */
    protected $layout = 'default';

    /**
     * 权限控制类
     * @var Auth
     */
    protected $auth = null;

    /**
     * 快速搜索时执行查找的字段
     */
    protected $searchFields = 'id';

    /**
     * 是否是关联查询
     */
    protected $relationSearch = false;

    /**
     * 是否开启Validate验证
     */
    protected $modelValidate = false;

    /**
     * 是否开启模型场景验证
     */
    protected $modelSceneValidate = false;

    /**
     * Multi方法可批量修改的字段
     */
    protected $multiFields = 'status,is_hot';

    protected $allowField = true;

    protected $authConfig = '';

    /**
     * 引入后台控制器的traits
     */
    use \app\admin\library\traits\Backend;


    public function _initialize(){
        error_reporting(E_ERROR | E_WARNING | E_PARSE);
        
        $this->authConfig = config('Auth');
        //模块名称
        $this->moduleName = $this->request->module();
        //控制器名称
        $this->controllerName = strtolower($this->request->controller());
        //方法名称
        $this->actionName = strtolower($this->request->action());

        $path = str_replace('.', '/', $this->controllerName) . '/' . $this->actionName;

        // session('admin_user' , ['uid' => 1 , 'username'=> 'admin']);
        // session('admin_user' , null );

        //初始化auth
        $this->auth = Auth::instance();

        // $url = input('get.referer');
        // $this->url = $url ? $url : url('index');

        //获取当前登陆信息
        $admin_user = session('admin_user');

        //判断控制器方法NOT AUTH 
        if ( !$this->isNotAuth() ){

            if(!$admin_user){
                $this->redirect('index/login');
            }
        }


        //加载配置文件
        // Config::load(APP_PATH.'extra/config.php');
        $sysConfig = config('system');
        $this->assign('sysConfig' , $sysConfig);

        //字典缓存
        $this->initDict();
    
    }

    //判断是否为超级管理员
    private function isUserAdministrator($uid = 0){
        //判断是否为超级管理员
        if($this->authConfig['USER_ADMINISTRATOR'] == $uid ){
            return true;
        }
        return false;
    }

    //判断是否为超级管理员
    private function isNotAuth(){
        //判断当前控制器是否需要验证
        $notAuthController = explode( ',' , strtolower($this->authConfig['NOT_AUTH_CONTROLLER']));

        $notAuthAction = explode(',',strtolower($this->authConfig['NOT_AUTH_ACTION']));
        //判断当前控制器是否不需要验证
        if(in_array( $this->controllerName , $notAuthController  ) ){
            //判断当前方法是否不需要验证
            if(in_array($this->actionName , $notAuthAction  ) ){
                return true;
            }
        }
        return false;
    }

    private function initDict(){
        $dict = cache( 'cache_dict');
        if( !$dict){
            $data = model('HouseDict')->select();
            cache('cache_dict' , $data );
        }
    } 

	/**
     * 生成查询所需要的条件,排序方式
     * @param mixed $searchfields 查询条件
     * @param boolean $relationSearch 是否关联查询
     * @return array
     */
    protected function buildparams($searchfields = null, $relationSearch = null , $sort = '' , $order = '')
    {
        $searchfields = is_null($searchfields) ? $this->searchFields : $searchfields;
        $relationSearch = is_null($relationSearch) ? $this->relationSearch : $relationSearch;
        $search = $this->request->get("search", '');
        $filter = $this->request->get("filter", '');
        $op = $this->request->get("op", '');
        // $sort = $this->request->get("sort", "id");
        // $order = $this->request->get("order", "asc");
        $offset = $this->request->get("offset", 0);
        $limit = $this->request->get("limit", 0);
        $filter = json_decode($filter, TRUE);
        $op = json_decode($op, TRUE);
        $filter = $filter ? $filter : [];
        $where = [];
        $tableName = '';
        if ($relationSearch)
        {
            if (!empty($this->model))
            {
                $class = get_class($this->model);
                $name = basename(str_replace('\\', '/', $class));
                $tableName = $this->model->getQuery()->getTable($name) . ".";
            }
            // if (stripos($sort, ".") === false)
            // {
            //     $sort = $tableName . $sort;
            // }
        }

        if ($search)
        {
            $searcharr = is_array($searchfields) ? $searchfields : explode(',', $searchfields);
            foreach ($searcharr as $k => &$v)
            {
                $v = $tableName . $v;
            }
            unset($v);
            $where[] = [implode("|", $searcharr), "LIKE", "%{$search}%"];
        }
        foreach ($filter as $k => $v)
        {
            $sym = isset($op[$k]) ? $op[$k] : '=';
            if (stripos($k, ".") === false)
            {
                $k = $tableName . $k;
            }
            $sym = isset($op[$k]) ? $op[$k] : $sym;
            switch ($sym)
            {
                case '=':
                case '!=':
                case 'LIKE':
                case 'NOT LIKE':
                    $where[] = [$k, $sym, $v];
                    break;
                case '>':
                case '>=':
                case '<':
                case '<=':
                    $where[] = [$k, $sym, intval($v)];
                    break;
                case 'IN(...)':
                case 'NOT IN(...)':
                    $where[] = [$k, str_replace('(...)', '', $sym), explode(',', $v)];
                    break;
                case 'BETWEEN':
                case 'NOT BETWEEN':
                    $where[] = [$k, $sym, array_slice(explode(',', $v), 0, 2)];
                    break;
                case 'LIKE %...%':
                    $where[] = [$k, 'LIKE', "%{$v}%"];
                    break;
                case 'IS NULL':
                case 'IS NOT NULL':
                    $where[] = [$k, strtolower(str_replace('IS ', '', $sym))];
                    break;
                default:
                    break;
            }
        }
        $where = function($query) use ($where) {
            foreach ($where as $k => $v)
            {
                if (is_array($v))
                {
                    call_user_func_array([$query, 'where'], $v);
                }
                else
                {
                    $query->where($v);
                }
            }
        };
        
        return [$where, $sort, $order, $offset, $limit];
    }
}
