<?php
	class DAO1CodigosRecuperacion{
		private $db=null;
		function DAO1CodigosRecuperacion($db){
			$this->db=$db;
		}
		function setDb($db){ $this->db=$db; }
		function crearVO() { 
			return new VO1CodigosRecuperacion();
		}
		public static function getTabla(){
			return '1CodigosRecuperacion';
		}
		function getRegistro($idCodigoRecuperacion) {
			$objVO=$this->crearVO();
			$objVO->setIdCodigoRecuperacion($idCodigoRecuperacion);
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idCodigoRecuperacion","tablaOrigen"=>"1CodigosRecuperacion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"1CodigosRecuperacion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"codigo","tablaOrigen"=>"1CodigosRecuperacion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"caducidad","tablaOrigen"=>"1CodigosRecuperacion"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idCodigoRecuperacion","tabla"=>"1CodigosRecuperacion","valor"=>$idCodigoRecuperacion));
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				$objVO->setIdCodigoRecuperacion($resultado[0]["idCodigoRecuperacion"]);
				$objVO->setIdUsuario($resultado[0]["idUsuario"]);
				$objVO->setCodigo($resultado[0]["codigo"]);
				$objVO->setCaducidad($resultado[0]["caducidad"]);
			}else{
				throw new sinResultados("No se encontro el registro solicitado.");
			}
			return $objVO;
		}
		function getRegistroCondiciones($condiciones){
			$objVO=$this->crearVO();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"*","tablaOrigen"=>"1CodigosRecuperacion"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			foreach($condiciones as $nombre=>$valor){
				ControlXML::agregarNodo($y,"Igual",array("campo"=>$nombre,"tabla"=>"1CodigosRecuperacion","valor"=>$valor));
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"*","tablaOrigen"=>"1CodigosRecuperacion"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			foreach($condiciones as $nombre=>$valor){
				ControlXML::agregarNodo($y,"Igual",array("campo"=>$nombre,"tabla"=>"1CodigosRecuperacion","valor"=>$valor));
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idCodigoRecuperacion","tablaOrigen"=>"1CodigosRecuperacion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"1CodigosRecuperacion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"codigo","tablaOrigen"=>"1CodigosRecuperacion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"caducidad","tablaOrigen"=>"1CodigosRecuperacion"));
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO1CodigosRecuperacion")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdCodigoRecuperacion(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idCodigoRecuperacion","tabla"=>"1CodigosRecuperacion","valor"=>$condiciones->getIdCodigoRecuperacion()));
							}
							if(strcmp($condiciones->getIdUsuario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idUsuario","tabla"=>"1CodigosRecuperacion","valor"=>$condiciones->getIdUsuario()));
							}
							if(strcmp($condiciones->getCodigo(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"codigo","tabla"=>"1CodigosRecuperacion","valor"=>$condiciones->getCodigo()));
							}
							if(strcmp($condiciones->getCaducidad(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"caducidad","tabla"=>"1CodigosRecuperacion","valor"=>$condiciones->getCaducidad()));
							}
						}
					}
				}
			}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdCodigoRecuperacion($reg["idCodigoRecuperacion"]);
					$objVO->setIdUsuario($reg["idUsuario"]);
					$objVO->setCodigo($reg["codigo"]);
					$objVO->setCaducidad($reg["caducidad"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idCodigoRecuperacion","tablaOrigen"=>"1CodigosRecuperacion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"1CodigosRecuperacion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"codigo","tablaOrigen"=>"1CodigosRecuperacion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"caducidad","tablaOrigen"=>"1CodigosRecuperacion"));
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO1CodigosRecuperacion")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdCodigoRecuperacion(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idCodigoRecuperacion","tabla"=>"1CodigosRecuperacion","valor"=>$condiciones->getIdCodigoRecuperacion()));
							}
							if(strcmp($condiciones->getIdUsuario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idUsuario","tabla"=>"1CodigosRecuperacion","valor"=>$condiciones->getIdUsuario()));
							}
							if(strcmp($condiciones->getCodigo(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"codigo","tabla"=>"1CodigosRecuperacion","valor"=>$condiciones->getCodigo()));
							}
							if(strcmp($condiciones->getCaducidad(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"caducidad","tabla"=>"1CodigosRecuperacion","valor"=>$condiciones->getCaducidad()));
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
		function consultarRegistros($vo1CodigosRecuperacion){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idCodigoRecuperacion","tablaOrigen"=>"1CodigosRecuperacion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"1CodigosRecuperacion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"codigo","tablaOrigen"=>"1CodigosRecuperacion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"caducidad","tablaOrigen"=>"1CodigosRecuperacion"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			if(!is_null($vo1CodigosRecuperacion->getIdCodigoRecuperacion())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"idCodigoRecuperacion", "tabla"=>"1CodigosRecuperacion", "valor"=>$vo1CodigosRecuperacion->getIdCodigoRecuperacion()));}
			if(!is_null($vo1CodigosRecuperacion->getIdUsuario())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"idUsuario", "tabla"=>"1CodigosRecuperacion", "valor"=>$vo1CodigosRecuperacion->getIdUsuario()));}
			if(!is_null($vo1CodigosRecuperacion->getCodigo())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"codigo", "tabla"=>"1CodigosRecuperacion", "valor"=>$vo1CodigosRecuperacion->getCodigo()));}
			if(!is_null($vo1CodigosRecuperacion->getCaducidad())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"caducidad", "tabla"=>"1CodigosRecuperacion", "valor"=>$vo1CodigosRecuperacion->getCaducidad()));}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdCodigoRecuperacion($reg["idCodigoRecuperacion"]);
					$objVO->setIdUsuario($reg["idUsuario"]);
					$objVO->setCodigo($reg["codigo"]);
					$objVO->setCaducidad($reg["caducidad"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idCodigoRecuperacion","tablaOrigen"=>"1CodigosRecuperacion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"1CodigosRecuperacion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"codigo","tablaOrigen"=>"1CodigosRecuperacion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"caducidad","tablaOrigen"=>"1CodigosRecuperacion"));
			$parametro = $consulta->addChild("Limitar");
			$parametro->addAttribute("regInicial", $n);
			$parametro->addAttribute("noRegistros", $m);
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO1CodigosRecuperacion")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdCodigoRecuperacion(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idCodigoRecuperacion","tabla"=>"1CodigosRecuperacion","valor"=>$condiciones->getIdCodigoRecuperacion()));
							}
							if(strcmp($condiciones->getIdUsuario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idUsuario","tabla"=>"1CodigosRecuperacion","valor"=>$condiciones->getIdUsuario()));
							}
							if(strcmp($condiciones->getCodigo(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"codigo","tabla"=>"1CodigosRecuperacion","valor"=>$condiciones->getCodigo()));
							}
							if(strcmp($condiciones->getCaducidad(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"caducidad","tabla"=>"1CodigosRecuperacion","valor"=>$condiciones->getCaducidad()));
							}
						}
					}
				}
			}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdCodigoRecuperacion($reg["idCodigoRecuperacion"]);
					$objVO->setIdUsuario($reg["idUsuario"]);
					$objVO->setCodigo($reg["codigo"]);
					$objVO->setCaducidad($reg["caducidad"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idCodigoRecuperacion","tablaOrigen"=>"1CodigosRecuperacion","valor"=>$registro->getIdCodigoRecuperacion()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"1CodigosRecuperacion","valor"=>$registro->getIdUsuario()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"codigo","tablaOrigen"=>"1CodigosRecuperacion","valor"=>$registro->getCodigo()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"caducidad","tablaOrigen"=>"1CodigosRecuperacion","valor"=>$registro->getCaducidad()));
			if($this->db->insertar($consulta)){
				$registro->setIdCodigoRecuperacion($this->db->ultimoId);
				return true;
			}else{
				return false;
			}
		}
		function actualizarRegistro($registro){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idCodigoRecuperacion","tablaOrigen"=>"1CodigosRecuperacion","valor"=>$registro->getIdCodigoRecuperacion()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"1CodigosRecuperacion","valor"=>$registro->getIdUsuario()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"codigo","tablaOrigen"=>"1CodigosRecuperacion","valor"=>$registro->getCodigo()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"caducidad","tablaOrigen"=>"1CodigosRecuperacion","valor"=>$registro->getCaducidad()));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idCodigoRecuperacion","tabla"=>"1CodigosRecuperacion","valor"=>$registro->getIdCodigoRecuperacion()));
			if($this->db->actualizar($consulta)){
				return true;
			}else{
				return false;
			}
		}
		function eliminarRegistro($registro){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("tablaOrigen"=>"1CodigosRecuperacion"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idCodigoRecuperacion","tabla"=>"1CodigosRecuperacion","valor"=>$registro->getIdCodigoRecuperacion()));
			if($this->db->eliminar($consulta)){
				return true;
			}else{
				return false;
			}
		}
	}
?>