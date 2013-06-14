<?php
	class DAO0Paquete{
		private $db=null;
		function DAO0Paquete(){
			$sesion=Sesion::getInstancia();
			$this->db=$sesion->getDB();
		}
		function setDb($db){ $this->db=$db; }
		function crearVO() { 
			return new VO0Paquete();
		}
		public static function getTabla(){
			return '0Paquete';
		}
		function getRegistro($idPaquete) {
			$objVO=$this->crearVO();
			$objVO->setIdPaquete($idPaquete);
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idPaquete","tablaOrigen"=>"0Paquete"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombrePaquete","tablaOrigen"=>"0Paquete"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idPaquete","tabla"=>"0Paquete","valor"=>$idPaquete));
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				$objVO->setIdPaquete($resultado[0]["idPaquete"]);
				$objVO->setNombrePaquete($resultado[0]["nombrePaquete"]);
			}else{
				throw new sinResultados("No se encontro el registro solicitado.");
			}
			return $objVO;
		}
		function getRegistroCondiciones($condiciones){
			$objVO=$this->crearVO();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"*","tablaOrigen"=>"0Paquete"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			foreach($condiciones as $nombre=>$valor){
				ControlXML::agregarNodo($y,"Igual",array("campo"=>$nombre,"tabla"=>"0Paquete","valor"=>$valor));
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"*","tablaOrigen"=>"0Paquete"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			foreach($condiciones as $nombre=>$valor){
				ControlXML::agregarNodo($y,"Igual",array("campo"=>$nombre,"tabla"=>"0Paquete","valor"=>$valor));
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idPaquete","tablaOrigen"=>"0Paquete"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombrePaquete","tablaOrigen"=>"0Paquete"));
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO0Paquete")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdPaquete(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idPaquete","tabla"=>"0Paquete","valor"=>$condiciones->getIdPaquete()));
							}
							if(strcmp($condiciones->getNombrePaquete(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"nombrePaquete","tabla"=>"0Paquete","valor"=>$condiciones->getNombrePaquete()));
							}
						}
					}
				}
			}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdPaquete($reg["idPaquete"]);
					$objVO->setNombrePaquete($reg["nombrePaquete"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idPaquete","tablaOrigen"=>"0Paquete"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombrePaquete","tablaOrigen"=>"0Paquete"));
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO0Paquete")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdPaquete(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idPaquete","tabla"=>"0Paquete","valor"=>$condiciones->getIdPaquete()));
							}
							if(strcmp($condiciones->getNombrePaquete(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"nombrePaquete","tabla"=>"0Paquete","valor"=>$condiciones->getNombrePaquete()));
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
		function consultarRegistros($vo0Paquete){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idPaquete","tablaOrigen"=>"0Paquete"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombrePaquete","tablaOrigen"=>"0Paquete"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			if(!is_null($vo0Paquete->getIdPaquete())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"idPaquete", "tabla"=>"0Paquete", "valor"=>$vo0Paquete->getIdPaquete()));}
			if(!is_null($vo0Paquete->getNombrePaquete())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"nombrePaquete", "tabla"=>"0Paquete", "valor"=>$vo0Paquete->getNombrePaquete()));}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdPaquete($reg["idPaquete"]);
					$objVO->setNombrePaquete($reg["nombrePaquete"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idPaquete","tablaOrigen"=>"0Paquete"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombrePaquete","tablaOrigen"=>"0Paquete"));
			$parametro = $consulta->addChild("Limitar");
			$parametro->addAttribute("regInicial", $n);
			$parametro->addAttribute("noRegistros", $m);
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO0Paquete")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdPaquete(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idPaquete","tabla"=>"0Paquete","valor"=>$condiciones->getIdPaquete()));
							}
							if(strcmp($condiciones->getNombrePaquete(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"nombrePaquete","tabla"=>"0Paquete","valor"=>$condiciones->getNombrePaquete()));
							}
						}
					}
				}
			}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdPaquete($reg["idPaquete"]);
					$objVO->setNombrePaquete($reg["nombrePaquete"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idPaquete","tablaOrigen"=>"0Paquete","valor"=>$registro->getIdPaquete()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombrePaquete","tablaOrigen"=>"0Paquete","valor"=>$registro->getNombrePaquete()));
			if($this->db->insertar($consulta)){
				$registro->setIdPaquete($this->db->ultimoId);
				return true;
			}else{
				return false;
			}
		}
		function actualizarRegistro($registro,$condiciones=null){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idPaquete","tablaOrigen"=>"0Paquete","valor"=>$registro->getIdPaquete()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombrePaquete","tablaOrigen"=>"0Paquete","valor"=>$registro->getNombrePaquete()));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			if(is_array($condiciones) && count($condiciones)>0){
				foreach($condiciones as $campo=>$valor){
					ControlXML::agregarNodo($y,"Igual",array("campo"=>"$campo","tabla"=>"0Paquete","valor"=>$valor));
				}
			}else{
				ControlXML::agregarNodo($y,"Igual",array("campo"=>"idPaquete","tabla"=>"0Paquete","valor"=>$registro->getIdPaquete()));
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
			ControlXML::agregarNodo($consulta,"Campo",array("tablaOrigen"=>"0Paquete"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idPaquete","tabla"=>"0Paquete","valor"=>$registro->getIdPaquete()));
			if($this->db->eliminar($consulta)){
				return true;
			}else{
				return false;
			}
		}
	}
?>