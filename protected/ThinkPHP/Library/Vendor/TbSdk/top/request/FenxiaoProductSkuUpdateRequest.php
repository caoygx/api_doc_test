<?php
/**
 * TOP API: taobao.fenxiao.product.sku.update request
 * 
 * @author auto create
 * @since 1.0, 2014-11-07 15:40:49
 */
class FenxiaoProductSkuUpdateRequest
{
	/** 
	 * 代销采购价
	 **/
	private $agentCostPrice;
	
	/** 
	 * 经销采购价
	 **/
	private $dealerCostPrice;
	
	/** 
	 * 产品ID
	 **/
	private $productId;
	
	/** 
	 * sku属性
	 **/
	private $properties;
	
	/** 
	 * 产品SKU库存
	 **/
	private $quantity;
	
	/** 
	 * 商家编码
	 **/
	private $skuNumber;
	
	/** 
	 * 采购基准价
	 **/
	private $standardPrice;
	
	private $apiParas = array();
	
	public function setAgentCostPrice($agentCostPrice)
	{
		$this->agentCostPrice = $agentCostPrice;
		$this->apiParas["agent_cost_price"] = $agentCostPrice;
	}

	public function getAgentCostPrice()
	{
		return $this->agentCostPrice;
	}

	public function setDealerCostPrice($dealerCostPrice)
	{
		$this->dealerCostPrice = $dealerCostPrice;
		$this->apiParas["dealer_cost_price"] = $dealerCostPrice;
	}

	public function getDealerCostPrice()
	{
		return $this->dealerCostPrice;
	}

	public function setProductId($productId)
	{
		$this->productId = $productId;
		$this->apiParas["product_id"] = $productId;
	}

	public function getProductId()
	{
		return $this->productId;
	}

	public function setProperties($properties)
	{
		$this->properties = $properties;
		$this->apiParas["properties"] = $properties;
	}

	public function getProperties()
	{
		return $this->properties;
	}

	public function setQuantity($quantity)
	{
		$this->quantity = $quantity;
		$this->apiParas["quantity"] = $quantity;
	}

	public function getQuantity()
	{
		return $this->quantity;
	}

	public function setSkuNumber($skuNumber)
	{
		$this->skuNumber = $skuNumber;
		$this->apiParas["sku_number"] = $skuNumber;
	}

	public function getSkuNumber()
	{
		return $this->skuNumber;
	}

	public function setStandardPrice($standardPrice)
	{
		$this->standardPrice = $standardPrice;
		$this->apiParas["standard_price"] = $standardPrice;
	}

	public function getStandardPrice()
	{
		return $this->standardPrice;
	}

	public function getApiMethodName()
	{
		return "taobao.fenxiao.product.sku.update";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->productId,"productId");
		RequestCheckUtil::checkNotNull($this->properties,"properties");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
