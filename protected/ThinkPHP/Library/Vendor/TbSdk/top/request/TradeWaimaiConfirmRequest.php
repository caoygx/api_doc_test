<?php
/**
 * TOP API: taobao.trade.waimai.confirm request
 * 
 * @author auto create
 * @since 1.0, 2014-11-07 15:40:49
 */
class TradeWaimaiConfirmRequest
{
	/** 
	 * 代送商ID
	 **/
	private $agentId;
	
	/** 
	 * 未确认发货的订单编号
	 **/
	private $orderId;
	
	private $apiParas = array();
	
	public function setAgentId($agentId)
	{
		$this->agentId = $agentId;
		$this->apiParas["agent_id"] = $agentId;
	}

	public function getAgentId()
	{
		return $this->agentId;
	}

	public function setOrderId($orderId)
	{
		$this->orderId = $orderId;
		$this->apiParas["order_id"] = $orderId;
	}

	public function getOrderId()
	{
		return $this->orderId;
	}

	public function getApiMethodName()
	{
		return "taobao.trade.waimai.confirm";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->orderId,"orderId");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
