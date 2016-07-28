<?php
/**
 * TOP API: taobao.fenxiao.dealer.requisitionorder.create request
 * 
 * @author auto create
 * @since 1.0, 2014-11-07 15:40:49
 */
class FenxiaoDealerRequisitionorderCreateRequest
{
	/** 
	 * 收货人所在街道地址
	 **/
	private $address;
	
	/** 
	 * 买家姓名（自提方式填写提货人姓名）
	 **/
	private $buyerName;
	
	/** 
	 * 收货人所在市
	 **/
	private $city;
	
	/** 
	 * 收货人所在区
	 **/
	private $district;
	
	/** 
	 * 身份证号（自提方式必填，填写提货人身份证号码，提货时用于确认提货人身份）
	 **/
	private $idCardNumber;
	
	/** 
	 * 配送方式。SELF_PICKUP：自提；LOGISTICS：仓库发货
	 **/
	private $logisticsType;
	
	/** 
	 * 买家的手机号码（1、此字段与phone字段至少填写一个。2、自提方式下此字段必填，保存提货人联系电话）
	 **/
	private $mobile;
	
	/** 
	 * 采购清单，存放多个采购明细，每个采购明细内部以‘:’隔开，多个采购明细之间以‘,’隔开. 例(分销产品id:skuid:购买数量:申请单价,分销产品id:skuid:购买数量:申请单价)，申请单价的单位为分。不存在sku请留空skuid，如（分销产品id::购买数量:申请单价）
	 **/
	private $orderDetail;
	
	/** 
	 * 买家联系电话（此字段和mobile字段至少填写一个）
	 **/
	private $phone;
	
	/** 
	 * 收货人所在地区邮政编码
	 **/
	private $postCode;
	
	/** 
	 * 收货人所在省份
	 **/
	private $province;
	
	private $apiParas = array();
	
	public function setAddress($address)
	{
		$this->address = $address;
		$this->apiParas["address"] = $address;
	}

	public function getAddress()
	{
		return $this->address;
	}

	public function setBuyerName($buyerName)
	{
		$this->buyerName = $buyerName;
		$this->apiParas["buyer_name"] = $buyerName;
	}

	public function getBuyerName()
	{
		return $this->buyerName;
	}

	public function setCity($city)
	{
		$this->city = $city;
		$this->apiParas["city"] = $city;
	}

	public function getCity()
	{
		return $this->city;
	}

	public function setDistrict($district)
	{
		$this->district = $district;
		$this->apiParas["district"] = $district;
	}

	public function getDistrict()
	{
		return $this->district;
	}

	public function setIdCardNumber($idCardNumber)
	{
		$this->idCardNumber = $idCardNumber;
		$this->apiParas["id_card_number"] = $idCardNumber;
	}

	public function getIdCardNumber()
	{
		return $this->idCardNumber;
	}

	public function setLogisticsType($logisticsType)
	{
		$this->logisticsType = $logisticsType;
		$this->apiParas["logistics_type"] = $logisticsType;
	}

	public function getLogisticsType()
	{
		return $this->logisticsType;
	}

	public function setMobile($mobile)
	{
		$this->mobile = $mobile;
		$this->apiParas["mobile"] = $mobile;
	}

	public function getMobile()
	{
		return $this->mobile;
	}

	public function setOrderDetail($orderDetail)
	{
		$this->orderDetail = $orderDetail;
		$this->apiParas["order_detail"] = $orderDetail;
	}

	public function getOrderDetail()
	{
		return $this->orderDetail;
	}

	public function setPhone($phone)
	{
		$this->phone = $phone;
		$this->apiParas["phone"] = $phone;
	}

	public function getPhone()
	{
		return $this->phone;
	}

	public function setPostCode($postCode)
	{
		$this->postCode = $postCode;
		$this->apiParas["post_code"] = $postCode;
	}

	public function getPostCode()
	{
		return $this->postCode;
	}

	public function setProvince($province)
	{
		$this->province = $province;
		$this->apiParas["province"] = $province;
	}

	public function getProvince()
	{
		return $this->province;
	}

	public function getApiMethodName()
	{
		return "taobao.fenxiao.dealer.requisitionorder.create";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->address,"address");
		RequestCheckUtil::checkNotNull($this->buyerName,"buyerName");
		RequestCheckUtil::checkNotNull($this->logisticsType,"logisticsType");
		RequestCheckUtil::checkNotNull($this->orderDetail,"orderDetail");
		RequestCheckUtil::checkMaxListSize($this->orderDetail,50,"orderDetail");
		RequestCheckUtil::checkNotNull($this->province,"province");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
