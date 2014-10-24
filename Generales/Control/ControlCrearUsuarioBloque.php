<?php

	class ControlCrearUsuarioBloque extends generalXML{
		function ControlCrearUsuarioBloque(){
		}
		function obtenerInterfazPrincipal(){
			$sesion = Sesion::getInstancia();
			$contenido = xml::add(null, "Contenido");
			
			

			$daoUsuario = new DAO0Usuario();

			
			$archivo = $sesion->leerParametroDestinoActual("archivo");
			if (strlen($archivo)>0){
				$datos=file_get_contents($archivo);
				if (strlen($datos)>0){
					$listaCodigos = explode("\n", $datos);
					foreach($listaCodigos as $codigo){
						if (strlen($codigo)>0){
							$datosFila = explode(",", $codigo);
							if (count($datosFila)==2){
								if (strlen($datosFila[0])>0 && strlen($datosFila[1])>0){
									$user = $datosFila[0];
									$pass = md5($datosFila[1]);
									try{
								
										$usuario = $daoUsuario->getRegistroCondiciones(array("user"=>$user));

										msg::add("El usuario ".$user." ya existe.", ERROR);
								
									}catch(sinResultados $e){
										$voUsuario = $daoUsuario->crearVO();
										$voUsuario->setUser($user);
										$voUsuario->setPass($pass);
										$voUsuario->setCorreo($user."@temporal.ticket");
										$voUsuario->setXmlPropiedades("<p/>");
										$voUsuario->setActivo(1);
										$daoUsuario->agregarRegistro($voUsuario);
										msg::add("Usuario creado: ".$user);
									}
								}
							}
						}
					}
				}
			}
			
			$form = cf::addForm($contenido, array("enctype"=>"multipart/form-data"));
			
			xml::add($form,"Wiki", "===Carga de usuarios en bloque===");
			xml::add($form,"Wiki", "====Formato====\nArchivo csv sin titulo y las siguientes columnas: Usuario,Clave. Separado por comas SIN comillas.");
			cf::add($form,array(
				"tipo"=>"oculto",
				"nombre"=>"operacion",
				"valorPorDefecto"=>"cargarArchivo"
				)
			);
			cf::add($form,array(
				"tipo"=>"archivo",
				"nombre"=>"archivo",
				"titulo"=>"Archivo lista de usuarios",
				)
			);
			cf::add($form,array(
				"tipo"=>"enviar",
				"nombre"=>"enviar",
				"titulo"=>"Cargar",
				)
			);
			
			
			return $contenido;
		}
	}
