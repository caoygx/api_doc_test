package util

import (
	"bufio"
	"fmt"
	"io"
	"io/ioutil"
	"math/rand"
	"net/http"
	"net/url"
	"os"
	"path"
	"strings"
	"time"

	"golang.org/x/net/html/charset"
	"golang.org/x/text/encoding"
)

func RandInt64(min, max int64) int64 {
	if min >= max || min == 0 || max == 0 {
		return max
	}
	return rand.Int63n(max-min) + min
}

func Post(rurl string, params string, method string) ([]byte, error) {
	return FileGetContent(rurl, params, method)
}
func FileGetContent(rurl string, params string, method string) ([]byte, error) {
	time.Sleep(time.Second * time.Duration(RandInt64(1, 15)))
	r, err := RequestByParams(rurl, params, method)
	if err != nil {
		fmt.Println("err:", err)
		var zero = []byte("")
		return zero, err
	}
	content := GetBody(r)
	return content, nil
}
func PrintBody(r *http.Response) {
	defer func() { _ = r.Body.Close() }()

	content, err := ioutil.ReadAll(r.Body)
	if err != nil {
		panic(err)
	}

	fmt.Printf("%s", content)
}
func GetBody(r *http.Response) []byte {
	defer func() { _ = r.Body.Close() }()
	content, err := ioutil.ReadAll(r.Body)
	if err != nil {
		panic(err)
	}
	return content
	//return string(content)
}

func RequestByParams(rurl string, params string, method string) (*http.Response, error) {

	fmt.Println(rurl)
	//var err error
	proxyUrl, err := url.Parse("http://127.0.0.1:8888")
	myClient := &http.Client{Transport: &http.Transport{Proxy: http.ProxyURL(proxyUrl)}}
	//myClient := &http.Client{}

	var request *http.Request

	if method == "post" {
		data := strings.NewReader(params)
		request, err = http.NewRequest(http.MethodPost, rurl, data)
		request.Header.Set("Content-Type", "application/x-www-form-urlencoded")
	} else {
		request, err = http.NewRequest(http.MethodGet, rurl, nil)
	}

	request.Header.Add("Accept", "application/json, text/plain, */*")
	request.Header.Add("Accept-Encoding", "gzip, deflate, br")
	request.Header.Add("Accept-Language", "zh-CN,zh;q=0.9")

	request.Header.Add("Referer", "https://data.appgrowing.cn/leaflet/brand")
	request.Header.Add("user-agent", "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.142 Safari/537.36 SocketLog(tabid=241&client_id=)")
	request.Header.Add("cookie", "_ga=GA1.2.148410523.1588820330; NPS_Dialog-324138=gAAAAABeoAt-fEanPNrov-oZeNpIVLTs_eiEOHFGC_w0-G7iYKdkTLB1bT8aosskZtlB4P-BMRphjvvaDeAUepMNJfEGO5Hu6Q==; AG_Token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6ImQzNzZkYWU5LTM1ZDUtMzBkNi1hZjM1LWM2MWFlN2FlZmYyYyIsImFjYyI6MzI0MTM4LCJleHAiOjE2MDAyNDc0NDMsImlhdCI6MTU5NzY1NTQ0NH0.KkLphEJu-YihyQQYzx1fIQSr3HqoKAilksbjZ4mJRXM; sensorsdata2015jssdkcross=%7B%22distinct_id%22%3A%22324138%22%2C%22first_id%22%3A%22171ed115e1d288-06dd3932e43dcc-3f385c06-1474560-171ed115e1e8a3%22%2C%22props%22%3A%7B%22%24latest_traffic_source_type%22%3A%22%E5%BC%95%E8%8D%90%E6%B5%81%E9%87%8F%22%2C%22%24latest_search_keyword%22%3A%22%E6%9C%AA%E5%8F%96%E5%88%B0%E5%80%BC%22%2C%22%24latest_referrer%22%3A%22https%3A%2F%2Fyoucloud.com%2Flogin%2Fappgrowing%2F%22%7D%2C%22%24device_id%22%3A%22171ed115e1d288-06dd3932e43dcc-3f385c06-1474560-171ed115e1e8a3%22%7D")
	//req.Header.Set("Cookie", "name=anny")

	//r, err := http.DefaultClient.Do(request)
	r, err := myClient.Do(request)
	if err != nil {
		//panic(err)
		fmt.Println(err)
	}
	//fmt.Println(r)
	//printBody(r)
	return r, err

}

func Download(imgUrl string) string {

	dir, _ := os.Getwd()
	dir = dir + string(os.PathSeparator) + "down" + string(os.PathSeparator)
	f, err := os.Stat(dir)
	fmt.Println(f)
	if err != nil {
		os.MkdirAll(dir, os.ModePerm)
	}
	fmt.Println(dir)
	//os.Exit(1)

	fileName := path.Base(imgUrl)

	res, err := RequestByParams(imgUrl, "", "get")
	if err != nil {
		return ""
	}
	// 获得get请求响应的reader对象
	reader := bufio.NewReaderSize(res.Body, 32*1024)

	savePath := dir + fileName
	file, err := os.Create(savePath)
	if err != nil {
		panic(err)
	}
	// 获得文件的writer对象
	writer := bufio.NewWriter(file)

	written, err := io.Copy(writer, reader)
	if err == nil {
		fmt.Printf("Total length: %d", written)
		return savePath
	}
	return ""

}

