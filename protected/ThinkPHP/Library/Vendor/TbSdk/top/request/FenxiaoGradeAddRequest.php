<?php
/**
 * TOP API: taobao.fenxiao.grade.add request
 * 
 * @author auto create
 * @since 1.0, 2014-11-07 15:40:49
 */
class FenxiaoGradeAddRequest
{
	/** 
	 * 等级名称，等级名称不可重复<br /> 支持最大长度为：20<br /> 支持的最大列表长度为：20
	 **/
	private $name;
	
	private $apiParas = array();
	
	public function setName($name)
	{
		$this->name = $name;
		$this->apiParas["name"] = $name;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getApiMethodName()
	{
		return "taobao.fenxiao.grade.add";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->name,"name");
		RequestCheckUtil::checkMaxLength($this->name,20,"name");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
