<?php
	class DAO0Logs{
		private $db=null;
		function DAO0Logs(){
			$sesion=Sesion::getInstancia();
			$this->db=$sesion->getDB();
		}
		function setDb($db){ $this->db=$db; }
		function crearVO() { 
			return new VO0Logs();
		}
		public static function getTabla(){
			return '0Logs';
		}
		function getRegistro($idLog) {
			$objVO=$this->crearVO();
			$objVO->setIdLog($idLog);
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idLog","tablaOrigen"=>"0Logs"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"0Logs"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"direcionIP","tablaOrigen"=>"0Logs"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"fechalog","tablaOrigen"=>"0Logs"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"ident","tablaOrigen"=>"0Logs"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"prioridad","tablaOrigen"=>"0Logs"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"mensaje","tablaOrigen"=>"0Logs"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idLog","tabla"=>"0Logs","valor"=>$idLog));
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				$objVO->setIdLog($resultado[0]["idLog"]);
				$objVO->setIdUsuario($resultado[0]["idUsuario"]);
				$objVO->setDirecionIP($resultado[0]["direcionIP"]);
				$objVO->setFechalog($resultado[0]["fechalog"]);
				$objVO->setIdent($resultado[0]["ident"]);
				$objVO->setPrioridad($resultado[0]["prioridad"]);
				$objVO->setMensaje($resultado[0]["mensaje"]);
			}else{
				throw new sinResultados("No se encontro el registro solicitado.");
			}
			return $objVO;
		}
		function getRegistroCondiciones($condiciones){
			$objVO=$this->crearVO();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"*","tablaOrigen"=>"0Logs"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			foreach($condiciones as $nombre=>$valor){
				ControlXML::agregarNodo($y,"Igual",array("campo"=>$nombre,"tabla"=>"0Logs","valor"=>$valor));
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"*","tablaOrigen"=>"0Logs"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			foreach($condiciones as $nombre=>$valor){
				ControlXML::agregarNodo($y,"Igual",array("campo"=>$nombre,"tabla"=>"0Logs","valor"=>$valor));
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idLog","tablaOrigen"=>"0Logs"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"0Logs"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"direcionIP","tablaOrigen"=>"0Logs"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"fechalog","tablaOrigen"=>"0Logs"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"ident","tablaOrigen"=>"0Logs"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"prioridad","tablaOrigen"=>"0Logs"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"mensaje","tablaOrigen"=>"0Logs"));
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO0Logs")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdLog(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idLog","tabla"=>"0Logs","valor"=>$condiciones->getIdLog()));
							}
							if(strcmp($condiciones->getIdUsuario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idUsuario","tabla"=>"0Logs","valor"=>$condiciones->getIdUsuario()));
							}
							if(strcmp($condiciones->getDirecionIP(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"direcionIP","tabla"=>"0Logs","valor"=>$condiciones->getDirecionIP()));
							}
							if(strcmp($condiciones->getFechalog(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"fechalog","tabla"=>"0Logs","valor"=>$condiciones->getFechalog()));
							}
							if(strcmp($condiciones->getIdent(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"ident","tabla"=>"0Logs","valor"=>$condiciones->getIdent()));
							}
							if(strcmp($condiciones->getPrioridad(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"prioridad","tabla"=>"0Logs","valor"=>$condiciones->getPrioridad()));
							}
							if(strcmp($condiciones->getMensaje(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"mensaje","tabla"=>"0Logs","valor"=>$condiciones->getMensaje()));
							}
						}
					}
				}
			}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdLog($reg["idLog"]);
					$objVO->setIdUsuario($reg["idUsuario"]);
					$objVO->setDirecionIP($reg["direcionIP"]);
					$objVO->setFechalog($reg["fechalog"]);
					$objVO->setIdent($reg["ident"]);
					$objVO->setPrioridad($reg["prioridad"]);
					$objVO->setMensaje($reg["mensaje"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idLog","tablaOrigen"=>"0Logs"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"0Logs"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"direcionIP","tablaOrigen"=>"0Logs"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"fechalog","tablaOrigen"=>"0Logs"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"ident","tablaOrigen"=>"0Logs"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"prioridad","tablaOrigen"=>"0Logs"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"mensaje","tablaOrigen"=>"0Logs"));
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO0Logs")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdLog(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idLog","tabla"=>"0Logs","valor"=>$condiciones->getIdLog()));
							}
							if(strcmp($condiciones->getIdUsuario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idUsuario","tabla"=>"0Logs","valor"=>$condiciones->getIdUsuario()));
							}
							if(strcmp($condiciones->getDirecionIP(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"direcionIP","tabla"=>"0Logs","valor"=>$condiciones->getDirecionIP()));
							}
							if(strcmp($condiciones->getFechalog(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"fechalog","tabla"=>"0Logs","valor"=>$condiciones->getFechalog()));
							}
							if(strcmp($condiciones->getIdent(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"ident","tabla"=>"0Logs","valor"=>$condiciones->getIdent()));
							}
							if(strcmp($condiciones->getPrioridad(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"prioridad","tabla"=>"0Logs","valor"=>$condiciones->getPrioridad()));
							}
							if(strcmp($condiciones->getMensaje(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"mensaje","tabla"=>"0Logs","valor"=>$condiciones->getMensaje()));
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
		function consultarRegistros($vo0Logs){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idLog","tablaOrigen"=>"0Logs"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"0Logs"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"direcionIP","tablaOrigen"=>"0Logs"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"fechalog","tablaOrigen"=>"0Logs"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"ident","tablaOrigen"=>"0Logs"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"prioridad","tablaOrigen"=>"0Logs"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"mensaje","tablaOrigen"=>"0Logs"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			if(!is_null($vo0Logs->getIdLog())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"idLog", "tabla"=>"0Logs", "valor"=>$vo0Logs->getIdLog()));}
			if(!is_null($vo0Logs->getIdUsuario())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"idUsuario", "tabla"=>"0Logs", "valor"=>$vo0Logs->getIdUsuario()));}
			if(!is_null($vo0Logs->getDirecionIP())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"direcionIP", "tabla"=>"0Logs", "valor"=>$vo0Logs->getDirecionIP()));}
			if(!is_null($vo0Logs->getFechalog())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"fechalog", "tabla"=>"0Logs", "valor"=>$vo0Logs->getFechalog()));}
			if(!is_null($vo0Logs->getIdent())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"ident", "tabla"=>"0Logs", "valor"=>$vo0Logs->getIdent()));}
			if(!is_null($vo0Logs->getPrioridad())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"prioridad", "tabla"=>"0Logs", "valor"=>$vo0Logs->getPrioridad()));}
			if(!is_null($vo0Logs->getMensaje())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"mensaje", "tabla"=>"0Logs", "valor"=>$vo0Logs->getMensaje()));}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdLog($reg["idLog"]);
					$objVO->setIdUsuario($reg["idUsuario"]);
					$objVO->setDirecionIP($reg["direcionIP"]);
					$objVO->setFechalog($reg["fechalog"]);
					$objVO->setIdent($reg["ident"]);
					$objVO->setPrioridad($reg["prioridad"]);
					$objVO->setMensaje($reg["mensaje"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idLog","tablaOrigen"=>"0Logs"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"0Logs"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"direcionIP","tablaOrigen"=>"0Logs"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"fechalog","tablaOrigen"=>"0Logs"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"ident","tablaOrigen"=>"0Logs"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"prioridad","tablaOrigen"=>"0Logs"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"mensaje","tablaOrigen"=>"0Logs"));
			$parametro = $consulta->addChild("Limitar");
			$parametro->addAttribute("regInicial", $n);
			$parametro->addAttribute("noRegistros", $m);
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO0Logs")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdLog(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idLog","tabla"=>"0Logs","valor"=>$condiciones->getIdLog()));
							}
							if(strcmp($condiciones->getIdUsuario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idUsuario","tabla"=>"0Logs","valor"=>$condiciones->getIdUsuario()));
							}
							if(strcmp($condiciones->getDirecionIP(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"direcionIP","tabla"=>"0Logs","valor"=>$condiciones->getDirecionIP()));
							}
							if(strcmp($condiciones->getFechalog(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"fechalog","tabla"=>"0Logs","valor"=>$condiciones->getFechalog()));
							}
							if(strcmp($condiciones->getIdent(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"ident","tabla"=>"0Logs","valor"=>$condiciones->getIdent()));
							}
							if(strcmp($condiciones->getPrioridad(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"prioridad","tabla"=>"0Logs","valor"=>$condiciones->getPrioridad()));
							}
							if(strcmp($condiciones->getMensaje(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"mensaje","tabla"=>"0Logs","valor"=>$condiciones->getMensaje()));
							}
						}
					}
				}
			}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdLog($reg["idLog"]);
					$objVO->setIdUsuario($reg["idUsuario"]);
					$objVO->setDirecionIP($reg["direcionIP"]);
					$objVO->setFechalog($reg["fechalog"]);
					$objVO->setIdent($reg["ident"]);
					$objVO->setPrioridad($reg["prioridad"]);
					$objVO->setMensaje($reg["mensaje"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idLog","tablaOrigen"=>"0Logs","valor"=>$registro->getIdLog()));
				if (!is_null($registro->getIdUsuario()))  ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"0Logs","valor"=>$registro->getIdUsuario()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"direcionIP","tablaOrigen"=>"0Logs","valor"=>$registro->getDirecionIP()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"fechalog","tablaOrigen"=>"0Logs","valor"=>$registro->getFechalog()));
				if (!is_null($registro->getIdent()))  ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"ident","tablaOrigen"=>"0Logs","valor"=>$registro->getIdent()));
				if (!is_null($registro->getPrioridad()))  ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"prioridad","tablaOrigen"=>"0Logs","valor"=>$registro->getPrioridad()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"mensaje","tablaOrigen"=>"0Logs","valor"=>$registro->getMensaje()));
			if($this->db->insertar($consulta)){
				$registro->setIdLog($this->db->ultimoId);
				return true;
			}else{
				return false;
			}
		}
		function actualizarRegistro($registro,$condiciones=null){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idLog","tablaOrigen"=>"0Logs","valor"=>$registro->getIdLog()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"0Logs","valor"=>$registro->getIdUsuario()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"direcionIP","tablaOrigen"=>"0Logs","valor"=>$registro->getDirecionIP()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"fechalog","tablaOrigen"=>"0Logs","valor"=>$registro->getFechalog()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"ident","tablaOrigen"=>"0Logs","valor"=>$registro->getIdent()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"prioridad","tablaOrigen"=>"0Logs","valor"=>$registro->getPrioridad()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"mensaje","tablaOrigen"=>"0Logs","valor"=>$registro->getMensaje()));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			if(is_array($condiciones) && count($condiciones)>0){
				foreach($condiciones as $campo=>$valor){
					ControlXML::agregarNodo($y,"Igual",array("campo"=>"$campo","tabla"=>"0Logs","valor"=>$valor));
				}
			}else{
				ControlXML::agregarNodo($y,"Igual",array("campo"=>"idLog","tabla"=>"0Logs","valor"=>$registro->getIdLog()));
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
			ControlXML::agregarNodo($consulta,"Campo",array("tablaOrigen"=>"0Logs"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idLog","tabla"=>"0Logs","valor"=>$registro->getIdLog()));
			if($this->db->eliminar($consulta)){
				return true;
			}else{
				return false;
			}
		}
	}
?>