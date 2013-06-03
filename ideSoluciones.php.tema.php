<?php

class tema extends ComponentePadre{

	var $xml=null;
	var $menu=null;
	var $contenido=array();
	var $css=array();
	var $js=array();
	var $configuracion=null;
	protected $xmlGeneral=null;
	protected $contenidoRecarga=false;
	protected $retornoAjax;

	function tema($xmlContenido=null, $respuestaAjax=null){
		$this->cabecerasHTML();
		if(class_exists("ConfiguracionLocal")){
			$this->configuracion= new ConfiguracionLocal();
		}else{
			$this->configuracion= new ConfiguracionGeneral();
		}
		if(is_null($xmlContenido)){
			$this->xml=new SimpleXMLElement("<Contenido />");
		}else{
			$this->xml=$xmlContenido;
		}
		$this->menu=array();
		$this->mensaje=array();
		$this->xmlGeneral=new generalXML();
		$this->procesadorAjax= new ProcesadorAjax();
		$this->analizarXMLContenido($respuestaAjax);

	}

	function getRetornoAjax(){
		return $this->retornoAjax;
	}

	function analizarXMLContenido($respuestaAjax){

		//echo "el xml es: ".$this->geshiXML($this->xml);

		$xmltmp=$this->xml->xpath('Menu');
		$this->extraerNodo($xmltmp, "tema_Menu");
		$xmltmp=$this->xml->xpath('//Contenido');
		$this->retornoAjax=$this->extraerNodo1p($xmltmp, "tema_Contenido", $respuestaAjax);
	}

	function tema_Menu($datos){
		$this->menu=$datos;
		//echo revisarArreglo($datos, "Datos menu");
		//$xmltmp=$datos->xpath('Campo');
		//$this->extraerNodo1p($xmltmp, "tema_CampoMenu",$datos["nombre"]);
	}

	function tema_CampoMenu($datos,$nombre){
		$this->menu[]=array($nombre,$datos["nombre"],$datos["destino"]);
	}

	function tema_Contenido($datos, $respuestaAjax=null){
		$resultadoAjax=false;
		//@todo quitar este comentario
		foreach($datos as $dato){
			//@todo quitar este comentario
			switch($dato->getName()){
				case "RespuestaDinamica":
					$resultadoAjax=$resultadoAjax or $this->procesadorAjax->setRespuestaAjax($respuestaAjax, $dato);
					break;
				default:
				    $this->contenido[]=$this->llamarClaseGenerica($dato);
                    //$this->generarJSs($this->obtenerJavascriptAIncluir());
				    //$this->generarCSSs($this->obtenerCssAIncluir());


			}
		}
		return $resultadoAjax;
	}

	function getMenu($menu, $nivel=0){
		$totalLi="";
		$estilo="";
		if ($nivel>0){
			$estilo="menuInterno";
		}
		$totalUl="";
		if($nivel==0){
			$sesion = Sesion::getInstancia();
			if($sesion->leerParametro("idUsuario")!=1){
				$daoUsuario = new DAO0Usuario($sesion->getDB());
				$voUsuario = $daoUsuario->getRegistro($sesion->leerParametro("idUsuario"));
				$totalUl = "<div style='text-align: center'>Bienvenido ".$voUsuario->getUser()."</div>";
			}
		}
		$totalUl.="<ul class='ulMenu ".$estilo."'>\n";
		foreach($menu->children() as $item){
			//echo revisarArreglo($item, "".$item["nombre"]);
			$parametros=explode("/", $item["nombre"]);
			if (count($item->children())>0){
				//echo "La cantidad de elementos en el sub menu es: ".count($item->children())."<br>";
				if (count($item->children())!=0){
					$totalLi.="<li class='margen1 padding1 borde1 ui-corner-all'><div class='ancho1 padding1 ui-priority-primary' onclick='ira(\"".$item["destino"]."\");'>".$item["nombre"]."</div>";
					$totalLi.=$this->getMenu($item, $nivel+1);
					$totalLi.="</li>\n";
				}
			}else{
				$totalLi.="<li class='margen1 padding1 ui-corner-all'><button class='ancho1 padding1' onclick='ira(\"".$item["destino"]."\");'>".$item["nombre"]."</button>";
				$totalLi.="</li>\n";
			}
		}
		$totalUl2="</ul>\n";
		if (strlen($totalLi)>0){
			return $totalUl.$totalLi.$totalUl2;
		}
		return "";
	}
	
