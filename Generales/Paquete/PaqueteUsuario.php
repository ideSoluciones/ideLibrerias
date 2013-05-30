<?php
	class PaqueteUsuario extends Paquete{
		/**
		* Login
		*/
		function nombreMenu_login($sesion){
			return "Ingreso";
		}
		function generarContenido_login($sesion, $contenido=null, $nombreCasoUso=null){
			if (is_null($sesion)){
				$sesion=Sesion::getInstancia();
			}
			if (is_null($nombreCasoUso)){
				$nombreCasoUso=$sesion->leerParametro("nombreCasoUso");
			}
			if (is_null($contenido)){
				$contenido=xml::add(null, "Contenido");
			}
			
			if ($sesion->leerParametroDestinoActual("Usuario")!="" && $sesion->leerParametroDestinoActual("Pass")!=""){
				$controlUsuario=new Control0Usuario($sesion->getDB());
				$voUsuario=$controlUsuario->autenticarUsuario($sesion->leerParametroDestinoActual("Usuario"), md5($sesion->leerParametroDestinoActual("Pass")));
				// Se settea una variable con el incremento de la renovación de expiración de sesión
				$timeout=$sesion->leerParametroFormularioActual("timeout");
				//msg::add($timeout);
				if ($timeout==""){
					$timeout = 30;
				}
				

				if (!is_null($voUsuario)){
					$destino=$sesion->leerParametroFormularioActual("destinoCasoUsoLogin");
					//echo generalXML::geshiXML($sesion->xml);
					Control0Usuario::ingresoUsuario($contenido,$voUsuario,resolverPath()."/".$destino,$timeout);
					return $contenido;
				}else{
					xml::add($contenido, 'Wiki', "==Error Accediendo==\nUsuario o contraseña invalido, intente de nuevo:");
				}
			}

			
			
			xml::add($contenido, "Html", '<style>#Formulario1{margin: 0px;height: 300px;}</style>');

			$Formulario = $contenido->addChild('Formulario');
			$Propiedad = $Formulario->addChild('Propiedad');
			$Propiedad->addAttribute('nombre','idCasoUso');
			$Propiedad->addAttribute('valor',Control0CasoUso::getIdCasoUso("login"));

			$Contenedor = $Formulario->addChild('Contenedor');
			$Contenedor->addAttribute('id','contenedorLogin');
			$Contenedor->addAttribute('clase','contenedorCentradoBonito');
			$Contenedor->addAttribute('titulo','Entrar a la aplicación');
				$Campo = $Contenedor->addChild('Campo');
					$Campo->addAttribute('tipo','cadena');
					$Campo->addAttribute('titulo','Usuario');
					$Campo->addAttribute('nombre','Usuario');
					$Campo->addAttribute('requerido','true');
				$Campo = $Contenedor->addChild('Campo');
					$Campo->addAttribute('tipo','clave');
					$Campo->addAttribute('titulo','Contraseña');
					$Campo->addAttribute('nombre','Pass');
					$Campo->addAttribute('requerido','true');
					$Campo->addAttribute('numeroCaracteresMin','2');
				$Campo = $Contenedor->addChild('Campo');
					$Campo->addAttribute('tipo','entero');
					$Campo->addAttribute('titulo','Duración de la sesión en minutos');
					$Campo->addAttribute('nombre','timeout');
					$Campo->addAttribute('requerido','true');
					$Campo->addAttribute('valorPorDefecto','30');
					$Campo->addAttribute('minimo','1');
					$Campo->addAttribute('maximo','1440');
				$Campo = $Contenedor->addChild('Campo');
					$Campo->addAttribute('tipo','enviar');
					$Campo->addAttribute('titulo','Enviar');
					$Campo->addAttribute('nombre','Enviar');
				$Campo = $Contenedor->addChild('Campo');
					$Campo->addAttribute('tipo','oculto');
					$Campo->addAttribute('nombre','destinoCasoUsoLogin');
					$Campo->addAttribute('valorPorDefecto',$nombreCasoUso);
			$wiki = $Contenedor->addChild('Wiki');
			$wiki[] = '[[recuperarContrasena | ¿Olvidó su contraseña? ]]';
				//echo generalXML::geshiXML($contenido);

			return $contenido;

		}
		
		function procesarFormulario_login($sesion){
			return $this->generarContenido_login($sesion);
		}



		/**
		* Logout
		*/

		function nombreMenu_logout($sesion){
			return "";
		}
		function generarContenido_logout($sesion){


	
			$salirInmediato=$sesion->leerParametroDestinoActual("inmediato");
			if (strcmp($salirInmediato, "true")==0){
				return $this->procesarFormulario_logout($sesion, true);
			}


			$contenido = new SimpleXMLElement('<Contenido/>');
			$Wiki1 = $contenido->addChild('Wiki');
			$Wiki1[]="==Formulario Salida==";
			$Formulario1 = $contenido->addChild('Formulario');
			$Propiedad2 = $Formulario1->addChild('Propiedad');
			$Propiedad2->addAttribute('nombre','idCasoUso');
			$Propiedad2->addAttribute('valor',$sesion->leerParametro("idCasoUso"));
			$Contenedor2 = $Formulario1->addChild('Contenedor');
			$Contenedor2->addAttribute('clase','contenedorCentradoBonito');
			$Contenedor2->addAttribute('id','contenedorLogout');
			$Contenedor2->addAttribute('titulo','¿Desea salir de la aplicación?');
			$Campo3 = $Contenedor2->addChild('Campo');
			$Campo3->addAttribute('tipo','enviar');
			$Campo3->addAttribute('titulo','Cancelar');
			$Campo3->addAttribute('nombre','salir');
			$Campo3 = $Contenedor2->addChild('Campo');
			$Campo3->addAttribute('tipo','enviar');
			$Campo3->addAttribute('titulo','Salir');
			$Campo3->addAttribute('nombre','salir');
			return $contenido;
		}
		function procesarFormulario_logout($sesion, $inmediato=false){
			if (strcmp($sesion->leerParametroFormularioActual("salir"), "Salir")==0 || $inmediato){

				$sesion->borrarParametros();

				$sesion->escribirParametro("idUsuario", "1", true);
				$_SESSION["idSesion"]=0;

				$contenido = new SimpleXMLElement('<Contenido/>');
				$Texto1 = $contenido->addChild('Texto');
				$Campo2 = $Texto1->addChild('Campo');
				$Campo2->addAttribute('nombre','titulo');
				$Campo2->addAttribute('nivel','1');
				$Campo2->addAttribute('valor','Saliendo del sistema');
				$Campo2 = $Texto1->addChild('Campo');
				$Campo2->addAttribute('nombre','contenido');
				$Campo2->addAttribute('valor','Ha salido del sistema');
				$htmlencodeado1 = $contenido->addChild('htmlencodeado');
				$htmlencodeado1[]=base64_encode("<meta http-equiv='Refresh' CONTENT='0; URL=".resolverPath()."' />");
				$wiki = $contenido->addChild('Wiki');
				$wiki[]="[[ | Continuar aquí.]]";


			}else{
				$contenido = new SimpleXMLElement('<Contenido/>');
				$Texto1 = $contenido->addChild('Texto');
				$Campo2 = $Texto1->addChild('Campo');
				$Campo2->addAttribute('nombre','titulo');
				$Campo2->addAttribute('nivel','1');
				$Campo2->addAttribute('valor','Saliendo del sistema');
				$Campo2 = $Texto1->addChild('Campo');
				$Campo2->addAttribute('nombre','contenido');
				$Campo2->addAttribute('valor','No ha salido del sistema');
				$htmlencodeado1 = $contenido->addChild('htmlencodeado');
				$htmlencodeado1[]=base64_encode("<meta http-equiv='Refresh' CONTENT='0; URL=".resolverPath()."' />");

			}
			return $contenido;
		}

		/*
		* recuperarContrasena
		*/
		function nombreMenu_recuperarContrasena($sesion){
			return "";
		}
		function generarContenido_recuperarContrasena($sesion, $contenido=null){

			if (is_null($contenido)){
				$contenido=new SimpleXMLElement("<Contenido/>");
			}

			// Se crea el XML a retornar
			$formulario=$contenido->addChild("Formulario");
			$nodo=$formulario->addChild("Propiedad");
			$nodo->addAttribute("nombre","idCasoUso");
			$nodo->addAttribute("valor",$sesion->leerParametro("idCasoUso"));
			// Se crea el campo oculto que identifica si se va a recuperar ó solicitar recuperación
			$oculto=$formulario->addChild("Campo");
			$oculto->addAttribute("nombre", "accion");
			$oculto->addAttribute("tipo", 'oculto');

			if(strcmp($sesion->args[1],"pw")==0 && !is_null($sesion->args[2]) && !is_null($sesion->args[3]))
			{
				// arg(2) y arg(3) estan en la URL

				// En el procesamiento se va a recuperar la contraseña
				$oculto->addAttribute("valorPorDefecto", "recuperar");

				// Se consulta si el usuario existe
				$usuario=Control0Usuario::identificarUsuario(array("usuario"=>$sesion->args[2]), $sesion->getDB());

				if(!is_null($usuario)){
					// arg(2) es legal
					// Se consulta si la pareja idUsuario, codigo es legal
					$codigo=Control0Usuario::verificarCodigoRecuperacion(array("idUsuario"=>$usuario->getIdUsuario(),"codigo"=>$sesion->args[3]),$sesion->getDB());
					if(!is_null($codigo) && $codigo==true){
						// arg(3) es legal
						$nodo=$formulario->addChild("Wiki");
						$nodo[]="= Restablezca su contraseña =
						Hola '''".$usuario->getUser()."''', por favor ingrese una nueva contraseña para su cuenta";
						$nodo=$formulario->addChild("Campo");
						$nodo->addAttribute("tipo","clave");
						$nodo->addAttribute("titulo","Contraseña:");
						$nodo->addAttribute("nombre","clave1");
						$nodo->addAttribute('requerido','true');
						$nodo=$formulario->addChild("Campo");
						$nodo->addAttribute("tipo","clave");
						$nodo->addAttribute("titulo","Confirme su contraseña:");
						$nodo->addAttribute("nombre","clave2");
						$nodo->addAttribute('requerido','true');
						$nodo=$formulario->addChild("Campo");
						$nodo->addAttribute("nombre", "idUsuario");
						$nodo->addAttribute("tipo", 'oculto');
						$nodo->addAttribute("valorPorDefecto", $usuario->getIdUsuario());
						$nodo=$formulario->addChild("Campo");
						$nodo->addAttribute("nombre", "codigo");
						$nodo->addAttribute("tipo", 'oculto');
						$nodo->addAttribute("valorPorDefecto", $sesion->args[3]);
						$nodo=$formulario->addChild("Campo");
						$nodo->addAttribute("tipo","enviar");
						$nodo->addAttribute("titulo","Enviar");
						$nodo->addAttribute("nombre","enviar");
					}else{
						// ToDo: notificar de código inválido
						header("location: ".resolverPath());
					}
				}else{
					// ToDo: notificar de usuario inválido
					header("location: ".resolverPath());
				}
			}else{

				$controlUsuario = new Control0Usuario($this->db);

				// En el procesamiento se va a solicitar un link de recuperacion
				$oculto->addAttribute("valorPorDefecto", "solicitar");

				$nodo=$formulario->addChild("Wiki");
				$nodo[]="= ¿Olvidó su contraseña? =
				Para recuperar su contraseña escriba el nombre de usuario con el cual está registrado en el sistema:";
				$nodo=$formulario->addChild("Propiedad");
				$nodo->addAttribute("nombre", "recaptcha");
				$nodo->addAttribute("valor", "true");
				$nodo=$formulario->addChild("Campo");
				$nodo->addAttribute("tipo","cadena");
				$nodo->addAttribute("nombre","usuario");
				$nodo->addAttribute('requerido','true');
				$nodo=$formulario->addChild("Campo");
				$nodo->addAttribute("tipo","enviar");
				$nodo->addAttribute("titulo","Enviar");
				$nodo->addAttribute("nombre","enviar");

			}

			return $contenido;
		}
		function procesarFormulario_recuperarContrasena($sesion){

			// Iniciación del XML de respuesta
			$xmlTexto=new SimpleXMLElement("<Contenido/>");
			$wiki=$xmlTexto->addChild("Wiki");
			// Recuperación parámetro acción
			$accion=$sesion->leerParametroFormularioActual("accion");
			if(strcasecmp($accion,"solicitar")==0){
				// Se solicitó un link de recuperacion
				$controlUsuario = new Control0Usuario($this->db);

				// Se identifica el usuario	
				$parametros["usuario"]= (string)$sesion->leerParametroDestinoActual("usuario");
				$usuario=$controlUsuario->identificarUsuario($parametros, $sesion->getDB());

				if($usuario!=null){
					// Instanciación de controles
					$controlMensajero = new ControlMensajero();
					$controlCasoUso = new Control0CasoUso($sesion->getDB());
					// Generar codigo temp
					$codigoTemp=ControlUtilidades::generarCadenaAleatorio();
					// Guardar el codigo temporal en la base de datos
					Control0Usuario::guardarCodigoRecuperacion(array("idUsuario"=>$usuario->getIdUsuario(),"codigo"=>$codigoTemp),$sesion->getDB());
					// Preparar correo
					$parametros=new SimpleXMLElement("<Parametros/>");
					$parametro=$parametros->addChild("Parametro");
					$parametro->addAttribute("nombre","correo");
					$parametro->addAttribute("valor",$usuario->getCorreo());
					$parametro=$parametros->addChild("Parametro");
					$parametro->addAttribute("nombre","asunto");
					$parametro->addAttribute("valor","Recuperación de contraseña de ".$sesion->configuracion->titulo);
					$mensaje="Hemos recibido su petición de recuperación de contraseña. Para recuperar su contraseña acceda el siguiente link en las próximas '''24 horas''':\n\n".
					"'''Nombre de usuario:''' ".$usuario->getUser()."\n".
					"'''Link para recuperar contraseña:'''\n".
					"http://".$_SERVER["HTTP_HOST"].resolverPath()."/".$controlCasoUso->getNombreCasoUso($sesion->leerParametro("idCasoUso"))."/pw/".$usuario->getUser()."/".$codigoTemp.
					"\n\n\nGracias,\n\n".
					"Equipo de ideSoluciones";
					$parametro=$parametros->addChild("Parametro");
					$parametro->addAttribute("nombre","mensaje");
					$parametro->addAttribute("valor",ControlUtilidades::renderizarHTML("<Wiki>".$mensaje."</Wiki>"));
					// Enviar correo
					$controlMensajero->enviarCorreo($parametros);
					$resultado=$controlMensajero->getNotificacion();
					// Notificar
					if($resultado["resultado"]){
						$wiki[]="= Hemos enviado un correo de asistencia para recuperar contraseña =
						Se ha enviado un email a la dirección de correo asociada a su cuenta. ".
						"Allí se encuentran instrucciones para restaurar su contraseña.\n\n".
						"Por favor sea paciente, la entrega del email puede retrasarse. Recuerde revisar su carpeta de spam o correo no deseado asi como sus filtros de correo.";
						// Se registra el envío de un link de recuperación
						// Los codigos de la tabla CodigosRecuperación SIEMPRE deben haber sido enviados
						ControlActividades::registrarEnBaseDatos(array("Usuario"=>$usuario->getUser()), $sesion->leerParametro("idUsuario"), "LinkRecuperacionContraseña");
					}else{
						new mensajes($resultado["mensaje"]);
					}
				}else{
					new mensajes("Lo sentimos, no tenemos registrado este nombre de usuario. Intente con otro nombre de usuario o ingrese una dirección de correo.");
					$contenido=new SimpleXMLElement('<Contenido/>');
					return $this->generarContenido_recuperarContrasena($sesion, $contenido);
				}
			}else{
				// Se solicita recuperar la contraseña
				$clave1=$sesion->leerParametroFormularioActual("clave1");
				$clave2=$sesion->leerParametroFormularioActual("clave2");
				// Se verifica que el par de claves ingresadas sean iguales
				if (strcmp($clave1, $clave2)==0){
					// Se crea el array de parámetros y se actualiza la contraseña
					$parametros["idUsuario"]=(string)$sesion->leerParametroFormularioActual("idUsuario");
					$parametros["codigo"]=(string)$sesion->leerParametroFormularioActual("codigo");
					$parametros["clave"]=$clave1;
					$actualizado=Control0Usuario::recuperarContrasena($parametros, $sesion->getDB());
					//Notifica el estado de la actualización
					if($actualizado){
						$wiki[]="= Exito en la actualización =\n".
						"Ahora puede autenticarse a su cuenta con su nueva contraseña.";
						Control0Usuario::borrarCodigoRecuperacion($parametros, $sesion->getDB());
						// Se registra la restauración de una contraseña
						ControlActividades::registrarEnBaseDatos(array("idUsuarioRecuperado"=>$parametros["idUsuario"]), $sesion->leerParametro("idUsuario"), "ContraseñaRestaurada");
					}else{
						// Si no se puede borrar o falla registrar en el log
						$wiki[]="= Error al actualizar =\n".
						'Ocurrió un error NO esperado en la actualización de los datos de su cuenta, por favor intentelo de nuevo o dirijase a la sección "contáctenos" para notificar el fallo.';
						// Se registra el fallo en el intento de restauración de contraseña
						ControlActividades::registrarEnArchivo(array("Link recuperacion"=>$link), $sesion->leerParametro("idUsuario"), "general.log", "ErrorRecuperandoContraseña");
					}
				}else{
					// Refrescar pagina con la misma url que esta en el navegador
					header("location: ".resolverPath()."/".implode("/",$sesion->args));
				}
			}

			return $xmlTexto;
		}
###############################################################
################## Contactenos #####################
###############################################################

		function nombreMenu_contactenos($sesion){
			return "Contactenos";
		}

		function generarContenido_contactenos($sesion){
			$contenido=new SimpleXMLElement("<Contenido/>");
			$wiki=$contenido->addChild("Wiki");
			$wiki[0]="= Contactenos =\nPor favor envienos sus comentarios";	
			$formulario=ControlFormulario::generarFormulario($contenido, $sesion->leerParametro("idCasoUso"));
				ControlFormulario::generarCampo($formulario, array(
											"nombre"=>"nombre",
											"tipo"=>"cadena",
											"titulo"=>"Nombre",
											));
				ControlFormulario::generarCampo($formulario, array(
											"nombre"=>"correo",
											"tipo"=>"correo",
											"titulo"=>"Correo",
											));
				ControlFormulario::generarCampo($formulario, array(
											"nombre"=>"comentario",
											"tipo"=>"texto",
											"titulo"=>"Comentario",
											));
				ControlFormulario::generarEnviar($formulario);

			return $contenido;
		}
		
		function procesarFormulario_contactenos($sesion){
			$contenido=new SimpleXMLElement("<Contenido/>");
			$wiki=$contenido->addChild("Wiki");
			$wiki[0]="= Contactenos =\nGracias por contactarnos";	
			return $contenido;
		}

###############################################################
################## Registro en línea #####################
###############################################################

		function nombreMenu_registroEnLinea($sesion){
			return "Registrate aquí";
		}

		function generarContenido_registroEnLinea($sesion){
			$contenido=new SimpleXMLElement("<Contenido/>");
			$parametros=array();
			$parametros["asunto"]= "[{$sesion->configuracion->titulo}][Registro] ".strftime("%Y-%m-%d %I:%M %P");
			$parametros["nombreCasoUso"]="nodo/principal";
			$mostrarMensaje=Control0Usuario::procesarFormularioNuevoUsuario($contenido,3,$parametros,"Activación completa, ahora puede ingresar el sistema.",true);
			return $contenido;
		}
		
		function procesarFormulario_registroEnLinea($sesion){
			return $this->generarContenido_registroEnLinea($sesion);
		}

	}
?>
