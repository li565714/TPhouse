<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
   'web_name' => '网站名称', 
   'keywords' => 'keywords',
   'description' => 'description',
   'beian'  => 'beian',
   'logo' => '20181113/35564dd837368bedf93f0c60221e2296.jpg',
   'QQ' => '123456',

   //VIP视频解析地址
   'vip_jx_url' => 'http://app.baiyug.cn:2019/vip/index.php?url=',
   'vip_jx_url2' => 'http://app.baiyug.cn:2019/vip/index.php?url=',
   'vip_jx_url3' => 'http://app.baiyug.cn:2019/vip/index.php?url=',
   'vip_jx_url4' => 'http://app.baiyug.cn:2019/vip/index.php?url=',
   'vip_jx_url5' => '2',

   //百度地图ak
   'Bmap_ak' => '6LgDkSdQF0IGhbhac4Lfjokl',
   //采集规则
   'collect_rule' => array(
      'type_id' => array(
         '10002' => '整租',
         '10001' => '合租',
         '10003' => '短租'
      ),
      'decorate_id' => array(
         '11004' => '豪装|豪装修|豪华装修',
         '11001' => '精装|精装修',
         '11002' => '简装|简装修|毛坯|简单装修',
         '11003' => '中装|中装修|中等装修|其他'
      ),
      'direction' => array(
         '13001' => '朝南|南北',
         '13002' => '朝北|东北',
         '13003' => '朝东',
         '13004' => '朝西',
         '13005' => '东南',
         '13006' => '西北',
         '13007' => '西南'
      ),
      'config' => array(
         '14001' => '床',
         '14002' => '衣柜',
         '14003' => '书桌',
         '14004' => '书桌椅',
         '14005' => 'wifi|宽带',
         '14006' => '洗衣机',
         '14007' => '冰箱',
         '14008' => '独立阳台|阳台',
         '14009' => '空调',
         '14010' => '电视',
         '14011' => '独立卫浴|卫生间',
         '14012' => '热水器',
         '14013' => '厨房|可做饭'
      ),
      'pay_type' => array(
         '12001' =>'押一付一|付1押1|面议',
         '12002' =>'押一付三|付3押1',
         '12003' =>'当月付',
         '12004' =>'半年付',
         '12005' =>'按天付'
      ),
      'build_type' => array(
         '17001' =>'普通住宅',
         '17002' =>'公寓',
         '17003' =>'别墅',
         '17004' =>'平方',
         '17005' =>'四合院',
         '17006' =>'其他'
      )
   ),

   'collect_soure' => array(
      'anjuke' => 'https://pages.anjukestatic.com/usersite/touch/img/app/144x144.png',
      'fang' => 'https://static.soufunimg.com/common_m/m_public/201511/images/app_fang2.png',
   )

];
