<?php
namespace app\index\controller;

class Vip {


    public function jx(){
        $url = input('url');

        if( strpos($url , 'youku.com') ){
            $this->video( $url , 'youku' );
        }

        if( strpos($url , 'iqiyi.com') ){
            
            $this->video( $url , 'iqiyi' );
        }

        if( strpos($url , 'v.qq.com') ){
      
            $this->video( $url , 'qq' );
        }

        if( strpos($url , 'sohu.com') ){
          
            $this->video( $url , 'sohu' );
        }

        if( strpos($url , 'mgtv.com') ){
          
            $this->video( $url , 'mgtv' );
        }


    }
    /**
     * 新增房屋信息
     */
    public function video( $url , $type ){
        
        //第一步
        $header = array(
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
            'User-Agent: Mozilla/5.0 (Linux; U; Android 8.0.0; zh-cn; MI 6 Build/OPR1.170623.027) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/61.0.3163.128 Mobile Safari/537.36 XiaoMi/MiuiBrowser/9.8.7',
             'Referer: http://app.baiyug.cn:2019'
        );

        $url = 'http://app.baiyug.cn:2019/vip/apis.php?url=' . $url;
        $getSign =  $this->curlQuery($url , [] , 0 , $header) ;
        preg_match_all('/<iframe[^>]*\s+src="([^"]*)"[^>]*>/is', $getSign,$arr);
        $header_url =  substr($arr[1][1] , 1 ) ;



        //第二步
        $header = array(
            'Referer: '.$url
        );
        $url2 = 'http://app.baiyug.cn:2019/vip/' . $header_url;

        $data2 =  $this->curlQuery($url2 , [] , 0 , $header) ;

        preg_match_all('/<iframe[^>]*\s+src="([^"]*)"[^>]*>/is', $data2,$arr2);


        //第三步
        $url3 =  $arr2[1][0];
        $header = array( 
           'Referer: '.$url2
        ); 
        $data3 = ( $this->curlQuery($url3 , [] , 0 , $header) );

        preg_match_all('/<iframe[^>]*\s+src="([^"]*)"[^>]*>/is', $data3,$arr3);
        
        if( $type == 'qq'){

            //preg_match_all('/<video[^>]*\s+src="([^"]*)"[^>]*>/is', $data3,$arr3);
            //echo $arr3[1][0];die;
        }


        //视频根目录
        $url_parse = parse_url($arr3[1][0]);
        
        $vv = $url_parse['scheme'] . "://" .  dirname( $url_parse['host'] . $url_parse['path'] ) . '/' ;

        //第四步
        $url4 =  $arr3[1][0]  ;
        $header = array( 
           'Referer: '.$url3
        ); 
        $data4 = ( $this->curlQuery($url4 , [] , 0 , $header) );
        
        
        if( $type =='iqiyi' ){
          
        }elseif( $type =='qq'){
            preg_match_all('/<iframe[^>]*\s+src="([^"]*)"[^>]*>/is', $data4,$iframeData);
            $video = parse_url($iframeData[1][0]);
            echo substr( $video['query'] , 4 ) ;
        } else {
            $iframeData =  str_replace(' src="/ckplayer/ckplayer.js"' , '  src="http://app.baiyug.cn:2019/ckplayer/ckplayer.js" ' , $data4);
            $iframeData =  str_replace('<video src="' , '<video src="'.$vv , $iframeData);
            $iframeData =  str_replace('.htm' , '' , $iframeData);
            $iframeData =  str_replace("a: '" , "a: '".$vv , $iframeData);
            
            // $iframeData =  str_replace('/ckplayer/ckplayer.swf' , 'http://app.baiyug.cn:2019/ckplayer/ckplayer.swf' , $iframeData);
            dump  ($iframeData);
        }
        
        
    }



    /**
     * 
     */
    public function videobak(){
        
        $header = array( 
           'Referer: http://app.baiyug.cn:2019/vip/api.php?url=V1ZWb1UwMUhUa1ZpTTFwTlRURnNNVnBXWXpWTlYwVjZWbGhXV2sxcWJEQlVSRTVoV20xTmVXRklXbXRsVkd4M1YydFpOVmRXVWxsalJ6Vk9WbFJXUmxsVVRuTlViVVp6VWxoc1ZWWlZWVFZWUmsweFlqSlNTRTFZVFQwPQ=='
        ); 

        $url = "http://app.baiyug.cn:2019/vip/youku.php?url=http://v.youku.com/v_show/id_XMzg1NDkyMjQ2MA==.html";
        $data = ( $this->curlQuery($url , [] , 0 , $header) );

        preg_match_all('/<iframe[^>]*\s+src="([^"]*)"[^>]*>/is', $data,$arr);
        $iframe_url =  'h'. substr($arr[1][0] , 1 , -1 ) ;
        $header2 = array(
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
            'User-Agent: Mozilla/5.0 (Linux; U; Android 8.0.0; zh-cn; MI 6 Build/OPR1.170623.027) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/61.0.3163.128 Mobile Safari/537.36 XiaoMi/MiuiBrowser/9.8.7',
            'Referer: http://app.baiyug.cn:2019/vip/youku.php?url=http://v.youku.com/v_show/id_XMzg1NDkyMjQ2MA==.html'
        );
        $iframeData = $this->curlQuery($iframe_url , [] , 0 , $header2) ;
        
        $iframeData =  str_replace(' src="/ckplayer/ckplayer.js"' , '  src="http://app.baiyug.cn:2019/ckplayer/ckplayer.js" ' , $iframeData);
        $iframeData =  str_replace('<video src="' , '<video src="http://all.baiyug.cn:2019/youku/' , $iframeData);
        $iframeData =  str_replace('.htm' , '' , $iframeData);
        $iframeData =  str_replace("a: '" , "a: 'http://all.baiyug.cn:2019/youku/" , $iframeData);
        
        // $iframeData =  str_replace('/ckplayer/ckplayer.swf' , 'http://app.baiyug.cn:2019/ckplayer/ckplayer.swf' , $iframeData);
        echo  ($iframeData);
        
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
