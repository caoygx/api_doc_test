package util

import (
	"database/sql"
	"fmt"
	"strings"
)

var db *sql.DB
var err error
var TablePrefix = "cgf_"

func init() {
	db, _ = sql.Open("mysql", "root:123456@(192.168.26.118:3306)/tesuo")
	//defer db.Close()
	err := db.Ping()
	if err != nil {
		fmt.Println("数据库连接失败")
		//return nil
	}
}

// func GetColumnComment() {

// 	DB.Where("name LIKE ?", "%jin%").Find(&users)
// }

func GetColumn(table string) map[string]map[string]string {

	var rows *sql.Rows
	sql := "SELECT COLUMN_NAME,DATA_TYPE,COLUMN_COMMENT FROM `information_schema`.`COLUMNS` WHERE `TABLE_SCHEMA` = 'tesuo' and `TABLE_NAME` = '" + TablePrefix + table + "' "
	rows, err = db.Query(sql) //获取所有数据
	fmt.Println(sql)
	if err != nil {
		panic(err)
	}
	var column_name, data_type, cloumn_comment string
	//results := make(map[string]interface{})
	//results2 := make([]interface{}, 0)
	//
	allColumns := make(map[string]map[string]string)
	for rows.Next() { //循环显示所有的数据

		rows.Scan(&column_name, &data_type, &cloumn_comment)
		//fmt.Println(table_name + " -- " + table_comment)

		if column_name != "" {
			row := make(map[string]string)
			row["column_name"] = column_name
			row["data_type"] = data_type
			if cloumn_comment != "" {
				row["cloumn_comment"] = cloumn_comment
			} else {
				row["cloumn_comment"] = column_name
			}
			//results[table_name] = row
			//rowWithKey := make(map[string]map[string]string)
			//rowWithKey[column_name] = row
			//results2 = append(results2, rowWithKey)
			allColumns[column_name] = row
		}
	}

	//fmt.Print(results)
	//fmt.Print(results2)
	return allColumns
}

func ParseComment(comment string) map[string]string {

	// if comment == "" {
	// 	return ""
	// }

	//先提取正则，避免正则里的特殊符号污染后面处理
	/*reg = '/<<(.+)>>/'
	validateReg = ''
	preg_match(reg,comment,match)
	if(!empty(match[1])){
		validateReg = match[1]
		comment = preg_replace(reg,'reg',comment)
	}
	*/

	//comment = '状态-select-禁用则不能访问 | 7 | require | 0:禁用,1:正常,2:审核中'
	//id=name='status'
	//attribute := make(map[string]map[string]string)
	attribute := make(map[string]string)
	//comment = '状态|0:禁用,1:正常,2:待审核'
	//状态-select-禁用则不能访问 | 7 | require | 0:禁用,1:正常,2:审核中
	//arr = explode("|", comment)
	arr := strings.Split(comment, "|")
	//arr = array_map('trim', arr)

	c := len(arr)
	switch true {
	//状态-select-禁用则不能访问 | 7 | require | 0:禁用,1:正常,2:审核中
	case (c >= 4):
		attribute["options"] = arr[3]
		attribute["checkType"] = arr[2]
		attribute["showPage"] = arr[1]
		attribute["title"] = arr[0]
		//attribute["options"] = arr[3]

		//fmt.Println(attribute)

	//状态-select-禁用则不能访问 | 7 | require
	case (c >= 3):
		attribute["checkType"] = arr[2]
		attribute["showPage"] = strings.TrimSpace(arr[1])
		attribute["title"] = strings.TrimSpace(arr[0])
		//fmt.Println(c)

	//状态-select-禁用则不能访问 | 7
	case (c >= 2):
		attribute["showPage"] = strings.TrimSpace(arr[1])
		attribute["title"] = strings.TrimSpace(arr[0])

		//if (!is_numeric(showPage)) E('显示页面属性必须是数字')

	//状态-select-禁用则不能访问
	case (c >= 1):
		attribute["title"] = strings.TrimSpace(arr[0])

	}

	// attribute["options"] = options
	// attribute["options"] = options

	// var (
	// 	tips     string = ""
	// 	htmlType string = ""
	// 	name     string = ""
	// )
	arrTitle := strings.Split(attribute["title"], "-")
	mapTitle := make(map[string]string)
	c = len(arrTitle)
	switch true {

	//状态-select-禁用则不能访问
	case (c >= 3):
		//tips = arrTitle[2]
		mapTitle["tips"] = arrTitle[2]
		mapTitle["htmlType"] = arrTitle[1]
		mapTitle["zh"] = arrTitle[0]

	//状态-select
	case (c >= 2):
		mapTitle["htmlType"] = arrTitle[1]
		mapTitle["zh"] = arrTitle[0]

	//状态
	case (c >= 1):
		//name = arrTitle[0]
		mapTitle["zh"] = arrTitle[0]
	}

	fmt.Println(mapTitle)
	/* var_dump(name)
	var_dump(htmlType)
	var_dump(tips)*/

	//显示页面分析
	/*
		if (strlen(showPage)<1 || showPage == null) showPage = 15

		if(strlen(showPage) == 4) showPage = bindec(showPage)
		arrShowPages = []
		if (showPage & self::ADD) arrShowPages[] = 'add'
		if (showPage & self::EDIT) arrShowPages[] = 'edit'
		if (showPage & self::LISTS) arrShowPages[] = 'list'
		if (showPage & self::SEARCH) arrShowPages[] = 'search'

		//选项分析
		if (!empty(options)) {
			arrOptions = []
			items = explode(",", options)
			options = []
			foreach (items as item) {
				list(value, text) = explode(':', item)
				arrOptions[value] = "text"
			}
			options = arrOptions
		}

		//验证规则分析
		if(!empty(checkType)){
			arrRules = []
			allRules = explode("-", checkType)
			foreach (allRules as k => v){
				ruleInfo = explode(':',v)
				temp = []
				temp['type'] = ruleInfo[0]
				if(ruleInfo[0] == 'reg'){
					temp['reg'] = validateReg
				}

				if(ruleInfo[1]){
					temp['msg']=ruleInfo[1]
				}
				arrRules[] = temp
			}

		}


		attribute = compact('name', 'htmlType', 'tips', 'showPage', 'arrShowPages', 'checkType','arrRules', 'options')
		return attribute
	*/
	//map[string]map[string]string

	fmt.Println(attribute)
	return mapTitle
}
