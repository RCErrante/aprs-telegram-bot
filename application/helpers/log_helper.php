<?php

class log_helper {

	 
	function log_message($data){
	 
		
		 file_put_contents("./logs/log_".date('Ymd', time()), date('H:i:s', time())."-> ".$data."\n", FILE_APPEND);
	
	}

}

?>
