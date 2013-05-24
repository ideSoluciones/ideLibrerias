<?php

	class Ventana extends ComponentePadre implements componente{
		
		var $arregloDeNiveles;
		
		function Ventana(){
			$this->js[]="Librerias/ideComponentes/Ventana/ventana.js";
			$this->css[]="Librerias/ideComponentes/Ventana/ventana.css";
		}

		function obtenerResultado($xml){
			$html="<div class='bloque_ventana'>";
			$html.="<div class='bloque_titulo'>{$xml["titulo"]}</div>";
			$html.="<div class='bloque_borde'>";
			$html.="<div class='bloque_contenido'>";
			foreach($xml->children() as $hijo){
				switch($hijo->getName()){
					default:
						$html.=$this->llamarClaseGenerica($hijo);
				}
			}
			$html.="</div>";
			$html.="<div class='bloque_bordeInferior altoBordeInferior'>";
			$html.="<div class='bloque_borde altoBordeInferior'></div>";
			$html.="</div>";
			$html.="</div>";
			return $html;
		}
	}

?>
