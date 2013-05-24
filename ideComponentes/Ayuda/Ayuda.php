<?php

	class Ayuda extends ComponentePadre implements componente{
		
		function Ayuda(){
			$this->js[]="Externos/jquery/jquery.bubbletip/jQuery.bubbletip-1.0.6.js";
			$this->css[]="Externos/jquery/jquery.bubbletip/bubbletip/bubbletip.css";
		}
	
		function obtenerResultado($xml, $principal=true){
			static $numeroAyuda=0;



			if (strcmp($xml["deltaDirection"], "")==0){
				$xml['deltaDirection']="right";
			}
			if (strcmp($xml["bindShow"], "")==0){
				$xml['bindShow']="mouseover";
			}
			if (strcmp($xml["bindHide"], "")==0){
				$xml['bindHide']="mouseout";
			}
			if (strcmp($xml["delayHide"], "")==0){
				$xml['delayHide']="1000";
			}
			
			if (strcmp($xml["imagen"], "")==0){
				$imagen=resolverPath()."/../Externos/iconos/tango/16x16/apps/help-browser.png";
			}else{
				$imagen=$xml["imagen"];
				unset($xml["imagen"]);
			}
			
			
			$propiedades="";
			foreach($xml->attributes() as $nombre => $valor) {
				if (strcmp($valor, "")!=0){
					$propiedades.=$nombre.": '".$valor."',\n";
				}
			}

			$html="
				<script>
					$(function () {
						$('#bubbletip".$numeroAyuda."').bubbletip($('#bubblehtml".$numeroAyuda."'), {
							".$propiedades."
						});
					});
				</script>
				<div class='botonAyuda'>
					<img id='bubbletip".$numeroAyuda."' border=0 src='".$imagen."' />
					<div id='bubblehtml".$numeroAyuda."'  style='display:none;'>
						";
						foreach($xml->children() as $hijo){
							$html.=$this->llamarClaseGenerica($hijo);
						}
						$html.="	
					</div>
				</div>\n";
			$numeroAyuda++;
			return $html;
		}
		
	}
	
	
	
?>
