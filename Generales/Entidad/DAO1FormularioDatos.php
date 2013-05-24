<?php
	class DAO1FormularioDatos{
		private $db=null;
		function DAO1FormularioDatos($db){
			$this->db=$db;
		}
		function setDb($db){ $this->db=$db; }
		function crearVO() { 
			return new VO1FormularioDatos();
		}
		function getRegistro($idFormularioDatos) {
			$objVO=$this->crearVO();
			$objVO->setIdFormularioDatos($idFormularioDatos);
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idFormularioDatos","tablaOrigen"=>"1FormularioDatos"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idFormulario","tablaOrigen"=>"1FormularioDatos"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlDatosEnvio","tablaOrigen"=>"1FormularioDatos"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlDatosFormulario","tablaOrigen"=>"1FormularioDatos"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"activo","tablaOrigen"=>"1FormularioDatos"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idFormularioDatos","tabla"=>"1FormularioDatos","valor"=>$idFormularioDatos));
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				$objVO->setIdFormularioDatos($resultado[0]["idFormularioDatos"]);
				$objVO->setIdFormulario($resultado[0]["idFormulario"]);
				$objVO->setXmlDatosEnvio($resultado[0]["xmlDatosEnvio"]);
				$objVO->setXmlDatosFormulario($resultado[0]["xmlDatosFormulario"]);
				$objVO->setActivo($resultado[0]["activo"]);
			}else{
				throw new sinResultados("No se encontro el registro solicitado.");
			}
			return $objVO;
		}
		function getRegistroCondiciones($condiciones){
			$objVO=$this->crearVO();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"*","tablaOrigen"=>"1FormularioDatos"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			foreach($condiciones as $nombre=>$valor){
				ControlXML::agregarNodo($y,"Igual",array("campo"=>$nombre,"tabla"=>"1FormularioDatos","valor"=>$valor));
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"*","tablaOrigen"=>"1FormularioDatos"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			foreach($condiciones as $nombre=>$valor){
				ControlXML::agregarNodo($y,"Igual",array("campo"=>$nombre,"tabla"=>"1FormularioDatos","valor"=>$valor));
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idFormularioDatos","tablaOrigen"=>"1FormularioDatos"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idFormulario","tablaOrigen"=>"1FormularioDatos"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlDatosEnvio","tablaOrigen"=>"1FormularioDatos"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlDatosFormulario","tablaOrigen"=>"1FormularioDatos"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"activo","tablaOrigen"=>"1FormularioDatos"));
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO1FormularioDatos")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdFormularioDatos(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idFormularioDatos","tabla"=>"1FormularioDatos","valor"=>$condiciones->getIdFormularioDatos()));
							}
							if(strcmp($condiciones->getIdFormulario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idFormulario","tabla"=>"1FormularioDatos","valor"=>$condiciones->getIdFormulario()));
							}
							if(strcmp($condiciones->getXmlDatosEnvio(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"xmlDatosEnvio","tabla"=>"1FormularioDatos","valor"=>$condiciones->getXmlDatosEnvio()));
							}
							if(strcmp($condiciones->getXmlDatosFormulario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"xmlDatosFormulario","tabla"=>"1FormularioDatos","valor"=>$condiciones->getXmlDatosFormulario()));
							}
							if(strcmp($condiciones->getActivo(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"activo","tabla"=>"1FormularioDatos","valor"=>$condiciones->getActivo()));
							}
						}
					}
				}
			}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdFormularioDatos($reg["idFormularioDatos"]);
					$objVO->setIdFormulario($reg["idFormulario"]);
					$objVO->setXmlDatosEnvio($reg["xmlDatosEnvio"]);
					$objVO->setXmlDatosFormulario($reg["xmlDatosFormulario"]);
					$objVO->setActivo($reg["activo"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idFormularioDatos","tablaOrigen"=>"1FormularioDatos"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idFormulario","tablaOrigen"=>"1FormularioDatos"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlDatosEnvio","tablaOrigen"=>"1FormularioDatos"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlDatosFormulario","tablaOrigen"=>"1FormularioDatos"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"activo","tablaOrigen"=>"1FormularioDatos"));
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO1FormularioDatos")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdFormularioDatos(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idFormularioDatos","tabla"=>"1FormularioDatos","valor"=>$condiciones->getIdFormularioDatos()));
							}
							if(strcmp($condiciones->getIdFormulario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idFormulario","tabla"=>"1FormularioDatos","valor"=>$condiciones->getIdFormulario()));
							}
							if(strcmp($condiciones->getXmlDatosEnvio(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"xmlDatosEnvio","tabla"=>"1FormularioDatos","valor"=>$condiciones->getXmlDatosEnvio()));
							}
							if(strcmp($condiciones->getXmlDatosFormulario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"xmlDatosFormulario","tabla"=>"1FormularioDatos","valor"=>$condiciones->getXmlDatosFormulario()));
							}
							if(strcmp($condiciones->getActivo(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"activo","tabla"=>"1FormularioDatos","valor"=>$condiciones->getActivo()));
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
		function consultarRegistros($vo1FormularioDatos){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idFormularioDatos","tablaOrigen"=>"1FormularioDatos"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idFormulario","tablaOrigen"=>"1FormularioDatos"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlDatosEnvio","tablaOrigen"=>"1FormularioDatos"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlDatosFormulario","tablaOrigen"=>"1FormularioDatos"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"activo","tablaOrigen"=>"1FormularioDatos"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			if(!is_null($vo1FormularioDatos->getIdFormularioDatos())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"idFormularioDatos", "tabla"=>"1FormularioDatos", "valor"=>$vo1FormularioDatos->getIdFormularioDatos()));}
			if(!is_null($vo1FormularioDatos->getIdFormulario())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"idFormulario", "tabla"=>"1FormularioDatos", "valor"=>$vo1FormularioDatos->getIdFormulario()));}
			if(!is_null($vo1FormularioDatos->getXmlDatosEnvio())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"xmlDatosEnvio", "tabla"=>"1FormularioDatos", "valor"=>$vo1FormularioDatos->getXmlDatosEnvio()));}
			if(!is_null($vo1FormularioDatos->getXmlDatosFormulario())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"xmlDatosFormulario", "tabla"=>"1FormularioDatos", "valor"=>$vo1FormularioDatos->getXmlDatosFormulario()));}
			if(!is_null($vo1FormularioDatos->getActivo())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"activo", "tabla"=>"1FormularioDatos", "valor"=>$vo1FormularioDatos->getActivo()));}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdFormularioDatos($reg["idFormularioDatos"]);
					$objVO->setIdFormulario($reg["idFormulario"]);
					$objVO->setXmlDatosEnvio($reg["xmlDatosEnvio"]);
					$objVO->setXmlDatosFormulario($reg["xmlDatosFormulario"]);
					$objVO->setActivo($reg["activo"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idFormularioDatos","tablaOrigen"=>"1FormularioDatos"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idFormulario","tablaOrigen"=>"1FormularioDatos"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlDatosEnvio","tablaOrigen"=>"1FormularioDatos"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlDatosFormulario","tablaOrigen"=>"1FormularioDatos"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"activo","tablaOrigen"=>"1FormularioDatos"));
			$parametro = $consulta->addChild("Limitar");
			$parametro->addAttribute("regInicial", $n);
			$parametro->addAttribute("noRegistros", $m);
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO1FormularioDatos")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdFormularioDatos(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idFormularioDatos","tabla"=>"1FormularioDatos","valor"=>$condiciones->getIdFormularioDatos()));
							}
							if(strcmp($condiciones->getIdFormulario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idFormulario","tabla"=>"1FormularioDatos","valor"=>$condiciones->getIdFormulario()));
							}
							if(strcmp($condiciones->getXmlDatosEnvio(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"xmlDatosEnvio","tabla"=>"1FormularioDatos","valor"=>$condiciones->getXmlDatosEnvio()));
							}
							if(strcmp($condiciones->getXmlDatosFormulario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"xmlDatosFormulario","tabla"=>"1FormularioDatos","valor"=>$condiciones->getXmlDatosFormulario()));
							}
							if(strcmp($condiciones->getActivo(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"activo","tabla"=>"1FormularioDatos","valor"=>$condiciones->getActivo()));
							}
						}
					}
				}
			}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdFormularioDatos($reg["idFormularioDatos"]);
					$objVO->setIdFormulario($reg["idFormulario"]);
					$objVO->setXmlDatosEnvio($reg["xmlDatosEnvio"]);
					$objVO->setXmlDatosFormulario($reg["xmlDatosFormulario"]);
					$objVO->setActivo($reg["activo"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idFormularioDatos","tablaOrigen"=>"1FormularioDatos","valor"=>$registro->getIdFormularioDatos()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idFormulario","tablaOrigen"=>"1FormularioDatos","valor"=>$registro->getIdFormulario()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlDatosEnvio","tablaOrigen"=>"1FormularioDatos","valor"=>$registro->getXmlDatosEnvio()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlDatosFormulario","tablaOrigen"=>"1FormularioDatos","valor"=>$registro->getXmlDatosFormulario()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"activo","tablaOrigen"=>"1FormularioDatos","valor"=>$registro->getActivo()));
			if($this->db->insertar($consulta)){
				$registro->setIdFormularioDatos($this->db->ultimoId);
				return true;
			}else{
				return false;
			}
		}
		function actualizarRegistro($registro){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idFormularioDatos","tablaOrigen"=>"1FormularioDatos","valor"=>$registro->getIdFormularioDatos()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idFormulario","tablaOrigen"=>"1FormularioDatos","valor"=>$registro->getIdFormulario()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlDatosEnvio","tablaOrigen"=>"1FormularioDatos","valor"=>$registro->getXmlDatosEnvio()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"xmlDatosFormulario","tablaOrigen"=>"1FormularioDatos","valor"=>$registro->getXmlDatosFormulario()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"activo","tablaOrigen"=>"1FormularioDatos","valor"=>$registro->getActivo()));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idFormularioDatos","tabla"=>"1FormularioDatos","valor"=>$registro->getIdFormularioDatos()));
			if($this->db->actualizar($consulta)){
				return true;
			}else{
				return false;
			}
		}
		function eliminarRegistro($registro){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("tablaOrigen"=>"1FormularioDatos"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idFormularioDatos","tabla"=>"1FormularioDatos","valor"=>$registro->getIdFormularioDatos()));
			if($this->db->eliminar($consulta)){
				return true;
			}else{
				return false;
			}
		}
	}
?>