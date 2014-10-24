<?php
class htmlencodeado extends ComponentePadre implements componente{

	public function obtenerResultado($dato){
		return base64_decode($dato);
	}
}

class HojaEstilo{
	public function obtenerResultado($dato){
		return $this->generarCSS($dato);
	}
	
	function generarCSS($url){
		return "<link rel='stylesheet' type='text/css' href='".resolverPath()."/".$url."' />";
		//return '<style type="text/css" media="all">@import url("'.$url.'");</style>'."\n";
	}
}

class ComandoPagina{
	/*
	 * @Todo,  falta colocar algunos parametros basicos como recargar la pagina
	 */
	public function obtenerResultado($dato){
		return "<div>Componente <strong>ComandoPagina</strong> en desarrollo...</div>";
	}
}


class Json extends ComponentePadre implements componente{
	public function obtenerResultado($dato){
		return $dato;
	}
}
class Html extends ComponentePadre implements componente{
	public function obtenerResultado($dato){
		//var_dump($dato);
		//echo "texto[".$dato->asXML()."]";
		//return str_replace("&#60;", "<", str_replace("&#62;", ">", $dato));
		return $dato;
	}
}

class Boton extends ComponentePadre implements componente{

	public function obtenerResultado($dato){

		$this->js[]="Externos/jquery/jquery.dropshadow.js";

		// Variable contadora para agrega como id a los botones que no tienen id
		static $boton = 1;
		
		$atributos="";
		foreach($dato->attributes() as $nombre=>$valor){
			switch($nombre){case "id":case "clase":case "titulo":case "path":case "imagen":case "codigo":break;default:
				$atributos.=$nombre."='".$valor."' ";
			}
		}
		if(isset($dato["id"])){
			$id = $dato["id"];
		}else{
			$id = "Boton".$boton;
			$boton++;
		}
		$click="";
		if(isset($dato["path"])){
			$click="$('#".$id."').click(function(){ira('".$dato["path"]."');});";
		}
		if(isset($dato["codigo"])){
			$click="$('#".$id."').click(function(){ ".$dato["codigo"]." });";
		}
		if(!isset($dato["sombra"])){
			$dato["sombra"]="white";
		}
		$eventos="<script>$(function(){\n{$click}\n$('#".$id."').dropshadow({color:'".$dato["sombra"]."'});});</script>";
		$titulo="";
		if(isset($dato["titulo"])){
			$titulo=$dato["titulo"];
		}
		$clase="";
		if(isset($dato["clase"])){
			$clase=$dato["clase"];
		}
		$varianteDeEstilo="";
		if(strcmp($titulo,"")!=0){
			$varianteDeEstilo="ConTexto";
		}
		$respuestaImagen="";
		if(isset($dato["imagen"])){
			$imagen=json_decode($dato["imagen"],true);
			$propiedades="";
			if(is_array($imagen)){
				foreach($imagen as $nombre=>$propiedad){
					$propiedades.=$nombre."='".$propiedad."' ";
				}
			}
			$respuestaImagen="<img $propiedades/>";
		}
		$estilo="ui-state-default ui-corner-all";
		if(isset($dato["ui"])){
			if(strcmp($dato["ui"],"false")==0){
				$estilo="";
			}
		}
		$respuesta="<button id='".$id."' type='button' class='$estilo ".$clase."' $atributos>{$respuestaImagen}$titulo</button> ";
		return $eventos.$respuesta;
	}
}

define("ALERTA",0,true);
define("ERROR",1,true);
define("CORREO",2,true);
class mensaje{

	private static $instancia;
	private $mensajes=array();

	public static function getInstance(){
		if(!self::$instancia instanceof self){
	 		self::$instancia = new self;
		}
		return self::$instancia;
	}

	public static function add($mensaje=null,$tipo=ALERTA,$conf=array()){
		$obj=mensaje::getInstance();
		$obj->mensaje($mensaje,$tipo,$conf);
	}

	public function obtenerResultado($dato=null){
		if(count($this->mensajes)>0){
			$mensajes=implode("",$this->mensajes);
			if(strlen($mensajes)>0){
				return $mensajes;
			}
		}
		return "";
	}

