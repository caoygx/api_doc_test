package util

import (
	"context"
	"fmt"
	"path"
	"path/filepath"

	uuid "github.com/iris-contrib/go.uuid"
	_ "github.com/jinzhu/gorm/dialects/mysql"
	"github.com/qiniu/api.v7/v7/auth/qbox"
	"github.com/qiniu/api.v7/v7/storage"
)

var (
	accessKey = "223vtPMRMbbF6z6uutpiA-zeSJXiVzV04u8NR_O-" // 七牛的accessKey 去七牛后台获取
	secretKey = "XNEbydtIkhOXAWzxNGy0cAkFG2RwUBZb_eL0z4cG" // 七牛的secretKey 去七牛后台获取
	bucket    = "uztoutiao"                                // 上传空间 去七牛后台创建
)

func init() {

}
func UploadImg(localFile string) string {
	// 鉴权
	mac := qbox.NewMac(accessKey, secretKey)

	// 上传策略
	putPolicy := storage.PutPolicy{
		Scope:   bucket,
		Expires: 7200,
	}

	// 获取上传token
	upToken := putPolicy.UploadToken(mac)

	// 上传Config对象
	cfg := storage.Config{}
	cfg.Zone = &storage.ZoneHuanan //指定上传的区域
	cfg.UseHTTPS = false           // 是否使用https域名
	cfg.UseCdnDomains = false      //是否使用CDN上传加速

	// 需要上传的文件
	//localFile := "./test.png"
	paths, fileName := filepath.Split(localFile)
	fmt.Println(paths, fileName)          //获取路径中的目录及文件名 E:\data\  test.txt
	fmt.Println(filepath.Base(localFile)) //获取路径中的文件名test.txt
	fmt.Println(path.Ext(localFile))      //获取路径中的文件的后缀 .txt

	// 创建 UUID v4
	u1 := uuid.Must(uuid.NewV4())
	println(`生成的UUID v4：`)
	println(u1.String())

	// 七牛key
	qiniuPath := u1.String() + "_test" + path.Ext(localFile)
	println(qiniuPath)

	// 构建表单上传的对象
	formUploader := storage.NewFormUploader(&cfg)
	ret := storage.PutRet{}

	// 上传文件
	err := formUploader.PutFile(context.Background(), &ret, upToken, qiniuPath, localFile, nil)
	if err != nil {
		fmt.Println("上传文件失败,原因:", err)
		return ""
	}
	fmt.Println("上传成功,key为:", ret.Key)
	return ret.Key
}
