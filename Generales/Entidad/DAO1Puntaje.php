<?php
	class DAO1Puntaje{
		private $db=null;
		function DAO1Puntaje($db){
			$this->db=$db;
		}
		function setDb($db){ $this->db=$db; }
		function crearVO() { 
			return new VO1Puntaje();
		}
		public static function getTabla(){
			return '1Puntaje';
		}
		function getRegistro($idPuntaje) {
			$objVO=$this->crearVO();
			$objVO->setIdPuntaje($idPuntaje);
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idPuntaje","tablaOrigen"=>"1Puntaje"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"1Puntaje"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"tiempo","tablaOrigen"=>"1Puntaje"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"puntaje","tablaOrigen"=>"1Puntaje"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nivel","tablaOrigen"=>"1Puntaje"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombreJuego","tablaOrigen"=>"1Puntaje"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlPropiedades","tablaOrigen"=>"1Puntaje"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idPuntaje","tabla"=>"1Puntaje","valor"=>$idPuntaje));
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				$objVO->setIdPuntaje($resultado[0]["idPuntaje"]);
				$objVO->setIdUsuario($resultado[0]["idUsuario"]);
				$objVO->setTiempo($resultado[0]["tiempo"]);
				$objVO->setPuntaje($resultado[0]["puntaje"]);
				$objVO->setNivel($resultado[0]["nivel"]);
				$objVO->setNombreJuego($resultado[0]["nombreJuego"]);
				$objVO->setXmlPropiedades($resultado[0]["xmlPropiedades"]);
			}else{
				throw new sinResultados("No se encontro el registro solicitado.");
			}
			return $objVO;
		}
		function getRegistroCondiciones($condiciones){
			$objVO=$this->crearVO();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"*","tablaOrigen"=>"1Puntaje"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			foreach($condiciones as $nombre=>$valor){
				ControlXML::agregarNodo($y,"Igual",array("campo"=>$nombre,"tabla"=>"1Puntaje","valor"=>$valor));
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"*","tablaOrigen"=>"1Puntaje"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			foreach($condiciones as $nombre=>$valor){
				ControlXML::agregarNodo($y,"Igual",array("campo"=>$nombre,"tabla"=>"1Puntaje","valor"=>$valor));
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idPuntaje","tablaOrigen"=>"1Puntaje"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"1Puntaje"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"tiempo","tablaOrigen"=>"1Puntaje"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"puntaje","tablaOrigen"=>"1Puntaje"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nivel","tablaOrigen"=>"1Puntaje"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombreJuego","tablaOrigen"=>"1Puntaje"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlPropiedades","tablaOrigen"=>"1Puntaje"));
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO1Puntaje")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdPuntaje(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idPuntaje","tabla"=>"1Puntaje","valor"=>$condiciones->getIdPuntaje()));
							}
							if(strcmp($condiciones->getIdUsuario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idUsuario","tabla"=>"1Puntaje","valor"=>$condiciones->getIdUsuario()));
							}
							if(strcmp($condiciones->getTiempo(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"tiempo","tabla"=>"1Puntaje","valor"=>$condiciones->getTiempo()));
							}
							if(strcmp($condiciones->getPuntaje(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"puntaje","tabla"=>"1Puntaje","valor"=>$condiciones->getPuntaje()));
							}
							if(strcmp($condiciones->getNivel(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"nivel","tabla"=>"1Puntaje","valor"=>$condiciones->getNivel()));
							}
							if(strcmp($condiciones->getNombreJuego(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"nombreJuego","tabla"=>"1Puntaje","valor"=>$condiciones->getNombreJuego()));
							}
							if(strcmp($condiciones->getXmlPropiedades(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"xmlPropiedades","tabla"=>"1Puntaje","valor"=>$condiciones->getXmlPropiedades()));
							}
						}
					}
				}
			}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdPuntaje($reg["idPuntaje"]);
					$objVO->setIdUsuario($reg["idUsuario"]);
					$objVO->setTiempo($reg["tiempo"]);
					$objVO->setPuntaje($reg["puntaje"]);
					$objVO->setNivel($reg["nivel"]);
					$objVO->setNombreJuego($reg["nombreJuego"]);
					$objVO->setXmlPropiedades($reg["xmlPropiedades"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idPuntaje","tablaOrigen"=>"1Puntaje"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"1Puntaje"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"tiempo","tablaOrigen"=>"1Puntaje"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"puntaje","tablaOrigen"=>"1Puntaje"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nivel","tablaOrigen"=>"1Puntaje"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombreJuego","tablaOrigen"=>"1Puntaje"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlPropiedades","tablaOrigen"=>"1Puntaje"));
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO1Puntaje")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdPuntaje(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idPuntaje","tabla"=>"1Puntaje","valor"=>$condiciones->getIdPuntaje()));
							}
							if(strcmp($condiciones->getIdUsuario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idUsuario","tabla"=>"1Puntaje","valor"=>$condiciones->getIdUsuario()));
							}
							if(strcmp($condiciones->getTiempo(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"tiempo","tabla"=>"1Puntaje","valor"=>$condiciones->getTiempo()));
							}
							if(strcmp($condiciones->getPuntaje(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"puntaje","tabla"=>"1Puntaje","valor"=>$condiciones->getPuntaje()));
							}
							if(strcmp($condiciones->getNivel(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"nivel","tabla"=>"1Puntaje","valor"=>$condiciones->getNivel()));
							}
							if(strcmp($condiciones->getNombreJuego(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"nombreJuego","tabla"=>"1Puntaje","valor"=>$condiciones->getNombreJuego()));
							}
							if(strcmp($condiciones->getXmlPropiedades(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"xmlPropiedades","tabla"=>"1Puntaje","valor"=>$condiciones->getXmlPropiedades()));
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
		function consultarRegistros($vo1Puntaje){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idPuntaje","tablaOrigen"=>"1Puntaje"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"1Puntaje"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"tiempo","tablaOrigen"=>"1Puntaje"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"puntaje","tablaOrigen"=>"1Puntaje"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nivel","tablaOrigen"=>"1Puntaje"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombreJuego","tablaOrigen"=>"1Puntaje"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlPropiedades","tablaOrigen"=>"1Puntaje"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			if(!is_null($vo1Puntaje->getIdPuntaje())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"idPuntaje", "tabla"=>"1Puntaje", "valor"=>$vo1Puntaje->getIdPuntaje()));}
			if(!is_null($vo1Puntaje->getIdUsuario())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"idUsuario", "tabla"=>"1Puntaje", "valor"=>$vo1Puntaje->getIdUsuario()));}
			if(!is_null($vo1Puntaje->getTiempo())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"tiempo", "tabla"=>"1Puntaje", "valor"=>$vo1Puntaje->getTiempo()));}
			if(!is_null($vo1Puntaje->getPuntaje())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"puntaje", "tabla"=>"1Puntaje", "valor"=>$vo1Puntaje->getPuntaje()));}
			if(!is_null($vo1Puntaje->getNivel())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"nivel", "tabla"=>"1Puntaje", "valor"=>$vo1Puntaje->getNivel()));}
			if(!is_null($vo1Puntaje->getNombreJuego())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"nombreJuego", "tabla"=>"1Puntaje", "valor"=>$vo1Puntaje->getNombreJuego()));}
			if(!is_null($vo1Puntaje->getXmlPropiedades())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"xmlPropiedades", "tabla"=>"1Puntaje", "valor"=>$vo1Puntaje->getXmlPropiedades()));}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdPuntaje($reg["idPuntaje"]);
					$objVO->setIdUsuario($reg["idUsuario"]);
					$objVO->setTiempo($reg["tiempo"]);
					$objVO->setPuntaje($reg["puntaje"]);
					$objVO->setNivel($reg["nivel"]);
					$objVO->setNombreJuego($reg["nombreJuego"]);
					$objVO->setXmlPropiedades($reg["xmlPropiedades"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idPuntaje","tablaOrigen"=>"1Puntaje"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"1Puntaje"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"tiempo","tablaOrigen"=>"1Puntaje"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"puntaje","tablaOrigen"=>"1Puntaje"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nivel","tablaOrigen"=>"1Puntaje"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombreJuego","tablaOrigen"=>"1Puntaje"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlPropiedades","tablaOrigen"=>"1Puntaje"));
			$parametro = $consulta->addChild("Limitar");
			$parametro->addAttribute("regInicial", $n);
			$parametro->addAttribute("noRegistros", $m);
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO1Puntaje")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdPuntaje(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idPuntaje","tabla"=>"1Puntaje","valor"=>$condiciones->getIdPuntaje()));
							}
							if(strcmp($condiciones->getIdUsuario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idUsuario","tabla"=>"1Puntaje","valor"=>$condiciones->getIdUsuario()));
							}
							if(strcmp($condiciones->getTiempo(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"tiempo","tabla"=>"1Puntaje","valor"=>$condiciones->getTiempo()));
							}
							if(strcmp($condiciones->getPuntaje(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"puntaje","tabla"=>"1Puntaje","valor"=>$condiciones->getPuntaje()));
							}
							if(strcmp($condiciones->getNivel(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"nivel","tabla"=>"1Puntaje","valor"=>$condiciones->getNivel()));
							}
							if(strcmp($condiciones->getNombreJuego(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"nombreJuego","tabla"=>"1Puntaje","valor"=>$condiciones->getNombreJuego()));
							}
							if(strcmp($condiciones->getXmlPropiedades(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"xmlPropiedades","tabla"=>"1Puntaje","valor"=>$condiciones->getXmlPropiedades()));
							}
						}
					}
				}
			}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdPuntaje($reg["idPuntaje"]);
					$objVO->setIdUsuario($reg["idUsuario"]);
					$objVO->setTiempo($reg["tiempo"]);
					$objVO->setPuntaje($reg["puntaje"]);
					$objVO->setNivel($reg["nivel"]);
					$objVO->setNombreJuego($reg["nombreJuego"]);
					$objVO->setXmlPropiedades($reg["xmlPropiedades"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idPuntaje","tablaOrigen"=>"1Puntaje","valor"=>$registro->getIdPuntaje()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"1Puntaje","valor"=>$registro->getIdUsuario()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"tiempo","tablaOrigen"=>"1Puntaje","valor"=>$registro->getTiempo()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"puntaje","tablaOrigen"=>"1Puntaje","valor"=>$registro->getPuntaje()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nivel","tablaOrigen"=>"1Puntaje","valor"=>$registro->getNivel()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombreJuego","tablaOrigen"=>"1Puntaje","valor"=>$registro->getNombreJuego()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlPropiedades","tablaOrigen"=>"1Puntaje","valor"=>$registro->getXmlPropiedades()));
			if($this->db->insertar($consulta)){
				$registro->setIdPuntaje($this->db->ultimoId);
				return true;
			}else{
				return false;
			}
		}
		function actualizarRegistro($registro){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idPuntaje","tablaOrigen"=>"1Puntaje","valor"=>$registro->getIdPuntaje()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"1Puntaje","valor"=>$registro->getIdUsuario()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"tiempo","tablaOrigen"=>"1Puntaje","valor"=>$registro->getTiempo()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"puntaje","tablaOrigen"=>"1Puntaje","valor"=>$registro->getPuntaje()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nivel","tablaOrigen"=>"1Puntaje","valor"=>$registro->getNivel()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombreJuego","tablaOrigen"=>"1Puntaje","valor"=>$registro->getNombreJuego()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlPropiedades","tablaOrigen"=>"1Puntaje","valor"=>$registro->getXmlPropiedades()));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idPuntaje","tabla"=>"1Puntaje","valor"=>$registro->getIdPuntaje()));
			if($this->db->actualizar($consulta)){
				return true;
			}else{
				return false;
			}
		}
		function eliminarRegistro($registro){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("tablaOrigen"=>"1Puntaje"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idPuntaje","tabla"=>"1Puntaje","valor"=>$registro->getIdPuntaje()));
			if($this->db->eliminar($consulta)){
				return true;
			}else{
				return false;
			}
		}
	}
?>