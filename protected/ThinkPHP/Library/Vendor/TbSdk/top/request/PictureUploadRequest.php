<?php
/**
 * TOP API: taobao.picture.upload request
 * 
 * @author auto create
 * @since 1.0, 2014-11-07 15:40:49
 */
class PictureUploadRequest
{
	/** 
	 * 图片上传的来源，有电脑版本宝贝发布，手机版本宝贝发布
client:computer电脑版本宝贝使用
client:phone手机版本宝贝使用
	 **/
	private $clientType;
	
	/** 
	 * 包括后缀名的图片标题,不能为空，如Bule.jpg,有些卖家希望图片上传后取图片文件的默认名
	 **/
	private $imageInputTitle;
	
	/** 
	 * 图片二进制文件流,不能为空,允许png、jpg、gif图片格式,3M以内。
	 **/
	private $img;
	
	/** 
	 * 图片分类ID，设置具体某个分类ID或设置0上传到默认分类，只能传入一个分类<br /> 支持最大值为：9223372036854775807<br /> 支持最小值为：0
	 **/
	private $pictureCategoryId;
	
	/** 
	 * 图片标题,如果为空,传的图片标题就取去掉后缀名的image_input_title,超过50字符长度会截取50字符,重名会在标题末尾加"(1)";标题末尾已经有"(数字)"了，则数字加1
	 **/
	private $title;
	
	private $apiParas = array();
	
	public function setClientType($clientType)
	{
		$this->clientType = $clientType;
		$this->apiParas["client_type"] = $clientType;
	}

	public function getClientType()
	{
		return $this->clientType;
	}

	public function setImageInputTitle($imageInputTitle)
	{
		$this->imageInputTitle = $imageInputTitle;
		$this->apiParas["image_input_title"] = $imageInputTitle;
	}

	public function getImageInputTitle()
	{
		return $this->imageInputTitle;
	}

	public function setImg($img)
	{
		$this->img = $img;
		$this->apiParas["img"] = $img;
	}

	public function getImg()
	{
		return $this->img;
	}

	public function setPictureCategoryId($pictureCategoryId)
	{
		$this->pictureCategoryId = $pictureCategoryId;
		$this->apiParas["picture_category_id"] = $pictureCategoryId;
	}

	public function getPictureCategoryId()
	{
		return $this->pictureCategoryId;
	}

	public function setTitle($title)
	{
		$this->title = $title;
		$this->apiParas["title"] = $title;
	}

	public function getTitle()
	{
		return $this->title;
	}

	public function getApiMethodName()
	{
		return "taobao.picture.upload";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->imageInputTitle,"imageInputTitle");
		RequestCheckUtil::checkNotNull($this->img,"img");
		RequestCheckUtil::checkNotNull($this->pictureCategoryId,"pictureCategoryId");
		RequestCheckUtil::checkMaxValue($this->pictureCategoryId,9223372036854775807,"pictureCategoryId");
		RequestCheckUtil::checkMinValue($this->pictureCategoryId,0,"pictureCategoryId");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
