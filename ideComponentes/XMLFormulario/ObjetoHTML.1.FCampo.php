<?php

	/**
	*    @name FTexto
	*    @abstract	
	*    @author Felipe Cano <fcano@idesoluciones.com >
	*    @version 1.0
	*/

	class FCampo extends ObjetoHTML{

		function FCampo($idObjeto, $nombre, $propiedades, $tipoSalida, $accionPlus){
			$this->setIdObjeto($idObjeto);
			$this->setNombre($nombre);
			$this->setPropiedades($propiedades);
			$this->setTipoSalida($tipoSalida);
			$this->setAccionPlus($accionPlus);
			$this->arrayReglas=array("reglas"=> array(), "mensajes"=>array());
			if (!isset($this->propiedades["valorPorDefecto"])){
				$this->propiedades["valorPorDefecto"]="";
			}
			
		}

		function toHTML(){
			$total="";
			$atributos="";
			$propiedades=$this->getPropiedades();
			$estilo="";
			if (isset($propiedades["error"])){
				if (strcmp(strtolower($propiedades["error"]),"true")==0){
					$estilo.=($propiedades["error"]==true?"error ":"");
				}
			}
			$estilo.="contenedorCampoFormulario".$propiedades["tipo"]." campoFormulario";
			$script="";
			$valorPorDefecto=isset($propiedades["valorPorDefecto"])?$propiedades["valorPorDefecto"]:"";

			
			foreach ($propiedades as $a => $i){
				if (strcmp($a,"autocomplete")==0 && strcmp($i,"off")==0){
					$atributos.=$a."=".$i;
				}else{
					if (strcmp($a,"funcionCambio")==0){
						//$atributos.=" onChange='".$i."(this);'";
						$script='
							<script>
								$(function() {
									$("#'.$this->getIdObjeto().'").change(function () {
										'.$i.'(this);
									});
								});
							</script>
						';
					}else{
						if (strcmp($a,"soloLectura")==0){
							$atributos.=" readonly=true ";
						}else{
							if (strcmp($a,"estilo")==0){
								$atributos.=" style='".$i."' ";
							}else{
								switch($a){
									case "tipo":case "campo":case "id":case "nombre":case "titulo":case "requerido":case "valorPorDefecto":case "error": case "hora":break;
									default: $atributos.=" ".$a."='".$i."'";
								}
							}
						}
					}
				}
			}

			$total=$script."";
			if (isset($propiedades["titulo"])){
				$total.="<div id='".$this->getIdObjeto()."Titulo' class='".$estilo."Titulo  etiquetaFormulario'>".$propiedades["titulo"];
			}
			if (isset($propiedades["titulo"])){
				$total.="</div>\n";
			}
			if (isset($propiedades["ayuda"])){
				$ayuda=xml::add(null, "Ayuda");
				xml::add($ayuda, "Wiki", $propiedades["ayuda"]);
				$total.=ide::renderizar($ayuda);
			}
			$total.="<input id='".$this->getIdObjeto()."' class='".$estilo."Campo' type='text' name='".$this->getNombre()."' value='".$valorPorDefecto."' ".$atributos." />\n";
			$error=isset($propiedades["error"])?$propiedades["error"]:false;
			$errorMensaje=isset($propiedades["errorMensaje"])?$propiedades["errorMensaje"]:"";
			$total.=$error?"<label class='error'>".$errorMensaje."</label>":"";
			$total.="";
			return $total;
		}

	}
	class FEntero extends FCampo {
		function FEntero($idObjeto, $nombre, $propiedades, $tipoSalida, $accionPlus){
			parent::FCampo($idObjeto, $nombre, $propiedades, $tipoSalida, $accionPlus);
			
			$this->arrayReglas["reglas"][]="\n".str_repeat("\t", 3).'entero: true';
			if(isset($propiedades["entero_msj"])){
				$this->arrayReglas["mensajes"][]="\n".str_repeat("\t", 3).'entero: "'.$propiedades["entero_msj"].'"';
			}
			if(isset($propiedades["minimo"])){
				$this->arrayReglas["reglas"][]="\n".str_repeat("\t", 3).'min: '.$propiedades["minimo"].'';
				if(isset($propiedades["minimo_msj"])){
					$this->arrayReglas["mensajes"][]="\n".str_repeat("\t", 3).'min: "'.$propiedades["minimo_msj"].'"';
				}else{
					$this->arrayReglas["mensajes"][]="\n".str_repeat("\t", 3).'min: "Por favor ingrese un valor mayor o igual que '.$propiedades["minimo"].'"';
				}
			}
			if(isset($propiedades["maximo"])){
				$this->arrayReglas["reglas"][]="\n".str_repeat("\t", 3).'max: '.$propiedades["maximo"].'';
				if(isset($propiedades["maximo_msj"])){
					$this->arrayReglas["mensajes"][]="\n".str_repeat("\t", 3).'max: "'.$propiedades["maximo_msj"].'"';
				}else{
					$this->arrayReglas["mensajes"][]="\n".str_repeat("\t", 3).'max: "Por favor ingrese un valor menor o igual que '.$propiedades["maximo"].'"';
				}
			}
		}
		public static function validar($campo,$valorDato){
			
			$respuesta = array("respuesta"=>true,"mensaje"=>"");
			
			if(isset($campo["requerido"])){
				if(strcmp($campo["requerido"],"false")==0){
					if(strlen($valorDato)==0){
						return $respuesta;
					}
				}
			}
			
			//entero
			if(!preg_match('/^-?\d+$/',$valorDato)){
				if(strcmp($campo["entero_msj"],"")!=0){
					$respuesta["mensaje"] = (string)$campo["entero_msj"];
				}else{
					$respuesta["mensaje"] = "Por favor digite un entero válido";
				}
				$respuesta["respuesta"] = false;
				return $respuesta;
			}
			//min
			if(strcmp($campo["minimo"],"")!=0 && $valorDato<$campo["minimo"]){
				if(strcmp($campo["minimo_msj"],"")!=0){
					$respuesta["mensaje"] = (string)$campo["minimo_msj"];
				}else{
					$respuesta["mensaje"] = "Por favor ingrese un valor mayor o igual que ".$campo["minimo"];
				}
				$respuesta["respuesta"] = false;
				return $respuesta;
			}
			//max
			if(strcmp($campo["maximo"],"")!=0 && $valorDato>$campo["maximo"]){
				if(strcmp($campo["maximo_msj"],"")!=0){
					$respuesta["mensaje"] = (string)$campo["maximo_msj"];
				}else{
					$respuesta["mensaje"] = "Por favor ingrese un valor menor o igual que ".$propiedades["maximo"];
				}
				$respuesta["respuesta"] = false;
				return $respuesta;
			}
			return $respuesta;
		}
	}
	class FDecimal extends FCampo {
		function FDecimal($idObjeto, $nombre, $propiedades, $tipoSalida, $accionPlus){
			parent::FCampo($idObjeto, $nombre, $propiedades, $tipoSalida, $accionPlus);

			$this->arrayReglas["reglas"][]="\n".str_repeat("\t", 3).'number: true';
			if(isset($propiedades["number_msj"])){
				$this->arrayReglas["mensajes"][]="\n".str_repeat("\t", 3).'number: "'.$propiedades["number_msj"].'"';
			}else{
				$this->arrayReglas["mensajes"][]="\n".str_repeat("\t", 3).'number: "Por favor ingrese un número válido"';
			}
		}
		public static function validar($campo,$valorDato){
			$respuesta = array("respuesta"=>true,"mensaje"=>"");
			//number
			if(!preg_match('/^-?(?:\d+|\d{1,3}(?:,\d{3})+)(?:\.\d+)?$/',$valorDato)){
				if(isset($campo["number_msj"])){
					$respuesta["mensaje"] = (string)$campo["number_msj"];
				}else{
					$respuesta["mensaje"] = "Por favor ingrese un número válido";
				}
				$respuesta["respuesta"] = false;
				return $respuesta;
			}
			return $respuesta;
		}
	}
	class FCadena extends FCampo {
		function FCadena($idObjeto, $nombre, $propiedades, $tipoSalida, $accionPlus){
			parent::FCampo($idObjeto, $nombre, $propiedades, $tipoSalida, $accionPlus);
			if(isset($propiedades["numeroCaracteresMin"])){
				$this->arrayReglas["reglas"][]="\n".str_repeat("\t", 3).'minlength: '.$propiedades["numeroCaracteresMin"].'';
				if(isset($propiedades["numeroCaracteresMin_msj"])){
					$this->arrayReglas["mensajes"][]="\n".str_repeat("\t", 3).'minlength: "'.$propiedades["numeroCaracteresMin_msj"].'"';
				}else{
					$this->arrayReglas["mensajes"][]="\n".str_repeat("\t", 3).'minlength: "Por favor digite al menos '.$propiedades["numeroCaracteresMin"].' caracteres"';
				}
			}
			if(isset($propiedades["numeroCaracteresMax"])){
				$this->arrayReglas["reglas"][]="\n".str_repeat("\t", 3).'maxlength: '.$propiedades["numeroCaracteresMax"].'';
				if(isset($propiedades["numeroCaracteresMax_msj"])){
					$this->arrayReglas["mensajes"][]="\n".str_repeat("\t", 3).'maxlength: "'.$propiedades["numeroCaracteresMax_msj"].'"';
				}else{
					$this->arrayReglas["mensajes"][]="\n".str_repeat("\t", 3).'maxlength: "Por favor no digite más de '.$propiedades["numeroCaracteresMax"].' caracteres"';
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
			//maxlength
			if(isset($campo["numeroCaracteresMax"])){
				if (strlen($valorDato)>$campo["numeroCaracteresMax"]){
					if(isset($campo["numeroCaracteresMax_msj"])){
						$respuesta["mensaje"] = (string)$campo["numeroCaracteresMax_msj"];
					}else{
						$respuesta["mensaje"] = "Por favor no digite más de ".$campo["numeroCaracteresMax"]." caracteres";
					}
					$respuesta["respuesta"] = false;
					return $respuesta;
				}
			}
			return $respuesta;
		}
	}
	class FCorreo extends FCampo {
		function FCorreo($idObjeto, $nombre, $propiedades, $tipoSalida, $accionPlus){
			parent::FCampo($idObjeto, $nombre, $propiedades, $tipoSalida, $accionPlus);
			$this->arrayReglas["reglas"][]="\n".str_repeat("\t", 3).'email: true';
			if(isset($propiedades["email_msj"])){
				$this->arrayReglas["mensajes"][]="\n".str_repeat("\t", 3).'email: "'.$propiedades["email_msj"].'"';
			}else{
				$this->arrayReglas["mensajes"][]="\n".str_repeat("\t", 3).'email: "Por favor ingrese una dirección de correo válida"';
			}
		}
		public static function validar($campo,$valorDato){
			$respuesta = array("respuesta"=>true,"mensaje"=>"");
			//email

			if(!preg_match("/^[A-Za-z0-9](([_\.\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([\.\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$/",$valorDato,$matches)){
				if(isset($propiedades["email_msj"])){
					$respuesta["mensaje"] = $propiedades["email_msj"];
				}else{
					$respuesta["mensaje"] = "Por favor ingrese una dirección de correo válida";
				}
				$respuesta["respuesta"] = false;
				return $respuesta;
			}
			return $respuesta;
		}
	}
	class FOculto extends FCampo {
		function FOculto($idObjeto, $nombre, $propiedades, $tipoSalida, $accionPlus){
			parent::FCampo($idObjeto, $nombre, $propiedades, $tipoSalida, $accionPlus);
		}
		function toHTML(){
			$propiedades=$this->getPropiedades();
			$atributos="";
			foreach ($propiedades as $a => $i){
				switch($a){
					case "tipo":case "campo":case "id":case "nombre":case "titulo":case "requerido":case "valorPorDefecto":case "error": case "hora":break;
					default: 
					//msg::add("--");
					//msg::add($atributos);
					//msg::add($a);
					//msg::add($i);
					$atributos.=" ".$a."='".$i."'";
				}
			}
			//echo "Oculto[".$propiedades["mostrarOculto"]."]";
			//var_dump($propiedades);
			if (!isset($propiedades["valorPorDefecto"])){
				$propiedades["valorPorDefecto"]="";
			}
			$total="";
			if (isset($propiedades["mostrarOculto"])){
				$total.="<input id='".$this->getIdObjeto()."' type='hidden' $atributos name='".$this->getNombre()."' value='".$propiedades["valorPorDefecto"]."'  />\n";
			}
			return $total;
		}
		public function pre2HTML(){
		}
		public function post2HTML(){
		}
	}

?>
