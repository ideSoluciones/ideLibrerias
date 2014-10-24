<?php

	class Tabla extends ComponentePadre implements componente{

		function Tabla(){
		}

		function obtenerResultado($xml, $principal=true){
			static $numeroTabla=0;
			$numeroTabla++;
			$html="";
			$cabeza="";
			$cuerpo="";
			$pie="";
			
			if ($principal){
				if (strcmp($xml["dinamico"], "true")==0){
					//$html="Tabla dinamica";

					$this->js[] ="Externos/jquery/jquery.table/tablesort/tablesort.min.js";
					$this->js[] ="Externos/jquery/jquery.table/tablesort/tablefilter.js";
					$this->css[]="Externos/jquery/jquery.table/tablesort/tablesort.css";

					$html.="";
				
					//$lineas=explode("\n", $xml["datos"]);
				
					$html.="<table id='tabla".$numeroTabla."' class='".$xml["clase"]."'>";

					//$html.="</tbody>";
					//$html.="<table><tr>".str_replace("\n", "</td></tr><tr><td>", str_replace("\t", "</td><td>", $xml["datos"]))."</tr></table>";
				}else{
			
	//echo generalXML::geshiXML($xml);
				
					//$this->js[]="Externos/jquery/jquery.thead/jquery.thead-1.0.js";
					//$this->js[]="Externos/jquery/table_floating_header.js";
					$this->js[]="Externos/jquery/jquery.dataTables/media/js/jquery.dataTables.js";

					$paginacion="true";
					$busquedas="true";
					$autoajuste="true";
					$informacion="true";
					$plano=false;
					if (strcmp($xml["plano"], "true")==0){
						$plano=true;
					}
					if (strcmp($xml["simple"], "true")==0){
						$paginacion="false";
						$busquedas="false";
						$autoajuste="false";
						$informacion="false";
					}
					if (strcmp($xml["busquedas"], "true")==0){
						$busquedas="true";
					}
					if (strcmp($xml["paginacion"], "true")==0){
						$paginacion="true";
					}
					if (strcmp($xml["autoajuste"], "true")==0){
						$autoajuste="true";
					}
					if (strcmp($xml["informacion"], "true")==0){
						$informacion="true";
					}
					$estilo="";
					if (isset($xml["estilo"])){
						$estilo="style='".$xml["estilo"]."'";
					}
	//						$('#tabla".$numeroTabla."').thead();
					if(!$plano){
						$this->css[]="Externos/jquery/jquery.dataTables/demo_table.css";
						$this->css[]="Externos/jquery/jquery.dataTables/demo_table_jui.css";
						$html.="
							<script >
							$(function() {
								$('#tabla".$numeroTabla."').dataTable({
									'iDisplayLength': 11,
									'bJQueryUI': true,
									'bPaginate': ".$paginacion.",
									'bFilter': ".$busquedas.",
									'bAutoWidth': ".$autoajuste.",
									'bInfo': ".$informacion.",
									'oLanguage': {'sUrl': '".resolverPath()."/../Externos/jquery/jquery.dataTables/lang_es.txt'},
									'sPaginationType': 'full_numbers'
								});
							});
							</script>";
					}
				if(isset($xml["cellpadding"])){
					$cellpadding=$xml["cellpadding"];
				}else{
					$cellpadding='0';
				}
				if(isset($xml["cellspacing"])){
					$cellspacing=$xml["cellspacing"];
				}else{
					$cellspacing='0';
				}
				if(isset($xml["border"])){
					$border=$xml["border"];
				}else{
					$border='0';
				}
				if(isset($xml["class"])){
					$class='display '.$xml["class"];
				}else{
					$class='display';
				}
				$atributos="";
				foreach ($xml->attributes() as $a => $i){
					switch($a){
						case "cellpadding":case "cellspacing":case "border":case "class":case "plano":case "simple":case "busquedas":case "paginacion":case "autoajuste":case "informacion":break;
						default: $atributos.=" ".$a."='".$i."'";
					}
				}
				
				$html.="<table id='tabla".$numeroTabla."' $estilo $atributos cellpadding='$cellpadding' cellspacing='$cellspacing' border='$border' class='$class'>";
				}
			}
			foreach($xml->children() as $hijo){
				switch($hijo->getName()){
					case "Cabecera":
						$cabeza="<thead><tr>";

						foreach($hijo->children() as $celda){
							if (strcmp($xml["dinamico"], "true")==0){
								$cabeza.="<th class='".$celda["clase"]."' >";
							}else{
								$cabeza.="<th>";
							}
							$cabeza.=$this->llamarClaseGenerica($celda);
							$cabeza.="</th>";
						}
						$cabeza.="</tr></thead>";
						break;
					case "Fila":
						if (strcmp((string)$hijo["onclick"], "")==0){
							$hijo["onclick"]="";
						}
						if (strcmp((string)$hijo["clase"], "")==0){
							$hijo["clase"]="";
						}
						$cuerpo.="<tr class=\"".(string)$hijo["clase"]."\" onclick=\"".(string)$hijo["onclick"]."\" >";

						foreach($hijo->children() as $celda){
							$estilo="";
							if (strcmp((string)$celda["estilo"], "")!=0){
								$estilo="style='".$celda["estilo"]."'";
							}
						
							$clase="";
							if (strcmp((string)$celda["clase"], "")!=0){
								$clase="class='".$celda["clase"]."'";
							}
						
							$cuerpo.="<td ".$estilo." ".$clase.">\n\t";
							$cuerpo.=$this->llamarClaseGenerica($celda);
							$cuerpo.="</td>\n";
						}
						$cuerpo.="</tr>\n";
						break;


					case "Pie":
						$pie="<tfoot><tr>";

						foreach($hijo->children() as $celda){
							$pie.="<td>";
							$pie.=$this->llamarClaseGenerica($celda);
							$pie.="</td>";
						}
						$pie.="</tr></tfoot>";
						break;

					default:
						$html.=$this->llamarClaseGenerica($hijo);
				}
			}
			
			if ($principal){
				$html.=$cabeza."<tbody>".$cuerpo."</tbody>".$pie;
			
				$html.="</table>";
			}
			
			return $html;
		}
		
	}
	
	
	
?>
