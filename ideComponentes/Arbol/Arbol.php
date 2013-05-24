<?php

	class Arbol extends ComponentePadre implements componente{
		
		var $archivos=false;		
		function Arbol(){
		}
	
		function obtenerResultado($xml, $principal=true, $contador="?"){
			static $numeroArbol=0;
			$html="";
			
			$this->setAtributoInexistente($xml, 'estilo', "");
			$this->setAtributoInexistente($xml, 'clase', "");
			$this->setAtributoInexistente($xml, 'id', "arbol".$numeroArbol);
			$this->setAtributoInexistente($xml, 'funcionProceso', "");
			
			
			
			$id=$xml["id"];
			$estilo=(string)$xml["estilo"];

			if ($principal){
				$this->setAtributoInexistente($xml, 'archivos', "false");
				if (strcmp($xml["archivos"], "true")==0){
					$this->archivos=true;
				}
			}
				
			if ($this->archivos){
			
				$this->setAtributoInexistente($xml, 'opciones', "");
				$this->js[]="Externos/jquery/jquery-treeview/jqueryFileTree.js";
				$this->css[]="Externos/jquery/jquery-treeview/jqueryFileTree.css";
				$html.='
					<script >
					$(function() {
						$("#'.$id.'").fileTree(
							{
								script: "'.resolverPath().'/conectorFileTree"
							}, 
							'.$xml["funcionProceso"].'
						);
					});
					</script>
				
					<div id="'.$id.'" class="arbol '.(string)$xml["clase"].'"></div>';
			
			}else{
				$this->js[]="Externos/jquery/jquery-treeview/jquery.treeview.js";
				$this->css[]="Externos/jquery/jquery-treeview/jquery.treeview.css";
				$this->setAtributoInexistente($xml, 'opciones', "{collapsed: true}");
				
				$clave=0;
				if ($principal){
					$clave=1;
					$numeroArbol++;
					$html.='
					<script >
					$(function() {
						$("#'.$id.'").treeview('.$xml["opciones"].');
					});
					</script>
				
<ul id="'.$id.'" style="'.$estilo.'" class="arbol '.(string)$xml["clase"].'">'."\n";
				
				}
			
				//static $ultimoHijo="";
				foreach($xml->children() as $hijo){
					switch($hijo->getName()){
						case "Nodo":
							$html.=''.
								'	<li><span class="file">'.
									$hijo["titulo"];
									foreach($hijo->children() as $hijos){
										$html.=$this->llamarClaseGenerica($hijos);
									}
							$html.='</span></li>'."\n";
							$clave++;
							break;
						case "Arbol":
							$html.=
							'	<li><span class="folder"></span>'.$hijo["titulo"].
									'<ul>'."\n".
										$this->obtenerResultado($hijo, false, $contador.($clave-1).".").
									'</ul>'."\n";
								'</li>'."\n";
							break;
						default:
							//$html.=$this->llamarClaseGenerica($hijo);
					}
				}
				if ($principal){
					$html.="</ul>\n";
				}
				
			}
			return $html;
		}
		
	}
	
	
	
?>
