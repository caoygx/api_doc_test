package util

import (
	"fmt"
	"os"

	"github.com/jinzhu/gorm"
	"gopkg.in/ini.v1"
)

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
	//defer db.Close()
}
