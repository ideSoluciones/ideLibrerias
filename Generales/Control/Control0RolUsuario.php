<?php
	class Control0RolUsuario{
		var $db;
		function Control0RolUsuario($db){
			$this->db=$db;
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
		function cargarRolesUsuario($sesion){
			$registros=$this->getRelaciones($sesion, '
				<Condiciones>
					<Igual tabla="0UsuarioRol" campo="idUsuario" valor="'.$sesion->leerParametro("idUsuario").'" />
				</Condiciones>

			');
			//echo "sql= ".$this->db->sql.", vs ".count($registros)."<br>";
			//echo revisarArreglo($registros, "Consulta registros cargarRolesUsuario");

			if (count($registros)>0){
				//echo "<div>Los roles del usuario son:";			
				foreach($registros as $i => $a){
					if (!$sesion->buscarParametro("rolUsuario", $a["idRol"])){
						$sesion->escribirParametro("rolUsuario", $a["idRol"]);
						//echo "<div>Roles [".$a["idRol"]."] => ".$a["nombre"]."</div>";
					}
				}
				//echo "</div>";
			}else{
				//echo "<div>El usuario no tiene roles</div>";
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
