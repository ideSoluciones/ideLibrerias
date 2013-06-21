<?php
	class DAO0RolCasoUso{
		private $db=null;
		function DAO0RolCasoUso(){
			$sesion=Sesion::getInstancia();
			$this->db=$sesion->getDB();
		}
		function setDb($db){ $this->db=$db; }
		function crearVO() { 
			return new VO0RolCasoUso();
		}
		public static function getTabla(){
			return '0RolCasoUso';
		}
		function getRegistro($idRol,$idCasoUso) {
			$objVO=$this->crearVO();
			$objVO->setIdRol($idRol);
			$objVO->setIdCasoUso($idCasoUso);
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idRol","tablaOrigen"=>"0RolCasoUso"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idCasoUso","tablaOrigen"=>"0RolCasoUso"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"condiciones","tablaOrigen"=>"0RolCasoUso"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idRol","tabla"=>"0RolCasoUso","valor"=>$idRol));
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idCasoUso","tabla"=>"0RolCasoUso","valor"=>$idCasoUso));
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				$objVO->setIdRol($resultado[0]["idRol"]);
				$objVO->setIdCasoUso($resultado[0]["idCasoUso"]);
				$objVO->setCondiciones($resultado[0]["condiciones"]);
			}else{
				throw new sinResultados("No se encontro el registro solicitado.");
			}
			return $objVO;
		}
		function getRegistroCondiciones($condiciones){
			$objVO=$this->crearVO();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"*","tablaOrigen"=>"0RolCasoUso"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			foreach($condiciones as $nombre=>$valor){
				ControlXML::agregarNodo($y,"Igual",array("campo"=>$nombre,"tabla"=>"0RolCasoUso","valor"=>$valor));
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"*","tablaOrigen"=>"0RolCasoUso"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			foreach($condiciones as $nombre=>$valor){
				ControlXML::agregarNodo($y,"Igual",array("campo"=>$nombre,"tabla"=>"0RolCasoUso","valor"=>$valor));
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idRol","tablaOrigen"=>"0RolCasoUso"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idCasoUso","tablaOrigen"=>"0RolCasoUso"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"condiciones","tablaOrigen"=>"0RolCasoUso"));
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO0RolCasoUso")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdRol(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idRol","tabla"=>"0RolCasoUso","valor"=>$condiciones->getIdRol()));
							}
							if(strcmp($condiciones->getIdCasoUso(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idCasoUso","tabla"=>"0RolCasoUso","valor"=>$condiciones->getIdCasoUso()));
							}
							if(strcmp($condiciones->getCondiciones(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"condiciones","tabla"=>"0RolCasoUso","valor"=>$condiciones->getCondiciones()));
							}
						}
					}
				}
			}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdRol($reg["idRol"]);
					$objVO->setIdCasoUso($reg["idCasoUso"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idRol","tablaOrigen"=>"0RolCasoUso"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idCasoUso","tablaOrigen"=>"0RolCasoUso"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"condiciones","tablaOrigen"=>"0RolCasoUso"));
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO0RolCasoUso")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdRol(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idRol","tabla"=>"0RolCasoUso","valor"=>$condiciones->getIdRol()));
							}
							if(strcmp($condiciones->getIdCasoUso(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idCasoUso","tabla"=>"0RolCasoUso","valor"=>$condiciones->getIdCasoUso()));
							}
							if(strcmp($condiciones->getCondiciones(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"condiciones","tabla"=>"0RolCasoUso","valor"=>$condiciones->getCondiciones()));
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
		function consultarRegistros($vo0RolCasoUso){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idRol","tablaOrigen"=>"0RolCasoUso"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idCasoUso","tablaOrigen"=>"0RolCasoUso"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"condiciones","tablaOrigen"=>"0RolCasoUso"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			if(!is_null($vo0RolCasoUso->getIdRol())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"idRol", "tabla"=>"0RolCasoUso", "valor"=>$vo0RolCasoUso->getIdRol()));}
			if(!is_null($vo0RolCasoUso->getIdCasoUso())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"idCasoUso", "tabla"=>"0RolCasoUso", "valor"=>$vo0RolCasoUso->getIdCasoUso()));}
			if(!is_null($vo0RolCasoUso->getCondiciones())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"condiciones", "tabla"=>"0RolCasoUso", "valor"=>$vo0RolCasoUso->getCondiciones()));}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdRol($reg["idRol"]);
					$objVO->setIdCasoUso($reg["idCasoUso"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idRol","tablaOrigen"=>"0RolCasoUso"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idCasoUso","tablaOrigen"=>"0RolCasoUso"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"condiciones","tablaOrigen"=>"0RolCasoUso"));
			$parametro = $consulta->addChild("Limitar");
			$parametro->addAttribute("regInicial", $n);
			$parametro->addAttribute("noRegistros", $m);
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO0RolCasoUso")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdRol(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idRol","tabla"=>"0RolCasoUso","valor"=>$condiciones->getIdRol()));
							}
							if(strcmp($condiciones->getIdCasoUso(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idCasoUso","tabla"=>"0RolCasoUso","valor"=>$condiciones->getIdCasoUso()));
							}
							if(strcmp($condiciones->getCondiciones(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"condiciones","tabla"=>"0RolCasoUso","valor"=>$condiciones->getCondiciones()));
							}
						}
					}
				}
			}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdRol($reg["idRol"]);
					$objVO->setIdCasoUso($reg["idCasoUso"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idRol","tablaOrigen"=>"0RolCasoUso","valor"=>$registro->getIdRol()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idCasoUso","tablaOrigen"=>"0RolCasoUso","valor"=>$registro->getIdCasoUso()));
				if (!is_null($registro->getCondiciones()))  ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"condiciones","tablaOrigen"=>"0RolCasoUso","valor"=>$registro->getCondiciones()));
			if($this->db->insertar($consulta)){
				$registro->setIdRol($this->db->ultimoId);
					$registro->setIdCasoUso($this->db->ultimoId);
				return true;
			}else{
				return false;
			}
		}
		function actualizarRegistro($registro,$condiciones=null){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idRol","tablaOrigen"=>"0RolCasoUso","valor"=>$registro->getIdRol()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idCasoUso","tablaOrigen"=>"0RolCasoUso","valor"=>$registro->getIdCasoUso()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"condiciones","tablaOrigen"=>"0RolCasoUso","valor"=>$registro->getCondiciones()));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			if(is_array($condiciones) && count($condiciones)>0){
				foreach($condiciones as $campo=>$valor){
					ControlXML::agregarNodo($y,"Igual",array("campo"=>"$campo","tabla"=>"0RolCasoUso","valor"=>$valor));
				}
			}else{
				ControlXML::agregarNodo($y,"Igual",array("campo"=>"idRol","tabla"=>"0RolCasoUso","valor"=>$registro->getIdRol()));
				ControlXML::agregarNodo($y,"Igual",array("campo"=>"idCasoUso","tabla"=>"0RolCasoUso","valor"=>$registro->getIdCasoUso()));
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
			ControlXML::agregarNodo($consulta,"Campo",array("tablaOrigen"=>"0RolCasoUso"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idRol","tabla"=>"0RolCasoUso","valor"=>$registro->getIdRol()));
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idCasoUso","tabla"=>"0RolCasoUso","valor"=>$registro->getIdCasoUso()));
			if($this->db->eliminar($consulta)){
				return true;
			}else{
				return false;
			}
		}
	}
?>