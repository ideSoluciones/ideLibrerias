<?php
	class DAO1Formulario{
		private $db=null;
		function DAO1Formulario($db){
			$this->db=$db;
		}
		function setDb($db){ $this->db=$db; }
		function crearVO() { 
			return new VO1Formulario();
		}
		function getRegistro($idFormulario) {
			$objVO=$this->crearVO();
			$objVO->setIdFormulario($idFormulario);
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idFormulario","tablaOrigen"=>"1Formulario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombreFormulario","tablaOrigen"=>"1Formulario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlPropiedadesFormulario","tablaOrigen"=>"1Formulario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlCamposFormulario","tablaOrigen"=>"1Formulario"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idFormulario","tabla"=>"1Formulario","valor"=>$idFormulario));
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				$objVO->setIdFormulario($resultado[0]["idFormulario"]);
				$objVO->setNombreFormulario($resultado[0]["nombreFormulario"]);
				$objVO->setXmlPropiedadesFormulario($resultado[0]["xmlPropiedadesFormulario"]);
				$objVO->setXmlCamposFormulario($resultado[0]["xmlCamposFormulario"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlPropiedadesFormulario","tablaOrigen"=>"1Formulario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlCamposFormulario","tablaOrigen"=>"1Formulario"));
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
							if(strcmp($condiciones->getXmlPropiedadesFormulario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"xmlPropiedadesFormulario","tabla"=>"1Formulario","valor"=>$condiciones->getXmlPropiedadesFormulario()));
							}
							if(strcmp($condiciones->getXmlCamposFormulario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"xmlCamposFormulario","tabla"=>"1Formulario","valor"=>$condiciones->getXmlCamposFormulario()));
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
					$objVO->setXmlPropiedadesFormulario($reg["xmlPropiedadesFormulario"]);
					$objVO->setXmlCamposFormulario($reg["xmlCamposFormulario"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlPropiedadesFormulario","tablaOrigen"=>"1Formulario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlCamposFormulario","tablaOrigen"=>"1Formulario"));
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
							if(strcmp($condiciones->getXmlPropiedadesFormulario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"xmlPropiedadesFormulario","tabla"=>"1Formulario","valor"=>$condiciones->getXmlPropiedadesFormulario()));
							}
							if(strcmp($condiciones->getXmlCamposFormulario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"xmlCamposFormulario","tabla"=>"1Formulario","valor"=>$condiciones->getXmlCamposFormulario()));
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlPropiedadesFormulario","tablaOrigen"=>"1Formulario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlCamposFormulario","tablaOrigen"=>"1Formulario"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			if(!is_null($vo1Formulario->getIdFormulario())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"idFormulario", "tabla"=>"1Formulario", "valor"=>$vo1Formulario->getIdFormulario()));}
			if(!is_null($vo1Formulario->getNombreFormulario())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"nombreFormulario", "tabla"=>"1Formulario", "valor"=>$vo1Formulario->getNombreFormulario()));}
			if(!is_null($vo1Formulario->getXmlPropiedadesFormulario())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"xmlPropiedadesFormulario", "tabla"=>"1Formulario", "valor"=>$vo1Formulario->getXmlPropiedadesFormulario()));}
			if(!is_null($vo1Formulario->getXmlCamposFormulario())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"xmlCamposFormulario", "tabla"=>"1Formulario", "valor"=>$vo1Formulario->getXmlCamposFormulario()));}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdFormulario($reg["idFormulario"]);
					$objVO->setNombreFormulario($reg["nombreFormulario"]);
					$objVO->setXmlPropiedadesFormulario($reg["xmlPropiedadesFormulario"]);
					$objVO->setXmlCamposFormulario($reg["xmlCamposFormulario"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlPropiedadesFormulario","tablaOrigen"=>"1Formulario"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlCamposFormulario","tablaOrigen"=>"1Formulario"));
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
							if(strcmp($condiciones->getXmlPropiedadesFormulario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"xmlPropiedadesFormulario","tabla"=>"1Formulario","valor"=>$condiciones->getXmlPropiedadesFormulario()));
							}
							if(strcmp($condiciones->getXmlCamposFormulario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"xmlCamposFormulario","tabla"=>"1Formulario","valor"=>$condiciones->getXmlCamposFormulario()));
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
					$objVO->setXmlPropiedadesFormulario($reg["xmlPropiedadesFormulario"]);
					$objVO->setXmlCamposFormulario($reg["xmlCamposFormulario"]);
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
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlPropiedadesFormulario","tablaOrigen"=>"1Formulario","valor"=>$registro->getXmlPropiedadesFormulario()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlCamposFormulario","tablaOrigen"=>"1Formulario","valor"=>$registro->getXmlCamposFormulario()));
			if($this->db->insertar($consulta)){
				$registro->setIdFormulario($this->db->ultimoId);
				return true;
			}else{
				return false;
			}
		}
		function actualizarRegistro($registro){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idFormulario","tablaOrigen"=>"1Formulario","valor"=>$registro->getIdFormulario()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"nombreFormulario","tablaOrigen"=>"1Formulario","valor"=>$registro->getNombreFormulario()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlPropiedadesFormulario","tablaOrigen"=>"1Formulario","valor"=>$registro->getXmlPropiedadesFormulario()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlCamposFormulario","tablaOrigen"=>"1Formulario","valor"=>$registro->getXmlCamposFormulario()));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idFormulario","tabla"=>"1Formulario","valor"=>$registro->getIdFormulario()));
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