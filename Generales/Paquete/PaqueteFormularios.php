<?php
	class PaqueteFormularios extends Paquete{

		//adminFormulario
		function nombreMenu_adminFormulario($sesion){
			return "Administrar/Formularios";
		}
		function generarContenido_adminFormulario($sesion){
			$w=$this->generarImec($sesion, getXml1Formulario(), array("editar", "borrar", "consultar", "nuevo"));
			return $w->generarContenido();
		}
		function procesarFormulario_adminFormulario($sesion){
			$w=$this->generarImec($sesion, getXml1Formulario(), array("editar", "borrar", "consultar", "nuevo"));
			return $w->procesarFormulario();
		}
		//adminFormularioDatos
		function nombreMenu_adminFormularioDatos($sesion){
			return "Administrar/Datos Formularios";
		}
		function generarContenido_adminFormularioDatos($sesion){
			$w=$this->generarImec($sesion, getXml1FormularioDatos(), array("editar", "borrar", "consultar", "nuevo"));
			return $w->generarContenido();
		}
		function procesarFormulario_adminFormularioDatos($sesion){
			$w=$this->generarImec($sesion, getXml1FormularioDatos(), array("editar", "borrar", "consultar", "nuevo"));
			return $w->procesarFormulario();
		}
		
		//form
		function nombreMenu_form($sesion){
			return "";
		}
		function generarContenido_form($sesion, $contenido=null){
			if (is_null($contenido)){
				$contenido=new SimpleXMLElement("<Contenido/>");
			}
			ControlAsistenteFormularios::generarFormulario($contenido, $sesion, $sesion->leerParametro("destinoAux"));
			return $contenido;
		}
		function procesarFormulario_form($sesion){
			$contenido=new SimpleXMLElement("<Contenido/>");
			$nombreFormulario=(string)$sesion->leerParametroFormularioActual("nombreFormulario");
			ControlAsistenteFormularios::procesarFormulario($contenido, $sesion, $nombreFormulario);
			ControlXML::agregarNodoTexto($contenido, "Wiki", "Datos almacenados\n");
			$sesion->escribirParametro("destinoAux", $nombreFormulario);
			return $this->generarContenido_form($sesion, $contenido);
		}
		
		
		//pedirDatos
		function nombreMenu_pedirDatos($sesion){
			return "";
		}
		function generarContenido_pedirDatos($sesion, $contenido=null){

			if (is_null($contenido)){
				$contenido=new SimpleXMLElement("<Contenido/>");
			}
			
			try{
				$especificacion = $sesion->leerParametroDestinoActual("especificacion");
				$encode64 = $sesion->leerParametroDestinoActual("encode64");
				//echo "{",$encode64,"}","{",$especificacion,"}";

				if (strcmp($encode64, "true")==0){
					//echo "{",$especificacion,"}";
					$especificacion=base64_decode($especificacion);
				}


				
				$especificacion = str_replace( "\\"  , ""  , $especificacion);
				$especificacion = new SimpleXMLElement("<Contenedor>".$especificacion."</Contenedor>");
			}catch (exception $e){
				$especificacion=null;
			}

			
			if (!is_null($especificacion)){
				
				$contenido->addAttribute("Unico", "true");
				//ControlXML::agregarNodoTexto($contenido, "Wiki", "==Pedir Datos==\nPidiendo datos:");
				$contenedor=ControlXML::agregarNodo($contenido, "Contenedor");
				append_simplexml($contenedor, $especificacion);
				//ajax="pedirDatosPaqueteFormularios";
			}else{
				ControlXML::agregarNodoTexto($contenido, "Wiki", "==Pedir Datos==\nPedido vacio");
			}
			return $contenido;
		}
		function procesarFormulario_pedirDatos($sesion){
			$contenido=new SimpleXMLElement("<Contenido/>");
			return $this->generarContenido_pedirDatos($sesion, $contenido);
		}
	}
?>
