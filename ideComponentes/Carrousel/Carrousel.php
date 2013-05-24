<?php

	class Carrousel extends ComponentePadre implements componente{
		
		function Carrousel(){
			/*http://code.google.com/p/jquery-infinite-carousel/*/
			$this->js[]="Externos/jquery/jquery.carrousel/jquery.infinite-carousel.js";
			$this->css[]="Externos/jquery/jquery.carrousel/jquery.infinite-carousel.css";
			$this->css[]="Externos/jquery/jquery.carrousel/jquery.infinite-carousel-estilos.css";
			//$this->css[]="Librerias/ideComponentes/Navegador/navegador.css";
		}
	
		function obtenerResultado($xml, $principal=true){
	
			static $numeroCarrousel=0;
			static $numeroHijo=0;
			$numeroHijo++;
			$html="";

			if ($principal){
				$this->setAtributoInexistente($xml, 'id', 'carrousel'.$numeroCarrousel);
				$this->setAtributoInexistente($xml, 'mensajeAnterior', 'Anterior');
				$this->setAtributoInexistente($xml, 'mensajeSiguiente', 'Siguiente');
				$this->setAtributoInexistente($xml, 'auto', "0");
				$numeroCarrousel++;
				
				
				
				$html.='
				<script>';
				
				if (strcmp($xml["auto"], "0")!=0){
					$html.='
					function slide'.$xml["id"].'(){
					  $("#'.$xml["id"].'Next").click();
					}';
				}
				$html.='				
					$(function(){
					';
					
				if (strcmp($xml["auto"], "0")!=0){
					$html.='
					var intervalId = window.setInterval(slide'.$xml["id"].', '.$xml["auto"].');';
				}
					
				$html.='
						$("#'.$xml["id"].'").carousel("#'.$xml["id"].'Prev", "#'.$xml["id"].'Next", {numElmts:3});
					});
				</script>';

				$html.='<div id="'.$xml["id"].'" class="carrousel">
					<ul>';
			}

			foreach($xml->children() as $hijo){
				switch($hijo->getName()){
					case "Nodo":
						$html .= '<li>';
						foreach($hijo->children() as $elementos){
							$html .= $this->llamarClaseGenerica($elementos);
						}
						$html .= '</li>';
						break;
					default:
						$html.=$this->llamarClaseGenerica($hijo);
				}
			}

			if ($principal){
				$html.="</ul></div>\n";
				$html.='	<div id="'.$xml["id"].'Prev" class="carrouselPrev">'.$xml["mensajeAnterior"].'</div>';
				$html.='	<div id="'.$xml["id"].'Next" class="carrouselNext">'.$xml["mensajeSiguiente"].'</div>';
			}else{
				//echo "retornando [".$html."]";
			}

			return $html;
		}
		
	}
	
	
	
?>
