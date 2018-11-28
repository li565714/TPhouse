<?php
namespace app\admin\controller;

use QL\QueryList;


class Collect extends Base
{

    protected $model = '';
    
    public function _initialize(){
        
        parent::_initialize();

        $this->model = model('admin/CollectRule');
        $this->allowField = true;

        $this->collect_rule = config('web.collect_rule');
      
    }
    /**
     * 采集管理
     * @author lgp
     * @datetime 2018/02/01 17:33
     * @version 1.0
     */
    public function index(){

       
    }



    /**
     * 采集
     * @author lgp
     * @datetime 2018/11/23 14:34
     * @version 1.0
     */
    public function collect( $rule = '' ){
        set_time_limit(0);
        $queryList = new QueryList();
        
        
        //列表页地址
        $list_url = 'https://bj.zu.anjuke.com/';
        //信息页地址
        // $info_url = 'https://bj.zu.anjuke.com/fangyuan/[houseid]';

        $info_url = 'https://m.anjuke.com/bj/rent/[houseid]-3';
         $regex1 = "/https:\/\/bj.zu.anjuke.com\/fangyuan\/(\d*)/";
        $listQuery = QueryList::Query($list_url,
            array(
                "title"=> array('#list-content h3' , 'text'),                        //标题
                "houseid"=> array('#list-content .zu-info h3 a' , 'href' , "" , function( $subject , $key ) use ( $regex1 ){
                    preg_match ( $regex1 , $subject , $matches );
                    if( count($matches) > 1){
                        return $matches[1];
                    } else {
                        return $matches;
                    }
                }) //连接标识
            )
        );



        //信息页匹配
        $regex2 ='/<img[\s\S]*?data-src-swipe\s*=\s*[\"|\'](.*?)[\"|\'][\s\S]*?>/';
        $regex3 = "/(\d*)室(\d*)厅(\d*)卫/";
        foreach ($listQuery->data as $key => $value) {
            $that_info_url = str_replace( "[houseid]" , $value['houseid'] , $info_url);
            $infoData = QueryList::Query($that_info_url,
                array(
                    "title"=> array('.prop-title' , 'text'),                        //标题
                    'xq_id'=> array('#comm-intro .comm-link span' , 'text') ,          //小区
                    'house_amount'=> array('.prop-price strong' , 'text' ,"" , function( $subject , $key ){
                        return $subject*1; //去掉文字
                    }) , //房屋价格
                    'type_id' => array('.prop-tags span:eq(0)' , 'text') ,  //出租类型
                    'area_size' => array('.prop-info-list li:eq(2)' , 'text' ,'-label' , function( $subject , $key ){
                        return $subject*1; //去掉文字
                    }) ,   //房屋面积
                    'decorate_id' => array('.prop-info-list li:eq(1)' , 'text' ,'-label') ,  //装修类型

                    'current_floor' => array('.prop-info-list li:eq(4)' , 'text' , '-label' , function( $subject , $key ){
                       return substr($subject , 0 , 6);
                    }) ,  //当前楼层
                    'countt_floor' => array('.prop-info-list li:eq(4)' , 'text'  , '-label' , function( $subject , $key ){
                        return substr($subject , -6 , 2);
                    }) ,  //总楼层
                    'direction' => array('.prop-info-list li:eq(3)' , 'text'  , '-label') ,  //方向
                    'description' => array('.prop-desc-content' , 'html') ,  //描述

                    'imgs' => array('#view-photo-wrap ' , 'html' ,"" , function( $subject , $key ) use ( $regex2 ){
                        preg_match_all ( $regex2 , $subject , $matches );
                        return implode(",",$matches[1] );
                    }),   //图片
                    'config' => array('#prop-fac-list ' , 'html' ,"" , function( $subject , $key ){
                        $tags = str_replace(array("\r\n", "\r", "\n"), "", trim(strip_tags($subject)) );
                        return  array_values( array_filter(explode(" ", $tags)));
                        
                    }), //房间配置
                    'room'  => array('.prop-info-list li:eq(0)' , 'text'  , '-label' , function( $subject , $key ) use ( $regex3 ){
                        preg_match ( $regex3 , $subject , $matches );
                        return $matches[1];
                    }) ,
                    'hall'  => array('.prop-info-list li:eq(0)' , 'text'  , '-label' , function( $subject , $key )use ( $regex3 ){
                        preg_match ( $regex3 , $subject , $matches );
                        return $matches[2];
                    }) ,
                    'who'  => array('.prop-info-list li:eq(0)' , 'text'  , '-label' , function( $subject , $key )use ( $regex3 ){
                        preg_match ( $regex3 , $subject , $matches );
                        return $matches[3];
                    }) ,
                    'phone'  => array('#view-footer .view-footer-tel' , 'href'  , '' , function( $subject , $key ){
                         return substr($subject , 4 , 11);
                    }) ,
                    'name'  => array('.footer-broker-name' , 'text' ) , //联系人
                    'company'  => array('.footer-broker-company' , 'text' ) , //所属公司
                    'pay_type'  => array('.prop-price' , 'text'  , '-span' , function( $subject , $key ){
                         return $subject;
                    }) ,
                ),'','UTF-8','UTF-8' , true);
        
             
            $datas[$key] = $infoData->data[0];
            $datas[$key]['soure_id'] = $value['houseid'];
            $datas[$key]['soure'] = 'anjuke';



            //判断是否采集过
            $isCollect = model('admin/collect_log')->where('soure' , 'anjuke')->where('soure_id' , $value['houseid'])->count();
            if(  $isCollect ){
                 unset($datas[$key]);
                 continue;
            }

            //判断是否采集成功
            if( !$datas[$key]['title']  || $datas[$key]['xq_id']  || $datas[$key]['house_amount'] || $datas[$key]['name'] || $datas[$key]['phone']){
                 unset($datas[$key]);
                 continue;
            }


            $houseModel = model('admin/house');
            $isHouse = $houseModel->where('soure' , 'anjuke')->where('soure_id' , $value['houseid'])->count();
            if( $isHouse ){
                unset($datas[$key]);
                continue;
            }


              

            $newData[0] = $datas[$key];
            $ndata =  $this->collectStrToId( $newData ) ;
            unset($datas[$key]);
            $ndata[0]['id']="";

            
            $houseModel->isUpdate(false)->allowField(true)->save( $ndata[0] );

            echo $datas[$key]['title'] . '---' . $value['houseid']  . ' --- ok <br/>';

            //增加采集日志
            model('admin/collect_log')->insert( array(
                'house_id' => $houseModel->id , 
                'soure_id' => $ndata[0]['soure_id'],
                'soure'    =>'anjuke'
            ));
        }

       
        echo 'ok';






        //安居客 pc 规则 无法获取手机号
        // $data = QueryList::Query('https://bj.zu.anjuke.com/',
// array(
//                     "title"=> array('.wrapper .house-title' , 'text'),                        //标题
//                     'xq_id'=> array('.wrapper .house-info-zufang li:eq(7) a:eq(0)' , 'text') ,          //小区
//                     'house_amount'=> array('.wrapper .house-info-zufang .price em' , 'text') , //房屋价格
//                     'type_id' => array('.title-label .rent' , 'text') ,  //出租类型
//                     'area_size' => array('.info-tag.no-line em' , 'text' ) ,   //房屋面积
//                     'decorate_id' => array('.house-info-zufang li:eq(5) .info' , 'text') ,  //装修类型
//                     'current_floor' => array('.house-info-zufang li:eq(4) .info' , 'text' , "" , function( $subject , $key ){
//                        return substr($subject , 0 , 6);
//                     }) ,  //当前楼层
//                     'countt_floor' => array('.house-info-zufang li:eq(4) .info' , 'text'  , "" , function( $subject , $key ){
//                         return substr($subject , -6 , 2);
//                     }) ,  //总楼层
//                     'direction' => array('.house-info-zufang li:eq(3) .info' , 'text') ,  //方向
//                     'description' => array('.auto-general' , 'html') ,  //描述
//                     'imgs' => array('#room_pic_wrap ' , 'html' ,"" , function( $subject , $key ) use ( $regex2 ){
//                         preg_match_all ( $regex2 , $subject , $matches );
//                         return $matches[1];
//                     }),   //图片
//                     'config' => array('.house-info-peitao ' , 'html' ,"" , function( $subject , $key ) use ( $regex3 ){
//                         preg_match_all ( $regex3 , $subject , $matches );
//                         return $matches[1];
//                     })   //图片

                    
//                 )
        // );

       
        
        
    }

