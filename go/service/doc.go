package service

import (
	"doc/model"
	"doc/util"
	"encoding/json"
	"fmt"
	"strconv"
	"strings"
)

type ResponseStandard struct {
	Code int         `json:"code"`
	Msg  string      `json:"msg"`
	Data interface{} `json:"data"`
}

type ResponseList struct {
	Code int    `json:"code"`
	Msg  string `json:"msg"`
	Data struct {
		ControllerName string   `json:"controllerName"`
		ActionName     string   `json:"actionName"`
		APIList        []string `json:"apiList"`
	} `json:"data"`
}

var BaseUrl = "http://www.tesuo.com/"

func SetDocResponse(doc model.Doc) {
	//t := reflect.TypeOf(api)
	requestURL := BaseUrl + doc.URL
	fmt.Println(requestURL)
	var hJson interface{}
	err := json.Unmarshal([]byte(doc.ParamJson), &hJson)
	if err != nil {
		fmt.Println(err)
	}

	//get definition and comment for all column
	allColumn := util.GetColumn("user")

	var arrParam []string
	strQuery := ""
	data, ok := hJson.(map[string]interface{})
	if ok {
		for k, v := range data {
			switch vtype := v.(type) {
			case string:
				strQuery += k + "=" + vtype + "&"
				fmt.Println(k, vtype)
			case float64: //JSON numbers
				strQuery += k + "=" + strconv.FormatFloat(vtype, 'f', -1, 64) + "&"
				fmt.Println(k, vtype)
			case bool: //JSON booleans
				fmt.Println(k, vtype)
				strQuery += k + "=" + strconv.FormatBool(vtype) + "&"
			case []interface{}: //JSON arrays
				for k2, v2 := range vtype {
					fmt.Println(k2, v2, vtype)
				}
			case map[string]interface{}: //JSON objects
				for k2, v2 := range vtype {
					fmt.Println(k2, v2, vtype)
				}
			default:
				//fmt.Println(vtype)
			}

			//fmt.Println(allColumn["nickname"]["cloumn_comment"])
			//comment := allColumn[k]["cloumn_comment"]
			attribute := util.ParseComment(allColumn[k]["cloumn_comment"])
			zh := attribute["zh"]
			//fmt.Println(attribute)
			arrParam = append(arrParam, k+" "+zh)
		}
	}

	//queryStr := pgo.HTTPBuildQuery(hJson)
	strQuery = strings.TrimRight(strQuery, "&")

	//fmt.Println(strings.Join(arrParam, "\n"))
	//os.Exit(1)

	response, err := util.FileGetContent(requestURL, strQuery, "post")
	if err != nil {
		fmt.Println(err)
	}

	res := ResponseStandard{}
	json.Unmarshal(response, &res)
	if res.Code == 1 {
		fmt.Println(string(response))

		doc.ReturnJson = string(response)
		doc.Param = strings.Join(arrParam, "\n")

		model.DB.Save(&doc)
		//doc.Update()
		//os.Exit(1)
	} else {
		fmt.Println(res.Msg)
	}

	//os.Exit(1)

}

func saveApi() {

}

//文档生成步骤
//1.url入库
//2.人工选参数
//3.自动获取结果

//将所有url入库
func GenerateAllDoc() {

	url := "http://www.tesuo.com/index/getAllRoute"
	response, err := util.FileGetContent(url, "", "get")
	if err != nil {
		fmt.Println(err)
	}

	arrRes := ResponseList{}
	json.Unmarshal(response, &arrRes)
	fmt.Println(arrRes.Data.APIList)
	apiList := arrRes.Data.APIList
	//apiList = []string{"user/register"} //, //"user/index"
	for _, v := range apiList {
		//fmt.Println(k, v)
		doc := model.Doc{}
		doc.URL = v
		doc.ProjectID = 2
		doc.Save()

		//GenerateDoc(v)
		//break
	}
	//fmt.Print(string(response))
}

func SetAllDocResponse(docID int) {
	list := []model.Doc{}
	if docID >= 0 {
		model.DB.Where("project_id = ? and id = ?", 2, docID).Find(&list)
	} else {
		model.DB.Where("project_id = ?", 2).Find(&list)
	}

	for _, v := range list {
		SetDocResponse(v)
		//fmt.Println(v)
	}
}
