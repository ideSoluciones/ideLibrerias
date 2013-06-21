<?php
	class DAO0Rol{
		private $db=null;
		function DAO0Rol(){
			$sesion=Sesion::getInstancia();
			$this->db=$sesion->getDB();
		}
		function setDb($db){ $this->db=$db; }
		function crearVO() { 
			return new VO0Rol();
		}
		public static function getTabla(){
			return '0Rol';
		}
		function getRegistro($idRol) {
			$objVO=$this->crearVO();
			$objVO->setIdRol($idRol);
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idRol","tablaOrigen"=>"0Rol"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombreRol","tablaOrigen"=>"0Rol"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idRol","tabla"=>"0Rol","valor"=>$idRol));
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				$objVO->setIdRol($resultado[0]["idRol"]);
				$objVO->setNombreRol($resultado[0]["nombreRol"]);
			}else{
				throw new sinResultados("No se encontro el registro solicitado.");
			}
			return $objVO;
		}
		function getRegistroCondiciones($condiciones){
			$objVO=$this->crearVO();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"*","tablaOrigen"=>"0Rol"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			foreach($condiciones as $nombre=>$valor){
				ControlXML::agregarNodo($y,"Igual",array("campo"=>$nombre,"tabla"=>"0Rol","valor"=>$valor));
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"*","tablaOrigen"=>"0Rol"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			foreach($condiciones as $nombre=>$valor){
				ControlXML::agregarNodo($y,"Igual",array("campo"=>$nombre,"tabla"=>"0Rol","valor"=>$valor));
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idRol","tablaOrigen"=>"0Rol"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombreRol","tablaOrigen"=>"0Rol"));
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO0Rol")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdRol(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idRol","tabla"=>"0Rol","valor"=>$condiciones->getIdRol()));
							}
							if(strcmp($condiciones->getNombreRol(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"nombreRol","tabla"=>"0Rol","valor"=>$condiciones->getNombreRol()));
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
					$objVO->setNombreRol($reg["nombreRol"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idRol","tablaOrigen"=>"0Rol"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombreRol","tablaOrigen"=>"0Rol"));
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO0Rol")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdRol(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idRol","tabla"=>"0Rol","valor"=>$condiciones->getIdRol()));
							}
							if(strcmp($condiciones->getNombreRol(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"nombreRol","tabla"=>"0Rol","valor"=>$condiciones->getNombreRol()));
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
		function consultarRegistros($vo0Rol){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idRol","tablaOrigen"=>"0Rol"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombreRol","tablaOrigen"=>"0Rol"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			if(!is_null($vo0Rol->getIdRol())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"idRol", "tabla"=>"0Rol", "valor"=>$vo0Rol->getIdRol()));}
			if(!is_null($vo0Rol->getNombreRol())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"nombreRol", "tabla"=>"0Rol", "valor"=>$vo0Rol->getNombreRol()));}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdRol($reg["idRol"]);
					$objVO->setNombreRol($reg["nombreRol"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idRol","tablaOrigen"=>"0Rol"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombreRol","tablaOrigen"=>"0Rol"));
			$parametro = $consulta->addChild("Limitar");
			$parametro->addAttribute("regInicial", $n);
			$parametro->addAttribute("noRegistros", $m);
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO0Rol")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdRol(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idRol","tabla"=>"0Rol","valor"=>$condiciones->getIdRol()));
							}
							if(strcmp($condiciones->getNombreRol(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"nombreRol","tabla"=>"0Rol","valor"=>$condiciones->getNombreRol()));
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
					$objVO->setNombreRol($reg["nombreRol"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idRol","tablaOrigen"=>"0Rol","valor"=>$registro->getIdRol()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombreRol","tablaOrigen"=>"0Rol","valor"=>$registro->getNombreRol()));
			if($this->db->insertar($consulta)){
				$registro->setIdRol($this->db->ultimoId);
				return true;
			}else{
				return false;
			}
		}
		function actualizarRegistro($registro,$condiciones=null){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idRol","tablaOrigen"=>"0Rol","valor"=>$registro->getIdRol()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombreRol","tablaOrigen"=>"0Rol","valor"=>$registro->getNombreRol()));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			if(is_array($condiciones) && count($condiciones)>0){
				foreach($condiciones as $campo=>$valor){
					ControlXML::agregarNodo($y,"Igual",array("campo"=>"$campo","tabla"=>"0Rol","valor"=>$valor));
				}
			}else{
				ControlXML::agregarNodo($y,"Igual",array("campo"=>"idRol","tabla"=>"0Rol","valor"=>$registro->getIdRol()));
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
			ControlXML::agregarNodo($consulta,"Campo",array("tablaOrigen"=>"0Rol"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idRol","tabla"=>"0Rol","valor"=>$registro->getIdRol()));
			if($this->db->eliminar($consulta)){
				return true;
			}else{
				return false;
			}
		}
	}
?>