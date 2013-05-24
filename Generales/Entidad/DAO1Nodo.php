<?php
	class DAO1Nodo{
		private $db=null;
		function DAO1Nodo($db){
			$this->db=$db;
		}
		function setDb($db){ $this->db=$db; }
		function crearVO() { 
			return new VO1Nodo();
		}
		public static function getTabla(){
			return '1Nodo';
		}
		function getRegistro($idNodo) {
			$objVO=$this->crearVO();
			$objVO->setIdNodo($idNodo);
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idNodo","tablaOrigen"=>"1Nodo"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idAutor","tablaOrigen"=>"1Nodo"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"path","tablaOrigen"=>"1Nodo"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"fecha","tablaOrigen"=>"1Nodo"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"titulo","tablaOrigen"=>"1Nodo"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"contenidoCorto","tablaOrigen"=>"1Nodo"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"contenidoCompleto","tablaOrigen"=>"1Nodo"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idNodo","tabla"=>"1Nodo","valor"=>$idNodo));
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				$objVO->setIdNodo($resultado[0]["idNodo"]);
				$objVO->setIdAutor($resultado[0]["idAutor"]);
				$objVO->setPath($resultado[0]["path"]);
				$objVO->setFecha($resultado[0]["fecha"]);
				$objVO->setTitulo($resultado[0]["titulo"]);
				$objVO->setContenidoCorto($resultado[0]["contenidoCorto"]);
				$objVO->setContenidoCompleto($resultado[0]["contenidoCompleto"]);
			}else{
				throw new sinResultados("No se encontro el registro solicitado.");
			}
			return $objVO;
		}
		function getRegistroCondiciones($condiciones){
			$objVO=$this->crearVO();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"*","tablaOrigen"=>"1Nodo"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			foreach($condiciones as $nombre=>$valor){
				ControlXML::agregarNodo($y,"Igual",array("campo"=>$nombre,"tabla"=>"1Nodo","valor"=>$valor));
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"*","tablaOrigen"=>"1Nodo"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			foreach($condiciones as $nombre=>$valor){
				ControlXML::agregarNodo($y,"Igual",array("campo"=>$nombre,"tabla"=>"1Nodo","valor"=>$valor));
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idNodo","tablaOrigen"=>"1Nodo"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idAutor","tablaOrigen"=>"1Nodo"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"path","tablaOrigen"=>"1Nodo"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"fecha","tablaOrigen"=>"1Nodo"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"titulo","tablaOrigen"=>"1Nodo"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"contenidoCorto","tablaOrigen"=>"1Nodo"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"contenidoCompleto","tablaOrigen"=>"1Nodo"));
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO1Nodo")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdNodo(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idNodo","tabla"=>"1Nodo","valor"=>$condiciones->getIdNodo()));
							}
							if(strcmp($condiciones->getIdAutor(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idAutor","tabla"=>"1Nodo","valor"=>$condiciones->getIdAutor()));
							}
							if(strcmp($condiciones->getPath(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"path","tabla"=>"1Nodo","valor"=>$condiciones->getPath()));
							}
							if(strcmp($condiciones->getFecha(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"fecha","tabla"=>"1Nodo","valor"=>$condiciones->getFecha()));
							}
							if(strcmp($condiciones->getTitulo(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"titulo","tabla"=>"1Nodo","valor"=>$condiciones->getTitulo()));
							}
							if(strcmp($condiciones->getContenidoCorto(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"contenidoCorto","tabla"=>"1Nodo","valor"=>$condiciones->getContenidoCorto()));
							}
							if(strcmp($condiciones->getContenidoCompleto(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"contenidoCompleto","tabla"=>"1Nodo","valor"=>$condiciones->getContenidoCompleto()));
							}
						}
					}
				}
			}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdNodo($reg["idNodo"]);
					$objVO->setIdAutor($reg["idAutor"]);
					$objVO->setPath($reg["path"]);
					$objVO->setFecha($reg["fecha"]);
					$objVO->setTitulo($reg["titulo"]);
					$objVO->setContenidoCorto($reg["contenidoCorto"]);
					$objVO->setContenidoCompleto($reg["contenidoCompleto"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idNodo","tablaOrigen"=>"1Nodo"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idAutor","tablaOrigen"=>"1Nodo"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"path","tablaOrigen"=>"1Nodo"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"fecha","tablaOrigen"=>"1Nodo"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"titulo","tablaOrigen"=>"1Nodo"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"contenidoCorto","tablaOrigen"=>"1Nodo"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"contenidoCompleto","tablaOrigen"=>"1Nodo"));
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO1Nodo")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdNodo(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idNodo","tabla"=>"1Nodo","valor"=>$condiciones->getIdNodo()));
							}
							if(strcmp($condiciones->getIdAutor(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idAutor","tabla"=>"1Nodo","valor"=>$condiciones->getIdAutor()));
							}
							if(strcmp($condiciones->getPath(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"path","tabla"=>"1Nodo","valor"=>$condiciones->getPath()));
							}
							if(strcmp($condiciones->getFecha(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"fecha","tabla"=>"1Nodo","valor"=>$condiciones->getFecha()));
							}
							if(strcmp($condiciones->getTitulo(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"titulo","tabla"=>"1Nodo","valor"=>$condiciones->getTitulo()));
							}
							if(strcmp($condiciones->getContenidoCorto(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"contenidoCorto","tabla"=>"1Nodo","valor"=>$condiciones->getContenidoCorto()));
							}
							if(strcmp($condiciones->getContenidoCompleto(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"contenidoCompleto","tabla"=>"1Nodo","valor"=>$condiciones->getContenidoCompleto()));
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
		function consultarRegistros($vo1Nodo){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idNodo","tablaOrigen"=>"1Nodo"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idAutor","tablaOrigen"=>"1Nodo"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"path","tablaOrigen"=>"1Nodo"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"fecha","tablaOrigen"=>"1Nodo"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"titulo","tablaOrigen"=>"1Nodo"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"contenidoCorto","tablaOrigen"=>"1Nodo"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"contenidoCompleto","tablaOrigen"=>"1Nodo"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			if(!is_null($vo1Nodo->getIdNodo())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"idNodo", "tabla"=>"1Nodo", "valor"=>$vo1Nodo->getIdNodo()));}
			if(!is_null($vo1Nodo->getIdAutor())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"idAutor", "tabla"=>"1Nodo", "valor"=>$vo1Nodo->getIdAutor()));}
			if(!is_null($vo1Nodo->getPath())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"path", "tabla"=>"1Nodo", "valor"=>$vo1Nodo->getPath()));}
			if(!is_null($vo1Nodo->getFecha())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"fecha", "tabla"=>"1Nodo", "valor"=>$vo1Nodo->getFecha()));}
			if(!is_null($vo1Nodo->getTitulo())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"titulo", "tabla"=>"1Nodo", "valor"=>$vo1Nodo->getTitulo()));}
			if(!is_null($vo1Nodo->getContenidoCorto())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"contenidoCorto", "tabla"=>"1Nodo", "valor"=>$vo1Nodo->getContenidoCorto()));}
			if(!is_null($vo1Nodo->getContenidoCompleto())){ ControlXML::agregarNodo($y,"Igual",array("campo"=>"contenidoCompleto", "tabla"=>"1Nodo", "valor"=>$vo1Nodo->getContenidoCompleto()));}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdNodo($reg["idNodo"]);
					$objVO->setIdAutor($reg["idAutor"]);
					$objVO->setPath($reg["path"]);
					$objVO->setFecha($reg["fecha"]);
					$objVO->setTitulo($reg["titulo"]);
					$objVO->setContenidoCorto($reg["contenidoCorto"]);
					$objVO->setContenidoCompleto($reg["contenidoCompleto"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idNodo","tablaOrigen"=>"1Nodo"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idAutor","tablaOrigen"=>"1Nodo"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"path","tablaOrigen"=>"1Nodo"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"fecha","tablaOrigen"=>"1Nodo"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"titulo","tablaOrigen"=>"1Nodo"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"contenidoCorto","tablaOrigen"=>"1Nodo"));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"contenidoCompleto","tablaOrigen"=>"1Nodo"));
			$parametro = $consulta->addChild("Limitar");
			$parametro->addAttribute("regInicial", $n);
			$parametro->addAttribute("noRegistros", $m);
			if(!is_null($condiciones)){
				if(is_object($condiciones)){
					if(strcmp(get_class($condiciones),"SimpleXMLElement")==0){
						simplexml_merge($consulta,$condiciones);
					}else{
						if(strcmp(get_class($condiciones),"VO1Nodo")==0){
							$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
							$y=ControlXML::agregarNodo($condicionesConsulta,"Y");
							if(strcmp($condiciones->getIdNodo(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idNodo","tabla"=>"1Nodo","valor"=>$condiciones->getIdNodo()));
							}
							if(strcmp($condiciones->getIdAutor(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"idAutor","tabla"=>"1Nodo","valor"=>$condiciones->getIdAutor()));
							}
							if(strcmp($condiciones->getPath(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"path","tabla"=>"1Nodo","valor"=>$condiciones->getPath()));
							}
							if(strcmp($condiciones->getFecha(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"fecha","tabla"=>"1Nodo","valor"=>$condiciones->getFecha()));
							}
							if(strcmp($condiciones->getTitulo(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"titulo","tabla"=>"1Nodo","valor"=>$condiciones->getTitulo()));
							}
							if(strcmp($condiciones->getContenidoCorto(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"contenidoCorto","tabla"=>"1Nodo","valor"=>$condiciones->getContenidoCorto()));
							}
							if(strcmp($condiciones->getContenidoCompleto(),"")!=0){
								ControlXML::agregarNodo($y,"Igual",array("campo"=>"contenidoCompleto","tabla"=>"1Nodo","valor"=>$condiciones->getContenidoCompleto()));
							}
						}
					}
				}
			}
			$resultado=$this->db->consultar($consulta);
			if(count($resultado)>0){
				foreach($resultado as $reg){
					$objVO=$this->crearVO();
					$objVO->setIdNodo($reg["idNodo"]);
					$objVO->setIdAutor($reg["idAutor"]);
					$objVO->setPath($reg["path"]);
					$objVO->setFecha($reg["fecha"]);
					$objVO->setTitulo($reg["titulo"]);
					$objVO->setContenidoCorto($reg["contenidoCorto"]);
					$objVO->setContenidoCompleto($reg["contenidoCompleto"]);
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
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idNodo","tablaOrigen"=>"1Nodo","valor"=>$registro->getIdNodo()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idAutor","tablaOrigen"=>"1Nodo","valor"=>$registro->getIdAutor()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"path","tablaOrigen"=>"1Nodo","valor"=>$registro->getPath()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"fecha","tablaOrigen"=>"1Nodo","valor"=>$registro->getFecha()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"titulo","tablaOrigen"=>"1Nodo","valor"=>$registro->getTitulo()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"contenidoCorto","tablaOrigen"=>"1Nodo","valor"=>$registro->getContenidoCorto()));
				ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"contenidoCompleto","tablaOrigen"=>"1Nodo","valor"=>$registro->getContenidoCompleto()));
			if($this->db->insertar($consulta)){
				$registro->setIdNodo($this->db->ultimoId);
				return true;
			}else{
				return false;
			}
		}
		function actualizarRegistro($registro){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idNodo","tablaOrigen"=>"1Nodo","valor"=>$registro->getIdNodo()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"idAutor","tablaOrigen"=>"1Nodo","valor"=>$registro->getIdAutor()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"path","tablaOrigen"=>"1Nodo","valor"=>$registro->getPath()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"fecha","tablaOrigen"=>"1Nodo","valor"=>$registro->getFecha()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"titulo","tablaOrigen"=>"1Nodo","valor"=>$registro->getTitulo()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"contenidoCorto","tablaOrigen"=>"1Nodo","valor"=>$registro->getContenidoCorto()));
			ControlXML::agregarNodo($consulta,"Campo",array("nombre"=>"contenidoCompleto","tablaOrigen"=>"1Nodo","valor"=>$registro->getContenidoCompleto()));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idNodo","tabla"=>"1Nodo","valor"=>$registro->getIdNodo()));
			if($this->db->actualizar($consulta)){
				return true;
			}else{
				return false;
			}
		}
		function eliminarRegistro($registro){
			$retorno=array();
			$consulta=new SimpleXMLElement("<Consulta />");
			ControlXML::agregarNodo($consulta,"Campo",array("tablaOrigen"=>"1Nodo"));
			$condicion = ControlXML::agregarNodo($consulta,"Condiciones");
			$y = ControlXML::agregarNodo($condicion,"Y");
			ControlXML::agregarNodo($y,"Igual",array("campo"=>"idNodo","tabla"=>"1Nodo","valor"=>$registro->getIdNodo()));
			if($this->db->eliminar($consulta)){
				return true;
			}else{
				return false;
			}
		}
	}
?>