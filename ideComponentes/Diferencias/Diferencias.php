<?php

	//require_once 'Text/Diff.php';
	//require_once 'Text/Diff/Renderer/unified.php';
	
	class Diferencias extends ComponentePadre implements componente{
		
		function Diferencias(){
			//$this->js[]="inc/Librerias/ideComponentes/Navegador/navegador.js";
			//$this->css[]="inc/Librerias/ideComponentes/Navegador/navegador.css";
		}
	
		function obtenerResultado($xml, $principal=true){
			$html="";
			
			foreach($xml->children() as $hijo){
				switch($hijo->getName()){
					case "Nodo":
						$html.='<h3><a href="#">'.$hijo['titulo'].'</a></h3>'.
							$this->obtenerResultado($hijo, false);
						break;
					default:
						$html.=$this->llamarClaseGenerica($hijo);
				}
			}
			$html.="</div>\n";
			return $html;
		}
		
	}
	
	
	
?>
