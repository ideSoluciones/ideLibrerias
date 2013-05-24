<?php

	/**
	*    @name FMapa
	*    @abstract
	*    @author Jorge Gonzalez <jgonzalez@idesoluciones.com >
	*    @version 1.0
	*/

	class FMapa extends ObjetoHTML{

		function FMapa($idObjeto, $nombre, $propiedades, $tipoSalida, $accionPlus){
			$this->ObjetoHTML();
			$this->setIdObjeto($idObjeto);
			$this->setNombre($nombre);
			$this->setTipoSalida($tipoSalida);
			$this->setAccionPlus($accionPlus);
			
			
			
			$this->setAtributoInexistente($propiedades, 'lon', "-74.087600");
			$this->setAtributoInexistente($propiedades, 'lat', "4.632800");
			$this->setAtributoInexistente($propiedades, 'zoom', "6");
			
			
			
			$this->setAtributoInexistente($propiedades, 'valorPorDefecto', "-74.087600,4.632800");
			
			$this->setPropiedades($propiedades);
			
		
			if(!isset($this->arrayReglas["reglas"]))
				$this->arrayReglas["reglas"]=array();

		}

		public static function validar($campo,$valorDato){
			$respuesta = array("respuesta"=>true,"mensaje"=>"");
			return $respuesta;
		}

		function toHTML(){
			$total="";
			$atributos="";
			$propiedades=$this->getPropiedades();
			
			$mapa=xml::add(null, "Mapa", array(
					"id"=>$propiedades["id"],
					"lon"=>$propiedades["lon"],
					"lat"=>$propiedades["lat"],
					"zoom"=>$propiedades["zoom"],
			));
			
			$cp = new ComponentePadre();
			$html="<div class='contenedorMapa'>".$cp->llamarClaseGenerica($mapa)."</div>";
			foreach($cp->css as $css){
				$this->agregarCss($css);
			}
			foreach($cp->js as $js){
				$this->agregarJs($js);
			}
			

			$otrosAtributos="";
			
			
			
				$puntos='

				<script type="text/javascript">
				var lastMark'.$propiedades["id"].'=null;
				$(function() {
					map'.$propiedades["id"].'.events.register("click", map'.$propiedades["id"].', function(e) {
						if (lastMark'.$propiedades["id"].'!=null){
							lastMark'.$propiedades["id"].'.destroy();
						}
						//var position = this.events.getMousePosition(e);
						var position = map'.$propiedades["id"].'.getLonLatFromPixel(e.xy);
						var size = new OpenLayers.Size(21,25);
						var offset = new OpenLayers.Pixel(-(size.w/2), -size.h);
						var icon = new OpenLayers.Icon("'.resolverPath().'/../Externos/OpenLayers/img/marker.png", size, offset);   
						lastMark'.$propiedades["id"].' = new OpenLayers.Marker(position,icon);
						markers'.$propiedades["id"].'.addMarker(lastMark'.$propiedades["id"].');
						position=position.transform(
								map'.$propiedades["id"].'.getProjectionObject(),
								new OpenLayers.Projection("EPSG:4326")
						);
						
						$("#in_'.$this->getIdObjeto().'").val(position.lon+","+position.lat);
					});
				});
				</script>
				';
				$input="
				<input id='in_".$this->getIdObjeto()."' type='hidden' name='".$this->getNombre()."' value='".$propiedades["valorPorDefecto"]."'  />\n
				";
				
				if (isset($propiedades["titulo"])){
					$total.="<div id='".$this->getIdObjeto()."Titulo' class='Titulo  etiquetaFormulario'>".$propiedades["titulo"]."</div>\n";
				}
				$total.="<div style='overflow:hidden;'>".$html.$input.$puntos."</div>";
				

				
			return $total;
		}
	}

?>
