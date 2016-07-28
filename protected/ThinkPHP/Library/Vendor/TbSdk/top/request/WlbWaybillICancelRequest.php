<?php
/**
 * TOP API: taobao.wlb.waybill.i.cancel request
 * 
 * @author auto create
 * @since 1.0, 2014-11-07 15:40:49
 */
class WlbWaybillICancelRequest
{
	/** 
	 * 取消接口入参
	 **/
	private $waybillApplyCancelRequest;
	
	private $apiParas = array();
	
	public function setWaybillApplyCancelRequest($waybillApplyCancelRequest)
	{
		$this->waybillApplyCancelRequest = $waybillApplyCancelRequest;
		$this->apiParas["waybill_apply_cancel_request"] = $waybillApplyCancelRequest;
	}

	public function getWaybillApplyCancelRequest()
	{
		return $this->waybillApplyCancelRequest;
	}

	public function getApiMethodName()
	{
		return "taobao.wlb.waybill.i.cancel";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->waybillApplyCancelRequest,"waybillApplyCancelRequest");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}