<?php
/**
 * TOP API: taobao.mbc.promotion.use request
 * 
 * @author auto create
 * @since 1.0, 2014-11-07 15:40:49
 */
class MbcPromotionUseRequest
{
	/** 
	 * 实际金额单位(分)
	 **/
	private $actualFee;
	
	/** 
	 * 优惠金额单位(分)
	 **/
	private $discountFee;
	
	/** 
	 * 有效结束时间
	 **/
	private $endTime;
	
	/** 
	 * 外部流水号，
promotion_type+outer_no 唯一
	 **/
	private $outerNo;
	
	/** 
	 * 权益ID
	 **/
	private $promotionId;
	
	/** 
	 * 权限类型
	 **/
	private $promotionType;
	
	/** 
	 * 有效开始时间
	 **/
	private $startTime;
	
	/** 
	 * 交易总金额单位(分)
	 **/
	private $totalFee;
	
	/** 
	 * 使用时间
	 **/
	private $useTime;
	
	/** 
	 * 买家淘宝ID
	 **/
	private $userId;
	
	private $apiParas = array();
	
	public function setActualFee($actualFee)
	{
		$this->actualFee = $actualFee;
		$this->apiParas["actual_fee"] = $actualFee;
	}

	public function getActualFee()
	{
		return $this->actualFee;
	}

	public function setDiscountFee($discountFee)
	{
		$this->discountFee = $discountFee;
		$this->apiParas["discount_fee"] = $discountFee;
	}

	public function getDiscountFee()
	{
		return $this->discountFee;
	}

	public function setEndTime($endTime)
	{
		$this->endTime = $endTime;
		$this->apiParas["end_time"] = $endTime;
	}

	public function getEndTime()
	{
		return $this->endTime;
	}

	public function setOuterNo($outerNo)
	{
		$this->outerNo = $outerNo;
		$this->apiParas["outer_no"] = $outerNo;
	}

	public function getOuterNo()
	{
		return $this->outerNo;
	}

	public function setPromotionId($promotionId)
	{
		$this->promotionId = $promotionId;
		$this->apiParas["promotion_id"] = $promotionId;
	}

	public function getPromotionId()
	{
		return $this->promotionId;
	}

	public function setPromotionType($promotionType)
	{
		$this->promotionType = $promotionType;
		$this->apiParas["promotion_type"] = $promotionType;
	}

	public function getPromotionType()
	{
		return $this->promotionType;
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

	public function setTotalFee($totalFee)
	{
		$this->totalFee = $totalFee;
		$this->apiParas["total_fee"] = $totalFee;
	}

	public function getTotalFee()
	{
		return $this->totalFee;
	}

	public function setUseTime($useTime)
	{
		$this->useTime = $useTime;
		$this->apiParas["use_time"] = $useTime;
	}

	public function getUseTime()
	{
		return $this->useTime;
	}

	public function setUserId($userId)
	{
		$this->userId = $userId;
		$this->apiParas["user_id"] = $userId;
	}

	public function getUserId()
	{
		return $this->userId;
	}

	public function getApiMethodName()
	{
		return "taobao.mbc.promotion.use";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->actualFee,"actualFee");
		RequestCheckUtil::checkNotNull($this->discountFee,"discountFee");
		RequestCheckUtil::checkNotNull($this->outerNo,"outerNo");
		RequestCheckUtil::checkNotNull($this->promotionId,"promotionId");
		RequestCheckUtil::checkNotNull($this->promotionType,"promotionType");
		RequestCheckUtil::checkNotNull($this->totalFee,"totalFee");
		RequestCheckUtil::checkNotNull($this->useTime,"useTime");
		RequestCheckUtil::checkNotNull($this->userId,"userId");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
