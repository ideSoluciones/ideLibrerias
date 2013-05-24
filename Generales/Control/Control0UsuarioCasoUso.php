<?php
	class Control0UsuarioCasoUso{
		var $db;
		function Control0UsuarioCasoUso($db){
			$this->db=$db;
		}
		function getRelaciones($sesion, $condiciones=""){
			return $this->db->consultar('
			<Consulta>
				<Campo nombre="*" tablaOrigen="0UsuarioCasoUso" />
				<Campo nombre="*" tablaOrigen="0CasoUso" />
				<Relacion>
					<Tabla campo="idCasoUso" nombre="0UsuarioCasoUso" />
					<Tabla campo="idCasoUso" nombre="0CasoUso" />
				</Relacion>
				'.$condiciones.'
			</Consulta>');
		}
		function limpiarRelaciones(){
			return $this->db->eliminar('
			<Consulta>
				<Campo nombre="*" tablaOrigen="0UsuarioCasoUso" />
			</Consulta>');
		}
		function agregarRelacion($datos1, $datos2){
			return $this->db->insertar('
			<Consulta>
				<Campo nombre="idUsuario" tablaOrigen="0UsuarioCasoUso" valor="'.$datos1.'"/>
				<Campo nombre="idCasoUso" tablaOrigen="0UsuarioCasoUso" valor="'.$datos2.'"/>
			</Consulta>');
		}
		function determinarCasosUsoUsuario($sesion){

			$registros=$this->db->consultar('
			<Consulta>
				<Campo nombre="*" tablaOrigen="0UsuarioCasoUso" />
				<Campo nombre="*" tablaOrigen="0CasoUso" />
				<Relacion>
					<Tabla campo="idCasoUso" nombre="0UsuarioCasoUso" />
					<Tabla campo="idCasoUso" nombre="0CasoUso" />
				</Relacion>
				<Condiciones>
					<Igual tabla="0UsuarioCasoUso" campo="idUsuario" valor="'.$sesion->leerParametro("idUsuario").'" />
				</Condiciones>
			</Consulta>');


			if ($sesion->leerParametro("idUsuario")!=1){
				
				$controlCasoUso= new Control0CasoUso($this->db);
				$idCasoUso=$controlCasoUso->getIdCasoUso("logout");
				$sesion->escribirParametroInterno("casoUsoUsuario", "logout", $idCasoUso);
			}
			//echo "sql= ".$this->db->sql.", vs ".count($registros)."<br>".$this->db->geshiXML($sesion->xml).revisarArreglo($registros, "Consulta registros determinarCasosUsoUsuario");
			if (count($registros)>0){
				//echo "<div>Los casos de uso que puede utilizar el usuario en UsuarioCasoUso son: [".count($registros)."]</div>";
				foreach($registros as $i => $a){
					//if (!$sesion->buscarParametroInterno("casoUsoUsuario", $a["nombreCasoUso"])){
						//echo "Vamos a agregar ",$a["nombreCasoUso"],"<br>";
						$sesion->escribirParametroInterno("casoUsoUsuario", $a["nombreCasoUso"], $a["idCasoUso"]);
						//echo "<div>[".$a["idCasoUso"]."] => ".$a["nombreCasoUso"]."</div>";
					//}
				}
				//echo "</div>";
			}else{
				//echo "<div>El usuario no tiene casos de uso</div>";
			}
		}
	}
?>
