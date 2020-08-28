package model

import (
	"fmt"
	"os"

	"github.com/jinzhu/gorm"
	_ "github.com/jinzhu/gorm/dialects/mysql"
	"gopkg.in/ini.v1"
)

type Doc struct {
	ID         int    `xorm:"not null pk autoincr comment('') INT(11)" json:"id"`
	Title      string `xorm:"comment('')" json:"title"`
	Method     string `xorm:"comment('')" json:"method"`
	URL        string `xorm:"comment('')" json:"url"`
	Param      string `xorm:"comment('')" json:"param"`
	ParamJson  string `xorm:"comment() json:"param_json"`
	Return     string `xorm:"comment('') json:"return"`
	ReturnJson string `xorm:"comment('') json:"return_json"`
	Module     string `xorm:"comment('') json:"module"`
	ProjectID  int    `xorm:"comment() json:"project_id"`
}

var DB *gorm.DB

func init() {
	cfg, err := ini.Load("./app.conf")
	if err != nil {
		fmt.Printf("Fail to read file: %v", err)
		os.Exit(1)
	}

	username := cfg.Section("mysql").Key("username").String()
	password := cfg.Section("mysql").Key("password").String()
	database := cfg.Section("mysql").Key("database").String()
	host := cfg.Section("mysql").Key("host").String()
	port := cfg.Section("mysql").Key("port").String()
	strConn := fmt.Sprintf("%s:%s@tcp(%s:%s)/%s?charset=utf8&parseTime=True&loc=Local", username, password, host, port, database)

	DB, err = gorm.Open("mysql", strConn)
	if err != nil {
		panic(err)
	}
	//defer DB.Close()
}

func (this *Doc) TableName() string {
	return "doc"
}

func (row *Doc) Save() {
	result := DB.Create(&row)
	if result.Error != nil {
		fmt.Println(result.Error)
		return
	}
	fmt.Println(row.ID)
}

/*
func (this *Doc) Update(row Doc) {
	//DB.First(&user)


	result := DB.Model(&row).Where("code = ?", row.Code).Update("MarketCap", row.MarketCap)

	// result := DB.Create(&row)
	if result.Error != nil {
		fmt.Println(result.Error)
		return
	}
	fmt.Println(row)
}

func (this *Doc) UpdateZhuying(row Doc) {
	fmt.Println(row)
	result := DB.Model(&row).Where("code = ?", row.Code).Update("Zhuying", row.Zhuying)
	if result.Error != nil {
		fmt.Println(result.Error)
		return
	}

}

func (this *Doc) UpdateNetProfit(row Doc) {
	fmt.Println(row)
	result := DB.Model(&row).Where("code = ?", row.Code).Update("NetProfit", row.NetProfit)
	if result.Error != nil {
		fmt.Println(result.Error)
		return
	}

}

func (this *Doc) GetAll() (rows []Doc) {
	//rows := []Piao{}
	DB.Find(&rows)
	return rows
	//fmt.Println(rows)

}
*/
