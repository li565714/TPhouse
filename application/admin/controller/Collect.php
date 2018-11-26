<?php
namespace app\admin\controller;

use QL\QueryList;


class Collect extends Base
{

    protected $model = '';
    
    public function _initialize(){
        
        parent::_initialize();

        $this->model = model('CollectRule');
        $this->allowField = true;
      
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
                    'pay_type'  => array('.prop-price' , 'text'  , '-span' , function( $subject , $key ){
                         return $subject;
                    }) ,
                )
            );
            
            $datas[$key] = $infoData->data[0];
            $datas[$key]['houseid'] = $value['houseid'];
            $datas[$key]['soure'] = 'anjuke';
            dump($datas);
            break;

        }

        foreach ($infoData->data as $key => $value) {
            
        }







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

   


}