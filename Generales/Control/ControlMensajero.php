<?php

	require_once('../Externos/phpmailer/class.phpmailer.php');

	class ControlMensajero{
		private $notificacion=array();
		function ControlMensajero(){
		}
		public function getNotificacion(){
			return $this->notificacion;
		}
		private function setNotificacion($notificacion){
			$this->notificacion=$notificacion;
		}
		//Envia un correo y settea el estado de la transacción en el array $notificacion
		//$notificacion["resultado"] es un bool: true= transacción exitosa, false= transaccion fallida
		function enviarCorreo($xml){
			$adjuntos=array();
			//Se inicializan algunas variables
			$mail = new PHPMailer();
			//Se settean algunos valores por defecto para SMTP
			/*
			$mail->SMTPAuth   = true;                  // Habilita la autenticación SMTP
			$mail->SMTPSecure = "ssl";                 // Establece el tipo de seguridad SMTP
			$mail->Host       = "smtp.gmail.com";      // Establece Gmail como el servidor SMTP
			$mail->Port       = 465;          
			*/
			
			
			
			$mail->IsSMTP();
			//$mail->SMTPDebug  = 2; 
$mail->SMTPAuth   = true;                  // Habilita la autenticación SMTP
$mail->SMTPSecure = "ssl";                 // Establece el tipo de seguridad SMTP
$mail->Host       = "smtp.gmail.com";      // Establece Gmail como el servidor SMTP
$mail->Port       = 465;                   // Establece el puerto del servidor SMTP de Gmail			
			$mail->Username = 'ventas@ticketshop.com.co';
			$mail->Password = 'mail75315';
			$mail->ContentType = "text/html";
			$mail->CharSet = "utf-8";
			//Se llenan los parámetros para enviar el correo
			
			
			/**
			msg::add("Estructura a enviar");
			msg::add($xml);
			*/
			
			foreach($xml->children() as $hijo){
				if(strcmp($hijo->getName(),"Parametro")==0){
					switch($hijo["nombre"]){
						case "correo":
							$mail->AddAddress($hijo["valor"]);
							break;
						case "cc_correo":
							$mail->AddCC($hijo["valor"]);
							break;
						case "cco_correo":
							$mail->AddBCC($hijo["valor"]);
							break;
						case "responder":
							$mail->AddReplyTo($hijo["valor"]);
							break;
						case "asunto":
							$mail->Subject = (string)$hijo["valor"];
							break;
						case "mensaje":
							$mensaje=$hijo["valor"];
							$mail->MsgHTML($mensaje);
							break;
						case "archivo":
							$mail->addAttachment((string)$hijo["valor"]);
							$adjuntos[]=(string)$hijo["valor"];
							break;
						case "smtpHost":
							$mail->Host = (string)$hijo["valor"];
							break;
						case "smtpPort":
							$mail->Port = (string)$hijo["valor"];
							break;
						case "smtpUser":
							$mail->Username = (string)$hijo["valor"];
							break;
						case "smtpPass":
							$mail->Password = (string)$hijo["valor"];
							break;
							
						case "desde":
							$mail->From = (string)$hijo["valor"];
							break;
						case "nombreDesde":
							$mail->FromName = (string)$hijo["valor"];
							break;
						default:
						//XML deformado
					}
				}else if(strcmp($hijo->getName(),"Mensaje")==0){
					$fabrica = new ComponentePadre();
					$html="";
					foreach($hijo as $msg){
						$html.=$fabrica->llamarClaseGenerica($msg);
					}
					$mail->MsgHTML($html);

				}else{
				//XML deformado
				}
			}
			
			//Se envia el correo y se notifica el estado de la transaccion
			if(!$mail->Send()) {
			  $this->setNotificacion(array("resultado"=>false, "mensaje"=>"Error en el envio: ".$mail->ErrorInfo));
			} else {
			  $this->setNotificacion(array("resultado"=>true, "mensaje"=> "Su correo ha sido enviado correctamente."));
			}
			foreach($adjuntos as $adjunto){
				unlink($adjunto);
			}
		}
		//Recibe una lista de correos validos separados por coma
		//Genera un xml con los correos recibidos que es valido para la especificacion que este componente espera recibir
		function separarCorreos($correos,$correoTag){
			$arrayCorreos = explode(",",$correos);
			$xmlCorreos= new SimpleXMLElement("<Parametros/>");
			//recorre la cadena de correos
			foreach($arrayCorreos as $correo){
				//Agrega el correo si al limpiarlo de espacios no está vacio
				$valor=str_replace(" ", "",$correo);
				if(strcmp($valor,"")!=0)
				{
					$parametro=$xmlCorreos->addChild("Parametro");
					$parametro->addAttribute("nombre",$correoTag);
					$parametro->addAttribute("valor",$valor);
				}
			}
			return $xmlCorreos;
		}
	}
?>
