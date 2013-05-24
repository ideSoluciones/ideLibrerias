<?php
	class Control0CasoUso{
		var $db;
		function Control0CasoUso($db){
			$this->db=$db;
		}
		static function getPaquete($idCasoUso){
			//echo "<div>Consultando la información de : ".$idCasoUso."</div>";
			$sesion=Sesion::getInstancia();
			$registros=$sesion->db->consultar('
				<Consulta>
					<Campo nombre="*" tablaOrigen="0CasoUso" />
					<Condiciones>
						<Igual tabla="0CasoUso" campo="idCasoUso" valor="'.$idCasoUso.'" />
					</Condiciones>
				</Consulta>');
			//echo "<div>El sql de la consulta de getPaquete es: ".$this->db->sql."</div>";
			if (count($registros)>0){
				return $registros[0]["idPaquete"];			
			}
			return "";
		}
		//@ToDo:
		//Convertir a estatica
		//Realizar un buffer para no tener que consultar en la base de datos
		static function getNombreCasoUso($idCasoUso){
			//echo "<div>Consultando la información de : ".$idCasoUso."</div>";
			$sesion=Sesion::getInstancia();
			$registros=$sesion->db->consultar('
				<Consulta>
					<Campo nombre="*" tablaOrigen="0CasoUso" />
					<Condiciones>
						<Igual tabla="0CasoUso" campo="idCasoUso" valor="'.$idCasoUso.'" />
					</Condiciones>
				</Consulta>');
			//echo "<div>El sql de la consulta de getPaquete es: ".$this->db->sql."</div>";
			if (count($registros)>0){
				return "".$registros[0]["nombreCasoUso"];			
			}
			return "";
		}
		static function getIdCasoUso($nombreCasoUso){
			//echo "<div>Consultando la información de : ".$idCasoUso."</div>";
			$sesion=Sesion::getInstancia();
			$registros=$sesion->db->consultar('
				<Consulta>
					<Campo nombre="*" tablaOrigen="0CasoUso" />
					<Condiciones>
						<Igual tabla="0CasoUso" campo="nombreCasoUso" valor="'.$nombreCasoUso.'" />
					</Condiciones>
				</Consulta>');
			//echo "<div>El sql de la consulta de getIdCasoUso es: ".$this->db->sql."</div>";
			if (count($registros)>0){
				return $registros[0]["idCasoUso"];			
			}
			return "";
		}
		static function getNombrePaquete($db, $idCasoUso){
			$sesion=Sesion::getInstancia();
			$registros=$sesion->db->consultar('
				<Consulta>
					<Campo nombre="*" tablaOrigen="0CasoUso" />
					<Campo nombre="*" tablaOrigen="0Paquete"/>
					<Relacion>
						<Tabla campo="idPaquete" nombre="0Paquete" />
						<Tabla campo="idPaquete" nombre="0CasoUso" />
					</Relacion>
					<Condiciones>
						<Igual tabla="0CasoUso" campo="idCasoUso" valor="'.$idCasoUso.'" />
					</Condiciones>
				</Consulta>');
			//echo "<div>El sql de la consulta de getNombrePaquete es: ".$this->db->sql."</div>";
			if (count($registros)>0){
				//echo "retornando: ".$registros[0]["nombrePaquete"];
				return $registros[0]["nombrePaquete"];
			}
			return "";
		}
		static function getLista($campo){
		
			//$daoCasoUso= new DAOCasoUso($this->db);
			$sesion=Sesion::getInstancia();
			$lista=$sesion->db->consultar('
				<Consulta>
					<Campo nombre="*" tablaOrigen="0CasoUso" />
					<Campo nombre="'.$campo.'" tablaOrigen="0CasoUso" />
				</Consulta>');
			return $lista;
		}
	}
?>
