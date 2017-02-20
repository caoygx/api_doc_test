<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2013 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace Org\Util;
defined('THINK_PATH') or exit();

/**
 * Redis缓存驱动 
 * 要求安装phpredis扩展：https://github.com/nicolasff/phpredis
 */
class Redis{
	 /**
	 * 架构函数
     * @param array $options 缓存参数
     * @access public
     */
    public function __construct($options=array()) {
        if ( !extension_loaded('redis') ) {
            E(L('_NOT_SUPPERT_').':redis');
        }
        if(empty($options)) {
            $options = array (
                'host'          => C('REDIS_HOST') ? C('REDIS_HOST') : '127.0.0.1',
                'port'          => C('REDIS_PORT') ? C('REDIS_PORT') : 6379,
                'timeout'       => C('DATA_CACHE_TIMEOUT') ? C('DATA_CACHE_TIMEOUT') : false,
                'persistent'    => false,
            );
        }
        $this->options =  $options;
        $this->options['expire'] =  isset($options['expire'])?  $options['expire']  :   C('DATA_CACHE_TIME');
        $this->options['prefix'] =  isset($options['prefix'])?  $options['prefix']  :   C('DATA_CACHE_PREFIX');        
        $this->options['length'] =  isset($options['length'])?  $options['length']  :   0;        
        $func = $options['persistent'] ? 'pconnect' : 'connect';
        $this->handler  = new \Redis;
        $options['timeout'] === false ?
            $this->handler->$func($options['host'], $options['port']) :
            $this->handler->$func($options['host'], $options['port'], $options['timeout']);
    }

    /**
     * 读取缓存
     * @access public
     * @param string $name 缓存变量名
     * @return mixed
     */
    public function get($name) {
        N('cache_read',1);
        $value = $this->handler->get($this->options['prefix'].$name);
        $jsonData  = json_decode( $value, true );
        return ($jsonData === NULL) ? $value : $jsonData;	//检测是否为JSON数据 true 返回JSON解析数组, false返回源数据
    }

    /**
     * 写入缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed $value  存储数据
     * @param integer $expire  有效时间（秒）
     * @return boolean
     */
    public function set($name, $value, $expire = null) {
        N('cache_write',1);
        if(is_null($expire)) {
            $expire  =  $this->options['expire'];
        }
        $name   =   $this->options['prefix'].$name;
        //对数组/对象数据进行缓存处理，保证数据完整性
        $value  =  (is_object($value) || is_array($value)) ? json_encode($value) : $value;
        if(is_int($expire)) {
            $result = $this->handler->setex($name, $expire, $value);
        }else{
            $result = $this->handler->set($name, $value);
        }
        if($result && $this->options['length']>0) {
            // 记录缓存队列
            $this->queue($name);
        }
        return $result;
    }

    /**
     * 删除缓存
     * @access public
     * @param string $name 缓存变量名
     * @return boolean
     */
    public function rm($name) {
        return $this->handler->delete($this->options['prefix'].$name);
    }

    /**
     * 清除缓存
     * @access public
     * @return boolean
     */
    public function clear() {
        return $this->handler->flushDB();
    }
	
	function incr(){
		
		$this->handler->set('test',"123");  
	}

	function lists(){
		$this->handler->rpush("fooList", "bar1");
	}
	
	function hashs(){
		$this->handler->hSet('h', 'key1', 'hello');
	}
}

