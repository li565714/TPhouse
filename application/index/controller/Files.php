<?php
namespace app\index\controller;
use \think\File;
//引用model
// use app\index\model\Node;

/**
 * =============================
 * TP5商城
 * 附件控制器
 */
class Files extends Base{   

    protected $model = '';
    
    public function _initialize(){
        parent::_initialize();
    }



    /**
     * 图片上传
     */
    public function upload()
    { 
        
        $file = request()->file('file');
        // 上传文件
        $info = $file->validate(['size'=>1024*1024*2,'ext'=>'jpg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'uploads');
        if ($info) {
            
            $data['pathname'] = str_replace("\\","/",$info->getPathname());
            $data['filename'] = str_replace("\\","/",$info->getFilename());
            $data['saveName'] = str_replace("\\","/",$info->getSaveName());
            //上传成功
            $ossUpload = $this->qcloudCosUpload( $data['filename'] , $data ,$info->getInfo());
            if($ossUpload == 'ok'){
                
                $path['info'] = $info->getInfo();
                $path['path'] = get_oss_img_crop( $data['saveName'] );
                $path['file'] = $data['saveName'] ;
                return json(array('status' => 1, 'data' =>$path ,  'msg' => '上传成功！'));
            } else {
                return json(array('status'=>0,'msg'=>$ossUpload));
            }
            
        }else{
            // 上传失败获取错误信息
            return json(array('status'=>0,'msg'=>$file->getError()));
        }
    }



    /**
     * 腾讯对象存储-文件上传
     * @datatime 2018/05/17 09:20
     * @author lgp
     */
    public function qcloudCosUpload( $file = '' , $info = array() , $fileInfo  ){
        if( !$file  || !$info ){
            return '缺少参数';
        }
        //引用COS sdk
        \think\Loader::import('qcloud.cos-php-sdk-v5.vendor.autoload'); 
        $cosClient = new \Qcloud\Cos\Client(
            array(
                'region'      => config('QCLOUD_COS.region'),
                'credentials' => array(
                    'appId'     => config('QCLOUD_COS.appId'),
                    'secretId'  => config('QCLOUD_COS.SecretId'),
                    'secretKey' => config('QCLOUD_COS.SecretKey')
                )
            )
        );
        $file = $info['pathname'];
        try {
            $data = array( 'Bucket' => config('QCLOUD_COS.bucket'), 'Key'  => $info['saveName'], 'Body' => fopen($file, 'rb') );
            //判断文件大小 大于5M就分块上传
            $result = $cosClient->Upload( $data['Bucket'] , $data['Key'] , $data['Body']  );

            //上传成功，自己编码
            if( $result ){
                if( config('QCLOUD_COS.unlink_file') == 1){
                    //是否删除本地
                    //unlink($file);
                }
                return 'ok';
            }
        } catch (\Exception $e) {
            echo "$e\n";die;
            return '上传失败';
        }

    }

    /**
     * 阿里对象存储-文件上传
     * @datatime 2018/1/19 16:20
     * @author lgp
     */
    public function aliOssUpload( $file = '' , $info = array() ){
        if( !$file  || !$info ){
            return '缺少参数';
        }

        //引用OSS sdk
        \think\Loader::import('aliyun-oss-php-sdk-master.autoload'); 

        $accessKeyId = config('ALI_OSS_CONFIG.accessKeyId');
        $accessKeySecret = config('ALI_OSS_CONFIG.accessKeySecret');
        $endpoint  =  config('ALI_OSS_CONFIG.endpoint');//你的阿里云OSS地址
        $isCName = config('ALI_OSS_CONFIG.isCName');
        $ossClient = new \OSS\OssClient($accessKeyId, $accessKeySecret, $endpoint , $isCName);
        $bucket = config('ALI_OSS_CONFIG.bucket');//oss中的文件上传空间
        $object =  $info['saveName'];//想要保存文件的名称
        
        $file = $info['pathname'];
        //$file = './uploads/'.$info['file']['savepath'].$info['file']['savename'];//文件路径，必须是本地的。
        try{
            $result = $ossClient->uploadFile($bucket,$object,$file);
            
            //上传成功，自己编码
            if($result){
                if( config('ALI_OSS_CONFIG.unlink_file') == 1){
                    //是否删除本地
                    unlink($file);
                }
                return 'ok';
            }
            
        }catch (Exception $e) {  
            return '上传oss异常';
        }
    }
   


}
