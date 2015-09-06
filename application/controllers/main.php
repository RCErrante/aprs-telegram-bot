<?php

class Main extends Controller {
	
	function index()
	{
		$model = $this->loadModel('APRS_model');
    	//$something = $model->getSomething($id);

		$botID = "88461816";

		$botToken = "";
		$website = "https://api.telegram.org/bot".$botToken;

		$update = file_get_contents($website."/getupdates?offset=911242050");
		echo "<pre>";
		print_r(json_decode($update));
		$data  = json_decode($update);

		

		//hacer equipos (no se ejecuta hasta un día antes de la partida)
		/* los que tengan valor positivo, se van ordenando en dos equipos pito pito
		los que no tienen valor se van añadiendo
			al añadirlo se busca el equipo con más afinidad de amistad

			si el equipo seleccionado tiene >2 miembros que el otro equipo , entonces se fuerza al
			menor equipo

		actualizar equipos.

		*/	




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
							'crearpartida' => '/(\/crearpartida)$/',
							'crearpartida_param' => '/(\\/crearpartida)\\s+(\\d\\d-\\d\\d-\\d\\d\\d\\d)+/',
							'cancelarpartida' => '/(\/cancelarpartida)$/',
						);

						$matches = null;
						foreach ($commands as $commandkey => $commandexp){

							$returnValue = preg_match($commandexp, $text, $matches);

							//print_r($matches);

							if (!empty($matches)) {

								switch($commandkey){
									case "crearpartida":
										echo "comando $commandkey<br>";
										$teclado = json_encode(array(array('/crearpartida 01-09-2015')));
										file_get_contents($website."/sendMessage?chat_id=$group_id&text=@$from_username Para crear partida usa /crearpartida dd-mm-aaaa.&reply_markup=$teclado");
									break;

									case "crearpartida_param":
										echo "comando $commandkey<br>";
										$fecha = $matches[2];
										$error = $model->crearpartida($fecha,$group_id,$from_id);
										file_get_contents($website."/sendMessage?chat_id=$group_id&text=".$error);
									break;

									case "cancelarpartida":
										echo "comando $commandkey<br>";
										
										$error = $model->cancelarpartida($group_id);
										file_get_contents($website."/sendMessage?chat_id=$group_id&text=Partida cerrada.");
										

									break;
									

								}

							}

							$matches = null;


						}
			
						//crearpartida - crea una partida

							//comprobar que eres un admin para este grupo

							//buscar si hay partida abierta asignada a este canal

							//si no va parametrizada

									//informamos que tiene que poner /crearpartida y la fecha
									//ponemos teclado con /crearpartida 16-05-2015 (siguiente domingo)

							//si va parametrizada

								//si no hay partida abierta para ese grupo

									/*creamos una y 
									mandamos mensaje con el número de partida y 
									y con la fecha seleccionada*/

						//cancelapartida - cierra la partida
							
							//comprobar que eres un admin para este grupo
							

							//si va parametrizada con fecha buscar esa fecha y cancelarla

							//sino va parametrizada

								//buscar si hay partida abierta asignada a este canal

								//si hay partida abierta
									//lanzar teclado al admin con las partidas disponibles a cancelar

									//teclado con /cancelar 16-09-2015 , /cancelar 15-09-2015

								//si no hay partida 

									//mensaje con "no hay partidas para cancelar"


					// user commands 


						//infopartida - Información de los equipos
						
							/*manda un mensaje con los datos de la partida
							no muestra los equipos hasta un día antes de la partida*/

							//equipos, fecha y geoposición del campo


						//apuntarse - Entras en el listado
		
							/*el bot te pregunta de que rol vas a la partida 
							te pone teclado con /rol asalto, /rol sniper*/

						//rol 

							/*si va parametrizado con asalto o sniper 
							actualiza la base de datos*/

							//sino informa al usuario con un teclado con opciones


						//confirmar - Confirmas asistencia a partida
		
							//actualiza la base de datos

						//desapuntarse - Sales del listado
		
							//te borra de la partida


		

		//al terminar guardamos el ultimo id de mensaje

	



			




		


		}

	}
 
}

?>
