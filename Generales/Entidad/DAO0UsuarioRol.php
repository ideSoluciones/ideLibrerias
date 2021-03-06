<?php
	class DAO0UsuarioRol{
		private $db=null;
		function DAO0UsuarioRol(){
			$sesion=Sesion::getInstancia();
			$this->db=$sesion->getDB();
		}
		function setDb($db){ $this->db=$db; }
		function crearVO() { 
			return new VO0UsuarioRol();
		}
		public static function getTabla(){
			return '0UsuarioRol';
		}
		function getRegistro($idUsuario,$idRol) {
			$objVO=$this->crearVO();
			$objVO->setIdUsuario($idUsuario);
			$objVO->setIdRol($idRol);
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"0UsuarioRol"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idRol","tablaOrigen"=>"0UsuarioRol"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idUsuario","tabla"=>"0UsuarioRol","valor"=>$idUsuario));
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idRol","tabla"=>"0UsuarioRol","valor"=>$idRol));
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				$objVO->setIdUsuario($resultado[0]["idUsuario"]);
				$objVO->setIdRol($resultado[0]["idRol"]);
			}else{
				throw new sinResultados("No se encontro el registro solicitado.");
			}
			return $objVO;
		}
		function getRegistroCondiciones($condiciones){
			$objVO=$this->crearVO();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"*","tablaOrigen"=>"0UsuarioRol"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			foreach($condiciones as $nombre=>$valor){
				ControlXML::agregarNodo($y,"Igual",array("campo"=>$nombre,"tabla"=>"0UsuarioRol","valor"=>$valor));
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"*","tablaOrigen"=>"0UsuarioRol"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			foreach($condiciones as $nombre=>$valor){
				ControlXML::agregarNodo($y,"Igual",array("campo"=>$nombre,"tabla"=>"0UsuarioRol","valor"=>$valor));
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"0UsuarioRol"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idRol","tablaOrigen"=>"0UsuarioRol"));
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO0UsuarioRol")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdUsuario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idUsuario","tabla"=>"0UsuarioRol","valor"=>$condiciones->getIdUsuario()));
							}
							if(strcmp($condiciones->getIdRol(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idRol","tabla"=>"0UsuarioRol","valor"=>$condiciones->getIdRol()));
							}
						}
					}
				}
			}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdUsuario($reg["idUsuario"]);
					$objVO->setIdRol($reg["idRol"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"0UsuarioRol"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idRol","tablaOrigen"=>"0UsuarioRol"));
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO0UsuarioRol")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdUsuario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idUsuario","tabla"=>"0UsuarioRol","valor"=>$condiciones->getIdUsuario()));
							}
							if(strcmp($condiciones->getIdRol(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idRol","tabla"=>"0UsuarioRol","valor"=>$condiciones->getIdRol()));
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
		function consultarRegistros($vo0UsuarioRol){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"0UsuarioRol"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idRol","tablaOrigen"=>"0UsuarioRol"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			if(!is_null($vo0UsuarioRol->getIdUsuario())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"idUsuario", "tabla"=>"0UsuarioRol", "valor"=>$vo0UsuarioRol->getIdUsuario()));}
			if(!is_null($vo0UsuarioRol->getIdRol())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"idRol", "tabla"=>"0UsuarioRol", "valor"=>$vo0UsuarioRol->getIdRol()));}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdUsuario($reg["idUsuario"]);
					$objVO->setIdRol($reg["idRol"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"0UsuarioRol"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idRol","tablaOrigen"=>"0UsuarioRol"));
			$parametro = $consulta->addChild("Limitar");
			$parametro->addAttribute("regInicial", $n);
			$parametro->addAttribute("noRegistros", $m);
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO0UsuarioRol")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdUsuario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idUsuario","tabla"=>"0UsuarioRol","valor"=>$condiciones->getIdUsuario()));
							}
							if(strcmp($condiciones->getIdRol(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idRol","tabla"=>"0UsuarioRol","valor"=>$condiciones->getIdRol()));
							}
						}
					}
				}
			}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdUsuario($reg["idUsuario"]);
					$objVO->setIdRol($reg["idRol"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"0UsuarioRol","valor"=>$registro->getIdUsuario()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idRol","tablaOrigen"=>"0UsuarioRol","valor"=>$registro->getIdRol()));
			if($this->db->insertar($consulta)){
				$registro->setIdUsuario($this->db->ultimoId);
					$registro->setIdRol($this->db->ultimoId);
				return true;
			}else{
				return false;
			}
		}
		function actualizarRegistro($registro,$condiciones=null){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idUsuario","tablaOrigen"=>"0UsuarioRol","valor"=>$registro->getIdUsuario()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idRol","tablaOrigen"=>"0UsuarioRol","valor"=>$registro->getIdRol()));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			if(is_array($condiciones) && count($condiciones)>0){
				foreach($condiciones as $campo=>$valor){
					ControlXML::agregarNodo($y,"Igual",array("campo"=>"$campo","tabla"=>"0UsuarioRol","valor"=>$valor));
				}
			}else{
				ControlXML::agregarNodo($y,"Igual",array("campo"=>"idUsuario","tabla"=>"0UsuarioRol","valor"=>$registro->getIdUsuario()));
				ControlXML::agregarNodo($y,"Igual",array("campo"=>"idRol","tabla"=>"0UsuarioRol","valor"=>$registro->getIdRol()));
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
			ControlXML::agregarNodo($consulta,"Campo",array("tablaOrigen"=>"0UsuarioRol"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idUsuario","tabla"=>"0UsuarioRol","valor"=>$registro->getIdUsuario()));
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idRol","tabla"=>"0UsuarioRol","valor"=>$registro->getIdRol()));
			if($this->db->eliminar($consulta)){
				return true;
			}else{
				return false;
			}
		}
	}
?>