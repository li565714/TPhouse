<?php
namespace app\common\model;

use think\Model;

class House extends Model
{

	protected $autoWriteTimestamp = true;

	protected $createTime = 'add_time';




	//更新房屋举报次数
	public function setIncComplaints( $id = 0){
		//更新举报次数
		$this->where('id', $id)->setInc('complaints_num');

		//获取房屋信息
		$info = $this->field('id , complaints_num')->where('id', $id)->find() ;
		
		//判断举报次数大于3次则删除
		if( $info['complaints_num'] >= 3){
			$this->delete( $id );
		}
	}

	/**
	 * 删除房间信息
	 */
	public function delete( $id = 0 ){
		$this->where('id', $id)->update(['is_delete' => 1]);
	}

}