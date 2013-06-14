<?php
	class DAO1Formulario{
		private $db=null;
		function DAO1Formulario(){
			$sesion=Sesion::getInstancia();
			$this->db=$sesion->getDB();
		}
		function setDb($db){ $this->db=$db; }
		function crearVO() { 
			return new VO1Formulario();
		}
		public static function getTabla(){
			return '1Formulario';
		}
		function getRegistro($idFormulario) {
			$objVO=$this->crearVO();
			$objVO->setIdFormulario($idFormulario);
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idFormulario","tablaOrigen"=>"1Formulario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombreFormulario","tablaOrigen"=>"1Formulario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"propiedadesFormulario","tablaOrigen"=>"1Formulario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"camposFormulario","tablaOrigen"=>"1Formulario"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idFormulario","tabla"=>"1Formulario","valor"=>$idFormulario));
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				$objVO->setIdFormulario($resultado[0]["idFormulario"]);
				$objVO->setNombreFormulario($resultado[0]["nombreFormulario"]);
				$objVO->setPropiedadesFormulario($resultado[0]["propiedadesFormulario"]);
				$objVO->setCamposFormulario($resultado[0]["camposFormulario"]);
			}else{
				throw new sinResultados("No se encontro el registro solicitado.");
			}
			return $objVO;
		}
		function getRegistroCondiciones($condiciones){
			$objVO=$this->crearVO();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"*","tablaOrigen"=>"1Formulario"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			foreach($condiciones as $nombre=>$valor){
				ControlXML::agregarNodo($y,"Igual",array("campo"=>$nombre,"tabla"=>"1Formulario","valor"=>$valor));
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"*","tablaOrigen"=>"1Formulario"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			foreach($condiciones as $nombre=>$valor){
				ControlXML::agregarNodo($y,"Igual",array("campo"=>$nombre,"tabla"=>"1Formulario","valor"=>$valor));
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idFormulario","tablaOrigen"=>"1Formulario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombreFormulario","tablaOrigen"=>"1Formulario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"propiedadesFormulario","tablaOrigen"=>"1Formulario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"camposFormulario","tablaOrigen"=>"1Formulario"));
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO1Formulario")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdFormulario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idFormulario","tabla"=>"1Formulario","valor"=>$condiciones->getIdFormulario()));
							}
							if(strcmp($condiciones->getNombreFormulario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"nombreFormulario","tabla"=>"1Formulario","valor"=>$condiciones->getNombreFormulario()));
							}
							if(strcmp($condiciones->getPropiedadesFormulario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"propiedadesFormulario","tabla"=>"1Formulario","valor"=>$condiciones->getPropiedadesFormulario()));
							}
							if(strcmp($condiciones->getCamposFormulario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"camposFormulario","tabla"=>"1Formulario","valor"=>$condiciones->getCamposFormulario()));
							}
						}
					}
				}
			}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdFormulario($reg["idFormulario"]);
					$objVO->setNombreFormulario($reg["nombreFormulario"]);
					$objVO->setPropiedadesFormulario($reg["propiedadesFormulario"]);
					$objVO->setCamposFormulario($reg["camposFormulario"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idFormulario","tablaOrigen"=>"1Formulario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombreFormulario","tablaOrigen"=>"1Formulario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"propiedadesFormulario","tablaOrigen"=>"1Formulario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"camposFormulario","tablaOrigen"=>"1Formulario"));
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO1Formulario")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdFormulario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idFormulario","tabla"=>"1Formulario","valor"=>$condiciones->getIdFormulario()));
							}
							if(strcmp($condiciones->getNombreFormulario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"nombreFormulario","tabla"=>"1Formulario","valor"=>$condiciones->getNombreFormulario()));
							}
							if(strcmp($condiciones->getPropiedadesFormulario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"propiedadesFormulario","tabla"=>"1Formulario","valor"=>$condiciones->getPropiedadesFormulario()));
							}
							if(strcmp($condiciones->getCamposFormulario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"camposFormulario","tabla"=>"1Formulario","valor"=>$condiciones->getCamposFormulario()));
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
		function consultarRegistros($vo1Formulario){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idFormulario","tablaOrigen"=>"1Formulario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombreFormulario","tablaOrigen"=>"1Formulario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"propiedadesFormulario","tablaOrigen"=>"1Formulario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"camposFormulario","tablaOrigen"=>"1Formulario"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			if(!is_null($vo1Formulario->getIdFormulario())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"idFormulario", "tabla"=>"1Formulario", "valor"=>$vo1Formulario->getIdFormulario()));}
			if(!is_null($vo1Formulario->getNombreFormulario())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"nombreFormulario", "tabla"=>"1Formulario", "valor"=>$vo1Formulario->getNombreFormulario()));}
			if(!is_null($vo1Formulario->getPropiedadesFormulario())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"propiedadesFormulario", "tabla"=>"1Formulario", "valor"=>$vo1Formulario->getPropiedadesFormulario()));}
			if(!is_null($vo1Formulario->getCamposFormulario())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"camposFormulario", "tabla"=>"1Formulario", "valor"=>$vo1Formulario->getCamposFormulario()));}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdFormulario($reg["idFormulario"]);
					$objVO->setNombreFormulario($reg["nombreFormulario"]);
					$objVO->setPropiedadesFormulario($reg["propiedadesFormulario"]);
					$objVO->setCamposFormulario($reg["camposFormulario"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idFormulario","tablaOrigen"=>"1Formulario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombreFormulario","tablaOrigen"=>"1Formulario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"propiedadesFormulario","tablaOrigen"=>"1Formulario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"camposFormulario","tablaOrigen"=>"1Formulario"));
			$parametro = $consulta->addChild("Limitar");
			$parametro->addAttribute("regInicial", $n);
			$parametro->addAttribute("noRegistros", $m);
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO1Formulario")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdFormulario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idFormulario","tabla"=>"1Formulario","valor"=>$condiciones->getIdFormulario()));
							}
							if(strcmp($condiciones->getNombreFormulario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"nombreFormulario","tabla"=>"1Formulario","valor"=>$condiciones->getNombreFormulario()));
							}
							if(strcmp($condiciones->getPropiedadesFormulario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"propiedadesFormulario","tabla"=>"1Formulario","valor"=>$condiciones->getPropiedadesFormulario()));
							}
							if(strcmp($condiciones->getCamposFormulario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"camposFormulario","tabla"=>"1Formulario","valor"=>$condiciones->getCamposFormulario()));
							}
						}
					}
				}
			}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdFormulario($reg["idFormulario"]);
					$objVO->setNombreFormulario($reg["nombreFormulario"]);
					$objVO->setPropiedadesFormulario($reg["propiedadesFormulario"]);
					$objVO->setCamposFormulario($reg["camposFormulario"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idFormulario","tablaOrigen"=>"1Formulario","valor"=>$registro->getIdFormulario()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombreFormulario","tablaOrigen"=>"1Formulario","valor"=>$registro->getNombreFormulario()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"propiedadesFormulario","tablaOrigen"=>"1Formulario","valor"=>$registro->getPropiedadesFormulario()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"camposFormulario","tablaOrigen"=>"1Formulario","valor"=>$registro->getCamposFormulario()));
			if($this->db->insertar($consulta)){
				$registro->setIdFormulario($this->db->ultimoId);
				return true;
			}else{
				return false;
			}
		}
		function actualizarRegistro($registro,$condiciones=null){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idFormulario","tablaOrigen"=>"1Formulario","valor"=>$registro->getIdFormulario()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombreFormulario","tablaOrigen"=>"1Formulario","valor"=>$registro->getNombreFormulario()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"propiedadesFormulario","tablaOrigen"=>"1Formulario","valor"=>$registro->getPropiedadesFormulario()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"camposFormulario","tablaOrigen"=>"1Formulario","valor"=>$registro->getCamposFormulario()));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			if(is_array($condiciones) && count($condiciones)>0){
				foreach($condiciones as $campo=>$valor){
					ControlXML::agregarNodo($y,"Igual",array("campo"=>"$campo","tabla"=>"1Formulario","valor"=>$valor));
				}
			}else{
				ControlXML::agregarNodo($y,"Igual",array("campo"=>"idFormulario","tabla"=>"1Formulario","valor"=>$registro->getIdFormulario()));
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
			ControlXML::agregarNodo($consulta,"Campo",array("tablaOrigen"=>"1Formulario"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idFormulario","tabla"=>"1Formulario","valor"=>$registro->getIdFormulario()));
			if($this->db->eliminar($consulta)){
				return true;
			}else{
				return false;
			}
		}
	}
?>