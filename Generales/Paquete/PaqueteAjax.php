<?php
	class PaqueteAjax extends Paquete{
	
		function PaqueteAjax($db){
			$this->Paquete($db);
		}

		function nombreMenu_ajax($sesion){
			return "";
		}
		function generarContenido_ajax($sesion){

			$contenido=ControlXML::nuevo("Contenido");
			$contenido->addAttribute("Unico", "true");
			$contenido->addAttribute("Cabeza", "false");
			
			$operacion=$sesion->leerParametroDestinoActual("o");
			if(strcmp($operacion,"p")==0){
				$p=json_decode(str_replace("\\'",'"',str_replace('\\"','"',str_replace('\\','',str_replace('\\n',"[nl]",$sesion->leerParametroDestinoActual("p"))))),true);
				if(isset($p["c"])){
					if(isset($p["p"])){
						ControlAjax::generarContenido($contenido,$p["c"],$p["p"]);
					}
				}
			}elseif(strcmp($operacion,"f")==0){
				$clave=$sesion->leerParametroDestinoActual("c");
				ControlAjax::generarContenido($contenido,$clave);
			}

			return $contenido;
		}
		
		function procesarFormulario_ajax($sesion){
			return $this->generarContenido_ajax();
		}
	}
?>
