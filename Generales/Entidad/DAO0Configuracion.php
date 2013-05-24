<?php
	class DAO0Configuracion{
		private $db=null;
		function DAO0Configuracion($db){
			$this->db=$db;
		}
		function setDb($db){ $this->db=$db; }
		function crearVO() { 
			return new VO0Configuracion();
		}
		public static function getTabla(){
			return '0Configuracion';
		}
		function getRegistro($idConfiguracion) {
			$objVO=$this->crearVO();
			$objVO->setIdConfiguracion($idConfiguracion);
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idConfiguracion","tablaOrigen"=>"0Configuracion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"0Configuracion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombreConfiguracion","tablaOrigen"=>"0Configuracion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlValor","tablaOrigen"=>"0Configuracion"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idConfiguracion","tabla"=>"0Configuracion","valor"=>$idConfiguracion));
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				$objVO->setIdConfiguracion($resultado[0]["idConfiguracion"]);
				$objVO->setIdUsuario($resultado[0]["idUsuario"]);
				$objVO->setNombreConfiguracion($resultado[0]["nombreConfiguracion"]);
				$objVO->setXmlValor($resultado[0]["xmlValor"]);
			}else{
				throw new sinResultados("No se encontro el registro solicitado.");
			}
			return $objVO;
		}
		function getRegistroCondiciones($condiciones){
			$objVO=$this->crearVO();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"*","tablaOrigen"=>"0Configuracion"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			foreach($condiciones as $nombre=>$valor){
				ControlXML::agregarNodo($y,"Igual",array("campo"=>$nombre,"tabla"=>"0Configuracion","valor"=>$valor));
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"*","tablaOrigen"=>"0Configuracion"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			foreach($condiciones as $nombre=>$valor){
				ControlXML::agregarNodo($y,"Igual",array("campo"=>$nombre,"tabla"=>"0Configuracion","valor"=>$valor));
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idConfiguracion","tablaOrigen"=>"0Configuracion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"0Configuracion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombreConfiguracion","tablaOrigen"=>"0Configuracion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlValor","tablaOrigen"=>"0Configuracion"));
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO0Configuracion")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdConfiguracion(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idConfiguracion","tabla"=>"0Configuracion","valor"=>$condiciones->getIdConfiguracion()));
							}
							if(strcmp($condiciones->getIdUsuario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idUsuario","tabla"=>"0Configuracion","valor"=>$condiciones->getIdUsuario()));
							}
							if(strcmp($condiciones->getNombreConfiguracion(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"nombreConfiguracion","tabla"=>"0Configuracion","valor"=>$condiciones->getNombreConfiguracion()));
							}
							if(strcmp($condiciones->getXmlValor(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"xmlValor","tabla"=>"0Configuracion","valor"=>$condiciones->getXmlValor()));
							}
						}
					}
				}
			}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdConfiguracion($reg["idConfiguracion"]);
					$objVO->setIdUsuario($reg["idUsuario"]);
					$objVO->setNombreConfiguracion($reg["nombreConfiguracion"]);
					$objVO->setXmlValor($reg["xmlValor"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idConfiguracion","tablaOrigen"=>"0Configuracion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"0Configuracion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombreConfiguracion","tablaOrigen"=>"0Configuracion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlValor","tablaOrigen"=>"0Configuracion"));
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO0Configuracion")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdConfiguracion(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idConfiguracion","tabla"=>"0Configuracion","valor"=>$condiciones->getIdConfiguracion()));
							}
							if(strcmp($condiciones->getIdUsuario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idUsuario","tabla"=>"0Configuracion","valor"=>$condiciones->getIdUsuario()));
							}
							if(strcmp($condiciones->getNombreConfiguracion(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"nombreConfiguracion","tabla"=>"0Configuracion","valor"=>$condiciones->getNombreConfiguracion()));
							}
							if(strcmp($condiciones->getXmlValor(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"xmlValor","tabla"=>"0Configuracion","valor"=>$condiciones->getXmlValor()));
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
		function consultarRegistros($vo0Configuracion){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idConfiguracion","tablaOrigen"=>"0Configuracion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"0Configuracion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombreConfiguracion","tablaOrigen"=>"0Configuracion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlValor","tablaOrigen"=>"0Configuracion"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			if(!is_null($vo0Configuracion->getIdConfiguracion())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"idConfiguracion", "tabla"=>"0Configuracion", "valor"=>$vo0Configuracion->getIdConfiguracion()));}
			if(!is_null($vo0Configuracion->getIdUsuario())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"idUsuario", "tabla"=>"0Configuracion", "valor"=>$vo0Configuracion->getIdUsuario()));}
			if(!is_null($vo0Configuracion->getNombreConfiguracion())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"nombreConfiguracion", "tabla"=>"0Configuracion", "valor"=>$vo0Configuracion->getNombreConfiguracion()));}
			if(!is_null($vo0Configuracion->getXmlValor())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"xmlValor", "tabla"=>"0Configuracion", "valor"=>$vo0Configuracion->getXmlValor()));}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdConfiguracion($reg["idConfiguracion"]);
					$objVO->setIdUsuario($reg["idUsuario"]);
					$objVO->setNombreConfiguracion($reg["nombreConfiguracion"]);
					$objVO->setXmlValor($reg["xmlValor"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idConfiguracion","tablaOrigen"=>"0Configuracion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"0Configuracion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombreConfiguracion","tablaOrigen"=>"0Configuracion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlValor","tablaOrigen"=>"0Configuracion"));
			$parametro = $consulta->addChild("Limitar");
			$parametro->addAttribute("regInicial", $n);
			$parametro->addAttribute("noRegistros", $m);
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO0Configuracion")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdConfiguracion(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idConfiguracion","tabla"=>"0Configuracion","valor"=>$condiciones->getIdConfiguracion()));
							}
							if(strcmp($condiciones->getIdUsuario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idUsuario","tabla"=>"0Configuracion","valor"=>$condiciones->getIdUsuario()));
							}
							if(strcmp($condiciones->getNombreConfiguracion(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"nombreConfiguracion","tabla"=>"0Configuracion","valor"=>$condiciones->getNombreConfiguracion()));
							}
							if(strcmp($condiciones->getXmlValor(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"xmlValor","tabla"=>"0Configuracion","valor"=>$condiciones->getXmlValor()));
							}
						}
					}
				}
			}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdConfiguracion($reg["idConfiguracion"]);
					$objVO->setIdUsuario($reg["idUsuario"]);
					$objVO->setNombreConfiguracion($reg["nombreConfiguracion"]);
					$objVO->setXmlValor($reg["xmlValor"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idConfiguracion","tablaOrigen"=>"0Configuracion","valor"=>$registro->getIdConfiguracion()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"0Configuracion","valor"=>$registro->getIdUsuario()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombreConfiguracion","tablaOrigen"=>"0Configuracion","valor"=>$registro->getNombreConfiguracion()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlValor","tablaOrigen"=>"0Configuracion","valor"=>$registro->getXmlValor()));
			if($this->db->insertar($consulta)){
				$registro->setIdConfiguracion($this->db->ultimoId);
				return true;
			}else{
				return false;
			}
		}
		function actualizarRegistro($registro){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idConfiguracion","tablaOrigen"=>"0Configuracion","valor"=>$registro->getIdConfiguracion()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"0Configuracion","valor"=>$registro->getIdUsuario()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombreConfiguracion","tablaOrigen"=>"0Configuracion","valor"=>$registro->getNombreConfiguracion()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlValor","tablaOrigen"=>"0Configuracion","valor"=>$registro->getXmlValor()));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idConfiguracion","tabla"=>"0Configuracion","valor"=>$registro->getIdConfiguracion()));
			if($this->db->actualizar($consulta)){
				return true;
			}else{
				return false;
			}
		}
		function eliminarRegistro($registro){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("tablaOrigen"=>"0Configuracion"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idConfiguracion","tabla"=>"0Configuracion","valor"=>$registro->getIdConfiguracion()));
			if($this->db->eliminar($consulta)){
				return true;
			}else{
				return false;
			}
		}
	}
?>