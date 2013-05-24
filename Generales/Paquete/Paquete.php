<?php
	
	class Paquete extends generalXML{
		var $db;
		var $casoUso;
		var $tipoPedido;
		
		var $prefijoAnclas="";
		var $ajax="xajax_";
		
		var $sesion;
		
		function Paquete(){
			$prefijoAnclas=resolverPath()."/";
			$sesion=Sesion::getInstancia();
			$this->db=$sesion->getDB();
		}
				
		function generarContenido($casoUso, $sesion){
			$nombre="generarContenido_".$casoUso;
			if (method_exists($this,$nombre)){
				$contenido= $this->$nombre($sesion);
			}else{
				$contenido= new SimpleXMLElement("<Contenido/>");
				$contenedor= $contenido->addChild("Contenedor");
				$contenedor->addAttribute("clase", "mensajeError");
				$wiki=$contenedor->addChild("Wiki");
				$wiki[]="==Error en caso de uso==\nEl caso de uso ".$casoUso." no existe [".$nombre."]";
				//msg::add($this);
				//msg::add(get_class($this));
				//msg::add(get_class_methods($this));
			}
			return $contenido;
		}
		function procesarFormulario($casoUso, $sesion){
			$nombre="procesarFormulario_".$casoUso;
			if (method_exists($this,$nombre)){
				$contenido= $this->$nombre($sesion);
			}else{
				$contenido= new SimpleXMlElemento("<Contenido/>");
				$contenedor= $contenido->addChild("Contenedor");
				$contenedor->addAttribute("clase", "error");
				$wiki=$contenedor->addChild("Wiki");
				$wiki[]="==Error==\n".$nombre." no existe";
			}
			return $contenido;
		}
		function nombreMenu($casoUso, $sesion){
			$nombre="nombreMenu_".$casoUso;
			return $this->$nombre($sesion);
		}
		function elementosMenu($sesion, $idCasoUso){
			$sesion=Sesion::getInstancia();
			$this->db=$sesion->getDB();
			$xmlMenu=new SimpleXMLElement("<Menu />");
			$registros=$this->db->consultar('
				<Consulta>
					<Campo nombre="*" tablaOrigen="0CasoUso" />
					<Condiciones>
						<Igual tabla="0CasoUso" campo="idCasoUso" valor="'.$idCasoUso.'" />
					</Condiciones>
				</Consulta>');
			if (count($registros)>0){
				foreach($registros as $j => $b){
					$funcion="nombreMenu_".$b["nombreCasoUso"];
					if (method_exists($this,$funcion)){
						//$destino=$this->prefijoAnclas.$sesion->getIdSesion()."/".$sesion->escribirAncla($b["nombreCasoUso"], $b["idCasoUso"]);
						$destino=$this->prefijoAnclas.$sesion->escribirAncla($b["nombreCasoUso"], $b["idCasoUso"]);
						$nombre=$this->$funcion($sesion);
						if ($nombre!=NULL){
							$campo=$xmlMenu->addChild("Campo");
							$campo->addAttribute("nombre", $nombre);
							$campo->addAttribute("destino", $destino);
						}
					}
				}
			}
			return $xmlMenu;
		}
		function generarImec($sesion, $clase, $permisos ,$urlcss=null){
			$ControlListas=new ControlListas($sesion,$clase,"adminIde".$clase->Propiedades["nombre"],array(),array(),array("nuevo","editar","borrar","filtro","desactivar"));
			return $ControlListas;
		}
		function salir($sesion, $terminarCon=0){
			$sesion->borrarParametrosDestino();
			exit($terminarCon);
		}		

	}

?>
