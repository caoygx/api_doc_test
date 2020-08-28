package main

import (
	"doc/service"

	_ "github.com/jinzhu/gorm/dialects/mysql"
)

func main() {

	//util.Download("a.jpg")

	//fmt.Println(model.DB)

	//apiGetTable()

	//service.GenerateAllDoc()
	docID := 63
	service.SetAllDocResponse(docID)
	//testjson()
}
