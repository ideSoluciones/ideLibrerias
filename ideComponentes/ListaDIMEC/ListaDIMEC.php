<?php

	class Lista extends ComponentePadre implements componente{
		
		var $arregloDeNiveles;
		private $titulo="";
		private $botones=array();
		private $botonesEspeciales=array();
		private $eventos=array();
		private $propiedades=array();
		private $navegacion;
		
		function Lista(){
			$this->js[]="Librerias/ideComponentes/ListaDIMEC/ListaDIMEC.js";
			//$this->js[]="Librerias/js/jquery/jquery.corner.js";
			$this->js[]="Librerias/js/jquery/jquery.shadow.js";
			$this->js[]="Librerias/js/jquery/jquery.dropshadow.js";
			$this->css[]="inc/Librerias/ideComponentes/ListaDIMEC/ListaDIMEC.css";
		}

		function obtenerResultado($xml){

			$cuerpo=$this->procesarXML($xml);
			
			$respuesta="<div class='ui-widget contenedorPrincipal'>";
			$respuesta.="<div class='ui-widget-header ui-corner-top' style='padding:3px;overflow:hidden;'>";
			if(strcmp($this->titulo,"")!=0){
				$respuesta.=$this->titulo;
			}
			$respuesta.="<div style='float:right;'>";
			if(isset($this->botones["superior"])){
				foreach($this->botones["superior"] as $cod=>$propiedades){
					$respuesta.=$this->generarBoton($cod,$propiedades);
				}
			}
			if(isset($this->botonesEspeciales["superior"])){
				foreach($this->botonesEspeciales["superior"] as $propiedades){
					$respuesta.=$propiedades;
				}
			}
			$respuesta.="</div>";
			$respuesta.="</div>";
			$id="";
			if(isset($this->propiedades["id"])){
				$id=$this->propiedades["id"];
			}
			$respuesta.="<div id='$id' style='overflow:auto;' class='ui-widget-content'>";
			$respuesta.=$cuerpo;
			$respuesta.="</div>";
			$respuesta.="<div style='overflow:hidden;' class='ui-widget-content'>";
			$respuesta.=$this->navegacion;
			$respuesta.="</div>";
			$respuesta.="<div class='ui-widget-header ui-corner-bottom' style='padding:3px;overflow:hidden;'>";
			if(isset($this->botones["inferior"])){
				foreach($this->botones["inferior"] as $cod=>$propiedades){
					$respuesta.=$this->generarBoton($cod,$propiedades);
				}
			}
			if(isset($this->botonesEspeciales["inferior"])){
				foreach($this->botonesEspeciales["inferior"] as $propiedades){
					$respuesta.=$propiedades;
				}
			}
			$respuesta.="</div>";
			$respuesta.="</div>";
			
			$script='
			<script>
				$(function(){
					$("#ListaDIMECC_botonAgregar").dropshadow({color:"white"});
					$("#ListaDIMECC_botonFiltrar").dropshadow({color:"white"});
					$("#ListaDIMECC_contenedorLista").shadow({color:"black",blur:3,x:5,y:3});
					$("#ListaDIMECC_contenedorCuerpo a").dropshadow({color:"silver"});';
			foreach($this->eventos as $evento){
				$script.=$evento;
			}		
			$script.='
				});
			</script>';

			return $script.$respuesta;
		}
		
		function procesarXML($xml){
			$html="";
			foreach($xml->children() as $hijo){
				switch($hijo->getName()){
					case "Lista":
						$html.=$this->procesarXML($hijo);
						break;
					case "Informacion":
						$atributos="";
						foreach($hijo->attributes() as $nombre=>$valor){
							$atributos.=$nombre."=\"".$valor."\" ";
						}
						$html.="<div $atributos>";
						$html.="<table class='tablaListaDIMECC'>";
						$html.=$this->procesarXML($hijo);
						$html.="</table>";
						$html.="</div>";
						break;
					case "Titulos":
						$atributos="";
						foreach($hijo->attributes() as $nombre=>$valor){
							$atributos.=$nombre."=\"".$valor."\" ";
						}
						$html.="<thead $atributos class='ui-state-active'><tr>";
						$html.=$this->procesarXML($hijo);
						$html.="</tr></thead>";
						break;
					case "Titulo":
						$atributos="";
						foreach($hijo->attributes() as $nombre=>$valor){
							$atributos.=$nombre."=\"".$valor."\" ";
						}
						$html.="<th $atributos>";
						$html.=$hijo["nombre"];
						$html.="</th>";
						break;
					case "Datos":
						$atributos="";
						foreach($hijo->attributes() as $nombre=>$valor){
							$atributos.=$nombre."=\"".$valor."\" ";
						}
						$html.="<tbody $atributos>";
						$html.=$this->procesarXML($hijo);
						$html.="</tbody>";
						break;
					case "Fila":
						$atributos="";
						foreach($hijo->attributes() as $nombre=>$valor){
							$atributos.=$nombre."=\"".$valor."\" ";
						}
						$html.="<tr $atributos>";
						$html.=$this->procesarXML($hijo);
						$html.="</tr>";
						break;
					case "Columna":
						$atributos="";
						foreach($hijo->attributes() as $nombre=>$valor){
							$atributos.=$nombre."=\"".$valor."\" ";
						}
						$contenido=$this->procesarXML($hijo);
						if(strlen($contenido)>0){
							$html.="<td $atributos>".$contenido."</td>";
						}
						break;
					case "Propiedades":
						$this->procesarXML($hijo);
						break;
					case "Propiedad":
						switch($hijo["nombre"]){
							case "titulo":
								$this->titulo=$hijo["valor"];
								break;
							case "boton":
								$propiedades=json_decode($hijo["valor"],true);
								if(isset($propiedades["panel"])){
									$this->botones["{$propiedades["panel"]}"][]=$propiedades;
								}
								break;
							default:
								$this->propiedades["{$hijo["nombre"]}"]=$hijo["valor"];
						}
						break;
					case "Componentes":
						$this->procesarXML($hijo);
						break;
					case "Componente":
						if(isset($hijo["panel"])){
							$this->botonesEspeciales["{$hijo["panel"]}"][]=$this->procesarXML($hijo);
						}
						break;
					case "Navegacion":
						$this->navegacion=$this->procesarXML($hijo);
						break;
					default:
						$html.=$this->llamarClaseGenerica($hijo);
						

				}
			}
			return $html;
		}
		
		function generarBoton($codigo,$propiedades){
			$id="";
			if(isset($propiedades["id"])){
				$id=$propiedades["id"];
			}
			$path="";
			if(isset($propiedades["path"])){
				$path=$propiedades["path"];
			}
			$panel="";
			if(isset($propiedades["panel"])){
				$panel=$propiedades["panel"];
			}
			$this->eventos[]="\n".'$("#ListaDIMECCboton_'.$panel.'_'.$codigo.'_'.$id.'").click(function(){ira("'.$path.'");});'."\n".'$("#ListaDIMECCboton_'.$panel.'_'.$codigo.'_'.$id.'").dropshadow({color:"white"});';
			$titulo="";
			if(isset($propiedades["titulo"])){
				$titulo=$propiedades["titulo"];
			}
			$varianteDeEstilo="";
			if(strcmp($titulo,"")!=0){
				$varianteDeEstilo="ConTexto";
			}
			$classImagen="";
			if(isset($propiedades["imagen"])){
				switch($propiedades["imagen"]){
					case "agregar":
						$classImagen="ListaDIMECC_iconoAgregar".$varianteDeEstilo;
						break;
					case "filtrar":
						$classImagen="ListaDIMECC_iconoFiltro".$varianteDeEstilo;
						break;
				}
			}
			$respuesta="<button id='ListaDIMECCboton_".$panel."_".$codigo."_".$id."' type='button' class='ui-state-default ui-corner-all'><div class='$classImagen'>$titulo</div></button> ";
			return $respuesta;
		}
	}

?>
