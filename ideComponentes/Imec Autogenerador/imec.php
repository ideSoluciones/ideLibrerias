<?php
	class Imec extends ComponentePadre{

		protected $xml;
		protected $contenido;
		protected $XMLFormulario=null;
		protected $XMLConjunto=null;

	
		public function obtenerResultado($dato){
			$this->setXMLImec($dato);
			return $this->toHTML();
		}

		function Imec($xml=null){/*Colocados por los logs de php function Imec($xlm=null){*/
			$this->XMLFormulario=new Formulario();
			$this->XMLConjunto=new XMLConjunto();
			$this->contenido=array();
			if(!is_null($xml)){
				$this->setXMLImec($xml);
			}
			//echo "XML:".generalPhp::geshi($this->xml,"xml");
		}

		function setXMLImec($xml){
			$this->campos=array();
			$this->xml=$xml;
			$this->Imec_analizarXML();
		}

		function Imec_analizarXML(){
			foreach($this->xml as $nodo){
				switch($nodo->getName()){
					case "Conjunto":
						$this->XMLConjunto->setXMLConjunto($nodo);
						$this->contenido[]=$this->XMLConjunto->toHTML();
						break;
					case "Etiqueta":
						$nivel=($nodo["nivel"]!=null && $nodo["nivel"]!="")?$nodo["nivel"]:2;
						$this->contenido[]="<div id='".$nodo["nombre"]."'><h".$nivel.">".$nodo["valor"]."</h".$nivel."></div>";
						break;
					default:
						$this->contenido[]=$this->llamarClaseGenerica($nodo);
						break;
				}
			}
		}

		function toHTML(){
			$total="<div class='administrar'>\n";
			$total.=@implode("",$this->contenido);
			$total.="</div>\n";
			return $total;
		}
	}
?>
