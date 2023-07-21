<?php

namespace App\Controllers;



class Queue extends BaseController
{

	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

	public function push()
	{	

		$data=$this->db->query("select * from b_activity_record limit 10000")->getResultArray();

		foreach ($data as $key => $value) {
			$res=$this->redis->rpush("activityRecord",json_encode($value,JSON_UNESCAPED_UNICODE));
		}

		// print_r($data);

		die('redis queue push data successful,queue length:'.sizeof($data));

	}

	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

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

	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
	
}
