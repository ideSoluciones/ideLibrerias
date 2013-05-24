<?php

	class ControlXMLPropiedades{
	
		private $sesion;
		
		function ControlXMLPropiedades(){
			$this->sesion=Sesion::getInstancia();
		}
		
		//Función encargada de recuperar un voFormulario de acuerdo al nombre solicitado
		function recuperarFormulario($tabla,$campo){
			$dao0XMLPropiedades = new DAO0XMLPropiedades($this->sesion->getDB());
			
			$consulta = new SimpleXMLElement("<Consulta />");
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			ControlXML::agregarNodo($y,"Igual",array("tabla"=>"0XMLPropiedades","campo"=>"tabla","valor"=>$tabla));
			ControlXML::agregarNodo($y,"Igual",array("tabla"=>"0XMLPropiedades","campo"=>"campo","valor"=>$campo));
						 
			try{
				$formularios = $dao0XMLPropiedades->getRegistros($consulta);
			}catch(sinResultados $e){
				$formularios = array();
			}

			if (count($formularios)==1){
				return $formularios[0];
			}else{
				throw new Exception("No hay especificación para este campo.");
			}
		}
		
		// Genera un formulario a partir de la información en la base de datos
		function generarFormulario($form, $tabla, $nombreCampo, $id, $valoresPorDefecto){
			$voFormulario = $this->recuperarFormulario($tabla, $nombreCampo);
			if (!is_null($voFormulario)){
				$xmlCamposFormulario = @new SimpleXMLElement($voFormulario->getXmlPropiedades());
				$datosPorDefecto=false;
				if(strlen($valoresPorDefecto)>0){
					$valoresPorDefecto=@new SimpleXMLElement($valoresPorDefecto);
					$valores=array();
					foreach($valoresPorDefecto->children() as $hijo){
						$valores["{$hijo["nombre"]}"]=(string)$hijo/*["valor"]*/;
					}
					$datosPorDefecto=true;
				}
				try{
					foreach($xmlCamposFormulario->children() as $campo){
						if (strcmp((string)$campo["nombre"],"")==0 ||
							strcmp((string)$campo["titulo"],"")==0 ||
							strcmp((string)$campo["tipo"],"")==0){
							ControlXML::agregarNodoTexto($form, "Wiki", "Error en el formulario ".$nombreFormulario." (".(string)$campo["nombre"].", ".(string)$campo["tipo"].", ".(string)$campo["titulo"].")");
							return ;
						}
						$datos = array();
						$nombreActual="";
						foreach($campo->attributes() as $nombre => $valor){
							if(strcmp($nombre,"nombre")==0){
								$campo[$nombre]=$id."_".$valor;
							}
							$datos[(string)$nombre]=(string)$campo[$nombre];
						}
						if($datosPorDefecto && isset($campo["nombre"])){
							$tmp=str_replace($id."_","",$campo["nombre"]);
							if(isset($valores[$tmp])){
								$datos["valorPorDefecto"]=siEsta($valores[$tmp]);
							}
						}
						$campoNuevo=xml::add($form, "Campo",$datos);
						append_simplexml($campoNuevo, $campo);
					}
				}catch(Exception $e){
					new mensajes($e->getMessage());
				}
			}else{
				ControlXML::agregarNodoTexto($form, "Wiki", "Formulario no encontrado");
			}
		}
		// Procesa un formulario a partir de la información en la base de datos
		function procesarFormulario($id,$tabla, $nombreCampo){
			$voFormulario = $this->recuperarFormulario($tabla, $nombreCampo);
			$xml=new SimpleXMLElement("<Propiedades />");
			$campos=$this->sesion->leerParametrosDestinoActual("/^$id/");
			foreach($campos as $nombre=>$valor){
				$nombrePropiedad=str_replace("{$id}_","",$nombre);
				$campo=ControlXML::agregarNodo($xml,"Propiedad",array("nombre"=>$nombrePropiedad/*,"valor"=>$valor*/));
				$campo[]=$valor;
			}
			return (string)$xml->asXML();
		}
	}
?>
