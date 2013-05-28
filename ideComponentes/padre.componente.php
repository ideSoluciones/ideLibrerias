<?php
	class ide extends ComponentePadre{
		static function renderizar($xml){
			$ayudante= new ComponentePadre();
			return $ayudante->llamarClaseGenerica($xml);
		}
	}
	class ComponentePadre extends generalXML{
		public $css=array();
		public $js=array();

		public function llamarClaseGenerica($hijo){
			$html="";
			$nombreClase=$hijo->getName();
			if (class_exists($nombreClase)){
				$claseGenerica= new $nombreClase();
				if (method_exists($claseGenerica, "obtenerResultado")){
					$html.=$claseGenerica->obtenerResultado($hijo)."\n";
					$this->css=array_merge_recursive($this->css, $claseGenerica->obtenerCssAIncluir());
					$this->js=array_merge_recursive($this->js,$claseGenerica->obtenerJavascriptAIncluir());
				} else {
					$html.=$this->geshiXML($hijo);
				}
			} else {
				$html.=$this->geshiXML($hijo);
			}
			return $html;
		}

		function obtenerJavascriptAIncluir(){
			return $this->js;
		}

		function obtenerCssAIncluir(){
			return $this->css;
		}

		// Revisa si el atributo especificado del XML recibido está setteado
		// si no tiene algún valor le coloca un valor por defecto recibido en los parámetros
		function setAtributoInexistente(&$xml, $atributo, $valorPorDefecto) {
			if (strcmp((string)$xml[(string)$atributo], "")==0){
				$xml[(string)$atributo] = $valorPorDefecto;
			}
		}
		
		function imprimirArreglo($arreglo, $pre, $pos){
			$respuesta="";
			foreach($arreglo as $elemento){
			    if(is_array($elemento)){
			        $respuesta.=$this->imprimirArreglo($elemento, $pre, $pos);
			    }else{
					$urls=explode("/",$elemento);
					$prefijo="";
					if (strcmp($urls[0], "Externos")==0 || strcmp($urls[0], "Librerias")==0){
						$prefijo="../";
					}
					$respuesta.= $pre.$prefijo.$elemento.$pos;
				}
			}
			return $respuesta;
		}
		
		function getCss($base=true){
			$sesion=Sesion::getInstancia();
			$respuesta="";
			if (isset($this->css["incluir"])){
				$this->css = array_unique($this->css["incluir"]);
			}
			$this->css = array_unique($this->css);
			if($base){
				array_splice($this->css, 0, 0, "Externos/jquery/jqueryui-temas/".$sesion->configuracion->temaLibrerias."/jquery-ui.css");
				array_splice($this->css, 0, 0, "Librerias/css/conestilo.css");
			}
			$respuesta.=$this->imprimirArreglo($this->css, "<link rel='stylesheet' type='text/css' href='".resolverPath()."/", "' />\n");
			return $respuesta;
		}
		
		function getJs($base=true){
			$respuesta="";
			if (isset($this->js["incluir"])){
				$this->js = array_unique($this->js["incluir"]);
		    }
			$this->js = array_unique($this->js);
			if($base){
				/*
				$sesion = Sesion::getInstancia();
				$id = $sesion->leerParametro("idUsuario");
				if ($id!=2){
					//array_splice($this->js, 0, 0, "Librerias/js/php.full.min.js");
				}
				*/
				$sesion = Sesion::getInstancia();
				array_splice($this->js, 1, 0, "Librerias/js/ideFunciones.js");
				array_splice($this->js, 1, 0, "Librerias/js/ideAjax.js");
				array_splice($this->js, 1, 0, "Externos/jquery/jqueryui-temas/jquery-ui-1.10.2.custom.min.js");
				array_splice($this->js, 0, 0, "Externos/jquery/jquery-1.9.1.min.js");
			}
			$respuesta.=$this->imprimirArreglo($this->js, "<script type='text/javascript' src='".resolverPath()."/", "'></script>\n");
			return $respuesta;
		}

	}

	
?>
