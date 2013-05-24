<?php

	class Mapa extends ComponentePadre implements componente{

		function Mapa(){
			//$this->js["incluir"][]="../Librerias/ideComponentes/Navegador/navegador.js";
			//$this->css["incluir"][]="../Librerias/ideComponentes/Navegador/navegador.css";
		}

		function obtenerResultado($xml, $principal=true){
			$html="";
			//echo "Tratando de renderizar: ", generalXML::geshiXML($xml);
			foreach($xml->children() as $hijo){
				switch($hijo->getName()){
					case "Contenido":
						$html.='<iframe width="'.$hijo['ancho'].'" height="'.$hijo['alto'].'" 
							frameborder="0" scrolling="no" marginheight="0" marginwidth="0" 
							src="'.$hijo['direccion'].'"></iframe><br/>
							<small>'.$hijo['mensaje'].' 
								<a href="'.$hijo['link'].'" target="_blank"
								style="color:#0000FF;text-align:left">'.$hijo['textoLink'].'</a>
							</small>';
							break;
					default:
						$html.=$this->llamarClaseGenerica($hijo);
				}
			}
			return $html;
		}
	}
?>