/*
func printBody(r *http.Response) {
	defer func() { _ = r.Body.Close() }()
	content, err := ioutil.ReadAll(r.Body)
	if err != nil {
		panic(err)
	}
	fmt.Printf("%s", content)
}
func GetBody(r *http.Response) []byte {
	defer func() { _ = r.Body.Close() }()
	content, err := ioutil.ReadAll(r.Body)
	if err != nil {
		panic(err)
	}
	return content
	//return string(content)
}*/

func requestByParams(rurl string) (*http.Response, error) {

	//rurl = "http://ad.uzipm.com/adMaterial/index?ret_format=json"
	request, err := http.NewRequest(http.MethodGet, rurl, nil)
	if err != nil {
		panic(err)
	}
	request.Header.Add("user-agent", "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.142 Safari/537.36 SocketLog(tabid=392&client_id=)")
	params := make(url.Values)
	params.Add("ret_format", "json")
	//request.URL.RawQuery = params.Encode()

	r, err := http.DefaultClient.Do(request)
	if err != nil {
		//panic(err)
		fmt.Println(err)
	}
	//fmt.Println(r)
	//printBody(r)
	return r, err

	/*
		proxyUrl, err := url.Parse("http://127.0.0.1:8888")
		myClient := &http.Client{Transport: &http.Transport{Proxy: http.ProxyURL(proxyUrl)}}
		fmt.Println(rurl)
		request, err := http.NewRequest(http.MethodGet, rurl, nil)
		request.Header.Add("user-agent", "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.142 Safari/537.36 SocketLog(tabid=392&client_id=)")

		request.Header.Add("Accept-Encoding", "gzip, deflate, br") //must

		//r, err := http.DefaultClient.Do(request)
		r, err := myClient.Do(request)
		if err != nil {
			//panic(err)
			fmt.Println(err)
		}
		//fmt.Println(r)
		//printBody(r)
		return r, err
	*/
}

func determineEncoding(r io.Reader) encoding.Encoding {
	bytes, err := bufio.NewReader(r).Peek(1024)
	if err != nil {
		panic(err)
	}
	e, _, _ := charset.DetermineEncoding(bytes, "")
	return e
}

func RequestByParams1111(rurl string) (*http.Response, error) {

	//rurl = "http://ad.uzipm.com/adMaterial/index?ret_format=json"
	fmt.Println(rurl)
	request, err := http.NewRequest(http.MethodGet, rurl, nil)
	if err != nil {
		panic(err)
	}
	request.Header.Add("user-agent", "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.142 Safari/537.36 SocketLog(tabid=295&client_id=)")
	request.Header.Add("cookie", "_ga=GA1.2.148410523.1588820330; _gid=GA1.2.608713056.1597028457; AG_Token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6ImQzNzZkYWU5LTM1ZDUtMzBkNi1hZjM1LWM2MWFlN2FlZmYyYyIsImFjYyI6MzI0MTM4LCJleHAiOjE1OTk2MjA0ODIsImlhdCI6MTU5NzAyODQ4M30.PkYO2z0H73h8mLjrznWDQnJOrmwSvBWwYdZ6xBECn2w; sensorsdata2015jssdkcross=%7B%22distinct_id%22%3A%22324138%22%2C%22first_id%22%3A%22171ed115e1d288-06dd3932e43dcc-3f385c06-1474560-171ed115e1e8a3%22%2C%22props%22%3A%7B%22%24latest_traffic_source_type%22%3A%22%E5%BC%95%E8%8D%90%E6%B5%81%E9%87%8F%22%2C%22%24latest_search_keyword%22%3A%22%E6%9C%AA%E5%8F%96%E5%88%B0%E5%80%BC%22%2C%22%24latest_referrer%22%3A%22https%3A%2F%2Fyoucloud.com%2Flogin%2Fappgrowing%2F%22%7D%2C%22%24device_id%22%3A%22171ed115e1d288-06dd3932e43dcc-3f385c06-1474560-171ed115e1e8a3%22%7D")

	params := make(url.Values)
	params.Add("ret_format", "json")
	//request.URL.RawQuery = params.Encode()

	r, err := http.DefaultClient.Do(request)
	if err != nil {
		//panic(err)
		fmt.Println(err)
	}
	//fmt.Println(r)
	//printBody(r)
	return r, err

	/*
		proxyUrl, err := url.Parse("http://127.0.0.1:8888")
		myClient := &http.Client{Transport: &http.Transport{Proxy: http.ProxyURL(proxyUrl)}}
		fmt.Println(rurl)
		request, err := http.NewRequest(http.MethodGet, rurl, nil)
		request.Header.Add("user-agent", "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.142 Safari/537.36 SocketLog(tabid=392&client_id=)")

		request.Header.Add("Accept-Encoding", "gzip, deflate, br") //must

		//r, err := http.DefaultClient.Do(request)
		r, err := myClient.Do(request)
		if err != nil {
			//panic(err)
			fmt.Println(err)
		}
		//fmt.Println(r)
		//printBody(r)
		return r, err
	*/
}
