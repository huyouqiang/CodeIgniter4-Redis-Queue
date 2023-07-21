# CodeIgniter4-Redis-Queue
基于CodeIgniter4操作Redis Queue操作Demo

##### 第三方redis操作类，补全ci4对于redis queue的操作

``` php
/**
* 在队列尾部插入一个元素
* @param unknown $key
* @param unknown $value
* 返回队列长度
*/
public function rPush($key,$value)
{
  return $this->redis->rPush($key,$value); 
}
    
/**
* 删除并返回队列中的尾元素
* @param unknown $key
*/
public function rPop($key)
{
  return $this->redis->rPop($key);
}    
```

#####  控制器Queue/push

````php
public function push()
{	

  $data=$this->db->query("select * from b_activity_record limit 10000")->getResultArray();

  foreach ($data as $key => $value) {
    $res=$this->redis->rpush("activityRecord",json_encode($value,JSON_UNESCAPED_UNICODE));
  }

  // print_r($data);

  die('redis queue push data successful,queue length:'.sizeof($data));

}
````

##### 控制器Queue/pop

````php
public function pop()
{	
  // Turn off output buffering
  ini_set('output_buffering', 'off');
  // Turn off PHP output compression
  ini_set('zlib.output_compression', false);
  //Flush (send) the output buffer and turn off output buffering
  while(@ob_end_flush());
  // Implicitly flush the buffer(s)
  ini_set('implicit_flush', true);
  ob_implicit_flush(true);

  while(1){

      $value = $this->redis->lpop('activityRecord');

    if($value){

      echo date('Y-m-d H:i:s')." redis queue value ".$value.PHP_EOL;

      $data=['create_time'=>time(),'redisList'=>$value];

      $res=$this->db->table('b_redisList')->insert($data);

    }else{

        echo date('Y-m-d H:i:s')." redis queue pop finish ".PHP_EOL;

    }

    usleep(0.1*1000*1000);

  }


}
````

##### supervisor queue-pop.ini

````shell
[program:queue-pop]
command=/usr/bin/php /www/blog/index.php queue pop
user=root
autostart=true
autorestart=true
startsecs=3
````

#####  my blog

http://xiaohu365.com

#####  ci4 php-cli doc

https://codeigniter.org.cn/user_guide/cli/cli.html
