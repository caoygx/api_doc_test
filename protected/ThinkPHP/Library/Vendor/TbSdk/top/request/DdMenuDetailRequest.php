<?php
/**
 * TOP API: taobao.dd.menu.detail request
 * 
 * @author auto create
 * @since 1.0, 2014-11-07 15:40:49
 */
class DdMenuDetailRequest
{
	/** 
	 * 菜单编号
	 **/
	private $menuId;
	
	/** 
	 * 支付订单ID
	 **/
	private $orderId;
	
	/** 
	 * 外部门店编码
	 **/
	private $outStoreId;
	
	/** 
	 * 淘宝商户id
	 **/
	private $storeId;
	
	private $apiParas = array();
	
	public function setMenuId($menuId)
	{
		$this->menuId = $menuId;
		$this->apiParas["menu_id"] = $menuId;
	}

	public function getMenuId()
	{
		return $this->menuId;
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

	public function setOutStoreId($outStoreId)
	{
		$this->outStoreId = $outStoreId;
		$this->apiParas["out_store_id"] = $outStoreId;
	}

	public function getOutStoreId()
	{
		return $this->outStoreId;
	}

	public function setStoreId($storeId)
	{
		$this->storeId = $storeId;
		$this->apiParas["store_id"] = $storeId;
	}

	public function getStoreId()
	{
		return $this->storeId;
	}

	public function getApiMethodName()
	{
		return "taobao.dd.menu.detail";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
