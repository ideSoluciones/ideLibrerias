<?php
	class Control0UsuarioRol extends generalXML{
		var $db;
		function Control0UsuarioRol(){
			$sesion=Sesion::getInstancia();
			$this->db=$sesion->getDB();
		}

		function getRelaciones($sesion, $condiciones=""){
			return $this->db->consultar('
			<Consulta>
				<Campo nombre="*" tablaOrigen="0UsuarioRol" />
				<Campo nombre="*" tablaOrigen="0Rol" />
				<Relacion>
					<Tabla campo="idRol" nombre="0Rol" />
					<Tabla campo="idRol" nombre="0UsuarioRol" />
				</Relacion>
				'.$condiciones.'
			</Consulta>');
		}
		function cargarRolesUsuario($sesion, $retorno=false){
		
			$registros=$this->getRelaciones($sesion, '
				<Condiciones>
					<Igual tabla="0UsuarioRol" campo="idUsuario" valor="'.$sesion->leerParametro("idUsuario").'" />
				</Condiciones>
			');

			$roles=array();
			if (count($registros)>0){
				//echo "<div>Los roles del usuario son:";			
				foreach($registros as $i => $a){
					//if (!$sesion->buscarParametro("rolUsuario", $a["idRol"])){
					if (!$retorno){
						$sesion->escribirParametroInterno("roles", $a["nombreRol"], $a["idRol"]);
					//}
					}else{
						$roles[]=array("nombre"=>$a["nombreRol"], "id"=>$a["idRol"]);
				//		echo "<div>Roles [".$a["idRol"]."] => ".$a["nombreRol"]."</div>";
					}
				}
				//echo "</div>";
			}else{
			//	echo "<div>El usuario no tiene roles</div>";
			}

			if ($retorno){
/*				var_dump($registros);
				var_dump($registros[0]);
				echo "sql= ".$sesion->db->sql.", vs ".count($roles)."<br>";
				*/
				return $roles;
			}
			
		}
		function limpiarRelaciones(){
			return $this->db->eliminar('
			<Consulta>
				<Campo nombre="*" tablaOrigen="0UsuarioRol" />
			</Consulta>');
		}
		function agregarRelacion($datos1, $datos2){
			return $this->db->insertar('
			<Consulta>
				<Campo nombre="idUsuario" tablaOrigen="0UsuarioRol" valor="'.$datos1.'"/>
				<Campo nombre="idRol"     tablaOrigen="0UsuarioRol" valor="'.$datos2.'"/>
			</Consulta>');
		}
	}
?>
