<?php

	class Acordeon extends ComponentePadre implements componente{
		
		function Acordeon(){
			//$this->js[]="Librerias/ideComponentes/Navegador/navegador.js";
			//$this->css[]="Librerias/ideComponentes/Navegador/navegador.css";
		}
	
		function obtenerResultado($xml, $principal=true){
			static $numeroAcordeon=0;
			$contador=0;
			$html="";
			
			$this->setAtributoInexistente($xml, 'estilo', "");
			$this->setAtributoInexistente($xml, 'clase', "");
			$this->setAtributoInexistente($xml, 'id', "accordion".$numeroAcordeon);
			$id=$xml["id"];
			$estilo=(string)$xml["estilo"];
			
			$this->setAtributoInexistente($xml, 'opciones', "");

			if ($principal){
				$numeroAcordeon++;
				$html.='
				<script >
				$(function() {
					$("#'.$id.'").accordion('.$xml["opciones"].');
				});
				</script>
				<div id="'.$id.'" style="'.$estilo.'" class="'.(string)$xml["clase"].'">';
				
			}
			
			//static $ultimoHijo="";
			foreach($xml->children() as $hijo){
				switch($hijo->getName()){
					case "Nodo":
						$html.='<h3><a href="#'.$id.'-'.$contador.'">'.$hijo['titulo'].'</a></h3>'.
							'<div id="'.$id.'-'.$contador.'">'.
								$this->obtenerResultado($hijo, false).
							'</div>';
						break;
					default:
						$html.=$this->llamarClaseGenerica($hijo);
				}
				$contador++;
			}
			if ($principal){
				$html.="</div>\n";
			}
			return $html;
		}
		
	}
	
	
	
?>
