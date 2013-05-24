<?php
	class Control0Rol{
		var $db;
		function Control0Rol($db){
			$this->db=$db;
		}
		function getLista($campo){
			return $this->db->consultar('
				<Consulta>
					<Campo nombre="*" tablaOrigen="0Rol" />
					<Campo nombre="'.$campo.'" tablaOrigen="0Rol" />
				</Consulta>');
		}
	}
?>
