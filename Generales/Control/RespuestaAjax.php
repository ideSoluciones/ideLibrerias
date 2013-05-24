<?php
	class RespuestaAjax{
		private $contenido;
		private $ajax;
		function __construct($contenido=null){
			if(is_null($contenido)){
				$this->contenido=ControlXML::nuevo("Contenido");
			}else{
				$this->contenido=$contenido;
			}
			$this->ajax=ControlXML::agregarNodo($this->contenido,"Ajax");
		}
		function respuesta(){
			return $this->contenido;
		}
		function agregarEvento($objeto,$evento,$js){
			if(strlen($objeto)>0&&strlen($evento)>0&&strlen($js)>0){
				ControlXML::agregarNodoTexto($this->ajax,"Operacion",base64_encode($js),array("tipo"=>"agregarEvento","objeto"=>$objeto,"evento"=>$evento));
			}
		}
		function alerta($mensaje){
			if(strlen($mensaje)>0){
				ControlXML::agregarNodoTexto($this->ajax,"Operacion",$mensaje,array("tipo"=>"alerta"));
			}
		}
		function anexar($objeto,$propiedad,$valor){
			if(strlen($objeto)>0&&strlen($propiedad)>0){
				if(is_object($valor)){
					if(strcmp(get_class($valor),"SimpleXMLElement")==0){
						$ComponentePadre=new ComponentePadre();
						$html=$ComponentePadre->getCss(false).$ComponentePadre->getJs(false).$ComponentePadre->llamarClaseGenerica($valor);
						ControlXML::agregarNodo($this->ajax,"Operacion",array("tipo"=>"anexar","objeto"=>$objeto,"propiedad"=>$propiedad,"valor"=>base64_encode($html)));
					}
				}else{
					if(is_string($valor)&&strlen($valor)>0){
						ControlXML::agregarNodo($this->ajax,"Operacion",array("tipo"=>"anexar","objeto"=>$objeto,"propiedad"=>$propiedad,"valor"=>base64_encode($valor)));
					}
				}
			}
		}
		function asignar($objeto,$propiedad,$valor){
			if(strlen($objeto)>0&&strlen($propiedad)>0){
				if(is_object($valor)){
					if(strcmp(get_class($valor),"SimpleXMLElement")==0){
						$ComponentePadre=new ComponentePadre();
						$contenido=$ComponentePadre->llamarClaseGenerica($valor);
						$html=$ComponentePadre->getCss(false).$ComponentePadre->getJs(false).$contenido;
						ControlXML::agregarNodo($this->ajax,"Operacion",array("tipo"=>"asignar","objeto"=>$objeto,"propiedad"=>$propiedad,"valor"=>base64_encode($html)));
					}
				}else{
					if(is_string($valor)&&strlen($valor)>0){
						ControlXML::agregarNodo($this->ajax,"Operacion",array("tipo"=>"asignar","objeto"=>$objeto,"propiedad"=>$propiedad,"valor"=>base64_encode($valor)));
					}
				}
			}
		}
		function borrar($objeto,$propiedad){
			if(strlen($objeto)>0&&strlen($propiedad)>0){
				ControlXML::agregarNodo($this->ajax,"Operacion",array("tipo"=>"borrar","objeto"=>$objeto,"propiedad"=>$propiedad));
			}
		}
		function asignarValor($objeto, $valor=""){
			if(strlen($objeto)>0){
				ControlXML::agregarNodo($this->ajax,"Operacion",array("tipo"=>"asignarValor","objeto"=>$objeto,"valor"=>$valor));
			}
		}
		function crear($objetoPadre,$etiqueta,$propiedades=array(),$texto=""){
			if(is_array($propiedades)){
				$prop=array();
				foreach($propiedades as $nombre=>$valor){
					$prop[]="".str_replace("'","\"",$nombre)."='".str_replace("'","\"",$valor)."'";
				}
				$prop=implode(" ",$prop);
				ControlXML::agregarNodo($this->ajax,"Operacion",array("tipo"=>"crear","objeto"=>$objetoPadre,"etiqueta"=>$etiqueta,"propiedades"=>$prop,"texto"=>$texto));
			}
		}
		function llamar($funcion,$parametros=array()){
			if(is_array($parametros)){
				$param=array();
				foreach($parametros as $parametro){
					if(is_string($parametro)){
						$param[]="'".str_replace("'",'"',$parametro)."'";
					}elseif(is_numeric($parametro)){
						$param[]=$parametro;
					}elseif(is_bool($parametro)){
						if($parametro){
							$param[]="true";
						}else{
							$param[]="false";
						}
					}
				}
				$param=implode(",",$param);
				ControlXML::agregarNodoTexto($this->ajax,"Operacion",$param,array("tipo"=>"llamar","funcion"=>$funcion));
			}
		}
		function incluirCSS($url){
			if(strlen($url)>0){
				ControlXML::agregarNodo($this->ajax,"Operacion",array("tipo"=>"incluirCSS","url"=>$url));
			}
		}
		function incluirJS($url){
			if(strlen($url)>0){
				ControlXML::agregarNodo($this->ajax,"Operacion",array("tipo"=>"incluirJS","url"=>$url));
			}
		}
		function borrarObjeto($objeto){
			ControlXML::agregarNodo($this->ajax,"Operacion",array("tipo"=>"borrarObjeto","objeto"=>$objeto));
		}
		function incluirScript($script){
			if(strlen($script)>0){
				ControlXML::agregarNodo($this->ajax,"Operacion",array("tipo"=>"incluirScript","script"=>$script));
			}
		}
		function script($script){
			if(strlen($script)>0){
				ControlXML::agregarNodoTexto($this->ajax,"Operacion",$script,array("tipo"=>"script"));
			}
		}
		function css($estilo){
			if(strlen($estilo)>0){
				ControlXML::agregarNodoTexto($this->ajax,"Operacion",$estilo,array("tipo"=>"css"));
			}
		}
	}
?>
