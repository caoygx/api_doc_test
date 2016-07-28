<?php
/**
 * TOP API: taobao.refund.refuse request
 * 
 * @author auto create
 * @since 1.0, 2014-11-07 15:40:49
 */
class RefundRefuseRequest
{
	/** 
	 * 退款单号
	 **/
	private $refundId;
	
	/** 
	 * 可选值为：售中：onsale，售后：aftersale，天猫退款为必填项。
	 **/
	private $refundPhase;
	
	/** 
	 * 退款版本号，天猫退款为必填项。
	 **/
	private $refundVersion;
	
	/** 
	 * 拒绝退款时的说明信息，长度2-200<br /> 支持最大长度为：200<br /> 支持的最大列表长度为：200
	 **/
	private $refuseMessage;
	
	/** 
	 * 拒绝退款时的退款凭证，一般是卖家拒绝退款时使用的发货凭证，最大长度130000字节，支持的图片格式：GIF, JPG, PNG。天猫退款为必填项。<br /> 支持的文件类型为：gif,jpg,png<br /> 支持的最大列表长度为：130000
	 **/
	private $refuseProof;
	
	private $apiParas = array();
	
	public function setRefundId($refundId)
	{
		$this->refundId = $refundId;
		$this->apiParas["refund_id"] = $refundId;
	}

	public function getRefundId()
	{
		return $this->refundId;
	}

	public function setRefundPhase($refundPhase)
	{
		$this->refundPhase = $refundPhase;
		$this->apiParas["refund_phase"] = $refundPhase;
	}

	public function getRefundPhase()
	{
		return $this->refundPhase;
	}

	public function setRefundVersion($refundVersion)
	{
		$this->refundVersion = $refundVersion;
		$this->apiParas["refund_version"] = $refundVersion;
	}

	public function getRefundVersion()
	{
		return $this->refundVersion;
	}

	public function setRefuseMessage($refuseMessage)
	{
		$this->refuseMessage = $refuseMessage;
		$this->apiParas["refuse_message"] = $refuseMessage;
	}

	public function getRefuseMessage()
	{
		return $this->refuseMessage;
	}

	public function setRefuseProof($refuseProof)
	{
		$this->refuseProof = $refuseProof;
		$this->apiParas["refuse_proof"] = $refuseProof;
	}

	public function getRefuseProof()
	{
		return $this->refuseProof;
	}

	public function getApiMethodName()
	{
		return "taobao.refund.refuse";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->refundId,"refundId");
		RequestCheckUtil::checkNotNull($this->refuseMessage,"refuseMessage");
		RequestCheckUtil::checkMaxLength($this->refuseMessage,200,"refuseMessage");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
