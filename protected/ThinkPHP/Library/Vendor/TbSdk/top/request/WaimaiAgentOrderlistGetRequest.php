<?php
/**
 * TOP API: taobao.waimai.agent.orderlist.get request
 * 
 * @author auto create
 * @since 1.0, 2014-11-07 15:40:49
 */
class WaimaiAgentOrderlistGetRequest
{
	/** 
	 * 结束时间，格式: yyyy-mm-dd hh:mm:ss
	 **/
	private $endTime;
	
	/** 
	 * 订单状态
	 **/
	private $orderStatus;
	
	/** 
	 * 页数，默认第一页
	 **/
	private $pageNo;
	
	/** 
	 * 每页数，最大不超过30
	 **/
	private $pageSize;
	
	/** 
	 * 店铺ID
	 **/
	private $shopId;
	
	/** 
	 * 开始时间，格式：yyyy-mm-dd hh:mm:ss
	 **/
	private $startTime;
	
	private $apiParas = array();
	
	public function setEndTime($endTime)
	{
		$this->endTime = $endTime;
		$this->apiParas["end_time"] = $endTime;
	}

	public function getEndTime()
	{
		return $this->endTime;
	}

	public function setOrderStatus($orderStatus)
	{
		$this->orderStatus = $orderStatus;
		$this->apiParas["order_status"] = $orderStatus;
	}

	public function getOrderStatus()
	{
		return $this->orderStatus;
	}

	public function setPageNo($pageNo)
	{
		$this->pageNo = $pageNo;
		$this->apiParas["page_no"] = $pageNo;
	}

	public function getPageNo()
	{
		return $this->pageNo;
	}

	public function setPageSize($pageSize)
	{
		$this->pageSize = $pageSize;
		$this->apiParas["page_size"] = $pageSize;
	}

	public function getPageSize()
	{
		return $this->pageSize;
	}

	public function setShopId($shopId)
	{
		$this->shopId = $shopId;
		$this->apiParas["shop_id"] = $shopId;
	}

	public function getShopId()
	{
		return $this->shopId;
	}

	public function setStartTime($startTime)
	{
		$this->startTime = $startTime;
		$this->apiParas["start_time"] = $startTime;
	}

	public function getStartTime()
	{
		return $this->startTime;
	}

	public function getApiMethodName()
	{
		return "taobao.waimai.agent.orderlist.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->pageNo,"pageNo");
		RequestCheckUtil::checkNotNull($this->pageSize,"pageSize");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
