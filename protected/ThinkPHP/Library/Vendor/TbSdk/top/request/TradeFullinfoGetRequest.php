<?php
/**
 * TOP API: taobao.trade.fullinfo.get request
 * 
 * @author auto create
 * @since 1.0, 2014-11-07 15:40:49
 */
class TradeFullinfoGetRequest
{
	/** 
	 * 1.Trade中可以指定返回的fields：seller_nick, buyer_nick, title, type, created, tid, seller_rate,buyer_flag, buyer_rate, status, payment, adjust_fee, post_fee, total_fee, pay_time, end_time, modified, consign_time, buyer_obtain_point_fee, point_fee, real_point_fee, received_payment, commission_fee, buyer_memo, seller_memo, alipay_no,alipay_id,buyer_message, pic_path, num_iid, num, price, buyer_alipay_no, receiver_name, receiver_state, receiver_city, receiver_district, receiver_address, receiver_zip, receiver_mobile, receiver_phone,seller_flag, seller_alipay_no, seller_mobile, seller_phone, seller_name, seller_email, available_confirm_fee, has_post_fee, timeout_action_time, snapshot_url, cod_fee, cod_status, shipping_type, trade_memo, is_3D,buyer_email,buyer_area, trade_from,is_lgtype,is_force_wlb,is_brand_sale,buyer_cod_fee,discount_fee,seller_cod_fee,express_agency_fee,invoice_name,service_orders,credit_cardfee,step_trade_status,step_paid_fee,mark_desc,has_yfx,yfx_fee,yfx_id,yfx_type,trade_source(注：当该授权用户为卖家时不能查看买家buyer_memo,buyer_flag),eticket_ext,send_time, is_daixiao,is_part_consign, arrive_interval, arrive_cut_time, consign_interval,zero_purchase,alipay_point,pcc_af,2.Order中可以指定返回fields：orders.title, orders.pic_path, orders.price, orders.num, orders.num_iid, orders.sku_id, orders.refund_status, orders.status, orders.oid, orders.total_fee, orders.payment, orders.discount_fee, orders.adjust_fee, orders.snapshot_url, orders.timeout_action_time，orders.sku_properties_name, orders.item_meal_name, orders.item_meal_id,orders.buyer_rate, orders.seller_rate, orders.outer_iid, orders.outer_sku_id, orders.refund_id, orders.seller_type, orders.is_oversold,orders.end_time,orders.order_from,orders.consign_time,orders.shipping_type,orders.logistics_company,orders.invoice_no, orders.is_daixiao
3.fields：orders（返回Order的所有内容）
4.flelds：promotion_details(返回promotion_details所有内容，优惠详情),invoice_name(发票抬头),orders.is_www(子订单是否是www订单,orders.store_code(发货的仓库编码)<br>
5. field:service_tags(返回物流标签)
	 **/
	private $fields;
	
	/** 
	 * 交易编号<br /> 支持最大值为：9223372036854775807<br /> 支持最小值为：1
	 **/
	private $tid;
	
	private $apiParas = array();
	
	public function setFields($fields)
	{
		$this->fields = $fields;
		$this->apiParas["fields"] = $fields;
	}

	public function getFields()
	{
		return $this->fields;
	}

	public function setTid($tid)
	{
		$this->tid = $tid;
		$this->apiParas["tid"] = $tid;
	}

	public function getTid()
	{
		return $this->tid;
	}

	public function getApiMethodName()
	{
		return "taobao.trade.fullinfo.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->fields,"fields");
		RequestCheckUtil::checkNotNull($this->tid,"tid");
		RequestCheckUtil::checkMaxValue($this->tid,9223372036854775807,"tid");
		RequestCheckUtil::checkMinValue($this->tid,1,"tid");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
