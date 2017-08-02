<?php

use PHPUnit\Framework\TestCase;
class TestController extends TestCase
{
    public function setUp()
    {
        $this->client = new \GuzzleHttp\Client( [ 'base_uri' => 'http://www.s.cn', 'http_errors' => false,
         ]);
    }
    public function testAction1()
    {
        /* 测试数据
        $arr = [];
        $arr['a1'] = '1';
        $arr['errorno'] = '0';
        $arr['errormsg'] = 1;
        $arr['data'] = [];
        echo json_encode($arr);
        exit;
         */
        $response = $this->client->get('/index/index');
        $body = $response->getBody(); //添加测试
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($body, true);
        $this->assertArrayHasKey('errorno', $data);
        $this->assertArrayHasKey('errormsg', $data);
        $this->assertArrayHasKey('data', $data);
        $this->assertEquals(0, $data['errorno']);
        $this->assertInternalType('array', $data['data']);
    }


}
