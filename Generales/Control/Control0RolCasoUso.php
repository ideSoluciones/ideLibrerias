<?php
	class Control0RolCasoUso extends generalXML{
		var $db;
		function Control0RolCasoUso($db){
			$this->db=$db;
		}
		function getRelaciones($sesion, $condiciones=""){
			return $this->db->consultar('
			<Consulta>
				<Campo nombre="*" tablaOrigen="0RolCasoUso" />
				<Campo nombre="*" tablaOrigen="0CasoUso" />
				<Relacion>
					<Tabla campo="idCasoUso" nombre="0RolCasoUso" />
					<Tabla campo="idCasoUso" nombre="0CasoUso" />
				</Relacion>
				'.$condiciones.'
			</Consulta>');
		}
		function limpiarRelaciones(){
			return $this->db->eliminar('
			<Consulta>
				<Campo nombre="*" tablaOrigen="0RolCasoUso" />
			</Consulta>');
		}
		function agregarRelacion($datos1, $datos2){
			return $this->db->insertar('
			<Consulta>
				<Campo nombre="idRol" tablaOrigen="0RolCasoUso" valor="'.$datos1.'"/>
				<Campo nombre="idCasoUso" tablaOrigen="0RolCasoUso" valor="'.$datos2.'"/>
			</Consulta>');
		}
		function determinarCasosUsoRol($sesion){
			//echo "Determinando casos de uso rol";
			//echo revisarArreglo($roles, "roles");

			$consulta=new SimpleXMLElement("<Consulta />");
			$parametro = $consulta->addChild("Campo");
			$parametro->addAttribute("nombre", "*");
			$parametro->addAttribute("tablaOrigen", "0RolCasoUso");
			$parametro = $consulta->addChild("Campo");
			$parametro->addAttribute("nombre", "*");
			$parametro->addAttribute("tablaOrigen", "0CasoUso");
			
			$relacion = $consulta->addChild("Relacion");
			$tabla = $relacion->addChild("Tabla");
			$tabla->addAttribute("campo","idCasoUso");
			$tabla->addAttribute("nombre","0RolCasoUso");
			$tabla = $relacion->addChild("Tabla");
			$tabla->addAttribute("campo","idCasoUso");
			$tabla->addAttribute("nombre","0CasoUso");
			
			
			$roles=$sesion->leerParametrosInternos("roles");
			//echo "Los roles son ", count($roles);
			//echo revisarArreglo($roles, "roles");
			if ($roles!=FALSE){
				//echo "agregando condiciones";
				$condiciones= $consulta->addChild("Condiciones");
				$O= $condiciones->addChild("O");
				
				foreach ($roles as $rol){
					$igual=$O->addChild("Igual");
					$igual->addAttribute("tabla", "0RolCasoUso");
					$igual->addAttribute("campo", "idRol");
					$igual->addAttribute("valor", $rol['valor']);
			
				}
				$registros=$this->db->consultar($consulta);
		
				if (count($registros)>0){
					//echo "<div>Los casos de uso que puede utilizar el Rol son: [".count($registros)."]";
					foreach($registros as $i => $a){
						//if (!$sesion->buscarParametroInterno("casoUsoRol", $a["nombreCasoUso"])){
							//Rol: [".$a["idCasoUso"]."] => ".$a["nombreCasoUso"]."</div>";
							$sesion->escribirParametroInterno("casoUsoRol", $a["nombreCasoUso"], $a["idCasoUso"]);
						//}
					}
					//echo "</div>";
				}else{
					//echo "<div>El Rol no tiene casos de uso</div>";
				}
			}
			
			//echo $this->geshiXML($consulta);
			//echo revisarArreglo($registros, "registros".$this->db->sql);
		}
		/*function determinarCasosUsoRol($sesion){
			
			$c = $sesion->xml->xpath("/Sesion/Parametro[@nombre='rolUsuario']");
			if (count($c)==0){
				//echo "<div>El usuario no tiene roles para derterminar si tiene casos de uso por rol</div>";
			}
			//echo "<div>Determinando casos de uso rol</div>";
			//echo revisarArreglo($c, "Parametro [@nombre='rolUsuario']");
			foreach($c as $i => $a){
				//echo "<div>Consultando para ".$a["nombre"]."= [".$a["valor"]."]</div>";
			
				$registros=$this->db->consultar('
				<Consulta>
					<Campo nombre="*" tablaOrigen="0RolCasoUso" />
					<Campo nombre="*" tablaOrigen="0CasoUso" />
					<Relacion>
						<Tabla campo="idCasoUso" nombre="0RolCasoUso" />
						<Tabla campo="idCasoUso" nombre="0CasoUso" />
					</Relacion>
					<Condiciones>
						<Igual tabla="0RolCasoUso" campo="idRol" valor="'.$a["valor"].'" />
					</Condiciones>
				</Consulta>');
				//echo "sql= ".$this->db->sql.", vs ".count($registros)."<br>";
				//echo revisarArreglo($registros, "Consulta registros determinarCasosUsoRol");
				if (count($registros)>0){
					//echo "<div>Los casos de uso que puede utilizar el rol son:";
					foreach($registros as $j => $b){
						if (!$sesion->buscarParametro("casoUsoRol", $b["idCasoUso"])){
							$sesion->escribirParametro("casoUsoRol", $b["idCasoUso"]);
							//echo "<div>[".$b["idCasoUso"]."] => ".$b["nombre"]."</div>";
						}
					}
					//echo "</div>";
				}else{
					//echo "<div>El rol no tiene casos de uso</div>";
				}
			}*/
	}
?>
