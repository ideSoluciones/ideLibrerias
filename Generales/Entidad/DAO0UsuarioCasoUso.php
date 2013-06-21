<?php
	class DAO0UsuarioCasoUso{
		private $db=null;
		function DAO0UsuarioCasoUso(){
			$sesion=Sesion::getInstancia();
			$this->db=$sesion->getDB();
		}
		function setDb($db){ $this->db=$db; }
		function crearVO() { 
			return new VO0UsuarioCasoUso();
		}
		public static function getTabla(){
			return '0UsuarioCasoUso';
		}
		function getRegistro($idCasoUso,$idUsuario) {
			$objVO=$this->crearVO();
			$objVO->setIdCasoUso($idCasoUso);
			$objVO->setIdUsuario($idUsuario);
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idCasoUso","tablaOrigen"=>"0UsuarioCasoUso"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"0UsuarioCasoUso"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"condiciones","tablaOrigen"=>"0UsuarioCasoUso"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idCasoUso","tabla"=>"0UsuarioCasoUso","valor"=>$idCasoUso));
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idUsuario","tabla"=>"0UsuarioCasoUso","valor"=>$idUsuario));
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				$objVO->setIdCasoUso($resultado[0]["idCasoUso"]);
				$objVO->setIdUsuario($resultado[0]["idUsuario"]);
				$objVO->setCondiciones($resultado[0]["condiciones"]);
			}else{
				throw new sinResultados("No se encontro el registro solicitado.");
			}
			return $objVO;
		}
		function getRegistroCondiciones($condiciones){
			$objVO=$this->crearVO();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"*","tablaOrigen"=>"0UsuarioCasoUso"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			foreach($condiciones as $nombre=>$valor){
				ControlXML::agregarNodo($y,"Igual",array("campo"=>$nombre,"tabla"=>"0UsuarioCasoUso","valor"=>$valor));
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"*","tablaOrigen"=>"0UsuarioCasoUso"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			foreach($condiciones as $nombre=>$valor){
				ControlXML::agregarNodo($y,"Igual",array("campo"=>$nombre,"tabla"=>"0UsuarioCasoUso","valor"=>$valor));
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idCasoUso","tablaOrigen"=>"0UsuarioCasoUso"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"0UsuarioCasoUso"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"condiciones","tablaOrigen"=>"0UsuarioCasoUso"));
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO0UsuarioCasoUso")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdCasoUso(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idCasoUso","tabla"=>"0UsuarioCasoUso","valor"=>$condiciones->getIdCasoUso()));
							}
							if(strcmp($condiciones->getIdUsuario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idUsuario","tabla"=>"0UsuarioCasoUso","valor"=>$condiciones->getIdUsuario()));
							}
							if(strcmp($condiciones->getCondiciones(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"condiciones","tabla"=>"0UsuarioCasoUso","valor"=>$condiciones->getCondiciones()));
							}
						}
					}
				}
			}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdCasoUso($reg["idCasoUso"]);
					$objVO->setIdUsuario($reg["idUsuario"]);
					$objVO->setCondiciones($reg["condiciones"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idCasoUso","tablaOrigen"=>"0UsuarioCasoUso"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"0UsuarioCasoUso"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"condiciones","tablaOrigen"=>"0UsuarioCasoUso"));
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO0UsuarioCasoUso")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdCasoUso(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idCasoUso","tabla"=>"0UsuarioCasoUso","valor"=>$condiciones->getIdCasoUso()));
							}
							if(strcmp($condiciones->getIdUsuario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idUsuario","tabla"=>"0UsuarioCasoUso","valor"=>$condiciones->getIdUsuario()));
							}
							if(strcmp($condiciones->getCondiciones(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"condiciones","tabla"=>"0UsuarioCasoUso","valor"=>$condiciones->getCondiciones()));
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
		function consultarRegistros($vo0UsuarioCasoUso){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idCasoUso","tablaOrigen"=>"0UsuarioCasoUso"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"0UsuarioCasoUso"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"condiciones","tablaOrigen"=>"0UsuarioCasoUso"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			if(!is_null($vo0UsuarioCasoUso->getIdCasoUso())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"idCasoUso", "tabla"=>"0UsuarioCasoUso", "valor"=>$vo0UsuarioCasoUso->getIdCasoUso()));}
			if(!is_null($vo0UsuarioCasoUso->getIdUsuario())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"idUsuario", "tabla"=>"0UsuarioCasoUso", "valor"=>$vo0UsuarioCasoUso->getIdUsuario()));}
			if(!is_null($vo0UsuarioCasoUso->getCondiciones())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"condiciones", "tabla"=>"0UsuarioCasoUso", "valor"=>$vo0UsuarioCasoUso->getCondiciones()));}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdCasoUso($reg["idCasoUso"]);
					$objVO->setIdUsuario($reg["idUsuario"]);
					$objVO->setCondiciones($reg["condiciones"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idCasoUso","tablaOrigen"=>"0UsuarioCasoUso"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"0UsuarioCasoUso"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"condiciones","tablaOrigen"=>"0UsuarioCasoUso"));
			$parametro = $consulta->addChild("Limitar");
			$parametro->addAttribute("regInicial", $n);
			$parametro->addAttribute("noRegistros", $m);
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO0UsuarioCasoUso")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdCasoUso(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idCasoUso","tabla"=>"0UsuarioCasoUso","valor"=>$condiciones->getIdCasoUso()));
							}
							if(strcmp($condiciones->getIdUsuario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idUsuario","tabla"=>"0UsuarioCasoUso","valor"=>$condiciones->getIdUsuario()));
							}
							if(strcmp($condiciones->getCondiciones(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"condiciones","tabla"=>"0UsuarioCasoUso","valor"=>$condiciones->getCondiciones()));
							}
						}
					}
				}
			}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdCasoUso($reg["idCasoUso"]);
					$objVO->setIdUsuario($reg["idUsuario"]);
					$objVO->setCondiciones($reg["condiciones"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idCasoUso","tablaOrigen"=>"0UsuarioCasoUso","valor"=>$registro->getIdCasoUso()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"0UsuarioCasoUso","valor"=>$registro->getIdUsuario()));
				if (!is_null($registro->getCondiciones()))  ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"condiciones","tablaOrigen"=>"0UsuarioCasoUso","valor"=>$registro->getCondiciones()));
			if($this->db->insertar($consulta)){
				$registro->setIdCasoUso($this->db->ultimoId);
					$registro->setIdUsuario($this->db->ultimoId);
				return true;
			}else{
				return false;
			}
		}
		function actualizarRegistro($registro,$condiciones=null){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idCasoUso","tablaOrigen"=>"0UsuarioCasoUso","valor"=>$registro->getIdCasoUso()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"0UsuarioCasoUso","valor"=>$registro->getIdUsuario()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"condiciones","tablaOrigen"=>"0UsuarioCasoUso","valor"=>$registro->getCondiciones()));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			if(is_array($condiciones) && count($condiciones)>0){
				foreach($condiciones as $campo=>$valor){
					ControlXML::agregarNodo($y,"Igual",array("campo"=>"$campo","tabla"=>"0UsuarioCasoUso","valor"=>$valor));
				}
			}else{
				ControlXML::agregarNodo($y,"Igual",array("campo"=>"idCasoUso","tabla"=>"0UsuarioCasoUso","valor"=>$registro->getIdCasoUso()));
				ControlXML::agregarNodo($y,"Igual",array("campo"=>"idUsuario","tabla"=>"0UsuarioCasoUso","valor"=>$registro->getIdUsuario()));
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
			ControlXML::agregarNodo($consulta,"Campo",array("tablaOrigen"=>"0UsuarioCasoUso"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idCasoUso","tabla"=>"0UsuarioCasoUso","valor"=>$registro->getIdCasoUso()));
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idUsuario","tabla"=>"0UsuarioCasoUso","valor"=>$registro->getIdUsuario()));
			if($this->db->eliminar($consulta)){
				return true;
			}else{
				return false;
			}
		}
	}
?>