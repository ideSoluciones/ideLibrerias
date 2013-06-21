<?php
	class DAO1FormularioDatos{
		private $db=null;
		function DAO1FormularioDatos(){
			$sesion=Sesion::getInstancia();
			$this->db=$sesion->getDB();
		}
		function setDb($db){ $this->db=$db; }
		function crearVO() { 
			return new VO1FormularioDatos();
		}
		public static function getTabla(){
			return '1FormularioDatos';
		}
		function getRegistro($idFormularioDatos) {
			$objVO=$this->crearVO();
			$objVO->setIdFormularioDatos($idFormularioDatos);
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idFormularioDatos","tablaOrigen"=>"1FormularioDatos"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idFormulario","tablaOrigen"=>"1FormularioDatos"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"datosEnvio","tablaOrigen"=>"1FormularioDatos"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"datosFormulario","tablaOrigen"=>"1FormularioDatos"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idFormularioDatos","tabla"=>"1FormularioDatos","valor"=>$idFormularioDatos));
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				$objVO->setIdFormularioDatos($resultado[0]["idFormularioDatos"]);
				$objVO->setIdFormulario($resultado[0]["idFormulario"]);
				$objVO->setDatosEnvio($resultado[0]["datosEnvio"]);
				$objVO->setDatosFormulario($resultado[0]["datosFormulario"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"datosEnvio","tablaOrigen"=>"1FormularioDatos"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"datosFormulario","tablaOrigen"=>"1FormularioDatos"));
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
							if(strcmp($condiciones->getDatosEnvio(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"datosEnvio","tabla"=>"1FormularioDatos","valor"=>$condiciones->getDatosEnvio()));
							}
							if(strcmp($condiciones->getDatosFormulario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"datosFormulario","tabla"=>"1FormularioDatos","valor"=>$condiciones->getDatosFormulario()));
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
					$objVO->setDatosEnvio($reg["datosEnvio"]);
					$objVO->setDatosFormulario($reg["datosFormulario"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"datosEnvio","tablaOrigen"=>"1FormularioDatos"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"datosFormulario","tablaOrigen"=>"1FormularioDatos"));
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
							if(strcmp($condiciones->getDatosEnvio(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"datosEnvio","tabla"=>"1FormularioDatos","valor"=>$condiciones->getDatosEnvio()));
							}
							if(strcmp($condiciones->getDatosFormulario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"datosFormulario","tabla"=>"1FormularioDatos","valor"=>$condiciones->getDatosFormulario()));
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"datosEnvio","tablaOrigen"=>"1FormularioDatos"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"datosFormulario","tablaOrigen"=>"1FormularioDatos"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			if(!is_null($vo1FormularioDatos->getIdFormularioDatos())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"idFormularioDatos", "tabla"=>"1FormularioDatos", "valor"=>$vo1FormularioDatos->getIdFormularioDatos()));}
			if(!is_null($vo1FormularioDatos->getIdFormulario())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"idFormulario", "tabla"=>"1FormularioDatos", "valor"=>$vo1FormularioDatos->getIdFormulario()));}
			if(!is_null($vo1FormularioDatos->getDatosEnvio())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"datosEnvio", "tabla"=>"1FormularioDatos", "valor"=>$vo1FormularioDatos->getDatosEnvio()));}
			if(!is_null($vo1FormularioDatos->getDatosFormulario())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"datosFormulario", "tabla"=>"1FormularioDatos", "valor"=>$vo1FormularioDatos->getDatosFormulario()));}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdFormularioDatos($reg["idFormularioDatos"]);
					$objVO->setIdFormulario($reg["idFormulario"]);
					$objVO->setDatosEnvio($reg["datosEnvio"]);
					$objVO->setDatosFormulario($reg["datosFormulario"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"datosEnvio","tablaOrigen"=>"1FormularioDatos"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"datosFormulario","tablaOrigen"=>"1FormularioDatos"));
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
							if(strcmp($condiciones->getDatosEnvio(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"datosEnvio","tabla"=>"1FormularioDatos","valor"=>$condiciones->getDatosEnvio()));
							}
							if(strcmp($condiciones->getDatosFormulario(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"datosFormulario","tabla"=>"1FormularioDatos","valor"=>$condiciones->getDatosFormulario()));
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
					$objVO->setDatosEnvio($reg["datosEnvio"]);
					$objVO->setDatosFormulario($reg["datosFormulario"]);
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
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"datosEnvio","tablaOrigen"=>"1FormularioDatos","valor"=>$registro->getDatosEnvio()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"datosFormulario","tablaOrigen"=>"1FormularioDatos","valor"=>$registro->getDatosFormulario()));
			if($this->db->insertar($consulta)){
				$registro->setIdFormularioDatos($this->db->ultimoId);
				return true;
			}else{
				return false;
			}
		}
		function actualizarRegistro($registro,$condiciones=null){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idFormularioDatos","tablaOrigen"=>"1FormularioDatos","valor"=>$registro->getIdFormularioDatos()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idFormulario","tablaOrigen"=>"1FormularioDatos","valor"=>$registro->getIdFormulario()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"datosEnvio","tablaOrigen"=>"1FormularioDatos","valor"=>$registro->getDatosEnvio()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"datosFormulario","tablaOrigen"=>"1FormularioDatos","valor"=>$registro->getDatosFormulario()));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			if(is_array($condiciones) && count($condiciones)>0){
				foreach($condiciones as $campo=>$valor){
					ControlXML::agregarNodo($y,"Igual",array("campo"=>"$campo","tabla"=>"1FormularioDatos","valor"=>$valor));
				}
			}else{
				ControlXML::agregarNodo($y,"Igual",array("campo"=>"idFormularioDatos","tabla"=>"1FormularioDatos","valor"=>$registro->getIdFormularioDatos()));
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