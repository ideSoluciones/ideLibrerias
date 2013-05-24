<?php
	class PaqueteNoticias extends Paquete{
		function PaqueteNoticias($db){
			$this->Paquete($db);
		}


		//adminNodo
		function nombreMenu_adminNodo($sesion){
			return "Administrar/Nodos";
		}
		function generarContenido_adminNodo($sesion){
			$w=$this->generarImec($sesion, getXml1Nodo(), array("editar", "borrar", "consultar", "nuevo"));
			return $w->generarContenido();
		}
		function procesarFormulario_adminNodo($sesion){
			$w=$this->generarImec($sesion, getXml1Nodo(), array("editar", "borrar", "consultar", "nuevo"));
			return $w->procesarFormulario();
		}

		/**
		* nodo
		*/
		function nombreMenu_nodo($sesion){
			//return "nodo";
		}
		function generarContenido_nodo($sesion){
			$daoNodo = new DAO1Nodo($this->db);
			if(class_exists("ConfiguracionLocal")){
				$configuracionLocal= new ConfiguracionLocal();
			}else{
				$configuracionLocal= new ConfiguracionGeneral();
			}
			
			
					
			$error=true;
			
			//echo "Estoy imprimiendo la info de un nodo: [".$sesion->leerParametro("destinoAux")." o ".$sesion->configuracion->nodoDefecto."]<br>";
			
			if (strlen($sesion->leerParametro("destinoAux"))<=0){
				$nombreNodo=$sesion->configuracion->destinoAuxDefecto;
				
			}else{
				$nombreNodo=$sesion->leerParametro("destinoAux");
				
			}
			
			//echo "El nombre del nodo es: [".$nombreNodo."]<br>";
			
			try{
			
			
				$consulta=new SimpleXMLElement("<Consulta />");
						
				
				
				$condiciones=$consulta->addChild("Condiciones");
				$igual=$condiciones->addChild("Igual");
				$igual->addAttribute("tabla","1Nodo");
				$igual->addAttribute("campo","path");
				$igual->addAttribute("valor", $nombreNodo);
			
			
				$condiciones=$consulta->addChild("Condiciones");
				
			
			
				$voNodos=$daoNodo->getRegistros($consulta);
				$voNodo=$voNodos[0];
				$titulo=$voNodo->getTitulo();
				$contenido=$voNodo->getContenidoCompleto();
			}catch(sinResultados $exception){
				$titulo="Contenido por defecto";
				$contenido="Texto";
			}
			
			//echo "El contenido es: [".$contenido."]<br>";
			$textoTitulo="";
			if (strlen($titulo)>0){
				$textoTitulo="
					<Texto>
						<Campo nombre='titulo' nivel='1' valor='".$titulo."'/>
						
					</Texto>";
			}
			$xmlFormulario=new SimpleXMLElement("
				<Contenido>
					".$textoTitulo."
					<Contenedor>".$contenido."</Contenedor>
				</Contenido>");
			//mensaje::add(generalXML::geshiXML($xmlFormulario));
			return $xmlFormulario;

		}
		function procesarFormulario_nodo($sesion){
			//echo revisarArreglo($sesion, "Sesion a procesar");
			$controlUsuario=new Control0Usuario($sesion->getDB());
			$usuario=$controlUsuario->autenticarUsuario($sesion->leerParametroFormularioActual("Usuario"), md5($sesion->leerParametroFormularioActual("Pass")));
/*			var_dump($sesion);
			echo "datos recividos: [".$sesion->leerParametroFormularioActual("Usuario")."] - [".
					md5($sesion->leerParametroFormularioActual("Pass"))."][".$sesion->leerParametroFormularioActual("campoSuperEscondido")."]<br>";*/
			//new mensajes (revisarArreglo($usuario, "usuario autenticado"));
			if ($usuario["idUsuario"]!=""){
				$sesion->borrarParametro("idUsuario");
				$sesion->escribirParametro("idUsuario", $usuario["idUsuario"]);
				$xmlTexto=new SimpleXMLElement("
					<Contenido>
						<Texto>
							<Campo nombre='titulo'    nivel='1' valor='Ingresando al sistema'/>
							<Campo nombre='contenido' valor='Exito ingresando al sistema ud es ".$usuario["idUsuario"]."' />
						</Texto>
						<htmlencodeado>".base64_encode("<meta http-equiv='Refresh' CONTENT='0; URL='".resolverPath()."' /><a href='".resolverPath()."'>Continuar aquí.</a>")."</htmlencodeado>
					</Contenido>");
				//new mensajes("<a href='?q=".$sesion->idSesion."'>Continuar aquí</a>");
			}else{
//				<Campo nombre='contenido' valor='".md5($sesion->leerParametroFormularioActual("Pass"))."' />

				$xmlTexto=new SimpleXMLElement("
					<Contenido>
						<Texto>
							<Campo nombre='titulo'    nivel='1' valor='generarContenido_nodo'/>
							<Campo nombre='contenido' valor='Usuario o contraseña invalido' />
						</Texto>
					</Contenido>");
			}
			return $xmlTexto;
		}
	}
?>
