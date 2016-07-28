<?php
/**
 * TOP API: taobao.videos.delete request
 * 
 * @author auto create
 * @since 1.0, 2014-11-07 15:40:49
 */
class VideosDeleteRequest
{
	/** 
	 * 在淘宝视频中的应用key，该值向淘宝视频申请产生
	 **/
	private $videoAppKey;
	
	/** 
	 * 视频id列表
	 **/
	private $videoIds;
	
	private $apiParas = array();
	
	public function setVideoAppKey($videoAppKey)
	{
		$this->videoAppKey = $videoAppKey;
		$this->apiParas["video_app_key"] = $videoAppKey;
	}

	public function getVideoAppKey()
	{
		return $this->videoAppKey;
	}

	public function setVideoIds($videoIds)
	{
		$this->videoIds = $videoIds;
		$this->apiParas["video_ids"] = $videoIds;
	}

	public function getVideoIds()
	{
		return $this->videoIds;
	}

	public function getApiMethodName()
	{
		return "taobao.videos.delete";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->videoAppKey,"videoAppKey");
		RequestCheckUtil::checkNotNull($this->videoIds,"videoIds");
		RequestCheckUtil::checkMaxListSize($this->videoIds,100,"videoIds");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
