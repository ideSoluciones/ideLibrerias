<?php

	class Tabs extends ComponentePadre implements componente{
		
		function Tabs(){
			//$this->js[]="../Librerias/ideComponentes/Navegador/navegador.js";
			//$this->css[]="../Librerias/ideComponentes/Navegador/navegador.css";
		}
	
		function obtenerResultado($xml, $principal=true, $id=0){
			static $numeroTab=0;
			$html="";
			$contador=0;
			

			$this->setAtributoInexistente($xml, 'estilo', "");
			$this->setAtributoInexistente($xml, 'clase', "");
			$this->setAtributoInexistente($xml, 'id', "tabs".$numeroTab);
			$id=$xml["id"];
			$estilo=(string)$xml["estilo"];
			
			$this->setAtributoInexistente($xml, 'opciones', "");
			
			
			

			if ($principal){
				$numeroTab++;
				$html.='
				<script >
				$(function() {
					$("#'.$id.'").tabs('.$xml["opciones"].');
				});
				</script>
				<div id="'.$id.'" style="'.$estilo.'" class="'.(string)$xml["clase"].'">';
				$contador=0;
			}

			$html1="";
			$html2="";
			//static $ultimoHijo="";
			foreach($xml->children() as $hijo){
				switch($hijo->getName()){
					case "Nodo":
						$html1.="\n".'<li><a href="#'.$id.'-'.$contador.'">'.$hijo['titulo'].'</a></li>';
						$html2.="<div id='".$id.'-'.$contador."'>\n";
						$html2.=$this->obtenerResultado($hijo, false, $contador);
						$html2.="</div>\n";
						break;
					default:
						$html.=$this->llamarClaseGenerica($hijo);
				}
				$contador++;
			}
			
			
			
			if ($principal){
				$html.='<ul>'.$html1.'</ul>'.$html2;
				$html.="</div>\n";
			}
			return $html;
		}
		
	}
	
	
	
?>
