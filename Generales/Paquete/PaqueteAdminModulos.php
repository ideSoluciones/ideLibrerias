<?php
	class PaqueteAdminModulos extends Paquete{
		function PaqueteAdminModulos($db){
			$this->Paquete($db);
			$this->listaClasesUsuarioCasoUso=array(
							"1" => getXml0Usuario(), 
							"2" => getXml0CasoUso(), 
							"R" => getXml0UsuarioCasoUso()
						);

			$this->xmlEspecificacionesUsuarioCasoUso=new SimpleXMLElement("<XMLRelacionesMN/>");
			$relacion=$this->xmlEspecificacionesUsuarioCasoUso->addChild("Relacion");
			$relacion->addAttribute("id", "1");
			$relacion->addAttribute("titulo", "Usuario");
			$relacion->addAttribute("campo", "user");
			$relacion=$this->xmlEspecificacionesUsuarioCasoUso->addChild("Relacion");
			$relacion->addAttribute("id", "2");
			$relacion->addAttribute("titulo", "CasoUso");
			$relacion->addAttribute("campo", "nombreCasoUso");

		
			$this->listaClasesRolCasoUso=array(
							"1" => getXml0Rol(), 
							"2" => getXml0CasoUso(), 
							"R" => getXml0RolCasoUso()
						);
			
			$this->xmlEspecificacionesRolCasoUso=new SimpleXMLElement("<XMLRelacionesMN/>");
			$relacion=$this->xmlEspecificacionesRolCasoUso->addChild("Relacion");
			$relacion->addAttribute("id", "1");
			$relacion->addAttribute("titulo", "Rol");
			$relacion->addAttribute("campo", "nombreRol");
			$relacion=$this->xmlEspecificacionesRolCasoUso->addChild("Relacion");
			$relacion->addAttribute("id", "2");
			$relacion->addAttribute("titulo", "CasoUso");
			$relacion->addAttribute("campo", "nombreCasoUso");

			
			
		}
		//Paquetes
		function nombreMenu_adminPaquetes($sesion){
			return "Administrar/Paquetes";
		}
		function generarContenido_adminPaquetes($sesion){
			$w=$this->generarImec($sesion, getXml0Paquete(), array("editar", "borrar", "consultar", "nuevo"));
			return $w->generarContenido();
		}
		function procesarFormulario_adminPaquetes($sesion){
			$w=$this->generarImec($sesion, getXml0Paquete(), array("editar", "borrar", "consultar", "nuevo"));
			return $w->procesarFormulario();
		}

		//Caso Uso
		function nombreMenu_adminCasoUso($sesion){
			return "Administrar/Casos Uso";
		}
		function generarContenido_adminCasoUso($sesion){
			$w=$this->generarImec($sesion, getXml0CasoUso(), array("editar", "borrar", "consultar", "nuevo"));
			//echo generalXML::geshiXML(getXml0CasoUso());
			return $w->generarContenido();
		}
		function procesarFormulario_adminCasoUso($sesion){
			$w=$this->generarImec($sesion, getXml0CasoUso(), array("editar", "borrar", "consultar", "nuevo"));
			return $w->procesarFormulario();
		}

		//Relacion Usuario CasoUso
		function nombreMenu_relacionUsuarioCasoUso($sesion){
			return "Relación/Permisos de los Usuarios";
		}
		function generarContenido_relacionUsuarioCasoUso($sesion){
			$contenido=xml::add(null, "Contenido");
			xml::add($contenido, "Wiki", "==Relación usuarios y roles==");
			$controlRelacionesMN = new ControlRelacionesMN(
										array(
											"entidad2"=>"0Usuario", 
											"campo2"=>"idUsuario", 
											"campoTexto2"=>"user", 
											"titulo2"=>"Usuario", 
											"campoUnion2"=>"idUsuario", 
											
											"entidad1"=>"0CasoUso",
											"campo1"=>"idCasoUso",
											"campoTexto1"=>"nombreCasoUso",
											"titulo1"=>"Nombre Caso Uso",
											"campoUnion1"=>"idCasoUso",
											
											"entidadUnion"=>"0UsuarioCasoUso", 
									));
			$controlRelacionesMN->procesarDatos();						
			$controlRelacionesMN->generarInterfaz($contenido);
			return $contenido;
		}
		function procesarFormulario_relacionUsuarioCasoUso($sesion){
			return $this->generarContenido_relacionUsuarioCasoUso($sesion);
		}


		//Relacion Rol CasoUso
		function nombreMenu_relacionRolCasoUso($sesion){
			return "Relación/Permisos de los Roles";
		}
		function generarContenido_relacionRolCasoUso($sesion){
			$contenido=xml::add(null, "Contenido");
			xml::add($contenido, "Wiki", "==Relación roles y casos de uso==");
			$controlRelacionesMN = new ControlRelacionesMN(
										array(
											"entidad1"=>"0Rol", 
											"campo1"=>"idRol", 
											"campoTexto1"=>"nombreRol", 
											"titulo1"=>"Nombre Rol", 
											"campoUnion1"=>"idRol", 
											
											"entidad2"=>"0CasoUso",
											"campo2"=>"idCasoUso",
											"campoTexto2"=>"nombreCasoUso",
											"titulo2"=>"Nombre Caso Uso",
											"campoUnion2"=>"idCasoUso",
											
											"entidadUnion"=>"0RolCasoUso", 
									));
			$controlRelacionesMN->procesarDatos();						
			$controlRelacionesMN->generarInterfaz($contenido);
			return $contenido;
		}
		function procesarFormulario_relacionRolCasoUso($sesion){
			return $this->generarContenido_relacionRolCasoUso($sesion);
		}
		function procesarFormularioInvalido_relacionRolCasoUso($sesion){
		}
		function validarFormulario_relacionRolCasoUso($sesion){
			$r=new RespuestaValidacion();
			$r->setResultado(true);
			return $r;
		}
	}
	
?>