/*
Redis::__construct构造函数
$redis = new Redis();

connect, open 链接redis服务
参数
host: string，服务地址
port: int,端口号
timeout: float,链接时长 (可选, 默认为 0 ，不限链接时间)
注: 在redis.conf中也有时间，默认为300

pconnect, popen 不会主动关闭的链接
参考上面

setOption 设置redis模式

getOption 查看redis设置的模式

ping 查看连接状态

get 得到某个key的值（string值）
如果该key不存在，return false

set 写入key 和 value（string值）
如果写入成功，return ture

setex 带生存时间的写入值
$redis->setex('key', 3600, 'value'); // sets key → value, with 1h TTL.

setnx 判断是否重复的，写入值
$redis->setnx('key', 'value');
$redis->setnx('key', 'value');

delete  删除指定key的值
返回已经删除key的个数（长整数）
$redis->delete('key1', 'key2');
$redis->delete(array('key3', 'key4', 'key5'));

ttl
得到一个key的生存时间

persist
移除生存时间到期的key
如果key到期 true 如果不到期 false

mset （redis版本1.1以上才可以用）
同时给多个key赋值
$redis->mset(array('key0' => 'value0', 'key1' => 'value1'));



multi, exec, discard
进入或者退出事务模式
参数可选Redis::MULTI或Redis::PIPELINE. 默认是 Redis::MULTI
Redis::MULTI：将多个操作当成一个事务执行
Redis::PIPELINE:让（多条）执行命令简单的，更加快速的发送给服务器，但是没有任何原子性的保证
discard:删除一个事务
返回值
multi()，返回一个redis对象，并进入multi-mode模式，一旦进入multi-mode模式，以后调用的所有方法都会返回相同的对象，只到exec(）方法被调用。

watch, unwatch （代码测试后，不能达到所说的效果）
监测一个key的值是否被其它的程序更改。如果这个key在watch 和 exec （方法）间被修改，这个 MULTI/EXEC 事务的执行将失败（return false）
unwatch  取消被这个程序监测的所有key
参数，一对key的列表
$redis->watch('x');

$ret = $redis->multi() ->incr('x') ->exec();


subscribe *
方法回调。注意，该方法可能在未来里发生改变

publish *
发表内容到某一个通道。注意，该方法可能在未来里发生改变

exists
判断key是否存在。存在 true 不在 false

incr, incrBy
key中的值进行自增1，如果填写了第二个参数，者自增第二个参数所填的值
$redis->incr('key1');
$redis->incrBy('key1', 10);

decr, decrBy
做减法，使用方法同incr

getMultiple
传参
由key组成的数组
返回参数
如果key存在返回value，不存在返回false
$redis->set('key1', 'value1'); $redis->set('key2', 'value2'); $redis->set('key3', 'value3'); $redis->getMultiple(array('key1', 'key2', 'key3'));
$redis->lRem('key1', 'A', 2);
$redis->lRange('key1', 0, -1);

list相关操作
lPush
$redis->lPush(key, value);
在名称为key的list左边（头）添加一个值为value的 元素

rPush
$redis->rPush(key, value);
在名称为key的list右边（尾）添加一个值为value的 元素

lPushx/rPushx
$redis->lPushx(key, value);
在名称为key的list左边(头)/右边（尾）添加一个值为value的元素,如果value已经存在，则不添加

lPop/rPop
$redis->lPop('key');
输出名称为key的list左(头)起/右（尾）起的第一个元素，删除该元素

blPop/brPop
$redis->blPop('key1', 'key2', 10);
lpop命令的block版本。即当timeout为0时，若遇到名称为key i的list不存在或该list为空，则命令结束。如果timeout>0，则遇到上述情况时，等待timeout秒，如果问题没有解决，则对keyi+1开始的list执行pop操作

lSize
$redis->lSize('key');
返回名称为key的list有多少个元素

lIndex, lGet
$redis->lGet('key', 0);
返回名称为key的list中index位置的元素

lSet
$redis->lSet('key', 0, 'X');
给名称为key的list中index位置的元素赋值为value

lRange, lGetRange
$redis->lRange('key1', 0, -1);
返回名称为key的list中start至end之间的元素（end为 -1 ，返回所有）

lTrim, listTrim
$redis->lTrim('key', start, end);
截取名称为key的list，保留start至end之间的元素

lRem, lRemove
$redis->lRem('key', 'A', 2);
删除count个名称为key的list中值为value的元素。count为0，删除所有值为value的元素，count>0从头至尾删除count个值为value的元素，count<0从尾到头删除|count|个值为value的元素

lInsert
在名称为为key的list中，找到值为pivot 的value，并根据参数Redis::BEFORE | Redis::AFTER，来确定，newvalue 是放在 pivot 的前面，或者后面。如果key不存在，不会插入，如果 pivot不存在，return -1
$redis->delete('key1'); $redis->lInsert('key1', Redis::AFTER, 'A', 'X'); $redis->lPush('key1', 'A'); $redis->lPush('key1', 'B'); $redis->lPush('key1', 'C'); $redis->lInsert('key1', Redis::BEFORE, 'C', 'X');
$redis->lRange('key1', 0, -1);
$redis->lInsert('key1', Redis::AFTER, 'C', 'Y');
$redis->lRange('key1', 0, -1);
$redis->lInsert('key1', Redis::AFTER, 'W', 'value');

rpoplpush
返回并删除名称为srckey的list的尾元素，并将该元素添加到名称为dstkey的list的头部
$redis->delete('x', 'y');
$redis->lPush('x', 'abc'); $redis->lPush('x', 'def'); $redis->lPush('y', '123'); $redis->lPush('y', '456'); // move the last of x to the front of y. var_dump($redis->rpoplpush('x', 'y'));
var_dump($redis->lRange('x', 0, -1));
var_dump($redis->lRange('y', 0, -1));

string(3) "abc"
array(1) { [0]=> string(3) "def" }
array(3) { [0]=> string(3) "abc" [1]=> string(3) "456" [2]=> string(3) "123" }

SET操作相关
sAdd
向名称为key的set中添加元素value,如果value存在，不写入，return false
$redis->sAdd(key , value);

sRem, sRemove
删除名称为key的set中的元素value
$redis->sAdd('key1' , 'set1');
$redis->sAdd('key1' , 'set2');
$redis->sAdd('key1' , 'set3');
$redis->sRem('key1', 'set2');

sMove
将value元素从名称为srckey的集合移到名称为dstkey的集合
$redis->sMove(seckey, dstkey, value);

sIsMember, sContains
名称为key的集合中查找是否有value元素，有ture 没有 false
$redis->sIsMember(key, value);

sCard, sSize
返回名称为key的set的元素个数

sPop
随机返回并删除名称为key的set中一个元素

sRandMember
随机返回名称为key的set中一个元素，不删除

sInter
求交集

sInterStore
求交集并将交集保存到output的集合
$redis->sInterStore('output', 'key1', 'key2', 'key3')

sUnion
求并集
$redis->sUnion('s0', 's1', 's2');
s0,s1,s2 同时求并集

sUnionStore
求并集并将并集保存到output的集合
$redis->sUnionStore('output', 'key1', 'key2', 'key3')；

sDiff
求差集

sDiffStore
求差集并将差集保存到output的集合

sMembers, sGetMembers
返回名称为key的set的所有元素

sort
排序，分页等
参数
'by' => 'some_pattern_*',
'limit' => array(0, 1),
'get' => 'some_other_pattern_*' or an array of patterns,
'sort' => 'asc' or 'desc',
'alpha' => TRUE,
'store' => 'external-key'
例子
$redis->delete('s'); $redis->sadd('s', 5); $redis->sadd('s', 4); $redis->sadd('s', 2); $redis->sadd('s', 1); $redis->sadd('s', 3);
var_dump($redis->sort('s')); // 1,2,3,4,5
var_dump($redis->sort('s', array('sort' => 'desc'))); // 5,4,3,2,1
var_dump($redis->sort('s', array('sort' => 'desc', 'store' => 'out'))); // (int)5
 
string命令
getSet
返回原来key中的值，并将value写入key
$redis->set('x', '42');
$exValue = $redis->getSet('x', 'lol'); // return '42', replaces x by 'lol'
$newValue = $redis->get('x')' // return 'lol'

append
string，名称为key的string的值在后面加上value
$redis->set('key', 'value1');
$redis->append('key', 'value2');
$redis->get('key');

getRange （方法不存在）
返回名称为key的string中start至end之间的字符
$redis->set('key', 'string value');
$redis->getRange('key', 0, 5);
$redis->getRange('key', -5, -1);

setRange （方法不存在）
改变key的string中start至end之间的字符为value
$redis->set('key', 'Hello world');
$redis->setRange('key', 6, "redis");
$redis->get('key');

strlen
得到key的string的长度
$redis->strlen('key');

getBit/setBit
返回2进制信息

zset（sorted set）操作相关
zAdd(key, score, member)：向名称为key的zset中添加元素member，score用于排序。如果该元素已经存在，则根据score更新该元素的顺序。
$redis->zAdd('key', 1, 'val1');
$redis->zAdd('key', 0, 'val0');
$redis->zAdd('key', 5, 'val5');
$redis->zRange('key', 0, -1); // array(val0, val1, val5)

zRange(key, start, end,withscores)：返回名称为key的zset（元素已按score从小到大排序）中的index从start到end的所有元素
$redis->zAdd('key1', 0, 'val0');
$redis->zAdd('key1', 2, 'val2');
$redis->zAdd('key1', 10, 'val10');
$redis->zRange('key1', 0, -1); // with scores $redis->zRange('key1', 0, -1, true);

zDelete, zRem
zRem(key, member) ：删除名称为key的zset中的元素member
$redis->zAdd('key', 0, 'val0');
$redis->zAdd('key', 2, 'val2');
$redis->zAdd('key', 10, 'val10');
$redis->zDelete('key', 'val2');
$redis->zRange('key', 0, -1);

zRevRange(key, start, end,withscores)：返回名称为key的zset（元素已按score从大到小排序）中的index从start到end的所有元素.withscores: 是否输出socre的值，默认false，不输出
$redis->zAdd('key', 0, 'val0');
$redis->zAdd('key', 2, 'val2');
$redis->zAdd('key', 10, 'val10');
$redis->zRevRange('key', 0, -1); // with scores $redis->zRevRange('key', 0, -1, true);

zRangeByScore, zRevRangeByScore
$redis->zRangeByScore(key, star, end, array(withscores， limit ));
返回名称为key的zset中score >= star且score <= end的所有元素

zCount
$redis->zCount(key, star, end);
返回名称为key的zset中score >= star且score <= end的所有元素的个数

zRemRangeByScore, zDeleteRangeByScore
$redis->zRemRangeByScore('key', star, end);
删除名称为key的zset中score >= star且score <= end的所有元素，返回删除个数

zSize, zCard
返回名称为key的zset的所有元素的个数

zScore
$redis->zScore(key, val2);
返回名称为key的zset中元素val2的score

zRank, zRevRank
$redis->zRevRank(key, val);
返回名称为key的zset（元素已按score从小到大排序）中val元素的rank（即index，从0开始），若没有val元素，返回“null”。zRevRank 是从大到小排序

zIncrBy
$redis->zIncrBy('key', increment, 'member');
如果在名称为key的zset中已经存在元素member，则该元素的score增加increment；否则向集合中添加该元素，其score的值为increment

zUnion/zInter
参数
keyOutput
arrayZSetKeys
arrayWeights
aggregateFunction Either "SUM", "MIN", or "MAX": defines the behaviour to use on duplicate entries during the zUnion.
对N个zset求并集和交集，并将最后的集合保存在dstkeyN中。对于集合中每一个元素的score，在进行AGGREGATE运算前，都要乘以对于的WEIGHT参数。如果没有提供WEIGHT，默认为1。默认的AGGREGATE是SUM，即结果集合中元素的score是所有集合对应元素进行SUM运算的值，而MIN和MAX是指，结果集合中元素的score是所有集合对应元素中最小值和最大值。

Hash操作
hSet
$redis->hSet('h', 'key1', 'hello');
向名称为h的hash中添加元素key1—>hello

hGet
$redis->hGet('h', 'key1');
返回名称为h的hash中key1对应的value（hello）

hLen
$redis->hLen('h');
返回名称为h的hash中元素个数

hDel
$redis->hDel('h', 'key1');
删除名称为h的hash中键为key1的域

hKeys
$redis->hKeys('h');
返回名称为key的hash中所有键

hVals
$redis->hVals('h')
返回名称为h的hash中所有键对应的value

hGetAll
$redis->hGetAll('h');
返回名称为h的hash中所有的键（field）及其对应的value

hExists
$redis->hExists('h', 'a');
名称为h的hash中是否存在键名字为a的域

hIncrBy
$redis->hIncrBy('h', 'x', 2);
将名称为h的hash中x的value增加2

hMset
$redis->hMset('user:1', array('name' => 'Joe', 'salary' => 2000));
向名称为key的hash中批量添加元素

hMGet
$redis->hmGet('h', array('field1', 'field2'));
返回名称为h的hash中field1,field2对应的value

redis 操作相关
flushDB
清空当前数据库

flushAll
清空所有数据库

randomKey
随机返回key空间的一个key
$key = $redis->randomKey();

select
选择一个数据库
move
转移一个key到另外一个数据库
$redis->select(0); // switch to DB 0
$redis->set('x', '42'); // write 42 to x
$redis->move('x', 1); // move to DB 1
$redis->select(1); // switch to DB 1
$redis->get('x'); // will return 42

rename, renameKey
给key重命名
$redis->set('x', '42');
$redis->rename('x', 'y');
$redis->get('y'); // → 42
$redis->get('x'); // → `FALSE`

renameNx
与remane类似，但是，如果重新命名的名字已经存在，不会替换成功

setTimeout, expire
设定一个key的活动时间（s）
$redis->setTimeout('x', 3);

expireAt
key存活到一个unix时间戳时间
$redis->expireAt('x', time() + 3);

keys, getKeys
返回满足给定pattern的所有key
$keyWithUserPrefix = $redis->keys('user*');

dbSize
查看现在数据库有多少key
$count = $redis->dbSize();

auth
密码认证
$redis->auth('foobared');

bgrewriteaof
使用aof来进行数据库持久化
$redis->bgrewriteaof();

slaveof
选择从服务器
$redis->slaveof('10.0.1.7', 6379);

save
将数据同步保存到磁盘

bgsave
将数据异步保存到磁盘

lastSave
返回上次成功将数据保存到磁盘的Unix时戳

info
返回redis的版本信息等详情



type
返回key的类型值
string: Redis::REDIS_STRING
set: Redis::REDIS_SET
list: Redis::REDIS_LIST
zset: Redis::REDIS_ZSET
hash: Redis::REDIS_HASH
other: Redis::REDIS_NOT_FOUND
*/

