<?php
	require_once("Log.php");

	class ControlActividades{
		private $logger = null;

		/**
		* Realiza una insepección de las variables especificadas por $parametros
		* y lo guarda en un archivo de log especificado por $archivo.
		*
		* @param array $parametros	Las variables a las cuales se les inspeccionará el contenido.
		* @param int $idUsuario		Id del usuario desde el cual se hizo la petición que se está registrando.
		* @param string $archivo	El nombre del archivo de log en el cual se hará el dump de las variables.
		*							Por defecto se escribe en general.log
		* @param string $ident		Es lo mismo que ident de Log.
		* @param array $prioridad	Es lo mismo que priority de Log.
		*
		* @access public
		*/
		public static function registrarEnArchivo($parametros, $idUsuario, $archivo="general.log", $ident="", $prioridad=null){
			// trackeando los args
			if(is_array($parametros)){
				$out = "";
				foreach($parametros as $id=>$parametro){
					$out .= "[[".$id."]] =>".neat_r($parametro,true)."\n";
				}
			}else{
				$out = neat_r($parametros,true);
			}
			$path = $_SESSION["paths"]["pathServidor"]."/logs/";
			$conf = array('timeFormat' => '%Y-%m-%d %H:%M:%S');
			$logger = &Log::singleton('file', $path.$archivo, $ident, $conf);
			$logger->log("IP origen: ".$_SERVER["REMOTE_ADDR"]."\n"."idUsuario: ".$idUsuario."\n".html_entity_decode(strip_tags($out)), $prioridad);
		}

		/**
		* Realiza un log en un archivo rotativo
		*
		* @param array $parametros	Las variables a las cuales se les inspeccionará el contenido.
		* @param string $archivo	Nombres del archivo, se le adjuntará la fecha antes del ultimo punto.
		*
		* @access public	
		*/
		public static function add($parametros, $archivo="general.log"){
			$sesion = Sesion::getInstancia();
			$idUsuario = $sesion->leerParametro("idUsuario");
			$ident="";
			$prioridad=null;
			$nombreArchivo = explode(".", $archivo);
			$extension = $nombreArchivo[count($nombreArchivo)-1];
			$archivoFinal = "";
			for($i=0;$i<count($nombreArchivo)-1;$i++){
				$archivoFinal .= $nombreArchivo[$i];
			}
			$archivoFinal .= "_".date("Y-m-d").".".$extension;
			ControlActividades::registrarEnArchivo($parametros, $idUsuario, $archivoFinal, $ident, $prioridad);
		}

		/**
		* Realiza un dump de las variables especificadas por $parametros y lo guarda
		* en una tabla que se accede mediante la conexión a base de datos especificada por $db.
		*
		* @param array $parametros	Las variables a las cuales se les inspeccionará el contenido.
		* @param string $idUsuario	Id del usuario desde el cual se hizo la petición que se está registrando.
		* @param string $db			Conexión valida a una base de datos.
		* @param string $ident		Es lo mismo que ident de Log.
		* @param array $prioridad	Es lo mismo que priority de Log.
		*
		* @access public
		*/
		public static function registrarEnBaseDatos($parametros, $idUsuario, $ident="", $prioridad=null, $enviarCorreo=false, $correo="controlactividades@idesoluciones.com"){
			$sesion=Sesion::getInstancia();
			$db=$sesion->getDB();
			// trackeando los args
			if(is_array($parametros)){
				$out = "";
				foreach($parametros as $id=>$parametro){
					$out .= "[[".$id."]] =>".print_r($parametro,true)."\n";
				}
			}else{
				$out = print_r($parametros,true);
			}
			// Se crea el DAO de la tabla de logs
			$daoLogs = new DAO0Logs($db);
			// Se crea y se llena el VO de logs
			$log = new VO0Logs();
			$log->setIdUsuario($idUsuario);
			$log->setFechalog(date('Y-m-d H:i:s',time()));
			$log->setIdent($ident);
			$log->setPrioridad($prioridad);
			$log->setMensaje(html_entity_decode(strip_tags($out)));
			$log->setDirecionIP($_SERVER["REMOTE_ADDR"]);
			$daoLogs->agregarRegistro($log);
			
			if ($enviarCorreo){
				$xml = ControlXML::nuevo("Parametros");
				ControlXML::agregarNodo($xml, "Parametro", array("nombre"=>"asunto", "valor"=>"[Control Actividades] ".$ident));
				ControlXML::agregarNodo($xml, "Parametro", array("nombre"=>"correo", "valor"=>$correo));
				ControlXML::agregarNodo($xml, "Parametro", array("nombre"=>"smtpHost", "valor"=>"ssl://smtp.gmail.com"));
				ControlXML::agregarNodo($xml, "Parametro", array("nombre"=>"smtpPort", "valor"=>"465"));
				ControlXML::agregarNodo($xml, "Parametro", array("nombre"=>"smtpUser", "valor"=>"bot@idesoluciones.com"));
				ControlXML::agregarNodo($xml, "Parametro", array("nombre"=>"smtpPass", "valor"=>"Z6M/wnZ(dyB,"));
				ControlXML::agregarNodo($xml, "Parametro", array("nombre"=>"nombreDesde", "valor"=>"ideSoluciones"));
				ControlXML::agregarNodo($xml, "Parametro", array("nombre"=>"responder", "valor"=>"info@idesoluciones.com"));


				$msg=ControlXML::agregarNodo($xml, "Mensaje");
				ControlXML::agregarNodoTexto($msg, "Wiki", html_entity_decode(strip_tags($out)));
			
				$mensajero= new ControlMensajero();
				$mensajero->enviarCorreo($xml);
				$notificacion=$mensajero->getNotificacion();
				return $notificacion;

			
			}
			
		}
		

	}
	
	class reg extends ControlActividades{}
?>
