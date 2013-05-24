<?php
	
	class ControlNuevosUsuariosConRolN extends ControlXML{
		
		private $idRol;
		private $sesion;
		
		function ControlNuevosUsuariosConRolN($idRol){
			$this->sesion=Sesion::getInstancia();
			$this->idRol=$idRol;
		}
		
		function generarContenido($xml){
			$daoRol=new DAO0Rol($this->sesion->getDB());
			$voRol=$daoRol->getRegistro($this->idRol);
			$filtros=array(
							array(
								"tipo"=>"constante",
								"camposAfectados"=>array(
									array(
										"tablaForanea"=>"0UsuarioRol",
										"campoForaneo"=>"idUsuario",
										"campoLocal"=>"idUsuario",
										"nombre"=>"idRol",
										"condicion"=>"igual",
										"valor"=>$this->idRol
										)
								),
							),
						);
			
			$ControlListas=new ControlListas($this->sesion,"0Usuario","admUs",array("user","correo",array("campo"=>"idUsuario","clase"=>"ControlNuevosUsuariosConRolN","funcion"=>"rolDeUsuario"),array("campo"=>"xmlPropiedades","clase"=>"PaqueteAdminUsuariosYRoles","funcion"=>"procesarCampoXMLPropiedades")),$filtros,array("nuevo","editar","borrar","filtro","desactivar"),array(),array(),array("idUsuario"=>"Roles","user"=>"Usuario","correo"=>"Correo","xmlPropiedades"=>"Más información"));
			$ControlListas->procesarFormularioSinContenido();
			$id=$ControlListas->propiedades->getPropiedad("ultimoId");
			if(intval($id)>0){
				if(!Control0Usuario::asignarRolAUsuario(intval($id),$this->idRol)){
					mensaje::add("Error asignando rol al nuevo usuario.",ERROR);
				}
			}
            $ControlListas->generarContenidoEn($xml,"Administrar {$voRol->getNombreRol()}");
		}
		
		function rolDeUsuario($xml,$ids,$valor){
			try{
				ControlXML::agregarNodoTexto($xml,"Wiki","'''Roles'''\n");
				$daoUsuarioRol=new DAO0UsuarioRol($this->sesion->getDB());
				$voUsuarioRol=new VO0UsuarioRol();
				$voUsuarioRol->setIdUsuario($valor);
				$roles=$daoUsuarioRol->getRegistros($voUsuarioRol);
				$daoRol=new DAO0Rol($this->sesion->getDB());
				foreach($roles as $rol){
					$voRol=$daoRol->getRegistro($rol->getIdRol());
					ControlXML::agregarNodoTexto($xml,"Wiki","*{$voRol->getNombreRol()}\n");
				}
			}catch(Exception $e){
				ControlXML::agregarNodoTexto($xml,"Wiki","*Sin rol.\n");
			}
		}

	}
?>
