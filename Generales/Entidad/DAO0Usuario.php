<?php
	class DAO0Usuario{
		private $db=null;
		function DAO0Usuario(){
			$sesion=Sesion::getInstancia();
			$this->db=$sesion->getDB();
		}
		function setDb($db){ $this->db=$db; }
		function crearVO() { 
			return new VO0Usuario();
		}
		public static function getTabla(){
			return '0Usuario';
		}
		function getRegistro($idUsuario) {
			$objVO=$this->crearVO();
			$objVO->setIdUsuario($idUsuario);
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"0Usuario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"user","tablaOrigen"=>"0Usuario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"pass","tablaOrigen"=>"0Usuario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"correo","tablaOrigen"=>"0Usuario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlPropiedades","tablaOrigen"=>"0Usuario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"activo","tablaOrigen"=>"0Usuario"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idUsuario","tabla"=>"0Usuario","valor"=>$idUsuario));
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				$objVO->setIdUsuario($resultado[0]["idUsuario"]);
				$objVO->setUser($resultado[0]["user"]);
				$objVO->setPass($resultado[0]["pass"]);
				$objVO->setCorreo($resultado[0]["correo"]);
				$objVO->setXmlPropiedades($resultado[0]["xmlPropiedades"]);
				$objVO->setActivo($resultado[0]["activo"]);
			}else{
				throw new sinResultados("No se encontro el registro solicitado.");
			}
			return $objVO;
		}
		function getRegistroCondiciones($condiciones){
			$objVO=$this->crearVO();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"*","tablaOrigen"=>"0Usuario"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			foreach($condiciones as $nombre=>$valor){
				ControlXML::agregarNodo($y,"Igual",array("campo"=>$nombre,"tabla"=>"0Usuario","valor"=>$valor));
			}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				$objVO->set($resultado[0]);
			}else{
				throw new sinResultados("No se encontro el registro solicitado.");
			}
			return $objVO;
		}
		function getRegistrosCondiciones($condiciones){
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"*","tablaOrigen"=>"0Usuario"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			foreach($condiciones as $nombre=>$valor){
				ControlXML::agregarNodo($y,"Igual",array("campo"=>$nombre,"tabla"=>"0Usuario","valor"=>$valor));
			}
			$resultados=$this->db->consultar($consulta);
			$objVOs=array();
			foreach($resultados as $resultado){
				$objVO=$this->crearVO();
				$objVO->set($resultado);
				$objVOs[]=$objVO;
			}
			return $objVOs;
		}
		function getRegistros($condiciones=null){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"0Usuario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"user","tablaOrigen"=>"0Usuario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"pass","tablaOrigen"=>"0Usuario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"correo","tablaOrigen"=>"0Usuario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlPropiedades","tablaOrigen"=>"0Usuario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"activo","tablaOrigen"=>"0Usuario"));
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO0Usuario")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdUsuario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idUsuario","tabla"=>"0Usuario","valor"=>$condiciones->getIdUsuario()));
							}
							if(strcmp($condiciones->getUser(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"user","tabla"=>"0Usuario","valor"=>$condiciones->getUser()));
							}
							if(strcmp($condiciones->getPass(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"pass","tabla"=>"0Usuario","valor"=>$condiciones->getPass()));
							}
							if(strcmp($condiciones->getCorreo(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"correo","tabla"=>"0Usuario","valor"=>$condiciones->getCorreo()));
							}
							if(strcmp($condiciones->getXmlPropiedades(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"xmlPropiedades","tabla"=>"0Usuario","valor"=>$condiciones->getXmlPropiedades()));
							}
							if(strcmp($condiciones->getActivo(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"activo","tabla"=>"0Usuario","valor"=>$condiciones->getActivo()));
							}
						}
					}
				}
			}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdUsuario($reg["idUsuario"]);
					$objVO->setUser($reg["user"]);
					$objVO->setPass($reg["pass"]);
					$objVO->setCorreo($reg["correo"]);
					$objVO->setXmlPropiedades($reg["xmlPropiedades"]);
					$objVO->setActivo($reg["activo"]);
					$retorno[]=$objVO;
				}
			}else{
				throw new sinResultados("No hay registros.");
			}
			return $retorno;
		}
		function getTotalRegistros($condiciones=null){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"0Usuario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"user","tablaOrigen"=>"0Usuario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"pass","tablaOrigen"=>"0Usuario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"correo","tablaOrigen"=>"0Usuario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlPropiedades","tablaOrigen"=>"0Usuario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"activo","tablaOrigen"=>"0Usuario"));
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO0Usuario")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdUsuario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idUsuario","tabla"=>"0Usuario","valor"=>$condiciones->getIdUsuario()));
							}
							if(strcmp($condiciones->getUser(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"user","tabla"=>"0Usuario","valor"=>$condiciones->getUser()));
							}
							if(strcmp($condiciones->getPass(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"pass","tabla"=>"0Usuario","valor"=>$condiciones->getPass()));
							}
							if(strcmp($condiciones->getCorreo(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"correo","tabla"=>"0Usuario","valor"=>$condiciones->getCorreo()));
							}
							if(strcmp($condiciones->getXmlPropiedades(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"xmlPropiedades","tabla"=>"0Usuario","valor"=>$condiciones->getXmlPropiedades()));
							}
							if(strcmp($condiciones->getActivo(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"activo","tabla"=>"0Usuario","valor"=>$condiciones->getActivo()));
							}
						}
					}
				}
			}
			$resultado=$this->db->numeroRegistros($consulta);
			if($resultado<0){
				throw new sinResultados("No hay registros.");
			}
			return $resultado;
		}
		function consultarRegistros($vo0Usuario){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"0Usuario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"user","tablaOrigen"=>"0Usuario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"pass","tablaOrigen"=>"0Usuario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"correo","tablaOrigen"=>"0Usuario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlPropiedades","tablaOrigen"=>"0Usuario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"activo","tablaOrigen"=>"0Usuario"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			if(!is_null($vo0Usuario->getIdUsuario())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"idUsuario", "tabla"=>"0Usuario", "valor"=>$vo0Usuario->getIdUsuario()));}
			if(!is_null($vo0Usuario->getUser())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"user", "tabla"=>"0Usuario", "valor"=>$vo0Usuario->getUser()));}
			if(!is_null($vo0Usuario->getPass())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"pass", "tabla"=>"0Usuario", "valor"=>$vo0Usuario->getPass()));}
			if(!is_null($vo0Usuario->getCorreo())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"correo", "tabla"=>"0Usuario", "valor"=>$vo0Usuario->getCorreo()));}
			if(!is_null($vo0Usuario->getXmlPropiedades())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"xmlPropiedades", "tabla"=>"0Usuario", "valor"=>$vo0Usuario->getXmlPropiedades()));}
			if(!is_null($vo0Usuario->getActivo())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"activo", "tabla"=>"0Usuario", "valor"=>$vo0Usuario->getActivo()));}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdUsuario($reg["idUsuario"]);
					$objVO->setUser($reg["user"]);
					$objVO->setPass($reg["pass"]);
					$objVO->setCorreo($reg["correo"]);
					$objVO->setXmlPropiedades($reg["xmlPropiedades"]);
					$objVO->setActivo($reg["activo"]);
					$retorno[]=$objVO;
				}
			}else{
				throw new sinResultados("No hay registros.");
			}
			return $retorno;
		}
		function getNMRegistros($condiciones=null,$n,$m){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"0Usuario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"user","tablaOrigen"=>"0Usuario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"pass","tablaOrigen"=>"0Usuario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"correo","tablaOrigen"=>"0Usuario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlPropiedades","tablaOrigen"=>"0Usuario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"activo","tablaOrigen"=>"0Usuario"));
			$parametro = $consulta->addChild("Limitar");
			$parametro->addAttribute("regInicial", $n);
			$parametro->addAttribute("noRegistros", $m);
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO0Usuario")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdUsuario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idUsuario","tabla"=>"0Usuario","valor"=>$condiciones->getIdUsuario()));
							}
							if(strcmp($condiciones->getUser(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"user","tabla"=>"0Usuario","valor"=>$condiciones->getUser()));
							}
							if(strcmp($condiciones->getPass(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"pass","tabla"=>"0Usuario","valor"=>$condiciones->getPass()));
							}
							if(strcmp($condiciones->getCorreo(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"correo","tabla"=>"0Usuario","valor"=>$condiciones->getCorreo()));
							}
							if(strcmp($condiciones->getXmlPropiedades(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"xmlPropiedades","tabla"=>"0Usuario","valor"=>$condiciones->getXmlPropiedades()));
							}
							if(strcmp($condiciones->getActivo(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"activo","tabla"=>"0Usuario","valor"=>$condiciones->getActivo()));
							}
						}
					}
				}
			}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdUsuario($reg["idUsuario"]);
					$objVO->setUser($reg["user"]);
					$objVO->setPass($reg["pass"]);
					$objVO->setCorreo($reg["correo"]);
					$objVO->setXmlPropiedades($reg["xmlPropiedades"]);
					$objVO->setActivo($reg["activo"]);
					$retorno[]=$objVO;
				}
			}else{
				throw new sinResultados("No hay registros.");
			}
			return $retorno;
		}
		function agregarRegistro($registro){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"0Usuario","valor"=>$registro->getIdUsuario()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"user","tablaOrigen"=>"0Usuario","valor"=>$registro->getUser()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"pass","tablaOrigen"=>"0Usuario","valor"=>$registro->getPass()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"correo","tablaOrigen"=>"0Usuario","valor"=>$registro->getCorreo()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlPropiedades","tablaOrigen"=>"0Usuario","valor"=>$registro->getXmlPropiedades()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"activo","tablaOrigen"=>"0Usuario","valor"=>$registro->getActivo()));
			if($this->db->insertar($consulta)){
				$registro->setIdUsuario($this->db->ultimoId);
				return true;
			}else{
				return false;
			}
		}
		function actualizarRegistro($registro,$condiciones=null){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"0Usuario","valor"=>$registro->getIdUsuario()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"user","tablaOrigen"=>"0Usuario","valor"=>$registro->getUser()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"pass","tablaOrigen"=>"0Usuario","valor"=>$registro->getPass()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"correo","tablaOrigen"=>"0Usuario","valor"=>$registro->getCorreo()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlPropiedades","tablaOrigen"=>"0Usuario","valor"=>$registro->getXmlPropiedades()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"activo","tablaOrigen"=>"0Usuario","valor"=>$registro->getActivo()));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			if(is_array($condiciones) && count($condiciones)>0){
				foreach($condiciones as $campo=>$valor){
					ControlXML::agregarNodo($y,"Igual",array("campo"=>"$campo","tabla"=>"0Usuario","valor"=>$valor));
				}
			}else{
				ControlXML::agregarNodo($y,"Igual",array("campo"=>"idUsuario","tabla"=>"0Usuario","valor"=>$registro->getIdUsuario()));
			}
			if($this->db->actualizar($consulta)){
				return true;
			}else{
				return false;
			}
		}
		function eliminarRegistro($registro){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("tablaOrigen"=>"0Usuario"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idUsuario","tabla"=>"0Usuario","valor"=>$registro->getIdUsuario()));
			if($this->db->eliminar($consulta)){
				return true;
			}else{
				return false;
			}
		}
	}
?>