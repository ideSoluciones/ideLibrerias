<?php

	/**
	*    @name FTexto
	*    @abstract	
	*    @author Felipe Cano <fcano@idesoluciones.com >
	*    @version 1.0
	*/
	
	class FClave extends FCampo{

		function FClave($idObjeto, $nombre, $propiedades, $tipoSalida, $accionPlus){
			parent::FCampo($idObjeto, $nombre, $propiedades, $tipoSalida, $accionPlus);
			if(!isset($propiedades["numeroCaracteresMin"])){
				$propiedades["numeroCaracteresMin"] = 8;
			}
			if(isset($propiedades["numeroCaracteresMin"])){
				$this->arrayReglas["reglas"][]="\n".str_repeat("\t", 3).'minlength: '.$propiedades["numeroCaracteresMin"].'';
				if(isset($propiedades["numeroCaracteresMin_msj"])){
					$this->arrayReglas["mensajes"][]="\n".str_repeat("\t", 3).'minlength: "'.$propiedades["numeroCaracteresMin_msj"].'"';
				}else{
					$this->arrayReglas["mensajes"][]="\n".str_repeat("\t", 3).'minlength: "Por favor digite al menos '.$propiedades["numeroCaracteresMin"].' caracteres"';
				}
			}
			if(isset($propiedades["confirmar"])){
				$this->arrayReglas["reglas"][]="\n".str_repeat("\t", 3).'equalTo: "#'.$propiedades["id"].'Confirmacion"';
				if(isset($propiedades["confirmar_msj"])){
					$this->arrayReglas["mensajes"][]="\n".str_repeat("\t", 3).'equalTo: "'.$propiedades["confirmar_msj"].'"';
				}else{
					$this->arrayReglas["mensajes"][]="\n".str_repeat("\t", 3).'equalTo: "Por favor digite el mismo password en los dos campos"';
				}
			}
		}
		public static function validar($campo,$valorDato){
			$respuesta = array("respuesta"=>true,"mensaje"=>"");
			//minlength
			if(isset($campo["numeroCaracteresMin"])){
				if(strlen($valorDato)<$campo["numeroCaracteresMin"]){
					if(isset($campo["numeroCaracteresMin_msj"])){
						$respuesta["mensaje"] = (string)$campo["numeroCaracteresMin_msj"];
					}else{
						$respuesta["mensaje"] = "Por favor digite al menos ".$campo["numeroCaracteresMin"]." caracteres";
					}
					$respuesta["respuesta"] = false;
					return $respuesta;
				}
			}
			//@ToDo: 
			return $respuesta;
		}
		function toHTML(){
			$total = "";
			$propiedades = $this->getPropiedades();
			$estilo="";
			if (isset($propiedades["error"])){
				if (strcmp(strtolower($propiedades["error"]),"true")==0){
					$estilo.=($propiedades["error"]==true?"error ":"");
				}
			}
			$estilo.="contenedorCampoFormulario".$propiedades["tipo"]." campoFormulario";

			$otrosAtributos="";
			foreach($propiedades as $a => $i){
				switch($a){
					case "errorMensaje":case "estilo":case "columnas":case "filas":case "opciones":case "tipo":case "campo":case "id":case "nombre":case "titulo":case "requerido":case "valorPorDefecto":case "error":break;
					default: $otrosAtributos.=" ".$a."='".$i."'";
				}
			}
			
			$total="";
			if (strlen($propiedades["titulo"])>0){
				$total.="	<div id='".$this->getIdObjeto()."Titulo' class='".$estilo."Titulo  etiquetaFormulario'>".$propiedades["titulo"]."</div>\n";
			}
			$total.="		<input id='".$propiedades["id"]."' $otrosAtributos class='".$estilo."Campo' type='password' name='".$this->getNombre()."' value='".$propiedades["valorPorDefecto"]."' />\n";
			$propiedades["errorMensaje"]=isset($propiedades["errorMensaje"])?$propiedades["errorMensaje"]:"";
			$total.=isset($propiedades["error"])?"					<label class='error'>".$propiedades["errorMensaje"]."</label>":"";
			$total.="\n";



			if (isset($propiedades["confirmar"])){
				if(strcmp($propiedades["confirmar"],"true")==0){
					$total.="<div id='".$this->getIdObjeto()."ContenedorConfirmacion' class='".$estilo."' >\n";
					if (strlen($propiedades["titulo"])>0){
						$total.="	<div id='".$this->getIdObjeto()."TituloConfirmacion' class='".$estilo."Titulo  etiquetaFormulario'>Confirmar ".$propiedades["titulo"]."</div>\n";
					}
					$total.="		<input id='".$propiedades["id"]."Confirmacion' class='".$estilo."Campo' type='password' name='".$this->getNombre()."Confirmacion' value='".$propiedades["valorPorDefecto"]."' />\n";
					$total.=$propiedades["error"]==true?"					<label class='error'>".$propiedades["errorMensaje"]."</label>":"";
					$total.="				</div>\n";
				}
			}

			return $total;
		}
	}

?>
