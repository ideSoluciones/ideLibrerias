<?php

	require_once('../Externos/recaptcha/recaptchalib.php');

	class Formulario extends ComponentePadre implements componente{

		private $propiedades;
		private $no=0;
		private $noB=0;
		private $pref;

		function Formulario(){
		}

		/**
		*
		* @param SimpleXMLElement formularioOriginal
		* aca estan las reglas extras que pueden pedirse
		*/
		public static function validarFormulario($datosFormulario, $formularioOriginal){
			//echo "Validando Formulario<br>";
			//echo "datosFormulario ", generalXML::geshiXML($datosFormulario);
			//echo "formularioOriginal ", generalXML::geshiXML($formularioOriginal);

			// Se settea recaptcha como valido y si no lo es luego se setteara
			$recaptchaValido = true;
			// Se recorre el XML del formulario RECIBIDO y se guarda el nombre y valor de <Parametro/>
			$arregloDatosFormulario=array();
			foreach($datosFormulario->children() as $dato){
				switch($dato->getName()){
				case 'Parametro':
					$arregloDatosFormulario[(string)$dato["nombre"]]=(string)$dato["valor"];
					break;
				case 'Propiedad':
					// Se consulta el atributo nombre de Propiedad
					if(strcmp($dato["nombre"],"recaptcha")==0 && strcmp($dato["valor"],"true")==0){
						// La propiedad es la correspondiente a recaptcha
						require_once('../Externos/recaptcha/recaptchalib.php');
						$privatekey = "6Lc5GAoAAAAAADCsjteKFirYj2U7GaxcbWDFHAZj ";
						$respuestaRecaptcha = recaptcha_check_answer ($privatekey,
													    $_SERVER["REMOTE_ADDR"],
													    $_POST["recaptcha_challenge_field"],
													    $_POST["recaptcha_response_field"]);
						//var_dump($dato);
						//var_dump($respuestaRecaptcha);
						$recaptchaValido = $respuestaRecaptcha->is_valid;
						if (!$recaptchaValido) {
							new mensajes("Por favor ingrese el código de verificación correctamente, intentelo de nuevo.");
						}
					}
					break;
				}
			}

			// Se recorre el XML del formulario ORIGINAL y se compara con el RECIBIDO validando el valor de cada campo
			$formularioValido=true;
			//La validación en el servidor no va a funcionar por la misma razon
			//que no estaba funcionando en javascript
			foreach($formularioOriginal->children() as $campo){
				switch($campo->getName()){
				case 'Campo':
					foreach($arregloDatosFormulario as $nombreDato => $valorDato){
						if (strcasecmp($nombreDato,(string)$campo["nombre"])==0){
							$campo['valorPorDefecto']=$valorDato;
							$nombreClase="F".strtolower($campo["tipo"]);
							$nombreClase{1}=strtoupper($nombreClase{1});
							// Se hace el llamado a la validación de cada campo
							$res = call_user_func(array($nombreClase,"validarReglas"), $nombreClase, $campo, $valorDato);
							// Se evalua si está setteado el atributo error para el campo
							if (is_null($campo['error'])){
								// No está setteado
								$campo->addAttribute('error', !$res["respuesta"]);
								$campo->addAttribute('errorMensaje', $res["mensaje"]);
							}else{
								// Esta setteado
								$campo['error'] = !$res["respuesta"];
								$campo['errorMensaje'] = $res["mensaje"];
							}
							$formularioValido = $formularioValido && $res["respuesta"];
						}
					}
					break;
				}
			}
			return $formularioValido && $recaptchaValido;
		}

		function obtenerResultado($xmlObtenerResultado, $pref=""){
			$sesion=Sesion::getInstancia();
			$nodos = $xmlObtenerResultado->xpath("//Propiedad[@nombre='idCasoUso' or @nombre='nombreCasoUso']");
			if(count($nodos)>0){
				$dato="".$nodos[0]["valor"];
			}
			
			foreach($xmlObtenerResultado as $nodo){
				if($nodo["nombre"]=="nombreCasoUso"){
					//$dato = $xmlObtenerResultado->xpath("//Propiedad[@nombre='idCasoUso' or @nombre='nombreCasoUso']");
					$dato=$nodo["valor"];
				}else if($nodo["nombre"]=="idCasoUso"){
					$dato=Control0CasoUso::getNombreCasoUso($nodo["valor"]);
				}
			}
			$sesion->agregarFormulario($xmlObtenerResultado, $dato,null);
			//msg::add($sesion);
			$this->js[]="Librerias/ideComponentes/XMLFormulario/XMLFormulario.js";
			$this->css[]="Librerias/ideComponentes/XMLFormulario/XMLFormulario.css";
			$this->js[]="Externos/jquery/jquery.validator/jquery.validate.js";
			static $contadorFormularios=0;
			$contadorFormularios++;
			/*
			$datosFormulario=$xmlObtenerResultado->attributes();
			$this->setXMLFormulario($xmlObtenerResultado, $datosFormulario["prefijo"]);
			$html=$this->toHTML();

			return $html;
			*/
			//echo "Los archivos Formulario son: ",revisarArreglo($this->js),revisarArreglo($this->css),$this->geshiXML($xmlObtenerResultado);

			if(strcmp($pref,"")==0){
				$pref="Formulario".$contadorFormularios;
				$id=$pref;
			}

			foreach($xmlObtenerResultado->children() as $hijo){
				switch($hijo->getName()){
					case "Propiedad":
						/*
						$xmlObtenerResultadotmp=$this->xml->xpath('Propiedad');
						$this->extraerNodo($xmlObtenerResultadotmp, "XMLFormulario_Propiedades");
						*/
						$nombrePropiedad=$hijo['nombre'];
						$this->propiedades["$nombrePropiedad"]=''.$hijo["valor"];
						/*
						if(isset($this->propiedades["id"]))
							$this->pref.=$this->propiedades["id"];
						*/
						if(strcmp($nombrePropiedad, "id")==0){
							$pref=(string)$hijo["valor"];
							$id=$pref;
						}
						break;
				}
			}

			$this->pref=$pref;
			$metodo="POST";
			$total="";
			if(isset($this->propiedades["Titulo"])){
				$total.="<div class='tituloFormulario'>".$this->propiedades["Titulo"]."</div>";
			}
			if(isset($this->propiedades["Metodo"])){
				$metodo=$this->propiedades["Metodo"];
			}
			$enctype="";
			if(isset($this->propiedades["enctype"])){
				$enctype="enctype='".$this->propiedades["enctype"]."'";
			}

			//$nodo->addAttribute("", "multipart/form-data");
			// No se esta utilizando, se vuelve a llenar dentro del siguiente for
			//$accionPlus="_".$this->propiedades["idCasoUso"]."_".$this->propiedades["idForm"];

			$htmlContenidos="";
			$htmlReglas="";
			//Variables para crear el HTML de reglas de validación en presentación
			$htmlMensajes="";
			$reglasBegin=true;

			$recaptcha="";
			//Se crea el html de captcha
			if (isset($this->propiedades["recaptcha"])){
				if(strcmp($this->propiedades["recaptcha"],"true")==0){
					$this->js[]="Externos/recaptcha/recaptcha.js";
					//Esta setteado recaptcha
					$publickey = "6Lc5GAoAAAAAAMyCkqDaecU3uvJGCKXKGMvkx_dV";
					$recaptcha=recaptcha_get_html($publickey);
				}
			}
			

			//Se instancian 2 veces el campo, por que desde el formulario se
			//deben saber todos los campo hijo que pueda tener así no sean hijos
			//directos
			$nuevoXML= new SimpleXMLElement("<Formulario/>");
			simplexml_merge($nuevoXML, $xmlObtenerResultado);
			

			$respuestaCamposFormulario = $nuevoXML->xpath("//Campo");

			/*
			if (!isset($this->propiedades["idCasoUso"])){
				$this->propiedades["idCasoUso"]="";
			}
			*/
			if (!isset($this->propiedades["nombreCasoUso"])){
				$this->propiedades["nombreCasoUso"]="";
			}
			if (!isset($this->propiedades["idForm"])){
				$this->propiedades["idForm"]="";
			}

			$accionPlus="_".$this->propiedades["nombreCasoUso"]."_".$this->propiedades["idForm"];
			$primero=true;
			$primeroHastaAca=true;
			foreach($respuestaCamposFormulario as $campoInternoFormulario){
				$campo= new Campo();
				//var_dump($campoInterno);
				// Este ajuste se hace por que se estaba llamando dos veces 
				// a la generación de componentes y no es necesario
				$campo->generarReglas($campoInternoFormulario, $id, "html", $accionPlus);
				$arrayReglas=$campo->clase->arrayReglas;
				//echo "<hr>";
				//var_dump($arrayReglas);
				if(!is_null($arrayReglas)){
					if(count($arrayReglas["reglas"])>0){
						if($reglasBegin)
						{
							$reglasBegin=false;
						}
						if(!$primero)
							$htmlReglas.=",";
						
						$htmlReglas.="\n	".$campo->clase->getNombre().": {";
						$htmlReglas.=implode(",",$arrayReglas["reglas"]);
						$htmlReglas.="\n".str_repeat("\t",3).'}';
						if(isset($arrayReglas["mensajes"])){
							if(count($arrayReglas["mensajes"])>0){
								if(!$primeroHastaAca)
									$htmlMensajes.=",";
								$htmlMensajes.="\n	".$campo->clase->getNombre().": {";
								$htmlMensajes.=implode(",",$arrayReglas["mensajes"]);
								$htmlMensajes.="\n".str_repeat("\t",2).'}';
								$primeroHastaAca=false;
							}
						}
						$primero=false;
					}
				}
				
			}
			
			foreach($xmlObtenerResultado->children() as $i=>$hijo){
				switch($hijo->getName()){
					case "Propiedad":
						break;
					case "Campo":

						$accionPlus="_".$this->propiedades["nombreCasoUso"]."_".$this->propiedades["idForm"];
						$campo= new Campo();

						if (strcmp($hijo["tipo"], "enviar")==0){
							$htmlContenidos.=$recaptcha;
							$recaptcha="";
						}

						$htmlContenidos.=$campo->obtenerResultado($hijo, $id, "html", $accionPlus);
						/*
						$arrayReglas=$campo->clase->arrayReglas;
						if(!is_null($arrayReglas)){
							if($reglasBegin)
							{
								$reglasBegin=false;
							}

							$htmlReglas.="\n	".$campo->clase->getNombre().": {";
							$htmlReglas.=$arrayReglas["reglas"];
							$htmlReglas.="\n".str_repeat("\t",3).'},';

							$htmlMensajes.="\n	".$campo->clase->getNombre().": {";
							$htmlMensajes.=$arrayReglas["mensajes"];
							$htmlMensajes.="\n".str_repeat("\t",2).'},';
						}
						*/

						$this->css=array_merge_recursive($this->css, $campo->obtenerCssAIncluir());
						$this->js=array_merge_recursive($this->js,$campo->obtenerJavascriptAIncluir());
						//@ToDo: ver si esta variable se puede quitar
						$this->no++;

						break;
					default:
						$htmlContenidos.=$this->llamarClaseGenerica($hijo);
				}
			}
			//Se crea el html de reglas y mensajes de validación
			$htmlValidacion="";
			if(!$reglasBegin){
				$htmlValidacion='
<script type="text/javascript">

jQuery.validator.addMethod("idefecha", validarFechaHora, "Por favor digite una fecha valida en el formato y-m-d h:m");
jQuery.validator.addMethod("entero", validarEntero, "Por favor digite un entero válido");
jQuery.validator.addMethod("idexml", validarXML, "XML no validado");
jQuery.validator.addMethod("color", validarColor, "Por favor ingrese un color válido");

$(function() {
	$("#'.$this->pref."\").validate({
	rules: {\n\t";

$htmlValidacion.=$htmlReglas;

$htmlValidacion.="\n".str_repeat("\t",2)."},
	messages: {
	";
$htmlValidacion.=$htmlMensajes;
$htmlValidacion.='
	}
	});
});
</script>';
			}else{
				$htmlReglas="";
			}

			if (!isset($this->propiedades["Accion"])){
				$this->propiedades["Accion"]="";
			}
			$total.="<div class='formulario'>".$htmlValidacion.
				"<form id='".$this->pref."' action='".$this->propiedades["Accion"]."' method='$metodo' target='_self' ".$enctype." >";
			$total.=$htmlContenidos;

			$total.="</form></div>";
			return $total;
		}

	}

?>
