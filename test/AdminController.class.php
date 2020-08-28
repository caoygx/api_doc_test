<?php

use PHPUnit\Framework\TestCase;
use HtmlParser\ParserDom;
class AdminController extends TestCase
{
    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;
    public function setUp()
    {
        //$this->client = new \GuzzleHttp\Client( [ 'base_uri' => 'http://www.s.cn', 'http_errors' => false,  ]);
        $this->cookieJar = new \GuzzleHttp\Cookie\CookieJar();

        $this->client = new \GuzzleHttp\Client([ 'cookies' => $this->cookieJar]);
        $this->url_user = "http://admin.21mmm.com/index.php?s=";
        //http://admin.21mmm.com/index.php?s=/Login/login





    }
    //public function testAction1()
    //{
        /* 测试数据
        $arr = [];
        $arr['a1'] = '1';
        $arr['errorno'] = '0';
        $arr['errormsg'] = 1;
        $arr['data'] = [];
        echo json_encode($arr);
        exit;
         */
       /* $response = $this->client->get('/index/index');
        $body = $response->getBody(); //添加测试
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($body, true);
        $this->assertArrayHasKey('errorno', $data);
        $this->assertArrayHasKey('errormsg', $data);
        $this->assertArrayHasKey('data', $data);
        $this->assertEquals(0, $data['errorno']);
        $this->assertInternalType('array', $data['data']);*/
   // }

    function testLogin(){
        $response = $this->client->post($this->url_user.'/Login/login',
            [
                'form_params'=>["name"=>'admin',"pwd"=>'admin',"sub"=>'登录'],
                'allow_redirects' => false,
            ]
        );
        //$this->assertEquals(200, $response->getStatusCode());
        $body = $response->getBody();
        $location = $response->getHeader('Location')[0];
        $prefix = '/index.php?s=';
        $this->assertEquals($prefix.'/Index/index.html',$location);
        /*foreach ($response->getHeaders() as $k=>$v){
            var_dump($v);
        }
        exit;
        echo($body);exit;
        $html_dom = new \HtmlParser\ParserDom($body);
        $pSuccess = $html_dom->find('p.success');
        $success = empty($pSuccess) ? false : true;
        $this->assertTrue($success);*/
    }

   /* function testRegister(){
        $registerUsername = 't'.time();
        $response = $this->client->post($this->url_user.'/Public/handerRegister',
                            ['form_params'=>["username"=>$registerUsername,"password"=>'a','repassword'=>'a']]);
        $this->assertEquals(200, $response->getStatusCode());



        $body = $response->getBody();
        $html_dom = new \HtmlParser\ParserDom($body);
        $pSuccess = $html_dom->find('p.success');
        $success = empty($pSuccess) ? false : true;
        $this->assertTrue($success);

        $cookieUser_id = $this->cookieJar->getCookieByName('CnMQkuser_id');
        $this->assertNotNull ($cookieUser_id->getValue());
        //$this->assertArrayHasKey('Value',(array)$cookieUser_id);
    }*/


}
