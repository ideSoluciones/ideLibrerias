<?php

	class ControlAsistenteFormularios{
		//Función encargada de recuperar un voFormulario de acuerdo al nombre solicitado
		public static function recuperarFormulario($sesion, $nombreFormulario){
			$daoFormulario = new DAO1Formulario($sesion->db);
			
			//// @ToDo: Cambiar estas lineas por la funcion que recibe un array
			$consulta = new SimpleXMLElement("<Consulta />");
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
						 ControlXML::agregarNodo($condicion,"Igual",array("tabla"=>"1Formulario","campo"=>"nombreFormulario","valor"=>$nombreFormulario));
						 
			try{
				$formularios = $daoFormulario->getRegistros($consulta);
			////
			}catch(sinResultados $e){
				$formularios = array();
			}
		
			if (count($formularios)==1){
				return $formularios[0];
			}
			return null;
		}
		// Genera un formulario a partir de la información en la base de datos
		public static function generarFormulario($nodo, $sesion, $nombreFormulario){
			$voFormulario = ControlAsistenteFormularios::recuperarFormulario($sesion, $nombreFormulario);
			if (!is_null($voFormulario)){
				$xmlPropiedadesFormulario = new SimpleXMLElement($voFormulario->getXmlPropiedadesFormulario());
				ControlXML::agregarNodoTexto($nodo, "Wiki", (string)$xmlPropiedadesFormulario["titulo"]);

				$form = ControlFormulario::generarFormulario($nodo, $sesion->leerParametro("idCasoUso"));
				$xmlCamposFormulario = new SimpleXMLElement($voFormulario->getXmlCamposFormulario());
				foreach($xmlCamposFormulario->children() as $campo){
					if (strcmp((string)$campo["nombre"],"")==0 ||
						strcmp((string)$campo["titulo"],"")==0 ||
						strcmp((string)$campo["tipo"],"")==0){
						ControlXML::agregarNodoTexto($nodo, "Wiki", "Error en el formulario ".$nombreFormulario." (".(string)$campo["nombre"].", ".(string)$campo["tipo"].", ".(string)$campo["titulo"].")");
						return ;
					}
					$datos = array();
					foreach($campo->attributes() as $nombre => $valor){
						$datos[(string)$nombre]=(string)$valor;
					}
					//@ToDo: Falta recibir variables por path y colocarlas en el formulario, ej:
					//form/NombreForm/var1/53/var2/67
					$campoNuevo=ControlXML::agregarNodo($form, "Campo", $datos);
					append_simplexml($campoNuevo, $campo);
				}
				$campo = ControlFormulario::generarCampo($form, array("tipo"=>"oculto", "nombre"=>"nombreFormulario", "valor"=>$nombreFormulario, "valorPorDefecto"=>$nombreFormulario));
				$campo = ControlFormulario::generarEnviar($form);
			}else{
				ControlXML::agregarNodoTexto($nodo, "Wiki", "Formulario no encontrado");
			}
		}
		// Procesa un formulario a partir de la información en la base de datos
		public static function procesarFormulario($nodo, $sesion, $nombreFormulario){
			$voFormulario=ControlAsistenteFormularios::recuperarFormulario($sesion, $nombreFormulario);
			if (!is_null($voFormulario)){
				$xmlCamposFormulario = new SimpleXMLElement($voFormulario->getXmlCamposFormulario());
				$datosEnvio= new SimpleXMLElement("<DatosEnvio />");
				$datosEnvio->addAttribute("fecha", date('Y-m-d H:i:s',time()));
				$datosEnvio->addAttribute("ip", $_SERVER["REMOTE_ADDR"]);
				$datosEnvio->addAttribute("path", $sesion->leerParametro("direccionCompleta"));
				$datosEnvio->addAttribute("idUsuario", $sesion->leerParametro("idUsuario"));
				$datosFormulario= new SimpleXMLElement("<DatosFormulario />");
				foreach($xmlCamposFormulario->children() as $campo){
					if (strcmp((string)$campo["nombre"],"")==0 ||
						strcmp((string)$campo["titulo"],"")==0 ||
						strcmp((string)$campo["tipo"],"")==0){
						ControlXML::agregarNodoTexto($nodo, "Wiki", "Error en el formulario ".$nombreFormulario." (".(string)$campo["nombre"].", ".(string)$campo["tipo"].", ".(string)$campo["titulo"].")");
						return ;
					}
					$datos = array();
					foreach($campo->attributes() as $nombre => $valor){
						$datos[(string)$nombre]=(string)$valor;
					}
					//ControlXML::agregarNodoTexto($nodo, "Wiki", "* Procesando ".$datos["nombre"]."\n");
					$valor=$sesion->leerParametroDestinoActual($datos["nombre"]);
					$datosFormulario->addAttribute($datos["nombre"], $valor);
				}
				
				$voFormularioDatos = new VO1FormularioDatos();
				$voFormularioDatos->setIdFormulario($voFormulario->getIdFormulario());
				$voFormularioDatos->setXmlDatosEnvio($datosEnvio->asXML());
				$voFormularioDatos->setXmlDatosFormulario($datosFormulario->asXML());
				$voFormularioDatos->setActivo("1");
				$daoFormularioDatos = new DAO1FormularioDatos($sesion->db);
				if (!$daoFormularioDatos->agregarRegistro($voFormularioDatos)){
					ControlXML::agregarNodoTexto($nodo, "Wiki", "Error agregando nuevo registro");
				}
			}
		}
	}
?>
