<?php
	class DAO0XMLPropiedades{
		private $db=null;
		function DAO0XMLPropiedades(){
			$sesion=Sesion::getInstancia();
			$this->db=$sesion->getDB();
		}
		function setDb($db){ $this->db=$db; }
		function crearVO() { 
			return new VO0XMLPropiedades();
		}
		public static function getTabla(){
			return '0XMLPropiedades';
		}
		function getRegistro($idXMLPropiedades) {
			$objVO=$this->crearVO();
			$objVO->setIdXMLPropiedades($idXMLPropiedades);
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idXMLPropiedades","tablaOrigen"=>"0XMLPropiedades"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombre0XMLPropiedades","tablaOrigen"=>"0XMLPropiedades"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"tabla","tablaOrigen"=>"0XMLPropiedades"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"campo","tablaOrigen"=>"0XMLPropiedades"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlPropiedades","tablaOrigen"=>"0XMLPropiedades"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idXMLPropiedades","tabla"=>"0XMLPropiedades","valor"=>$idXMLPropiedades));
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				$objVO->setIdXMLPropiedades($resultado[0]["idXMLPropiedades"]);
				$objVO->setNombre0XMLPropiedades($resultado[0]["nombre0XMLPropiedades"]);
				$objVO->setTabla($resultado[0]["tabla"]);
				$objVO->setCampo($resultado[0]["campo"]);
				$objVO->setXmlPropiedades($resultado[0]["xmlPropiedades"]);
			}else{
				throw new sinResultados("No se encontro el registro solicitado.");
			}
			return $objVO;
		}
		function getRegistroCondiciones($condiciones){
			$objVO=$this->crearVO();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"*","tablaOrigen"=>"0XMLPropiedades"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			foreach($condiciones as $nombre=>$valor){
				ControlXML::agregarNodo($y,"Igual",array("campo"=>$nombre,"tabla"=>"0XMLPropiedades","valor"=>$valor));
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"*","tablaOrigen"=>"0XMLPropiedades"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			foreach($condiciones as $nombre=>$valor){
				ControlXML::agregarNodo($y,"Igual",array("campo"=>$nombre,"tabla"=>"0XMLPropiedades","valor"=>$valor));
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idXMLPropiedades","tablaOrigen"=>"0XMLPropiedades"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombre0XMLPropiedades","tablaOrigen"=>"0XMLPropiedades"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"tabla","tablaOrigen"=>"0XMLPropiedades"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"campo","tablaOrigen"=>"0XMLPropiedades"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlPropiedades","tablaOrigen"=>"0XMLPropiedades"));
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO0XMLPropiedades")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdXMLPropiedades(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idXMLPropiedades","tabla"=>"0XMLPropiedades","valor"=>$condiciones->getIdXMLPropiedades()));
							}
							if(strcmp($condiciones->getNombre0XMLPropiedades(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"nombre0XMLPropiedades","tabla"=>"0XMLPropiedades","valor"=>$condiciones->getNombre0XMLPropiedades()));
							}
							if(strcmp($condiciones->getTabla(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"tabla","tabla"=>"0XMLPropiedades","valor"=>$condiciones->getTabla()));
							}
							if(strcmp($condiciones->getCampo(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"campo","tabla"=>"0XMLPropiedades","valor"=>$condiciones->getCampo()));
							}
							if(strcmp($condiciones->getXmlPropiedades(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"xmlPropiedades","tabla"=>"0XMLPropiedades","valor"=>$condiciones->getXmlPropiedades()));
							}
						}
					}
				}
			}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdXMLPropiedades($reg["idXMLPropiedades"]);
					$objVO->setNombre0XMLPropiedades($reg["nombre0XMLPropiedades"]);
					$objVO->setTabla($reg["tabla"]);
					$objVO->setCampo($reg["campo"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idXMLPropiedades","tablaOrigen"=>"0XMLPropiedades"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombre0XMLPropiedades","tablaOrigen"=>"0XMLPropiedades"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"tabla","tablaOrigen"=>"0XMLPropiedades"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"campo","tablaOrigen"=>"0XMLPropiedades"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlPropiedades","tablaOrigen"=>"0XMLPropiedades"));
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO0XMLPropiedades")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdXMLPropiedades(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idXMLPropiedades","tabla"=>"0XMLPropiedades","valor"=>$condiciones->getIdXMLPropiedades()));
							}
							if(strcmp($condiciones->getNombre0XMLPropiedades(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"nombre0XMLPropiedades","tabla"=>"0XMLPropiedades","valor"=>$condiciones->getNombre0XMLPropiedades()));
							}
							if(strcmp($condiciones->getTabla(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"tabla","tabla"=>"0XMLPropiedades","valor"=>$condiciones->getTabla()));
							}
							if(strcmp($condiciones->getCampo(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"campo","tabla"=>"0XMLPropiedades","valor"=>$condiciones->getCampo()));
							}
							if(strcmp($condiciones->getXmlPropiedades(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"xmlPropiedades","tabla"=>"0XMLPropiedades","valor"=>$condiciones->getXmlPropiedades()));
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
		function consultarRegistros($vo0XMLPropiedades){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idXMLPropiedades","tablaOrigen"=>"0XMLPropiedades"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombre0XMLPropiedades","tablaOrigen"=>"0XMLPropiedades"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"tabla","tablaOrigen"=>"0XMLPropiedades"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"campo","tablaOrigen"=>"0XMLPropiedades"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlPropiedades","tablaOrigen"=>"0XMLPropiedades"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			if(!is_null($vo0XMLPropiedades->getIdXMLPropiedades())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"idXMLPropiedades", "tabla"=>"0XMLPropiedades", "valor"=>$vo0XMLPropiedades->getIdXMLPropiedades()));}
			if(!is_null($vo0XMLPropiedades->getNombre0XMLPropiedades())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"nombre0XMLPropiedades", "tabla"=>"0XMLPropiedades", "valor"=>$vo0XMLPropiedades->getNombre0XMLPropiedades()));}
			if(!is_null($vo0XMLPropiedades->getTabla())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"tabla", "tabla"=>"0XMLPropiedades", "valor"=>$vo0XMLPropiedades->getTabla()));}
			if(!is_null($vo0XMLPropiedades->getCampo())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"campo", "tabla"=>"0XMLPropiedades", "valor"=>$vo0XMLPropiedades->getCampo()));}
			if(!is_null($vo0XMLPropiedades->getXmlPropiedades())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"xmlPropiedades", "tabla"=>"0XMLPropiedades", "valor"=>$vo0XMLPropiedades->getXmlPropiedades()));}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdXMLPropiedades($reg["idXMLPropiedades"]);
					$objVO->setNombre0XMLPropiedades($reg["nombre0XMLPropiedades"]);
					$objVO->setTabla($reg["tabla"]);
					$objVO->setCampo($reg["campo"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idXMLPropiedades","tablaOrigen"=>"0XMLPropiedades"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombre0XMLPropiedades","tablaOrigen"=>"0XMLPropiedades"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"tabla","tablaOrigen"=>"0XMLPropiedades"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"campo","tablaOrigen"=>"0XMLPropiedades"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlPropiedades","tablaOrigen"=>"0XMLPropiedades"));
			$parametro = $consulta->addChild("Limitar");
			$parametro->addAttribute("regInicial", $n);
			$parametro->addAttribute("noRegistros", $m);
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO0XMLPropiedades")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdXMLPropiedades(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idXMLPropiedades","tabla"=>"0XMLPropiedades","valor"=>$condiciones->getIdXMLPropiedades()));
							}
							if(strcmp($condiciones->getNombre0XMLPropiedades(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"nombre0XMLPropiedades","tabla"=>"0XMLPropiedades","valor"=>$condiciones->getNombre0XMLPropiedades()));
							}
							if(strcmp($condiciones->getTabla(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"tabla","tabla"=>"0XMLPropiedades","valor"=>$condiciones->getTabla()));
							}
							if(strcmp($condiciones->getCampo(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"campo","tabla"=>"0XMLPropiedades","valor"=>$condiciones->getCampo()));
							}
							if(strcmp($condiciones->getXmlPropiedades(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"xmlPropiedades","tabla"=>"0XMLPropiedades","valor"=>$condiciones->getXmlPropiedades()));
							}
						}
					}
				}
			}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdXMLPropiedades($reg["idXMLPropiedades"]);
					$objVO->setNombre0XMLPropiedades($reg["nombre0XMLPropiedades"]);
					$objVO->setTabla($reg["tabla"]);
					$objVO->setCampo($reg["campo"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idXMLPropiedades","tablaOrigen"=>"0XMLPropiedades","valor"=>$registro->getIdXMLPropiedades()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombre0XMLPropiedades","tablaOrigen"=>"0XMLPropiedades","valor"=>$registro->getNombre0XMLPropiedades()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"tabla","tablaOrigen"=>"0XMLPropiedades","valor"=>$registro->getTabla()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"campo","tablaOrigen"=>"0XMLPropiedades","valor"=>$registro->getCampo()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlPropiedades","tablaOrigen"=>"0XMLPropiedades","valor"=>$registro->getXmlPropiedades()));
			if($this->db->insertar($consulta)){
				$registro->setIdXMLPropiedades($this->db->ultimoId);
				return true;
			}else{
				return false;
			}
		}
		function actualizarRegistro($registro,$condiciones=null){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idXMLPropiedades","tablaOrigen"=>"0XMLPropiedades","valor"=>$registro->getIdXMLPropiedades()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombre0XMLPropiedades","tablaOrigen"=>"0XMLPropiedades","valor"=>$registro->getNombre0XMLPropiedades()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"tabla","tablaOrigen"=>"0XMLPropiedades","valor"=>$registro->getTabla()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"campo","tablaOrigen"=>"0XMLPropiedades","valor"=>$registro->getCampo()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlPropiedades","tablaOrigen"=>"0XMLPropiedades","valor"=>$registro->getXmlPropiedades()));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			if(is_array($condiciones) && count($condiciones)>0){
				foreach($condiciones as $campo=>$valor){
					ControlXML::agregarNodo($y,"Igual",array("campo"=>"$campo","tabla"=>"0XMLPropiedades","valor"=>$valor));
				}
			}else{
				ControlXML::agregarNodo($y,"Igual",array("campo"=>"idXMLPropiedades","tabla"=>"0XMLPropiedades","valor"=>$registro->getIdXMLPropiedades()));
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
			ControlXML::agregarNodo($consulta,"Campo",array("tablaOrigen"=>"0XMLPropiedades"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idXMLPropiedades","tabla"=>"0XMLPropiedades","valor"=>$registro->getIdXMLPropiedades()));
			if($this->db->eliminar($consulta)){
				return true;
			}else{
				return false;
			}
		}
	}
?>