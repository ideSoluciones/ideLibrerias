<?php

	class Preguntas extends ComponentePadre implements componente{
		
		var $js=array();
		var $css=array();
		var $ENDL="\n";
		var $noOpciones=4;
		var $propiedad=array();
		
		function Preguntas(){
			$this->css[]="Librerias/ideComponentes/tests/tests.css";
		}
	
		function obtenerResultado($xml,$primero=true){
			static $id=0;
			$html="";
			foreach($xml->children() as $hijo){
				switch($hijo->getName()){
					case "Pregunta":
						$id=$hijo['id'];
						$html.='<div class="pregunta"><div class="textoPregunta">'.$hijo['texto'].'</div>';
						$html.=$this->obtenerResultado($hijo,false);
						$html.='</div>';
						//$id++;
						break;
					case "Respuestas":
						$verdaderas=array();
						$falsas=array();
						foreach($hijo->children() as $respuesta){
							//echo "<br>R:".$respuesta["texto"];
							if(strcmp(strtoupper($respuesta["tipo"]),"VERDADERA")==0){
								$verdaderas[]=array("texto"=>$respuesta["texto"],"valor"=>$respuesta["valor"]);
							}
							if(strcmp(strtoupper($respuesta["tipo"]),"FALSA")==0){
								$falsas[]=array("texto"=>$respuesta["texto"],"valor"=>$respuesta["valor"]);
							}
						}
						$html.=$this->generarRespuestas("P".$id,$verdaderas,$falsas);
						break;
					case "Propiedad":
						$this->propiedad["{$hijo['nombre']}"]=$hijo['valor'];
						break;
					default:
						$html.=$this->llamarClaseGenerica($hijo);
				}

			}
			$html=($primero?"<div id='".$this->propiedad["id"]."'><form id='".(isset($this->propiedad["prefijo"])?$this->propiedad["prefijo"]:"")."' action='".$this->propiedad["Accion"]."' method='POST' target='_self' >":"").$html.($primero?"<input type='submit' name='operacion' value='Calificar' /></form></div>":"");
			return $html;
		}

		function generarRespuestas($id,$verdaderas,$falsas){
			shuffle($falsas);
			shuffle($verdaderas);
			$opcionesDefinitivas=array();
			for($i=0,$j=0;$i<$this->noOpciones;$i++){
				$tmp1=rand(1, 999);
				$tmp2=rand(1, 999);
				//echo "<br>".$verdaderas[$i]["texto"];
				if(isset($verdaderas[$i]["texto"])&& isset($verdaderas[$i]["valor"])){
					$opcionesDefinitivas[]="<div class='opcion'><input name='$id' type='radio' value='".base64_encode($tmp1.":".$verdaderas[$i]["valor"].":".$tmp2)."'/>".$verdaderas[$i]["texto"]."</div>".$this->ENDL;
				}else{
					if(isset($falsas[$j]["texto"])&&isset($falsas[$j]["valor"])){
						$opcionesDefinitivas[]="<div class='opcion'><input name='$id' type='radio' value='".base64_encode($tmp1.":".$falsas[$j]["valor"].":".$tmp2)."'/>".$falsas[$j]["texto"]."</div>".$this->ENDL;
						$j++;
					}
				}
			}
			shuffle($opcionesDefinitivas);
			$total="<div class='respuestasTest'>".$this->ENDL;
				foreach($opcionesDefinitivas as $opcion){
					$total.=$opcion.$this->ENDL;
				}
			$total.="</div>".$this->ENDL;

			return $total;
		}
	}
?>
