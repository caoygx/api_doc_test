<?php
/**
 * TOP API: taobao.hotel.fixbooking request
 * 
 * @author auto create
 * @since 1.0, 2014-11-07 15:40:49
 */
class HotelFixbookingRequest
{
	/** 
	 * booking酒店ID
	 **/
	private $bookingId;
	
	/** 
	 * 国家代码
	 **/
	private $couCode;
	
	/** 
	 * 酒店名称
	 **/
	private $hnCode;
	
	/** 
	 * 评价数
	 **/
	private $revCount;
	
	private $apiParas = array();
	
	public function setBookingId($bookingId)
	{
		$this->bookingId = $bookingId;
		$this->apiParas["booking_id"] = $bookingId;
	}

	public function getBookingId()
	{
		return $this->bookingId;
	}

	public function setCouCode($couCode)
	{
		$this->couCode = $couCode;
		$this->apiParas["cou_code"] = $couCode;
	}

	public function getCouCode()
	{
		return $this->couCode;
	}

	public function setHnCode($hnCode)
	{
		$this->hnCode = $hnCode;
		$this->apiParas["hn_code"] = $hnCode;
	}

	public function getHnCode()
	{
		return $this->hnCode;
	}

	public function setRevCount($revCount)
	{
		$this->revCount = $revCount;
		$this->apiParas["rev_count"] = $revCount;
	}

	public function getRevCount()
	{
		return $this->revCount;
	}

	public function getApiMethodName()
	{
		return "taobao.hotel.fixbooking";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->bookingId,"bookingId");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
