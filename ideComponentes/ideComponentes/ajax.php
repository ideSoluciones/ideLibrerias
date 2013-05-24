<?php

	class Ajax extends ComponentePadre implements componente{
		public function obtenerResultado($xml){
			$enScript=false;
			foreach($xml->attributes() as $nombre=>$valor){
				switch($nombre){
					case "inline":
						if(strcmp(strtolower($valor),"true")==0){
							$enScript=true;
						}
						break;
				}
			}
			$json=array();
			//echo generalXML::geshiXML($xml);
			foreach($xml->children() as $hijo){
				$script="";
				switch($hijo->getName()){
					case "Operacion":
						$tipo=$hijo["tipo"];
						switch($tipo){
							case "agregarEvento":
								$json[]='{nombre:"agregarEvento",objeto:"'.$hijo["objeto"].'",evento:"'.$hijo["evento"].'",js:"'.$hijo.'"}';
								break;
							case "alerta":
								$json[]='{nombre:"alerta",mensaje:"'.$hijo.'"}';
								break;
							case "anexar":
								$json[]='{nombre:"anexar",objeto:"'.$hijo["objeto"].'",propiedad:"'.$hijo["propiedad"].'",valor:"'.$hijo["valor"].'"}';
								break;
							case "asignar":
								$json[]='{nombre:"asignar",objeto:"'.$hijo["objeto"].'",propiedad:"'.$hijo["propiedad"].'",valor:"'.$hijo["valor"].'"}';
								break;
							case "borrar":
								$json[]='{nombre:"borrar",objeto:"'.$hijo["objeto"].'",propiedad:"'.$hijo["propiedad"].'"}';
								break;
							case "asignarValor":
								$json[]='{nombre:"asignarValor",objeto:"'.$hijo["objeto"].'",valor:"'.$hijo["valor"].'"}';
								break;
							case "crear":
								$json[]='{nombre:"crear",objeto:"'.$hijo["objeto"].'",etiqueta:"'.$hijo["etiqueta"].'",propiedades:"'.$hijo["propiedades"].'",texto:"'.$hijo["texto"].'"}';
								break;
							case "llamar":
								$script=$hijo["funcion"]."(".$hijo.");";
							case "script":
								if(strlen($script)<=0){
									$script=$hijo;
								}
								$json[]='{nombre:"script",script:"'.$script.'"}';
								break;
							case "incluirCSS":
								$json[]='{nombre:"incluirCSS",url:"'.$hijo["url"].'"}';
								break;
							case "incluirJS":
								$json[]='{nombre:"incluirJS",url:"'.$hijo["url"].'"}';
								break;
							case "incluirScript":
								$json[]='{nombre:"incluirScript",script:"'.$hijo["script"].'"}';
								break;
							case "borrarObjeto":
								$json[]='{nombre:"borrarObjeto",objeto:"'.$hijo["objeto"].'"}';
								break;
							case "css":
								$json[]='{nombre:"css",estilo:"'.$hijo.'"}';
								break;
						}
						break;
				}
			}
			$json=str_replace("\n","\\n",'['.implode(",",$json).']');
			if($enScript){
				$json="<script>new ajax_operacion(".$json.");</script>";
			}
			return $json;
		}
	}

?>
