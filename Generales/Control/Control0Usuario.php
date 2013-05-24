<?php
	class Control0Usuario{
		var $db;
		function Control0Usuario($db){
			$this->db=$db;
		}
		
		function autenticarUsuario($user, $pass){
			try{
				$daoUsario=new DAO0Usuario($this->db);

				$Consulta0 = new SimpleXMLElement('<Consulta/>');
				$Campo1 = $Consulta0->addChild('Campo');
				$Campo1->addAttribute('nombre','*');
				$Campo1->addAttribute('tablaOrigen','0Usuario');
				$Condiciones1 = $Consulta0->addChild('Condiciones');
				$Y2 = $Condiciones1->addChild('Y');
				$Igual3 = $Y2->addChild('Igual');
				$Igual3->addAttribute('tabla','0Usuario');
				$Igual3->addAttribute('campo','user');
				$Igual3->addAttribute('valor',''.$user.'');
				$Igual3 = $Y2->addChild('Igual');
				$Igual3->addAttribute('tabla','0Usuario');
				$Igual3->addAttribute('campo','pass');
				$Igual3->addAttribute('valor',''.$pass.'');
				$Igual3 = $Y2->addChild('Igual');
				$Igual3->addAttribute('tabla','0Usuario');
				$Igual3->addAttribute('campo','activo');
				$Igual3->addAttribute('valor','1');
				
				$usuarios=$daoUsario->getRegistros($Consulta0);
				return $usuarios[0];
				
			}catch(sinResultados $e){
				return null;				
			}
		}
		function getLista($campo){
			$lista=$this->db->consultar('
				<Consulta>
					<Campo nombre="*" tablaOrigen="0Usuario" />
					<Campo nombre="'.$campo.'" tablaOrigen="0Usuario" />
				</Consulta>');
			return $lista;
		}

		//Busca un usuario identificado por los valores de $parametros
		function identificarUsuario($parametros,$db){

			$daoUsuario= new DAO0Usuario($db);

			$consulta=new SimpleXMLElement('<Consulta />');
			$nodo=$consulta->addChild("Campo");
			$nodo->addAttribute("nombre","user");
			$nodo->addAttribute("tabla", "0Usuario");
			$nodo=$consulta->addChild("Condiciones");			
			$nodo=$nodo->addChild("Igual");
			$nodo->addAttribute("tabla", "0Usuario");
			$nodo->addAttribute("campo", "user");
			$nodo->addAttribute("valor", $parametros["usuario"]);

			try{
				$vosUsuario= $daoUsuario->getRegistros($consulta);
			}
			catch(sinResultados $e){
				return null;
			}

			if(count($vosUsuario)>1){
				//asercion("Error en la recuperación de los datos del usuario. Existe mas de un usuario con el mismo nombre.<br>".revisarArreglo($vosUsuario)." - sql:".$this->db->sql);
				asercion("Error en la recuperación de los datos del usuario. Existe mas de un usuario con el mismo nombre.");
			}elseif(count($vosUsuario)==1){
				return $vosUsuario[0];
			}
		}
		
		public static function asignarRolAUsuario($idUausrio,$idRol){
			$sesion=Sesion::getInstancia();
			$daoUsuarioRol=new DAO0UsuarioRol($sesion->getDB());
			$voUsuarioRol=new VO0UsuarioRol();
			$voUsuarioRol->setIdUsuario($idUausrio);
			$voUsuarioRol->setIdRol($idRol);
			try{
				$daoUsuarioRol->agregarRegistro($voUsuarioRol);
				return true;
			}catch(Exception $e){
				return false;
			}
		}
		
		public static function ingresoUsuario($xmlContenido,$voUsuario,$url,$timeout=60){
			$sesion=Sesion::getInstancia();
			$sesion->borrarParametro("idUsuario");
			$sesion->escribirParametro("idUsuario", $voUsuario->getIdUsuario());
			$sesion->borrarParametro("nombreUsuario");
			$sesion->escribirParametro("nombreUsuario", $voUsuario->getUser());

			// Se settea la hora de expiracion de sesión y el incremento del timeout
			$sesion->escribirParametro('fin'.$sesion->configuracion->titulo , time()+60*$timeout);
			// Se renueva el valor de la variable con el incremento de la renovación de expiración de sesión
			$sesion->escribirParametro('timeout'.$sesion->configuracion->titulo , $timeout);

			$wiki = $xmlContenido->addChild('Wiki');
			$wiki[]="==Ingresando al sistema==\n===Bienvenido ".$voUsuario->getUser()."===";
			$htmlencodeado1 = $xmlContenido->addChild('htmlencodeado');
			$htmlencodeado1[]=base64_encode("<meta http-equiv='Refresh' CONTENT='0; URL=".$url."' />");
			$wiki = $xmlContenido->addChild('Wiki');
			$wiki[]="[[ | Continuar aquí.]]";
		}
		
		public static function obtenerFormularioNuevoUsuario($xml,$mensajeActivacionCompleta="",$ingresoAutomatico=false,$casoDeUso=""){
			$sesion=Sesion::getInstancia();
			$mostrarFormularioRegistro=true;
			for($i=0;$i<count($sesion->args);$i++){
				switch($sesion->args["$i"]){
					case "pw":
						if(isset($sesion->args["".($i+1).""]) && isset($sesion->args["".($i+2).""])){
							if(strlen($sesion->args["".($i+1).""])>0 && strlen($sesion->args["".($i+2).""])>0){
								$mostrarFormularioRegistro=false;
								$usuario=Control0Usuario::identificarUsuario(array("usuario"=>$sesion->args[$i+1]), $sesion->getDB());
								if(!is_null($usuario)){
									$codigo=Control0Usuario::verificarCodigoRecuperacion(array("idUsuario"=>$usuario->getIdUsuario(),"codigo"=>$sesion->args["".($i+2).""]),$sesion->getDB());
									if(!is_null($codigo) && $codigo==true){
										Control0Usuario::borrarCodigoRecuperacion(array("idUsuario"=>$usuario->getIdUsuario(),"codigo"=>$sesion->args["".($i+2).""]),$sesion->getDB());
										$usuario->setActivo(1);
										try{
											$dao=new DAO0Usuario($sesion->getDB());
											$dao->actualizarRegistro($usuario);
											ControlXML::agregarNodoTexto($xml,"Wiki","=Activación completa=\n{$mensajeActivacionCompleta}\n\nGracias.");
											if($ingresoAutomatico){
												Control0Usuario::ingresoUsuario($xml,$usuario,resolverPath("/".$casoDeUso));
											}
										}catch(Exception $e){
											mensaje::add($e->getMessage(),ERROR);
											$mostrarFormularioRegistro=true;
										}
									}else{ $mostrarFormularioRegistro=true; }
								}else{ $mostrarFormularioRegistro=true; }
							}
						}
						break;
				}
			}
			if($mostrarFormularioRegistro){
				$ControlDimec=new ControlDimec($sesion,"0Usuario","nuevoUsuario",true);
				$ControlDimec->generarContenido($xml,"nuevo","Registro",array(),array("activo"=>0),array("user"=>"Usuario","pass"=>"Contraseña","correo"=>"Correo electrónico"));
			}
		}
		
		
		public static function procesarFormularioNuevoUsuario($contenido,$idRol,$parametros,$mensajeActivacionCompleta="",$ingresoAutomatico=false){
			$sesion=Sesion::getInstancia();
			$casoDeUso=siEsta($parametros["nombreCasoUso"]);
            $ControlDimec=new ControlDimec($sesion,"0Usuario","nuevoUsuario",true);
            if($idUsuario=$ControlDimec->procesarFormulario()){
	            if(Control0Usuario::asignarRolAUsuario($idUsuario,$idRol)){
		        	$dao=new DAO0Usuario($sesion->getDB());
		        	$voUsuario=$dao->getRegistro($idUsuario);
		        	$controlCasoUso = new Control0CasoUso($sesion->getDB());
		        	// Generar codigo temp
					$codigoTemp=ControlUtilidades::generarCadenaAleatorio();
					// Guardar el codigo temporal en la base de datos
					Control0Usuario::guardarCodigoRecuperacion(array("idUsuario"=>$voUsuario->getIdUsuario(),"codigo"=>$codigoTemp),$sesion->getDB());
		        	$xml = ControlXML::nuevo("Parametros");
					ControlXML::agregarNodo($xml, "Parametro", array("nombre"=>"asunto", "valor"=>$parametros["asunto"]));
					ControlXML::agregarNodo($xml, "Parametro", array("nombre"=>"correo", "valor"=>$voUsuario->getCorreo()));
					ControlXML::agregarNodo($xml, "Parametro", array("nombre"=>"smtpHost", "valor"=>$sesion->configuracion->configuracionEnvioCorreo["host"]));
					ControlXML::agregarNodo($xml, "Parametro", array("nombre"=>"smtpPort", "valor"=>$sesion->configuracion->configuracionEnvioCorreo["puerto"]));
					ControlXML::agregarNodo($xml, "Parametro", array("nombre"=>"smtpUser", "valor"=>$sesion->configuracion->configuracionEnvioCorreo["user"]));
					ControlXML::agregarNodo($xml, "Parametro", array("nombre"=>"smtpPass", "valor"=>$sesion->configuracion->configuracionEnvioCorreo["pass"]));
					ControlXML::agregarNodo($xml, "Parametro", array("nombre"=>"desde", "valor"=>$sesion->configuracion->configuracionEnvioCorreo["desde"]));
					ControlXML::agregarNodo($xml, "Parametro", array("nombre"=>"nombreDesde", "valor"=>$sesion->configuracion->configuracionEnvioCorreo["nombreDesde"]));
					ControlXML::agregarNodo($xml, "Parametro", array("nombre"=>"responder", "valor"=>$sesion->configuracion->configuracionEnvioCorreo["responder"]));

					$mensaje="Hemos recibido su petición de registro. Para su activación acceda el siguiente link en las próximas '''24 horas''':\n\n".
					"'''Nombre de usuario:''' ".$voUsuario->getUser()."\n".
					"'''Link para activación:'''\n".
					"http://".rawurlencode($_SERVER["HTTP_HOST"].resolverPath()."/".$controlCasoUso->getNombreCasoUso($sesion->leerParametro("idCasoUso"))."/pw/".$voUsuario->getUser()."/".$codigoTemp).
					"\n\n\nGracias,\n\n".
					"Ventas Ticketshop";
					$msg=ControlXML::agregarNodo($xml, "Mensaje");
					ControlXML::agregarNodoTexto($msg, "Wiki", $mensaje);
				
					$mensajero= new ControlMensajero();
					$mensajero->enviarCorreo($xml);
					$notificacion=$mensajero->getNotificacion();
					if(siEsta($notificacion["resultado"],false)){
						ControlXML::agregarNodoTexto($contenido,"Wiki","=Registro realizado correctamente=\n Ha sido enviado un mensaje de verificación a su correo electrónico, visite el link que se ha enviado para la activación de su cuenta.\n\nGracias por su registro.");
					}else{
						ControlXML::agregarNodoTexto($contenido,"Wiki","=Error en registro=\nEl mensaje no pudo ser enviado, contacte al administrador.");
					}
					return true;
				}else{
					mensaje::add("Ha ocurrido un error asignando el rol al usuario, contacte al administrador.",ERROR);
				}
            }else{
            	Control0Usuario::obtenerFormularioNuevoUsuario($contenido,$mensajeActivacionCompleta,$ingresoAutomatico,$casoDeUso);
            }
            return false;
		}
		
		//Cambia la contraseña de un usuario por una generada aleatoriamente
		//Retorna el nuevo pass si se puedo actualizar, si no retorna cadena vacía
		public static function recuperarContrasena($parametros,$db) {
				$daoUsuario= new DAO0Usuario($db);
				$voUsuario=$daoUsuario->getRegistro($parametros["idUsuario"]);
				$voUsuario->setPass(md5($parametros["clave"]));
				return $daoUsuario->actualizarRegistro($voUsuario);
		}
		//Agrega un registro en la tabla CodigoRecuperacion con los datos de $parametros
		public static function guardarCodigoRecuperacion($parametros,$db){
			// Creación del DAO para operaciones en la tabla de codigos de recuperacion de clave
			$daoRecuperacion = new DAO1CodigosRecuperacion($db);
			// Se crea un nuevo VO de codigo de recuperacion
			$codigoRec = new VO1CodigosRecuperacion();
			$codigoRec->setCodigo($parametros["codigo"]);
			$codigoRec->setIdUsuario($parametros["idUsuario"]);
			$codigoRec->setCaducidad(date('Y-m-d H:i:s',strtotime("+1 day")));

			// Se agrega el VO creado a la Base de Datos
			$exito=false;
			do
			{
				try{
					$daoRecuperacion->agregarRegistro($codigoRec);
					$exito=true;
				}catch(XMLSQLExcepcionRegistroDuplicado $e){
					
				}
			}while(!$exito);
		}
		// Busca en la tabla CodigosRecuperacion si la pareja idUsuario,codigo existe
		// si existe retorna true, si no existe retorna false
		// si algo fallo retorna null
		public static function verificarCodigoRecuperacion($parametros,$db){

			$daoRecuperacion= new DAO1CodigosRecuperacion($db);

			$consulta=new SimpleXMLElement('<Consulta />');
			$nodo=$consulta->addChild("Campo");
			$nodo->addAttribute("nombre","codigo");
			$nodo->addAttribute("tabla", "1CodigosRecuperacion");
			$nodo=$consulta->addChild("Condiciones");			
			$y=$nodo->addChild("Y");
			$nodo=$y->addChild("Igual");
			$nodo->addAttribute("tabla", "1CodigosRecuperacion");
			$nodo->addAttribute("campo", "idUsuario");
			$nodo->addAttribute("valor", $parametros["idUsuario"]);
			$nodo=$y->addChild("Igual");
			$nodo->addAttribute("tabla", "1CodigosRecuperacion");
			$nodo->addAttribute("campo", "codigo");
			$nodo->addAttribute("valor", $parametros["codigo"]);
			$nodo=$y->addChild("Otro");
			$nodo->addAttribute("signo", "mayorIgual");
			$nodo->addAttribute("tabla", "1CodigosRecuperacion");
			$nodo->addAttribute("campo", "caducidad");
			$nodo->addAttribute("valor", date('Y-m-d H:i:s',time()));

			try{
				$vosRecuperacion= $daoRecuperacion->getRegistros($consulta);
			}
			catch(sinResultados $e){
				return false;
			}

			if(count($vosRecuperacion)==1){
				return true;
			}

			return null;
		}
		// Borra los códigos de recuperación utlizando los datos de $parametros
		// Retorna: true si borró bien
		//			false si falló el borrado
		//			null si exite mas de un registro con los mismo datos
		public static function borrarCodigoRecuperacion($parametros,$db){

			$daoRecuperacion= new DAO1CodigosRecuperacion($db);

			$consulta=new SimpleXMLElement('<Consulta />');
			$nodo=$consulta->addChild("Campo");
			$nodo->addAttribute("nombre","codigo");
			$nodo->addAttribute("tabla", "1CodigosRecuperacion");
			$nodo=$consulta->addChild("Condiciones");			
			$y=$nodo->addChild("Y");
			$nodo=$y->addChild("Igual");
			$nodo->addAttribute("tabla", "1CodigosRecuperacion");
			$nodo->addAttribute("campo", "idUsuario");
			$nodo->addAttribute("valor", $parametros["idUsuario"]);
			$nodo=$y->addChild("Igual");
			$nodo->addAttribute("tabla", "1CodigosRecuperacion");
			$nodo->addAttribute("campo", "codigo");
			$nodo->addAttribute("valor", $parametros["codigo"]);

			try{
				$vosRecuperacion= $daoRecuperacion->getRegistros($consulta);
			}catch(sinResultados $e){
				return null;
			}
			if(count($vosRecuperacion)==1){
				return $daoRecuperacion->eliminarRegistro($vosRecuperacion[0]);
			}
		}
	}
?>