	/*

	function getMenu($menu, $nivel=0){
		$totalLi="";
		$estilo="";
		if ($nivel>0){
			$estilo="menuInterno";
		}
		$totalUl="<ul class='ulMenu ".$estilo."'>\n";
		foreach($menu->children() as $item){
			//echo revisarArreglo($item, "".$item["nombre"]);
			$parametros=explode("/", $item["nombre"]);
			if (count($item->children())>0){
				//echo "La cantidad de elementos en el sub menu es: ".count($item->children())."<br>";
				if (count($item->children())!=0){
					$totalLi.="<li><a class='linkMenu' href='".$item["destino"]."'>".$item["nombre"]."</a>";
					$totalLi.=$this->getMenu($item, $nivel+1);
					$totalLi.="</li>\n";
				}
			}else{
				$totalLi.="<li><a class='linkMenu' href='".$item["destino"]."'>".$item["nombre"]."</a>";
				$totalLi.="</li>\n";
			}
		}
		$totalUl2="</ul>\n";
		if (strlen($totalLi)>0){
			return $totalUl.$totalLi.$totalUl2;
		}
		return "";
	}
	
	*/

	function getContenido(){
		return implode("",$this->contenido);
	}
	function toHTMLContenido($nombre='contenido'){
		$respuesta= "<div id='contenido' class='".$nombre."'>\n";
		$respuesta.= $this->getContenido();
		$respuesta.= "</div>\n";
		return $respuesta;
	}
	function imprimirContenido(){
		echo "<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='es' lang='es' dir='ltr'>\n";
		//$this->css[]="";
		echo $this->getHead();
		echo "<body>\n";
		echo $this->toHTMLContenido('contenidoUnico');
		flush();
		echo "</body>\n";
		echo "</html>";
		return "";

	}
	function toXML(){
		header ("content-type: text/xml");
		echo '<?xml version="1.0"?>'."\n";
		echo '<?xml-stylesheet type="text/xsl" href="'.resolverPath()."/".'Estilos/Temas/tema.xsl"?>'."\n";
		echo "<Tema>\n";
		foreach($this->xml->children() as $hijo){
			echo $hijo->asXML();
		}
		echo "\n</Tema>";
		flush();
		return "";
	}

	function toIFRAME(){
		echo "<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='es' lang='es' dir='ltr'>\n";
		echo "<head>\n<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />\n";
		foreach($this->css as $css){
			echo $css;
		}
		echo "<link rel='stylesheet' type='text/css' href='../Librerias/css/conestilo.css' />\n";
		echo "<title>ideProyecto</title>\n</head>\n";
		flush();
		echo "<body onload='scrollTo(0, document.body.scrollHeight)'>\n";
		echo "<div id='contenido' class='contenido'>\n";
		echo $this->getContenido();
		echo "</div>\n";
		flush();
		echo "</body>\n";
		echo "</html>";
		return "";
	}

	/*
	 * Funciones genericas para generar el tema
	 */
	function cabecerasHTML(){
		header('Content-type: text/html; charset=utf-8');
	}



	
	function getCssConstantes(){
		$sesion=Sesion::getInstancia();
		$respuesta="
			<style type='text/css'>
				.mensajeError{
					background:transparent url(".$sesion->leerparametro("pathCliente")."/../Externos/iconos/tango/32x32/status/dialog-error.png) no-repeat scroll 0 0;
				}
			</style>\n";
		return $respuesta;
	}
	function getJsConstantes(){
		$sesion=Sesion::getInstancia();
		$respuesta="
			<script type='text/javascript' >
				pathCliente='".$sesion->leerparametro("pathCliente")."';
				direccionCompleta='".$sesion->leerparametro("direccionCompleta")."';
			</script>\n";
		return $respuesta;
	}
	function getMeta(){
		$respuesta = "<title>".$this->configuracion->titulo."</title>\n";
		$respuesta.= "<meta name='author' content='".$this->configuracion->autor."' />\n";
		$respuesta.= "<meta name='description' content='".$this->configuracion->descripcion."' />\n";
		$respuesta.= "<meta name='keywords' content='".$this->configuracion->keywords."' />\n";
		//$respuesta.= "<link rel='shortcut icon' href='".resolverPath()."/".$this->configuracion->favicon."' type='image/x-icon' />\n\n";
		return $respuesta;
	}
	function getHead(){
		$respuesta= "<head>\n<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />\n";
		$respuesta.=$this->getMeta();
		$respuesta.=$this->getCss();
		$respuesta.=$this->getJs();
		$respuesta.=$this->getCssConstantes();
		$respuesta.=$this->getJsConstantes();

/*<!--[if lte IE 6]>
	<script type="text/javascript" src="'.resolverPath().'/../Externos/jquery/jquery.pngfix/jquery.pngFix.js"></script> 		
	<script type="text/javascript"> 
		$(function(){ 
		    $(document).pngFix(); 
		}); 
	</script>
<![endif]-->
*/
		$respuesta.='

		<!--[if lte IE 7]>
		<script type="text/javascript" >
var IE6UPDATE_OPTIONS = {
	icons_path: "'.resolverPath().'/../Externos/jquery/jquery.navegadoresViejos/ie6update/images/",
	url: "http://www.mozilla-europe.org/es/firefox/",
	message: "Internet Explorer esta desactualizado, es necesario instalar un navegador más moderno para visitar este sitio. Click para actualizar ... "
}
		</script>
		<script type="text/javascript" src="'.resolverPath().'/../Externos/jquery/jquery.navegadoresViejos/ie6update/ie6update.js"></script>
		<![endif]-->
		
		
		';
		$respuesta.="</head>";
		return $respuesta;
	}
	

