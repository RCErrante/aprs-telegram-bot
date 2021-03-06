<?php

class Main extends Controller {
	
	function index()
	{
		$log = $this->loadHelper('log_helper');
		
		//$log->log_message("");
		
		
		global $config;
//		$model = $this->loadModel('aprs_model');

		$botID = $config['botID'];
		$botToken = $config['botToken'];
		$aprsfiApiKey = $config['aprsfiApiKey'];
		$lastid = intval(file_get_contents("./last.id"));

		$website = "https://api.telegram.org/bot".$botToken;

		// cuando se configure el webhook poner file_get_contents(php://input)

		$update = file_get_contents("php://input");
		//$update = file_get_contents($website."/getupdates?offset=$lastid");
		
		$msg = $data = json_decode($update);
		
//$log->log_message(print_r($data, true));

		//hacemos la consulta de getupdates
		//la recorremos para cada mensaje:

//		foreach ($data->update as $key => $msg){  // wh!

//			echo "Analizando mensaje $update<br>";

			$in_group = !isset($msg->message->chat->username);
			$last_update_id = $msg->update_id;

			//echo "mensaje a grupo :". intval($in_group) ."<br>";

			//comprobar que estamos en un grupo
			//if (!$in_group) continue;

			$group_id 	= $msg->message->chat->id;
			$from_id	= $msg->message->from->id;
			$from_username	= $msg->message->from->username;
			if (isset($msg->message->text)) $text = $msg->message->text;

//rutina:
	//si el mensaje concuerda con alguno de estos hacer lo que se tenga que hacer

	// admin commands
	$commands = array(
			'help' => '/(\\/start|\\/help)$/',
			'aprshelp' => '/(\\/aprshelp|\\/aprshelp@\S+)$/',
			'last' => '/(\\/last|\\/last@\S+)\\s+(\S+)+/',
			'msgs' => '/(\\/msgs|\\/msgs@\S+)\\s+(\S+)+/',
			'wx' => '/(\\/wx|\\/wx@\S+)\\s+(\S+)+/',
                        'ED5ZAH' => '/(\\/ed5zah|\\/ed5zah@\S+)$/',
		);

		$matches = null;
		foreach ($commands as $commandkey => $commandexp){

			$returnValue = preg_match($commandexp, $text, $matches);

			if (!empty($matches)) {
				$log->log_message($matches[0]." from: ".$from_id."(".$from_username.") group_id: ".$group_id);
				
				switch($commandkey){
					case "help":
						file_get_contents($website."/sendMessage?chat_id=$group_id&text=Use /aprshelp to get a list of commands");
					break;
					
					case "aprshelp":
						file_get_contents($website."/sendMessage?chat_id=$group_id&text=Comands:%0A".
							"/last ID - last beacon from ID%0A".
							"/msgs ID - show last 10 messages from ID%0A".
							"/wx ID - show weather info from ID");
					break;

					case "last":
						$indicativo = $matches[2];
						$result = file_get_contents("http://api.aprs.fi/api/get?name=$indicativo&what=loc&apikey=$aprsfiApiKey&format=json");
						
						$location = json_decode($result);
						file_get_contents($website."/sendLocation?chat_id=$group_id&latitude="
							.$location->entries[0]->lat."&longitude=".$location->entries[0]->lng);
						file_get_contents($website."/sendMessage?chat_id=$group_id&text=UTC:"
							.gmdate("Y-m-d H:i:s",$location->entries[0]->lasttime)." - ".$location->entries[0]->comment);
						
					break;

					case "msgs":
						$indicativo = $matches[2];
						$result = file_get_contents("http://api.aprs.fi/api/get?what=msg&dst=$indicativo&apikey=$aprsfiApiKey&format=json");
						
						$msgs = json_decode($result);
						foreach($msgs->entries as $msg) {
							file_get_contents($website."/sendMessage?chat_id=$group_id&text=>".gmdate("Y-m-d H:i:s",$msg->time).
							": ".$msg->srccall." -> ".$msg->dst.": ".urlencode($msg->message));
						}
						
					break;
					
					case "wx":
						$indicativo = $matches[2];
						$result = file_get_contents("http://api.aprs.fi/api/get?name=$indicativo&what=wx&apikey=$aprsfiApiKey&format=json");
						$result_loc = file_get_contents("http://api.aprs.fi/api/get?name=$indicativo&what=loc&apikey=$aprsfiApiKey&format=json");
						
						$location = json_decode($result_loc);
						file_get_contents($website."/sendLocation?chat_id=$group_id&latitude="
							.$location->entries[0]->lat."&longitude=".$location->entries[0]->lng);

						$wx = json_decode($result);
						file_get_contents($website."/sendMessage?chat_id=$group_id&text=WX%20from:".$wx->entries[0]->name."%20t:"
							.gmdate("Y-m-d%20H:i:s",$wx->entries[0]->time)."%20T:".$wx->entries[0]->temp."%20P:".$wx->entries[0]->pressure
							."%20H:".$wx->entries[0]->humidity."%20WD:".$wx->entries[0]->wind_direction."%20WS:".$wx->entries[0]->wind_speed."");
						
					break;

					case "ED5ZAH":
						$result = file_get_contents("https://api.brandmeister.network/v1.0/repeater/?action=get&q=214501");
						$result = json_decode($result);
						file_get_contents($website."/sendMessage?chat_id=$group_id&text=ED5ZAH%20last_update:%20"
							.$result->last_updated."%20status:%20".$result->status);
					break;
				}
			}
			$matches = null;
			$lastid = $last_update_id+1;
		}
//	} // wh!
	file_put_contents("./last.id",$lastid);
	}
}
?>
