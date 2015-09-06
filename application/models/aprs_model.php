<?php

class APRS_model extends Model {
	
	public function getSomething($id)
	{
		$id = $this->escapeString($id);
		$result = $this->query('SELECT * FROM something WHERE id="'. $id .'"');
		return $result;
	}

	public function crearpartida($fecha,$grupo,$user){

		$result = $this->query("select * from admins where $user = user_id and $grupo = group_id");

		if (!empty($result)){

			$result2 = $this->query("select * from partidas where group_id = $grupo and open = 1");

			if (!empty($result2)) {
				return "Solo se puede tener una partida abierta a la vez";
			} else {
				$this->query("insert into partidas (group_id,open,date) values ($grupo,1,'$fecha')");
				echo "insert into partidas (group_id,open,date) values ($grupo,1,'$fecha')";exit;
				return "Partida creada para la fecha $fecha";
			}

		}
		return "Necesitas ser admin para crear partidas";

	}
 
	public function cancelarpartida($grupo){

		$result = $this->query("delete from partidas where group_id = $grupo and open = 1");

	}

}

?>