/*

1，connect

描述：实例连接到一个Redis.
参数：host: string，port: int
返回值：BOOL 成功返回：TRUE;失败返回：FALSE
查看复制打印?

    示例：  
      
    <?php  
    $redis = new redis();  
    $result = $redis->connect('127.0.0.1', 6379);  
    var_dump($result); //结果：bool(true)  
    ?>  

2，set

描述：设置key和value的值
参数：Key Value
返回值：BOOL 成功返回：TRUE;失败返回：FALSE

示例：
查看复制打印?

    <?php  
    $redis = new redis();  
    $redis->connect('127.0.0.1', 6379);  
    $result = $redis->set('test',"11111111111");  
    var_dump($result);    //结果：bool(true)  
    ?>  

3，get

描述：获取有关指定键的值
参数：key
返回值：string或BOOL 如果键不存在，则返回 FALSE。否则，返回指定键对应的value值。

范例：
查看复制打印?

    <?php  
    $redis = new redis();  
    $redis->connect('127.0.0.1', 6379);  
    $result = $redis->get('test');  
    var_dump($result);   //结果：string(11) "11111111111"  
    ?>  

4，delete

描述：删除指定的键
参数：一个键，或不确定数目的参数，每一个关键的数组：key1 key2 key3 … keyN
返回值：删除的项数
范例：
查看复制打印?

    <?php  
    $redis = new redis();  
    $redis->connect('127.0.0.1', 6379);  
    $redis->set('test',"1111111111111");  
    echo $redis->get('test');   //结果：1111111111111  
    $redis->delete('test');  
    var_dump($redis->get('test'));  //结果：bool(false)  
    ?>  

5，setnx

描述：如果在数据库中不存在该键，设置关键值参数
参数：key value
返回值：BOOL 成功返回：TRUE;失败返回：FALSE

范例：
查看复制打印?

    <?php  
    $redis = new redis();  
    $redis->connect('127.0.0.1', 6379);  
    $redis->set('test',"1111111111111");  
    $redis->setnx('test',"22222222");  
    echo $redis->get('test');  //结果：1111111111111  
    $redis->delete('test');  
    $redis->setnx('test',"22222222");  
    echo $redis->get('test');  //结果：22222222  
    ?>  

6，exists

描述：验证指定的键是否存在
参数key
返回值：Bool 成功返回：TRUE;失败返回：FALSE

范例：
查看复制打印?

    <?php  
    $redis = new redis();  
    $redis->connect('127.0.0.1', 6379);  
    $redis->set('test',"1111111111111");  
    var_dump($redis->exists('test'));  //结果：bool(true)  
    ?>  

7，incr

描述：数字递增存储键值键.
参数：key value：将被添加到键的值
返回值：INT the new value
实例：
查看复制打印?

    <?php  
    $redis = new redis();  
    $redis->connect('127.0.0.1', 6379);  
    $redis->set('test',"123");  
    var_dump($redis->incr("test"));  //结果：int(124)  
    var_dump($redis->incr("test"));  //结果：int(125)  
    ?>  

8，decr

描述：数字递减存储键值。
参数：key value：将被添加到键的值
返回值：INT the new value
实例：
查看复制打印?

    <?php  
    $redis = new redis();  
    $redis->connect('127.0.0.1', 6379);  
    $redis->set('test',"123");  
    var_dump($redis->decr("test"));  //结果：int(122)  
    var_dump($redis->decr("test"));  //结果：int(121)  
    ?>  

9，getMultiple

描述：取得所有指定键的值。如果一个或多个键不存在，该数组中该键的值为假
参数：其中包含键值的列表数组
返回值：返回包含所有键的值的数组
实例：
查看复制打印?

    <?php  
    $redis = new redis();  
    $redis->connect('127.0.0.1', 6379);  
    $redis->set('test1',"1");  
    $redis->set('test2',"2");  
    $result = $redis->getMultiple(array('test1','test2'));  
    print_r($result);   //结果：Array ( [0] => 1 [1] => 2 )  
    ?>  

10，lpush

描述：由列表头部添加字符串值。如果不存在该键则创建该列表。如果该键存在，而且不是一个列表，返回FALSE。
参数：key,value
返回值：成功返回数组长度，失败false

实例：
查看复制打印?

    <?php  
    $redis = new redis();  
    $redis->connect('127.0.0.1', 6379);  
    $redis->delete('test');  
    var_dump($redis->lpush("test","111"));   //结果：int(1)  
    var_dump($redis->lpush("test","222"));   //结果：int(2)  
    ?>  

11，rpush

描述：由列表尾部添加字符串值。如果不存在该键则创建该列表。如果该键存在，而且不是一个列表，返回FALSE。
参数：key,value
返回值：成功返回数组长度，失败false
范例：
查看复制打印?

    <?php  
    $redis = new redis();  
    $redis->connect('127.0.0.1', 6379);  
    $redis->delete('test');  
    var_dump($redis->lpush("test","111"));   //结果：int(1)  
    var_dump($redis->lpush("test","222"));   //结果：int(2)  
    var_dump($redis->rpush("test","333"));   //结果：int(3)  
    var_dump($redis->rpush("test","444"));   //结果：int(4)  
    ?>  

12，lpop

描述：返回和移除列表的第一个元素
参数：key
返回值：成功返回第一个元素的值 ，失败返回false

范例：
查看复制打印?

    <?php  
    $redis = new redis();  
    $redis->connect('127.0.0.1', 6379);  
    $redis->delete('test');  
    $redis->lpush("test","111");  
    $redis->lpush("test","222");  
    $redis->rpush("test","333");  
    $redis->rpush("test","444");  
    var_dump($redis->lpop("test"));  //结果：string(3) "222"  
    ?>  

12，rpop

描述：返回和移除列表的最后一个元素
参数：key
返回值：成功返回最后一个元素的值 ，失败返回false

范例：
查看复制打印?

    <?php  
    $redis = new redis();  
    $redis->connect('127.0.0.1', 6379);  
    $redis->delete('test');  
    $redis->lpush("test","111");  
    $redis->lpush("test","222");  
    $redis->rpush("test","333");  
    $redis->rpush("test","444");  
    var_dump($redis->rpop("test"));  //结果：string(3) "444"  
    ?>  

13，lsize,llen

描述：返回的列表的长度。如果列表不存在或为空，该命令返回0。如果该键不是列表，该命令返回FALSE。
参数：Key
返回值：成功返回数组长度，失败false
范例：
查看复制打印?

    <?php  
    $redis = new redis();  
    $redis->connect('127.0.0.1', 6379);  
    $redis->delete('test');  
    $redis->lpush("test","111");  
    $redis->lpush("test","222");  
    $redis->rpush("test","333");  
    $redis->rpush("test","444");  
    var_dump($redis->lsize("test"));  //结果：int(4)  
    ?>  

14，lget

描述：返回指定键存储在列表中指定的元素。 0第一个元素，1第二个… -1最后一个元素，-2的倒数第二…错误的索引或键不指向列表则返回FALSE。
参数：key index
返回值：成功返回指定元素的值，失败false

范例：
查看复制打印?

    <?php  
    $redis = new redis();  
    $redis->connect('127.0.0.1', 6379);  
    $redis->delete('test');  
    $redis->lpush("test","111");  
    $redis->lpush("test","222");  
    $redis->rpush("test","333");  
    $redis->rpush("test","444");  
    var_dump($redis->lget("test",3));  //结果：string(3) "444"  
    ?>  

15，lset

描述：为列表指定的索引赋新的值,若不存在该索引返回false.
参数：key index value
返回值：成功返回true,失败false

范例：
查看复制打印?

    <?php  
    $redis = new redis();  
    $redis->connect('127.0.0.1', 6379);  
    $redis->delete('test');  
    $redis->lpush("test","111");  
    $redis->lpush("test","222");  
    var_dump($redis->lget("test",1));  //结果：string(3) "111"  
    var_dump($redis->lset("test",1,"333"));  //结果：bool(true)  
    var_dump($redis->lget("test",1));  //结果：string(3) "333"  
    ?>  

16，lgetrange

描述：
返回在该区域中的指定键列表中开始到结束存储的指定元素，lGetRange(key, start, end)。0第一个元素，1第二个元素… -1最后一个元素，-2的倒数第二…
参数：key start end
返回值：成功返回查找的值，失败false

范例：
查看复制打印?

    <?php  
    $redis = new redis();  
    $redis->connect('127.0.0.1', 6379);  
    $redis->delete('test');  
    $redis->lpush("test","111");  
    $redis->lpush("test","222");  
    print_r($redis->lgetrange("test",0,-1));  //结果：Array ( [0] => 222 [1] => 111 )  
    ?>  

17,lremove

描述：从列表中从头部开始移除count个匹配的值。如果count为零，所有匹配的元素都被删除。如果count是负数，内容从尾部开始删除。
参数：key count value
返回值：成功返回删除的个数，失败false
范例：
查看复制打印?

    <?php  
    $redis = new redis();  
    $redis->connect('127.0.0.1', 6379);  
    $redis->delete('test');  
    $redis->lpush('test','a');  
    $redis->lpush('test','b');  
    $redis->lpush('test','c');  
    $redis->rpush('test','a');  
    print_r($redis->lgetrange('test', 0, -1)); //结果：Array ( [0] => c [1] => b [2] => a [3] => a )  
    var_dump($redis->lremove('test','a',2));   //结果：int(2)  
    print_r($redis->lgetrange('test', 0, -1)); //结果：Array ( [0] => c [1] => b )  
    ?>  

18，sadd

描述：为一个Key添加一个值。如果这个值已经在这个Key中，则返回FALSE。
参数：key value
返回值：成功返回true,失败false
范例：
查看复制打印?

    <?php  
    $redis = new redis();  
    $redis->connect('127.0.0.1', 6379);  
    $redis->delete('test');  
    var_dump($redis->sadd('test','111'));   //结果：bool(true)  
    var_dump($redis->sadd('test','333'));   //结果：bool(true)  
    print_r($redis->sort('test')); //结果：Array ( [0] => 111 [1] => 333 )  
    ?>  

19，sremove

描述：删除Key中指定的value值
参数：key member
返回值：true or false
范例：
查看复制打印?

    <?php  
    $redis = new redis();  
    $redis->connect('127.0.0.1', 6379);  
    $redis->delete('test');  
    $redis->sadd('test','111');  
    $redis->sadd('test','333');  
    $redis->sremove('test','111');  
    print_r($redis->sort('test'));    //结果：Array ( [0] => 333 )  
    ?>  

20,smove

描述：将Key1中的value移动到Key2中
参数：srcKey dstKey member
返回值：true or false
范例
查看复制打印?

    <?php  
    $redis = new redis();  
    $redis->connect('127.0.0.1', 6379);  
    $redis->delete('test');  
    $redis->delete('test1');  
    $redis->sadd('test','111');  
    $redis->sadd('test','333');  
    $redis->sadd('test1','222');  
    $redis->sadd('test1','444');  
    $redis->smove('test',"test1",'111');  
    print_r($redis->sort('test1'));    //结果：Array ( [0] => 111 [1] => 222 [2] => 444 )  
    ?>  

21，scontains

描述：检查集合中是否存在指定的值。
参数：key value
返回值：true or false
范例：
查看复制打印?

    <?php  
    $redis = new redis();  
    $redis->connect('127.0.0.1', 6379);  
    $redis->delete('test');  
    $redis->sadd('test','111');  
    $redis->sadd('test','112');  
    $redis->sadd('test','113');  
    var_dump($redis->scontains('test', '111')); //结果：bool(true)  
    ?>  

22,ssize

描述：返回集合中存储值的数量
参数：key
返回值：成功返回数组个数，失败0
范例：
查看复制打印?

    <?php  
    $redis = new redis();  
    $redis->connect('127.0.0.1', 6379);  
    $redis->delete('test');  
    $redis->sadd('test','111');  
    $redis->sadd('test','112');  
    echo $redis->ssize('test');   //结果：2  
    ?>  

23，spop

描述：随机移除并返回key中的一个值
参数：key
返回值：成功返回删除的值，失败false

范例：
查看复制打印?

    <?php  
    $redis = new redis();  
    $redis->connect('127.0.0.1', 6379);  
    $redis->delete('test');  
    $redis->sadd("test","111");  
    $redis->sadd("test","222");  
    $redis->sadd("test","333");  
    var_dump($redis->spop("test"));  //结果：string(3) "333"  
    ?>  

24,sinter

描述：返回一个所有指定键的交集。如果只指定一个键，那么这个命令生成这个集合的成员。如果不存在某个键，则返回FALSE。
参数：key1, key2, keyN
返回值：成功返回数组交集，失败false

范例：
查看复制打印?

    <?php  
    $redis = new redis();  
    $redis->connect('127.0.0.1', 6379);  
    $redis->delete('test');  
    $redis->sadd("test","111");  
    $redis->sadd("test","222");  
    $redis->sadd("test","333");  
    $redis->sadd("test1","111");  
    $redis->sadd("test1","444");  
    var_dump($redis->sinter("test","test1"));  //结果：array(1) { [0]=> string(3) "111" }  
    ?>  

25,sinterstore

描述：执行sInter命令并把结果储存到新建的变量中。
参数：
Key: dstkey, the key to store the diff into.
Keys: key1, key2… keyN. key1..keyN are intersected as in sInter.
返回值：成功返回，交集的个数，失败false
范例:
查看复制打印?

    <?php  
    $redis = new redis();  
    $redis->connect('127.0.0.1', 6379);  
    $redis->delete('test');  
    $redis->sadd("test","111");  
    $redis->sadd("test","222");  
    $redis->sadd("test","333");  
    $redis->sadd("test1","111");  
    $redis->sadd("test1","444");  
    var_dump($redis->sinterstore('new',"test","test1"));  //结果：int(1)  
    var_dump($redis->smembers('new'));  //结果:array(1) { [0]=> string(3) "111" }  
    ?>  

26,sunion

描述：
返回一个所有指定键的并集
参数：
Keys: key1, key2, … , keyN
返回值：成功返回合并后的集，失败false
范例：
查看复制打印?

    <?php  
    $redis = new redis();  
    $redis->connect('127.0.0.1', 6379);  
    $redis->delete('test');  
    $redis->sadd("test","111");  
    $redis->sadd("test","222");  
    $redis->sadd("test","333");  
    $redis->sadd("test1","111");  
    $redis->sadd("test1","444");  
    print_r($redis->sunion("test","test1"));  //结果：Array ( [0] => 111 [1] => 222 [2] => 333 [3] => 444 )  
    ?>  

27,sunionstore

描述：执行sunion命令并把结果储存到新建的变量中。
参数：
Key: dstkey, the key to store the diff into.
Keys: key1, key2… keyN. key1..keyN are intersected as in sInter.
返回值：成功返回，交集的个数，失败false
范例:
查看复制打印?

    <?php  
    $redis = new redis();  
    $redis->connect('127.0.0.1', 6379);  
    $redis->delete('test');  
    $redis->sadd("test","111");  
    $redis->sadd("test","222");  
    $redis->sadd("test","333");  
    $redis->sadd("test1","111");  
    $redis->sadd("test1","444");  
    var_dump($redis->sinterstore('new',"test","test1"));  //结果：int(4)  
    print_r($redis->smembers('new'));  //结果:Array ( [0] => 111 [1] => 222 [2] => 333 [3] => 444 )  
    ?>  

28,sdiff

描述：返回第一个集合中存在并在其他所有集合中不存在的结果
参数：Keys: key1, key2, … , keyN: Any number of keys corresponding to sets in redis.
返回值：成功返回数组，失败false
范例：
查看复制打印?

    <?php  
    $redis = new redis();  
    $redis->connect('127.0.0.1', 6379);  
    $redis->delete('test');  
    $redis->sadd("test","111");  
    $redis->sadd("test","222");  
    $redis->sadd("test","333");  
    $redis->sadd("test1","111");  
    $redis->sadd("test1","444");  
    print_r($redis->sdiff("test","test1"));  //结果：Array ( [0] => 222 [1] => 333 )  
    ?>  

29,sdiffstore

描述：执行sdiff命令并把结果储存到新建的变量中。
参数：
Key: dstkey, the key to store the diff into.
Keys: key1, key2, … , keyN: Any number of keys corresponding to sets in redis
返回值：成功返回数字，失败false
范例：
查看复制打印?

    <?php  
    $redis = new redis();  
    $redis->connect('127.0.0.1', 6379);  
    $redis->delete('test');  
    $redis->sadd("test","111");  
    $redis->sadd("test","222");  
    $redis->sadd("test","333");  
    $redis->sadd("test1","111");  
    $redis->sadd("test1","444");  
    var_dump($redis->sdiffstore('new',"test","test1"));  //结果：int(2)  
    print_r($redis->smembers('new'));  //结果:Array ( [0] => 222 [1] => 333 )  
    ?>  

30,smembers, sgetmembers

描述：
返回集合的内容
参数：Key: key
返回值：An array of elements, the contents of the set.
范例：
查看复制打印?

    <?php  
    $redis = new redis();  
    $redis->connect('127.0.0.1', 6379);  
    $redis->delete('test');  
    $redis->sadd("test","111");  
    $redis->sadd("test","222");  
    print_r($redis->smembers('test'));  //结果:Array ( [0] => 111 [1] => 222 )  
    ?>  
*/