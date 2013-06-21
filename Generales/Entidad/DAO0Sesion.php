<?php
	class DAO0Sesion{
		private $db=null;
		function DAO0Sesion($db=null){
			if (is_null($db)){
				$sesion=Sesion::getInstancia();
				$this->db=$sesion->getDB();
			}else{
				$this->db=$db;
			}
		}
		function setDb($db){ $this->db=$db; }
		function crearVO() { 
			return new VO0Sesion();
		}
		public static function getTabla(){
			return '0Sesion';
		}
		function getRegistro($idSesion) {
			$objVO=$this->crearVO();
			$objVO->setIdSesion($idSesion);
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idSesion","tablaOrigen"=>"0Sesion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"datosSesion","tablaOrigen"=>"0Sesion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"0Sesion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"ultimoAcceso","tablaOrigen"=>"0Sesion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"datosAcceso","tablaOrigen"=>"0Sesion"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idSesion","tabla"=>"0Sesion","valor"=>$idSesion));
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				$objVO->setIdSesion($resultado[0]["idSesion"]);
				$objVO->setDatosSesion($resultado[0]["datosSesion"]);
				$objVO->setIdUsuario($resultado[0]["idUsuario"]);
				$objVO->setUltimoAcceso($resultado[0]["ultimoAcceso"]);
				$objVO->setDatosAcceso($resultado[0]["datosAcceso"]);
			}else{
				throw new sinResultados("No se encontro el registro solicitado.");
			}
			return $objVO;
		}
		function getRegistroCondiciones($condiciones){
			$objVO=$this->crearVO();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"*","tablaOrigen"=>"0Sesion"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			foreach($condiciones as $nombre=>$valor){
				ControlXML::agregarNodo($y,"Igual",array("campo"=>$nombre,"tabla"=>"0Sesion","valor"=>$valor));
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"*","tablaOrigen"=>"0Sesion"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			foreach($condiciones as $nombre=>$valor){
				ControlXML::agregarNodo($y,"Igual",array("campo"=>$nombre,"tabla"=>"0Sesion","valor"=>$valor));
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idSesion","tablaOrigen"=>"0Sesion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"datosSesion","tablaOrigen"=>"0Sesion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"0Sesion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"ultimoAcceso","tablaOrigen"=>"0Sesion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"datosAcceso","tablaOrigen"=>"0Sesion"));
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO0Sesion")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdSesion(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idSesion","tabla"=>"0Sesion","valor"=>$condiciones->getIdSesion()));
							}
							if(strcmp($condiciones->getDatosSesion(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"datosSesion","tabla"=>"0Sesion","valor"=>$condiciones->getDatosSesion()));
							}
							if(strcmp($condiciones->getIdUsuario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idUsuario","tabla"=>"0Sesion","valor"=>$condiciones->getIdUsuario()));
							}
							if(strcmp($condiciones->getUltimoAcceso(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"ultimoAcceso","tabla"=>"0Sesion","valor"=>$condiciones->getUltimoAcceso()));
							}
							if(strcmp($condiciones->getDatosAcceso(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"datosAcceso","tabla"=>"0Sesion","valor"=>$condiciones->getDatosAcceso()));
							}
						}
					}
				}
			}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdSesion($reg["idSesion"]);
					$objVO->setDatosSesion($reg["datosSesion"]);
					$objVO->setIdUsuario($reg["idUsuario"]);
					$objVO->setUltimoAcceso($reg["ultimoAcceso"]);
					$objVO->setDatosAcceso($reg["datosAcceso"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idSesion","tablaOrigen"=>"0Sesion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"datosSesion","tablaOrigen"=>"0Sesion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"0Sesion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"ultimoAcceso","tablaOrigen"=>"0Sesion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"datosAcceso","tablaOrigen"=>"0Sesion"));
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO0Sesion")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdSesion(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idSesion","tabla"=>"0Sesion","valor"=>$condiciones->getIdSesion()));
							}
							if(strcmp($condiciones->getDatosSesion(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"datosSesion","tabla"=>"0Sesion","valor"=>$condiciones->getDatosSesion()));
							}
							if(strcmp($condiciones->getIdUsuario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idUsuario","tabla"=>"0Sesion","valor"=>$condiciones->getIdUsuario()));
							}
							if(strcmp($condiciones->getUltimoAcceso(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"ultimoAcceso","tabla"=>"0Sesion","valor"=>$condiciones->getUltimoAcceso()));
							}
							if(strcmp($condiciones->getDatosAcceso(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"datosAcceso","tabla"=>"0Sesion","valor"=>$condiciones->getDatosAcceso()));
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
		function consultarRegistros($vo0Sesion){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idSesion","tablaOrigen"=>"0Sesion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"datosSesion","tablaOrigen"=>"0Sesion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"0Sesion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"ultimoAcceso","tablaOrigen"=>"0Sesion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"datosAcceso","tablaOrigen"=>"0Sesion"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			if(!is_null($vo0Sesion->getIdSesion())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"idSesion", "tabla"=>"0Sesion", "valor"=>$vo0Sesion->getIdSesion()));}
			if(!is_null($vo0Sesion->getDatosSesion())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"datosSesion", "tabla"=>"0Sesion", "valor"=>$vo0Sesion->getDatosSesion()));}
			if(!is_null($vo0Sesion->getIdUsuario())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"idUsuario", "tabla"=>"0Sesion", "valor"=>$vo0Sesion->getIdUsuario()));}
			if(!is_null($vo0Sesion->getUltimoAcceso())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"ultimoAcceso", "tabla"=>"0Sesion", "valor"=>$vo0Sesion->getUltimoAcceso()));}
			if(!is_null($vo0Sesion->getDatosAcceso())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"datosAcceso", "tabla"=>"0Sesion", "valor"=>$vo0Sesion->getDatosAcceso()));}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdSesion($reg["idSesion"]);
					$objVO->setDatosSesion($reg["datosSesion"]);
					$objVO->setIdUsuario($reg["idUsuario"]);
					$objVO->setUltimoAcceso($reg["ultimoAcceso"]);
					$objVO->setDatosAcceso($reg["datosAcceso"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idSesion","tablaOrigen"=>"0Sesion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"datosSesion","tablaOrigen"=>"0Sesion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"0Sesion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"ultimoAcceso","tablaOrigen"=>"0Sesion"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"datosAcceso","tablaOrigen"=>"0Sesion"));
			$parametro = $consulta->addChild("Limitar");
			$parametro->addAttribute("regInicial", $n);
			$parametro->addAttribute("noRegistros", $m);
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO0Sesion")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdSesion(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idSesion","tabla"=>"0Sesion","valor"=>$condiciones->getIdSesion()));
							}
							if(strcmp($condiciones->getDatosSesion(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"datosSesion","tabla"=>"0Sesion","valor"=>$condiciones->getDatosSesion()));
							}
							if(strcmp($condiciones->getIdUsuario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idUsuario","tabla"=>"0Sesion","valor"=>$condiciones->getIdUsuario()));
							}
							if(strcmp($condiciones->getUltimoAcceso(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"ultimoAcceso","tabla"=>"0Sesion","valor"=>$condiciones->getUltimoAcceso()));
							}
							if(strcmp($condiciones->getDatosAcceso(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"datosAcceso","tabla"=>"0Sesion","valor"=>$condiciones->getDatosAcceso()));
							}
						}
					}
				}
			}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdSesion($reg["idSesion"]);
					$objVO->setDatosSesion($reg["datosSesion"]);
					$objVO->setIdUsuario($reg["idUsuario"]);
					$objVO->setUltimoAcceso($reg["ultimoAcceso"]);
					$objVO->setDatosAcceso($reg["datosAcceso"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idSesion","tablaOrigen"=>"0Sesion","valor"=>$registro->getIdSesion()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"datosSesion","tablaOrigen"=>"0Sesion","valor"=>$registro->getDatosSesion()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"0Sesion","valor"=>$registro->getIdUsuario()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"ultimoAcceso","tablaOrigen"=>"0Sesion","valor"=>$registro->getUltimoAcceso()));
				if (!is_null($registro->getDatosAcceso()))  ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"datosAcceso","tablaOrigen"=>"0Sesion","valor"=>$registro->getDatosAcceso()));
			if($this->db->insertar($consulta)){
				$registro->setIdSesion($this->db->ultimoId);
				return true;
			}else{
				return false;
			}
		}
		function actualizarRegistro($registro,$condiciones=null){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idSesion","tablaOrigen"=>"0Sesion","valor"=>$registro->getIdSesion()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"datosSesion","tablaOrigen"=>"0Sesion","valor"=>$registro->getDatosSesion()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"0Sesion","valor"=>$registro->getIdUsuario()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"ultimoAcceso","tablaOrigen"=>"0Sesion","valor"=>$registro->getUltimoAcceso()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"datosAcceso","tablaOrigen"=>"0Sesion","valor"=>$registro->getDatosAcceso()));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			if(is_array($condiciones) && count($condiciones)>0){
				foreach($condiciones as $campo=>$valor){
					ControlXML::agregarNodo($y,"Igual",array("campo"=>"$campo","tabla"=>"0Sesion","valor"=>$valor));
				}
			}else{
				ControlXML::agregarNodo($y,"Igual",array("campo"=>"idSesion","tabla"=>"0Sesion","valor"=>$registro->getIdSesion()));
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
			ControlXML::agregarNodo($consulta,"Campo",array("tablaOrigen"=>"0Sesion"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idSesion","tabla"=>"0Sesion","valor"=>$registro->getIdSesion()));
			if($this->db->eliminar($consulta)){
				return true;
			}else{
				return false;
			}
		}
	}
?>
