package main

import (
	"doc/service"

	_ "github.com/jinzhu/gorm/dialects/mysql"
)

func main() {

	//生成文档3步

	//1.获取所有路由，将其加入到文档中。
	//service.GenerateAllDoc()

	//2.手动添加每个接口的参数

	//3.自动根据配置好的参数来生成文档结果
	docID := 63 //id为0则表示生成所有接口的请求值
	service.SetAllDocResponse(docID)

}
