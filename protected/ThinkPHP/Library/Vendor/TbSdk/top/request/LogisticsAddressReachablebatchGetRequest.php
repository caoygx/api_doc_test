<?php
/**
 * TOP API: taobao.logistics.address.reachablebatch.get request
 * 
 * @author auto create
 * @since 1.0, 2014-11-07 15:40:49
 */
class LogisticsAddressReachablebatchGetRequest
{
	/** 
	 * 筛单用户输入地址结构
	 **/
	private $addressList;
	
	private $apiParas = array();
	
	public function setAddressList($addressList)
	{
		$this->addressList = $addressList;
		$this->apiParas["address_list"] = $addressList;
	}

	public function getAddressList()
	{
		return $this->addressList;
	}

	public function getApiMethodName()
	{
		return "taobao.logistics.address.reachablebatch.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkMaxListSize($this->addressList,20,"addressList");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
