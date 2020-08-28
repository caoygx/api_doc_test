package main

import (
	"database/sql"
	"encoding/json"
	"fmt"

	"github.com/gin-gonic/gin"
	_ "github.com/jinzhu/gorm/dialects/mysql"
)

func main() {

	//util.Download("a.jpg")

	//fmt.Println(model.DB)

	apiGetTable()

	//service.GenerateAllDoc()
	//service.SetAllDocResponse()
	//testjson()
}

var json_str string = `{"mobile":"13162836361","password":"123456","repassword":"123456","code":"aa","arr":[{"a":"aa"},{"b":"bb"}]}`

func testjson() {
	var hJson interface{}
	err := json.Unmarshal([]byte(json_str), &hJson)
	if err != nil {
		fmt.Println(err)
	}
	data, ok := hJson.(map[string]interface{})
	if ok {
		for k, v := range data {
			switch vtype := v.(type) {
			case string:
				fmt.Println(k, vtype)
			case int:
				fmt.Println(k, vtype)
			case bool:
				fmt.Println(k, vtype)
			case []interface{}:
				for k2, v2 := range vtype {
					fmt.Println(k2, v2, vtype)
				}
			default:
				fmt.Println(vtype)
			}
		}
	}
	// res, err := simplejson.NewJson([]byte(json_str))

	// if err != nil {
	// 	fmt.Printf("%v\n", err)
	// 	return
	// }

	// //获取json字符串中的 result 下的 timeline 下的 rows 数组
	// rows, err := res.Get("result").Get("timeline").Get("rows").Array()

	// //遍历rows数组
	// for _, row := range rows {
	// 	//对每个row获取其类型，每个row相当于 C++/Golang 中的map、Python中的dict
	// 	//每个row对应一个map，该map类型为map[string]interface{}，也即key为string类型，value是interface{}类型
	// 	if each_map, ok := row.(map[string]interface{}); ok {

	// 		//可以看到each_map["start_ts"]类型是json.Number
	// 		//而json.Number是golang自带json库中decode.go文件中定义的: type Number string
	// 		//因此json.Number实际上是个string类型
	// 		fmt.Println(reflect.TypeOf(each_map["start_ts"]))

	// 		if start_ts, ok := each_map["start_ts"].(json.Number); ok {
	// 			start_ts_int, err := strconv.ParseInt(string(start_ts), 10, 0)
	// 			if err == nil {
	// 				fmt.Println(start_ts_int)
	// 			}
	// 		}

	// 		if number, ok := each_map["number"].(string); ok {
	// 			fmt.Println(number)
	// 		}

	// 	}
	// }
}

func apiGetTable() {
	//Default返回一个默认的路由引擎
	r := gin.Default()
	r.Use(CORSMiddleware())
	r.GET("/table", func(c *gin.Context) {
		result := getTable()
		//data, _ := json.Marshal(result)
		//var s = []string{"1", "2"}

		//输出json结果给调用方
		/*c.JSON(200, gin.H{
			"data": string(data),
		})*/
		ret := make(map[string]interface{})
		ret["code"] = 1
		ret["msg"] = "success"
		ret["data"] = result
		c.JSON(200, ret)
	})
	r.GET("/column", func(c *gin.Context) {
		result := getColumn(c.Query("table"))
		//data, _ := json.Marshal(result)
		//var s = []string{"1", "2"}

		//输出json结果给调用方
		/*c.JSON(200, gin.H{
			"data": string(data),
		})*/
		ret := make(map[string]interface{})
		ret["code"] = 1
		ret["msg"] = "success"
		ret["data"] = result
		c.JSON(200, ret)
	})
	r.Run() // listen and serve on 0.0.0.0:8080
}

func CORSMiddleware() gin.HandlerFunc {
	return func(c *gin.Context) {
		c.Writer.Header().Set("Access-Control-Allow-Origin", c.Request.Header.Get("Origin"))
		c.Writer.Header().Set("Access-Control-Max-Age", "86400")
		c.Writer.Header().Set("Access-Control-Allow-Methods", "POST, GET, OPTIONS, PUT, DELETE, UPDATE")
		c.Writer.Header().Set("Access-Control-Allow-Headers", "Access-Control-Allow-Origin, Origin, Content-Type, Content-Length, Accept-Encoding, X-CSRF-Token, Authorization")
		c.Writer.Header().Set("Access-Control-Expose-Headers", "Content-Length")
		c.Writer.Header().Set("Access-Control-Allow-Credentials", "true")

		fmt.Println(c.Request.Method)

		if c.Request.Method == "OPTIONS" {
			fmt.Println("OPTIONS")
			c.AbortWithStatus(200)
		} else {
			c.Next()
		}
	}
}

func getTable() []interface{} {
	db, _ := sql.Open("mysql", "root:123456@(192.168.26.118:3306)/tesuo")
	defer db.Close()
	err := db.Ping()
	if err != nil {
		fmt.Println("数据库连接失败")
		return nil
	}

	var rows *sql.Rows
	rows, _ = db.Query("SELECT TABLE_NAME,TABLE_COMMENT FROM `information_schema`.`TABLES` WHERE `TABLE_SCHEMA` = 'tesuo' ") //获取所有数据
	var table_name, table_comment string
	//results := make(map[string]interface{})
	results2 := make([]interface{}, 0)
	//
	for rows.Next() { //循环显示所有的数据

		rows.Scan(&table_name, &table_comment)
		//fmt.Println(table_name + " -- " + table_comment)

		if table_name != "" {
			row := make(map[string]string)
			row["table_name"] = table_name

			if table_comment != "" {
				row["table_comment"] = table_comment
			}
			//results[table_name] = row
			results2 = append(results2, row)
		}
	}

	//fmt.Print(results)
	fmt.Print(results2)
	return results2
}

func getColumn(table string) []interface{} {
	db, _ := sql.Open("mysql", "root:123456@(192.168.26.118:3306)/tesuo")
	defer db.Close()
	err := db.Ping()
	if err != nil {
		fmt.Println("数据库连接失败")
		return nil
	}

	var rows *sql.Rows
	sql := "SELECT COLUMN_NAME,DATA_TYPE,COLUMN_COMMENT FROM `information_schema`.`COLUMNS` WHERE `TABLE_SCHEMA` = 'tesuo' and `TABLE_NAME` = '" + table + "' "
	rows, err = db.Query(sql) //获取所有数据
	fmt.Println(sql)
	if err != nil {
		panic(err)
	}
	var column_name, data_type, cloumn_comment string
	//results := make(map[string]interface{})
	results2 := make([]interface{}, 0)
	//
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
			results2 = append(results2, row)
		}
	}

	//fmt.Print(results)
	fmt.Print(results2)
	return results2
}