	function mensaje($mensaje=null,$tipo=ALERTA,$conf=array()){
		if (!is_null($mensaje)){
			if(is_object($mensaje)){
				switch(get_class($mensaje)){
					case "Sesion":
						$mensaje=generalXML::geshiXML($mensaje->xml);
						break;
					case "SimpleXMLElement":
						$mensaje=generalXML::geshiXML($mensaje);
						break;
					default:
						$mensaje=print_r($mensaje,true);
				}
			}
			if(is_array($mensaje)){
				$mensaje=print_r($mensaje,true);
			}
			if(strlen($mensaje)>0){
				switch($tipo){
					case ALERTA:
						$this->mensajes[]=mensaje::crearMensajeAlerta($mensaje);
						break;
					case ERROR:
						$this->mensajes[]=mensaje::crearMensajeError($mensaje);
						break;
					case CORREO:
						$xml = ControlXML::nuevo("Parametros");
						$sesion=Sesion::getInstancia();
						// HOST
						$host="ssl://box307.bluehost.com";
						if(isset($sesion->configuracion->configuracionEnvioCorreo["host"])){
							$host=$sesion->configuracion->configuracionEnvioCorreo["host"];
						}
						if(isset($conf["host"])){
							$host=$conf["host"];
						}
						// PUERTO
						$puerto="465";
						if(isset($sesion->configuracion->configuracionEnvioCorreo["puerto"])){
							$puerto=$sesion->configuracion->configuracionEnvioCorreo["puerto"];
						}
						if(isset($conf["puerto"])){
							$puerto=$conf["puerto"];
						}
						// USUARIO
						$user="mensajero@a.idesoluciones.com";
						if(isset($sesion->configuracion->configuracionEnvioCorreo["user"])){
							$user=$sesion->configuracion->configuracionEnvioCorreo["user"];
						}
						if(isset($conf["user"])){
							$user=$conf["user"];
						}
						// CONTRASEÑA
						$pass="C$$$.7Qu@gtI";
						if(isset($sesion->configuracion->configuracionEnvioCorreo["pass"])){
							$pass=$sesion->configuracion->configuracionEnvioCorreo["pass"];
						}
						if(isset($conf["pass"])){
							$pass=$conf["pass"];
						}
						// DESDE
						$desde="info@idesoluciones.com";
						if(isset($sesion->configuracion->configuracionEnvioCorreo["desde"])){
							$desde=$sesion->configuracion->configuracionEnvioCorreo["desde"];
						}
						if(isset($conf["desde"])){
							$desde=$conf["desde"];
						}
						// NOMBRE DESDE
						$nombreDesde="ideSoluciones";
						if(isset($sesion->configuracion->configuracionEnvioCorreo["nombreDesde"])){
							$nombreDesde=$sesion->configuracion->configuracionEnvioCorreo["nombreDesde"];
						}
						if(isset($conf["nombreDesde"])){
							$nombreDesde=$conf["nombreDesde"];
						}
						// RESPONDER
						$responder="info@idesoluciones.com";
						if(isset($sesion->configuracion->configuracionEnvioCorreo["responder"])){
							$responder=$sesion->configuracion->configuracionEnvioCorreo["responder"];
						}
						if(isset($conf["responder"])){
							$responder=$conf["responder"];
						}
						// CORREO DESTINO
						$correo="info@idesoluciones.com";
						if(isset($sesion->configuracion->configuracionEnvioCorreo["correo"])){
							$correo=$sesion->configuracion->configuracionEnvioCorreo["correo"];
						}
						if(isset($conf["correo"])){
							$correo=$conf["correo"];
						}
						// ASUNTO
						$asunto="[".$sesion->configuracion->titulo."][LOG] ".strftime("%Y-%m-%d %I:%M %P");
						if(isset($sesion->configuracion->configuracionEnvioCorreo["asunto"])){
							$asunto=$sesion->configuracion->configuracionEnvioCorreo["asunto"];
						}
						if(isset($conf["asunto"])){
							$asunto=$conf["asunto"];
						}

						ControlXML::agregarNodo($xml, "Parametro", array("nombre"=>"asunto", "valor"=>$asunto));
						ControlXML::agregarNodo($xml, "Parametro", array("nombre"=>"correo", "valor"=>$correo));
						ControlXML::agregarNodo($xml, "Parametro", array("nombre"=>"smtpHost", "valor"=>$host));
						ControlXML::agregarNodo($xml, "Parametro", array("nombre"=>"smtpPort", "valor"=>$puerto));
						ControlXML::agregarNodo($xml, "Parametro", array("nombre"=>"smtpUser", "valor"=>$user));
						ControlXML::agregarNodo($xml, "Parametro", array("nombre"=>"smtpPass", "valor"=>$pass));
						ControlXML::agregarNodo($xml, "Parametro", array("nombre"=>"desde", "valor"=>$desde));
						ControlXML::agregarNodo($xml, "Parametro", array("nombre"=>"nombreDesde", "valor"=>$nombreDesde));
						ControlXML::agregarNodo($xml, "Parametro", array("nombre"=>"responder", "valor"=>$responder));

						$msg=ControlXML::agregarNodo($xml, "Mensaje");
						ControlXML::agregarNodoTexto($msg, "Wiki", $mensaje);

						$mensajero= new ControlMensajero();
						$mensajero->enviarCorreo($xml);
						$notificacion=$mensajero->getNotificacion();
						break;
					default:
						$this->mensajes[]="<pre>".$mensaje."</pre>";
				}
			}
		}
	}
	
	
	public static function crearMensajeAlerta($mensaje){
		$r ="";
		if(strlen($mensaje)>0){
			$r ='<div class="ui-widget" >';
			$r.='<div id="" class="ui-state-highlight ui-corner-all" style="overflow:auto;">';
			$r.='<span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-info"></span>';
			$r.="<pre>".$mensaje."</pre>";
			$r.='</div>';
			$r.='</div>';
		}
		return $r;
	}
	public static function crearMensajeError($mensaje){
		$r ="";
		if(strlen($mensaje)>0){
			$r ='<div class="ui-widget" >';
			$r.='<div id="" class="ui-state-error ui-corner-all" style="overflow:auto;">';
			$r.='<span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-alert"></span>';
			$r.="<pre>".$mensaje."</pre>";
			$r.='</div>';
			$r.='</div>';
		}
		return $r;
	}
	
}
class mensajes extends mensaje{
	function mensajes($mensaje=null,$tipo="alerta"){
		mensaje::add($mensaje,$tipo);
	}
}
class msg extends mensaje{}

class cfg {
	public static function get($variable){
		$sesion = Sesion::getInstancia();
		if (isset($sesion->configuracion->$variable)){
			return $sesion->configuracion->$variable;
		}
		return "";
	}
}

?>
