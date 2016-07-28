<?php
/**
 * TOP API: taobao.fenxiao.dealer.requisitionorder.query request
 * 
 * @author auto create
 * @since 1.0, 2014-11-07 15:40:49
 */
class FenxiaoDealerRequisitionorderQueryRequest
{
	/** 
	 * 经销采购单编号。
多个编号用英文符号的逗号隔开。最多支持50个经销采购单编号的查询。
	 **/
	private $dealerOrderIds;
	
	/** 
	 * 多个字段用","分隔。 fields 如果为空：返回所有经销采购单对象(dealer_orders)字段。 如果不为空：返回指定采购单对象(dealer_orders)字段。 例1： dealer_order_details.product_id 表示只返回product_id 例2： dealer_order_details 表示只返回明细列表
	 **/
	private $fields;
	
	private $apiParas = array();
	
	public function setDealerOrderIds($dealerOrderIds)
	{
		$this->dealerOrderIds = $dealerOrderIds;
		$this->apiParas["dealer_order_ids"] = $dealerOrderIds;
	}

	public function getDealerOrderIds()
	{
		return $this->dealerOrderIds;
	}

	public function setFields($fields)
	{
		$this->fields = $fields;
		$this->apiParas["fields"] = $fields;
	}

	public function getFields()
	{
		return $this->fields;
	}

	public function getApiMethodName()
	{
		return "taobao.fenxiao.dealer.requisitionorder.query";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->dealerOrderIds,"dealerOrderIds");
		RequestCheckUtil::checkMaxListSize($this->dealerOrderIds,50,"dealerOrderIds");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
