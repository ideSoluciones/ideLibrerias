<?php

	class Sudoku extends ComponentePadre implements componente{

		public function obtenerResultado($xml){

			//$this->js[]="Externos/jquery/jquery.dropshadow.js";
		
			$atributos="";
			foreach($xml->attributes() as $nombre=>$valor){
				switch($nombre){case "":break;default:
					$atributos.=$nombre."=\"".$valor."\" ";
				}
			}
			$html="<table>";
			$i=0;
			$j=0;
			$gf=0;
			$gc=0;
			$tablero=array();
			foreach($xml->children() as $hijo){
				switch($hijo->getName()){
					case "Casilla":
						$gc=intval($j/3);
						$gf=intval($i/3);
						$tablero[$gf][$gc][]=array("valor"=>$hijo["valor"],"tipo"=>$hijo["tipo"],"nombre"=>siEsta($hijo["nombre"]));
						$j++;
						if($j>=9){
							$j=0;
							$i++;
						}
						break;
				}
			}
			$html.="<table $atributos>";
			foreach($tablero as $fila){
				$html.="<tr>";
				foreach($fila as $columna){
					$html.="<td style='padding:0px;'>";
					$html.="<table>";
					for($i=0;$i<count($columna);$i++){
						if(($i%3)==0){
							$html.="<tr>";
						}
						switch($columna[$i]["tipo"]){
							case "f":
								$html.="<td style='text-align:center;width:40px;height:40px;padding:0px;'>".$columna[$i]["valor"]."</td>";
								break;
							case "v":
								$html.="<td style='text-align:center;width:40px;height:40px;padding:0px;'><input style='width:20px;' value='".$columna[$i]["valor"]."' name='".$columna[$i]["nombre"]."'/></td>";
								break;
						}
						if(($i%3)==2){
							$html.="</tr>";
						}
					}
					$html.="</table>";
					$html.="</td>";
				}
				$html.="</tr>";
			}
			$html.="</table>";
						
			return $html;
		}
	}

?>
