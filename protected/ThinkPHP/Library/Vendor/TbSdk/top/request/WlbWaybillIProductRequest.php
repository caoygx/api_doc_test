<?php
/**
 * TOP API: taobao.wlb.waybill.i.product request
 * 
 * @author auto create
 * @since 1.0, 2014-11-07 15:40:49
 */
class WlbWaybillIProductRequest
{
	/** 
	 * 查询物流商电子面单产品类型入参
	 **/
	private $waybillProductTypeRequest;
	
	private $apiParas = array();
	
	public function setWaybillProductTypeRequest($waybillProductTypeRequest)
	{
		$this->waybillProductTypeRequest = $waybillProductTypeRequest;
		$this->apiParas["waybill_product_type_request"] = $waybillProductTypeRequest;
	}

	public function getWaybillProductTypeRequest()
	{
		return $this->waybillProductTypeRequest;
	}

	public function getApiMethodName()
	{
		return "taobao.wlb.waybill.i.product";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->waybillProductTypeRequest,"waybillProductTypeRequest");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
