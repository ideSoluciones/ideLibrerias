<?php
	class PaqueteAdminUsuariosYRoles extends Paquete{
		var $xmlEspecificaciones;
		var $listaClases;
		function PaqueteAdminUsuariosYRoles($db){
			$this->Paquete($db);
			$this->xmlEspecificaciones=new SimpleXMLElement("
				<XMLRelacionesMN>
					<Relacion id='1' titulo='Usuario' campo='user'/>
					<Relacion id='2' titulo='Rol' campo='nombreRol'/>
				</XMLRelacionesMN>");
			$this->listaClases=array(
							"1" => getXml0Usuario(), 
							"2" => getXml0Rol(), 
							"R" => getXml0UsuarioRol()
						);
		}


//		adminUsuarios
		function nombreMenu_adminUsuarios($sesion){
			return "Administrar/Usuarios";
		}
		function generarContenido_adminUsuarios($sesion){
			$contenido=new SimpleXMLElement("<Contenido />");
			$ControlListas=new ControlListas($sesion,"0Usuario","adm0Usuario",array("user","correo",array("campo"=>"xmlPropiedades","clase"=>"PaqueteAdminUsuariosYRoles","funcion"=>"procesarCampoXMLPropiedades")),
						array(
							array(
								"tipo"=>"campoBuscar",
								"nombre"=>"miFiltroPorNombre",
								"titulo"=>"Buscar por:",
								"camposAfectados"=>array(
										array(
												"nombre"=>"user",
												"condicion"=>"como"
										),
										array(
											"nombre"=>"xmlPropiedades",
											"condicion"=>"como"
										)
								)
							)

						),
						
						array("nuevo","editar","borrar","filtro","desactivar"));
			$ControlListas->procesarFormularioSinContenido();
			$ControlListas->generarContenidoEn($contenido,"Administrar usuarios");
			return $contenido;
		}
		function procesarFormulario_adminUsuarios($sesion){
			return $this->generarContenido_adminUsuarios($sesion);
		}
		
		function procesarCampoXMLPropiedades($xmlContenido, $llave, $campo){
			ControlXML::agregarNodoTexto($xmlContenido,"Wiki","");	
			$xml=new SimpleXMLElement($campo);
			$nodos=$xml->xpath("//Propiedad");
			foreach($nodos as $nodo){
				//TODO: BORRAR {$nodo["valor"]}, ES SOLO POR COMPATIBILIDAD
				ControlXML::agregarNodoTexto($xmlContenido,"Wiki","'''{$nodo["nombre"]}:'''{$nodo["valor"]}{$nodo}");	
			}
		}
		
//		adminRoles
		function nombreMenu_adminRoles($sesion){
			return "Administrar/Roles";
		}
		function generarContenido_adminRoles($sesion){
			$w=$this->generarImec($sesion, getXml0Rol(), array("editar", "borrar", "consultar", "nuevo"));
			return $w->generarContenido();
		}
		function procesarFormulario_adminRoles($sesion){
			$w=$this->generarImec($sesion, getXml0Rol(), array("editar", "borrar", "consultar", "nuevo"));
			return $w->procesarFormulario();
		}

//		relacionUsuariosRoles
		function nombreMenu_relacionUsuariosRoles($sesion){
			return "Relación/Roles de los Usuarios";
		}
		function generarContenido_relacionUsuariosRoles($sesion){
			$contenido=xml::add(null, "Contenido");
			xml::add($contenido, "Wiki", "==Relación usuarios y roles==");
			$controlRelacionesMN = new ControlRelacionesMN(
										array(
											"entidad1"=>"0Usuario", 
											"campo1"=>"idUsuario", 
											"campoTexto1"=>"user", 
											"titulo1"=>"Usuario", 
											"campoUnion1"=>"idUsuario", 
											
											"entidad2"=>"0Rol",
											"campo2"=>"idRol",
											"campoTexto2"=>"nombreRol",
											"titulo2"=>"Nombre Rol",
											"campoUnion2"=>"idRol",
											
											"entidadUnion"=>"0UsuarioRol", 
									));
			$controlRelacionesMN->procesarDatos();						
			$controlRelacionesMN->generarInterfaz($contenido);
			return $contenido;
		/*
			$r=new RelacionesMN($sesion, $this->listaClases, $this->xmlEspecificaciones);
			$contenidoRelaciones=$r->generarContenido();
			$contenido=new SimpleXMLElement("
				<Contenido>
						<Texto>
							<Campo nombre='titulo' nivel='1' valor='Administración Relaciones Usuario Rol'/>
							<Campo nombre='contenido' valor='Seleccione los roles del usuario' />
						</Texto>
				</Contenido>");
			$this->simplexml_merge($contenido, $contenidoRelaciones);
			return $contenido;
			*/
		}
		function procesarFormulario_relacionUsuariosRoles($sesion){
			return $this->generarContenido_relacionUsuariosRoles($sesion);
			/*
			$r=new RelacionesMN($sesion, $this->listaClases, $this->xmlEspecificaciones);
			$contenidoRelaciones=$r->procesarFormulario();
			$contenido=new SimpleXMLElement("
				<Contenido>
						<Texto>
							<Campo nombre='titulo' nivel='1' valor='Administración Relaciones Usuario Rol'/>
							<Campo nombre='contenido' valor='Cambios procesados' />
							<Campo nombre='contenido' valor='Seleccione los roles del usuario' />
						</Texto>
				</Contenido>");
			$this->simplexml_merge($contenido, $contenidoRelaciones);
			return $contenido;
			*/
		}
	}
	
	
?>
