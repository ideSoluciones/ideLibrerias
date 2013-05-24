<?php
	class DAO0CasoUso{
		private $db=null;
		function DAO0CasoUso($db){
			$this->db=$db;
		}
		function setDb($db){ $this->db=$db; }
		function crearVO() { 
			return new VO0CasoUso();
		}
		public static function getTabla(){
			return '0CasoUso';
		}
		function getRegistro($idCasoUso) {
			$objVO=$this->crearVO();
			$objVO->setIdCasoUso($idCasoUso);
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idCasoUso","tablaOrigen"=>"0CasoUso"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idPaquete","tablaOrigen"=>"0CasoUso"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombreCasoUso","tablaOrigen"=>"0CasoUso"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idCasoUso","tabla"=>"0CasoUso","valor"=>$idCasoUso));
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				$objVO->setIdCasoUso($resultado[0]["idCasoUso"]);
				$objVO->setIdPaquete($resultado[0]["idPaquete"]);
				$objVO->setNombreCasoUso($resultado[0]["nombreCasoUso"]);
			}else{
				throw new sinResultados("No se encontro el registro solicitado.");
			}
			return $objVO;
		}
		function getRegistroCondiciones($condiciones){
			$objVO=$this->crearVO();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"*","tablaOrigen"=>"0CasoUso"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			foreach($condiciones as $nombre=>$valor){
				ControlXML::agregarNodo($y,"Igual",array("campo"=>$nombre,"tabla"=>"0CasoUso","valor"=>$valor));
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"*","tablaOrigen"=>"0CasoUso"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			foreach($condiciones as $nombre=>$valor){
				ControlXML::agregarNodo($y,"Igual",array("campo"=>$nombre,"tabla"=>"0CasoUso","valor"=>$valor));
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idCasoUso","tablaOrigen"=>"0CasoUso"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idPaquete","tablaOrigen"=>"0CasoUso"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombreCasoUso","tablaOrigen"=>"0CasoUso"));
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO0CasoUso")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdCasoUso(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idCasoUso","tabla"=>"0CasoUso","valor"=>$condiciones->getIdCasoUso()));
							}
							if(strcmp($condiciones->getIdPaquete(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idPaquete","tabla"=>"0CasoUso","valor"=>$condiciones->getIdPaquete()));
							}
							if(strcmp($condiciones->getNombreCasoUso(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"nombreCasoUso","tabla"=>"0CasoUso","valor"=>$condiciones->getNombreCasoUso()));
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
					$objVO->setIdPaquete($reg["idPaquete"]);
					$objVO->setNombreCasoUso($reg["nombreCasoUso"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idCasoUso","tablaOrigen"=>"0CasoUso"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idPaquete","tablaOrigen"=>"0CasoUso"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombreCasoUso","tablaOrigen"=>"0CasoUso"));
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO0CasoUso")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdCasoUso(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idCasoUso","tabla"=>"0CasoUso","valor"=>$condiciones->getIdCasoUso()));
							}
							if(strcmp($condiciones->getIdPaquete(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idPaquete","tabla"=>"0CasoUso","valor"=>$condiciones->getIdPaquete()));
							}
							if(strcmp($condiciones->getNombreCasoUso(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"nombreCasoUso","tabla"=>"0CasoUso","valor"=>$condiciones->getNombreCasoUso()));
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
		function consultarRegistros($vo0CasoUso){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idCasoUso","tablaOrigen"=>"0CasoUso"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idPaquete","tablaOrigen"=>"0CasoUso"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombreCasoUso","tablaOrigen"=>"0CasoUso"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			if(!is_null($vo0CasoUso->getIdCasoUso())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"idCasoUso", "tabla"=>"0CasoUso", "valor"=>$vo0CasoUso->getIdCasoUso()));}
			if(!is_null($vo0CasoUso->getIdPaquete())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"idPaquete", "tabla"=>"0CasoUso", "valor"=>$vo0CasoUso->getIdPaquete()));}
			if(!is_null($vo0CasoUso->getNombreCasoUso())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"nombreCasoUso", "tabla"=>"0CasoUso", "valor"=>$vo0CasoUso->getNombreCasoUso()));}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdCasoUso($reg["idCasoUso"]);
					$objVO->setIdPaquete($reg["idPaquete"]);
					$objVO->setNombreCasoUso($reg["nombreCasoUso"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idCasoUso","tablaOrigen"=>"0CasoUso"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idPaquete","tablaOrigen"=>"0CasoUso"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombreCasoUso","tablaOrigen"=>"0CasoUso"));
			$parametro = $consulta->addChild("Limitar");
			$parametro->addAttribute("regInicial", $n);
			$parametro->addAttribute("noRegistros", $m);
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO0CasoUso")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdCasoUso(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idCasoUso","tabla"=>"0CasoUso","valor"=>$condiciones->getIdCasoUso()));
							}
							if(strcmp($condiciones->getIdPaquete(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idPaquete","tabla"=>"0CasoUso","valor"=>$condiciones->getIdPaquete()));
							}
							if(strcmp($condiciones->getNombreCasoUso(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"nombreCasoUso","tabla"=>"0CasoUso","valor"=>$condiciones->getNombreCasoUso()));
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
					$objVO->setIdPaquete($reg["idPaquete"]);
					$objVO->setNombreCasoUso($reg["nombreCasoUso"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idCasoUso","tablaOrigen"=>"0CasoUso","valor"=>$registro->getIdCasoUso()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idPaquete","tablaOrigen"=>"0CasoUso","valor"=>$registro->getIdPaquete()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombreCasoUso","tablaOrigen"=>"0CasoUso","valor"=>$registro->getNombreCasoUso()));
			if($this->db->insertar($consulta)){
				$registro->setIdCasoUso($this->db->ultimoId);
				return true;
			}else{
				return false;
			}
		}
		function actualizarRegistro($registro){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idCasoUso","tablaOrigen"=>"0CasoUso","valor"=>$registro->getIdCasoUso()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idPaquete","tablaOrigen"=>"0CasoUso","valor"=>$registro->getIdPaquete()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombreCasoUso","tablaOrigen"=>"0CasoUso","valor"=>$registro->getNombreCasoUso()));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idCasoUso","tabla"=>"0CasoUso","valor"=>$registro->getIdCasoUso()));
			if($this->db->actualizar($consulta)){
				return true;
			}else{
				return false;
			}
		}
		function eliminarRegistro($registro){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("tablaOrigen"=>"0CasoUso"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idCasoUso","tabla"=>"0CasoUso","valor"=>$registro->getIdCasoUso()));
			if($this->db->eliminar($consulta)){
				return true;
			}else{
				return false;
			}
		}
	}
?>