	function getEstadisticas(){
		global $tiempoInicial;
		$tiempoFinal=tick();
		$datosInicial=explode(".", $tiempoInicial);
		$datosFinal=explode(".", $tiempoFinal);
		
		$tiempo=(float)$tiempoFinal-(float)$tiempoInicial;
		
		$sesion=Sesion::getInstancia();		
		
		ControlActividades::registrarEnArchivo(
			array(
				"User"			=> $_SERVER['HTTP_USER_AGENT'],
				"Url" 			=> $sesion->leerParametro("direccionCompleta"),
				"Time"			=> date("H:i:s",$datosInicial[0]).",".$datosInicial[1]." a ".date("H:i:s",$datosFinal[0]).",".$datosFinal[1]." = ".$tiempo." seg",
				"Mem Used"		=> file_size(memory_get_usage())."/".file_size(memory_get_peak_usage()),
				"Max Mem Used"	=> file_size(memory_get_usage(true))."/".file_size(memory_get_peak_usage(true)),
			), 
			$sesion->leerParametro("idUsuario"), "estadisticas.log", "generación");
		return	"\n<!--\n".
				"Fecha:".strftime("%Y-%m-%d")."\n".
				"Tiempo utilizado: ".date("H:i:s",$datosInicial[0]).",".$datosInicial[1]." a ".date("H:i:s",$datosFinal[0]).",".$datosFinal[1]." = ".$tiempo." segundos\n".
				"Memoria utilizada: ".file_size(memory_get_usage())."/".file_size(memory_get_peak_usage())."\n".
				"Memoria maximo utilizada: ".file_size(memory_get_usage(true))."/".file_size(memory_get_peak_usage(true))."\n".
				"-->";
	}
	
	
	function getAnalytics($codigo){
		if (strcmp($codigo, "")!=0)
			return '
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("'.$codigo.'");
pageTracker._trackPageview();
} catch(err) {}</script>			
			
			';
		return "";
	}
	
	function getMensajes(){
		$m= mensaje::getInstance();
		$mensajes=$m->obtenerResultado();
		if(strcmp($mensajes,"")!=0){
			return $mensajes;
		}
		return "";
	}
	
	
	
	function toHTML(){
		echo "<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='es' lang='es' dir='ltr'>\n";
		echo $this->getHead();
		echo '<body style="background:url('.resolverPath("/../Librerias/img/fondo.png").') repeat-x;">';
		echo '
			<script type="text/javascript">    
			$(function(){
				$("button").button();
			});
			</script>
		';
		echo '<div id="pagina" class="contenedorPrincipalPagina ui-widget" >';
			echo '<div class="overFlowHidden padding1 ui-widget-header ui-corner-top">';
				if (strcmp($this->configuracion->logo, "")!=0){
					echo '<img src="'.resolverPath("/{$this->configuracion->logo}").'" class="logoIde" />';
				}
				echo '<div class="tituloPrincipal"><a href="'.resolverPath().'/">'.$this->configuracion->titulo.'</a></div>';
			echo '</div>';
			echo '<div class="padding1 ui-widget-content" style="overflow:hidden;">';
				echo '<div class="padding1 ui-widget" style="float:left;width:20%;">';
					echo '<div class="padding1 ui-widget-header ui-corner-all">Menú</div>';
					if(count($this->menu)){
						echo '<div id="menuPrincipal" class="ui-widget-content">';
							echo $this->getMenu($this->menu);
						echo '</div>';
					}
				echo '</div>';
			
				echo '<div class="padding1 ui-widget-content" style="float:left;width:76%;">';
						echo $this->getMensajes();
						echo "<div id='' class='overFlowAuto padding2'>\n";
						echo $this->getContenido();
						echo "</div>\n";
						flush();
				echo '</div>';
			echo '</div>';
			echo '<div id="" class="ui-widget-header ui-corner-bottom">';
			echo 	 '<p>&copy; 2007-'.date("Y").'  '.$this->configuracion->cliente.'  |  Desarrollado por ideSoluciones </p>';
			echo '</div>';
			flush();
			echo $this->getAnalytics($this->configuracion->codigoAnalytics);
			echo $this->getEstadisticas();
		echo '</body>';
		echo "</html>";
		return "";
	}
}

?>