    /**
     * 采集内容转换
     */
    private function collectStrToId( $data = array() ){
        $ak = config('web.Bmap_ak');
        foreach ($data as $key => $value) {
            //判断小区是否存在
            $xiaoqu = model('admin/xiaoqu')->where('name' , $value['xq_id'])->find();
            if( $xiaoqu ){
                $data[$key]['xq_id'] = $xiaoqu['id'];
            } else {
                //不存在则 地图获取小区经纬度
                $url = 'http://api.map.baidu.com/geocoder/v2/?address='.$value['xq_id'].'&city=北京&output=json&ak=' . $ak ;
                $bmapResult = $this->curlQuery( $url , array() ,  0);
                $bmapResult = json_decode($bmapResult , true);
                //没有找到小区则退出当前循环
                if( $bmapResult['status'] == 0 ){
                    //保存小区信息
                    $xq_id = model('admin/xiaoqu')->insertGetId( array(
                        'name' => $value['xq_id'] , 
                        'lat' => $bmapResult['result']['location']['lat'],
                        'lon' => $bmapResult['result']['location']['lng']
                    ));
                    if( $xq_id ){
                        $data[$key]['xq_id'] = $xq_id;
                    } else {
                        unset( $data[$key]);
                        continue;
                    }
                    
                }else {
                    unset( $data[$key]);
                    continue;
                }
            }

            //出租类型
            $type_ids = $this->collect_rule['type_id'];
            foreach ($type_ids as $type_key => $type_val) {
                if( strpos($type_val , $value['type_id'] ) !==false){
                    $data[$key]['type_id'] = $type_key;
                    break;
                }
            }

            //装修类型
            $decorate_ids = $this->collect_rule['decorate_id'];
            foreach ($decorate_ids as $decorate_key => $decorate_val) {
                if( strpos( $decorate_val , $value['decorate_id']  ) !==false){
                    $data[$key]['decorate_id'] = $decorate_key;
                    break;
                }
            }

            //方向
            $direction_ids = $this->collect_rule['direction'];
            foreach ($direction_ids as $k => $val) {
                if( strpos( $val , $value['direction']  ) !==false){
                    $data[$key]['direction'] = $k;
                    break;
                }
            }

            //支付方式
            $pay_types = $this->collect_rule['pay_type'];
            foreach ($pay_types as $k => $val) {
                if( strpos( $val , $value['pay_type']  ) !==false){
                    $data[$key]['pay_type'] = $k;
                    break;
                }
            }

            //电梯
            if( $value['countt_floor'] > 6){
                $data[$key]['is_elevator'] = 1;
            }

            //房间配置
            $configs = $this->collect_rule['config'];
            $config_ids = [];
            if($value['config']){
                foreach ($value['config'] as $conKey => $conVal) {

                    foreach ($configs as $k => &$val) {
                        if( strpos( $val , $conVal  ) !== false){
                            $config_ids[] = $k;
                            unset($val);
                            break;
                        }
                    }
                }
            }
            
            $data[$key]['config_id'] = implode(',', $config_ids) ;

        }
        return $data;
    }


    private function curlQuery( $url , $postdata = array() , $isPost = 1  , $header = array () ){

        //$header[]= 'X-APICloud-AppKey:'.$appKey;

        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置否输出到页面
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10 ); //设置连接等待时间
        curl_setopt($ch, CURLOPT_HEADER, 0);   //返回头部信息
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        if( $isPost == 1){
            curl_setopt($ch, CURLOPT_POST, $isPost);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        }
        
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//这个是重点。
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 检查证书中是否设置域名  
        $data=curl_exec($ch);
        curl_close($ch);
    
        return $data;
    }




}