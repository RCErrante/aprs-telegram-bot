<?php

class Main extends Controller {
	
	function index()
	{
		global $config;
//		$model = $this->loadModel('aprs_model');
    	//$something = $model->getSomething($id);

		$botID = $config['botID'];
		$botToken = $config['botToken'];
		$aprsfiApiKey = $config['aprsfiApiKey'];

		$website = "https://api.telegram.org/bot".$botToken;

		$update = file_get_contents($website."/getupdates");
		echo "<pre>";
		print_r(json_decode($update));
		$data  = json_decode($update);

		//hacemos la consulta de getupdates

		//la recorremos
			//para cada mensaje:

		foreach ($data->result as $key => $msg){

			echo "Analizando mensaje $key<br>";

			$in_group = !isset($msg->message->chat->username);
			$last_update_id = $msg->update_id;

			echo "mensaje a grupo :". intval($in_group) ."<br>";


				// only in groups

					//comprobar que estamos en un grupo

			if (!$in_group) continue;


			$group_id 	= $msg->message->chat->id;
			$from_id	= $msg->message->from->id;
			$from_username	= $msg->message->from->username;
			$text 		= $msg->message->text;

	/*

stdClass Object
(
    [ok] => 1
    [result] => Array
        (
            [0] => stdClass Object
                (
                    [update_id] => 911242039
                    [message] => stdClass Object
                        (
                            [message_id] => 63
                            [from] => stdClass Object
                                (
                                    [id] => 8908013
                                    [first_name] => Guillermo - Killer
                                    [username] => killer415
                                )

                            [chat] => stdClass Object
                                (
                                    [id] => -12658615
                                    [title] => Airsoft partidas club
                                )

                            [date] => 1441465223
                            [text] => /crearpartida
                        )

                )

*/


//rutina:


	//si el mensaje concuerda con alguno de estos hacer lo que se tenga que hacer


	

	// admin commands
	$commands = array(
			'asdf' => '/(\/asdf )$/',
			'last' => '/(\\/last)\\s+(\S+)+/',
			'cancelarpartida' => '/(\/cancelarpartida)$/',
		);

		$matches = null;
		foreach ($commands as $commandkey => $commandexp){

			$returnValue = preg_match($commandexp, $text, $matches);

			//print_r($matches);

			if (!empty($matches)) {

				switch($commandkey){
					case "asdf":
						echo "comando $commandkey<br>";
						$teclado = json_encode(array(array('/asdf 01-09-2015')));
						file_get_contents($website."/sendMessage?chat_id=$group_id&text=@$from_username Para crear partida usa /crearpartida dd-mm-aaaa.&reply_markup=$teclado");
					break;

					case "last":
						echo "comando $commandkey<br>";
						$indicativo = $matches[2];
						$result = file_get_contents($website."http://api.aprs.fi/api/get?name=$indicativo&what=loc&apikey=$aprsfiApiKey&format=json");
						
						echo '<pre>';
						print_r(json_decode($result));
						exit;
						file_get_contents($website."/sendMessage?chat_id=$group_id&text=".$error);
					break;

					case "cancelarpartida":
						echo "comando $commandkey<br>";
						
//						$error = $model->cancelarpartida($group_id);
						file_get_contents($website."/sendMessage?chat_id=$group_id&text=Partida cerrada.");
						

					break;
					

				}

			}

			$matches = null;


		}
	}
	}
}

?>
