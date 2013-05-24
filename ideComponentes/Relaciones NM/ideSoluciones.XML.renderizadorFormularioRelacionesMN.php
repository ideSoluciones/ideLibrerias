<?php

	class FormularioRelacionesMN extends ComponentePadre{
	
		var $xml;
		var $matrizRelaciones;
		var $lista1;
		var $lista2;
		
		public function obtenerResultado($dato){
			$this->setXMLFormularioRelacionesMN($dato);
			return $this->toHTML();
		}

		//$this->renderizadorFormularioRelacionesMN->setXMLFormularioRelacionesMN($dato);
		function setXMLFormularioRelacionesMN($xml){
			
			$this->xml=$xml;
			$lista1t=$xml->xpath("//Clase[@id='1']/Elemento");
			$lista2t=$xml->xpath("//Clase[@id='2']/Elemento");
			
			foreach($lista1t as $i => $a){
				$id=$a["idElemento"];
				$this->lista1["$id"]=$a;
			}
			foreach($lista2t as $i => $a){
				$id=$a["idElemento"];
				$this->lista2["$id"]=$a;
			}
			
			
			$this->matrizRelaciones=array();
			if(is_array($this->lista1)){
				foreach($this->lista1 as $i => $a){
					$xx="".$a['idElemento']."";
					$matrizRelaciones[]=array();
					if(is_array($this->lista2)){
						foreach($this->lista2 as $j => $b){
							$yy="".$b['idElemento']."";
							$this->matrizRelaciones[$xx][$yy]=false;
						}
					}
				}
			}
			$relaciones=$this->xml->xpath("//Relaciones/Relacion");
			//echo $this->geshiXML($this->xml);
			foreach($relaciones as $i => $a){
				//echo revisarArreglo($a, "a");
				$xx=0+$a['idElemento1'];
				$yy=0+$a['idElemento2'];
				$this->matrizRelaciones[$xx][$yy]=true;
			}
			//new mensajes(revisarArreglo($this->matrizRelaciones, "matrizRelaciones"));
			//new mensajes(revisarArreglo($relaciones, "relaciones"));
		}
		
		function toHTML(){
			$accion="";
			$metodo="POST";
			$nombreRelacion="";
			//$this->lista1=$this->xml->xpath("//Clase[@id='1']/Elemento");
			//$this->lista2=$this->xml->xpath("//Clase[@id='2']/Elemento");
			$propiedades=$this->xml->xpath("//Propiedad");
			foreach($propiedades as $i => $a){
				if ($a["nombre"]=="Accion"){
					$accion=$a["valor"];
				}
				if ($a["nombre"]=="Metodo"){
					$metodo=$a["valor"];
				}
				if ($a["nombre"]=="nombreClaseControlRelacion"){
					$nombreRelacion=$a["valor"];
				}
			}
			//echo revisarArreglo($this->lista1, "lista1");
			//echo revisarArreglo($this->lista2, "lista2");
			//echo revisarArreglo($this->matrizRelaciones, "MatrizRelaciones");
			

			$contenido="<div class='RelacionesMN'>";
				$contenido.="<form name='' action='".$accion."' method='".$metodo."' target='_self' >";
				$contenido.='<table >';
				$contenido.='<thead>
						<tr>
							<th></th>';
//				$contenido.='<td></td>';
				if(is_array($this->lista2)){
					foreach($this->lista2 as $i => $a){
						$contenido.='<th>'.$a["titulo"].'</th>';
					}
				}
				$contenido.='</thead>
					<tbody>';
				//echo revisarArreglo($this->matrizRelaciones);
				//echo revisarArreglo($this->lista1);
				$botonEnviar=false;
				foreach($this->matrizRelaciones as $i => $a){
					$contenido.='<tr>';
					$contenido.='<td>'.$this->lista1[0+$i]["titulo"].'</td>';
					foreach($a as $j => $b){
						if ($b){
							$checked="checked";
						}else{
							$checked="";
						}
						$contenido.="<td><input type='checkbox' name='relacion_".$i."_".$j."' value='1' $checked /></td>";
						$botonEnviar=true;
					}
					$contenido.='</tr>';
				}
				$contenido.='</tbody></table>';
				if($botonEnviar)
					$contenido.='<input type="submit" value="Guardar Cambios" />';
				$contenido.="</form>";
			$contenido.="</div>";
			return $contenido;
		}
	}
?>
