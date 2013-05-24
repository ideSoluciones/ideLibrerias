<?php

	/**
	*    @name FColor
	*    @abstract
	*    @author Jorge Gonzalez <jgonzalez@idesoluciones.com >
	*    @version 1.0
	*/

	class FColor extends ObjetoHTML{

		private $R;
		private $G;
		private $B;

		function FColor($idObjeto, $nombre, $propiedades, $tipoSalida, $accionPlus){
			$this->ObjetoHTML();
			$this->setIdObjeto($idObjeto);
			$this->setNombre($nombre);
			$this->setTipoSalida($tipoSalida);
			$this->setAccionPlus($accionPlus);
			if (!isset($propiedades["valorPorDefecto"])){
				$propiedades["valorPorDefecto"]="#000000";
			}
		    $listaColores=$this->html2rgb($propiedades["valorPorDefecto"]);

			$this->setPropiedades($propiedades);
			$this->setR($listaColores[0]);
			$this->setG($listaColores[1]);
			$this->setB($listaColores[2]);

			$this->agregarCss("Externos/jquery/ui.colorPicker/css/colorpicker.css");
			$this->agregarCss("Externos/jquery/ui.colorPicker/css/layout.css");
			$this->agregarJs("Externos/jquery/ui.colorPicker/js/colorpicker.js");
			if(!isset($this->arrayReglas["reglas"]))
				$this->arrayReglas["reglas"]=array();

			$this->arrayReglas["reglas"][]="\n".str_repeat("\t", 3).'color: true';
			if(isset($propiedades["color_msj"])){
				$this->arrayReglas["mensajes"][]="\n".str_repeat("\t", 3).'color: "'.$propiedades["color_msj"].'"';
			}
		}

		public static function validar($campo,$valorDato){
			$respuesta = array("respuesta"=>true,"mensaje"=>"");
			//color
			if(!preg_match("/^#?([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?$/",$valorDato)){
				if(strcmp($campo["color_msj"],"")!=0){
					$respuesta["mensaje"] = (string)$campo["color_msj"];
				}else{
					$respuesta["mensaje"] = "Por favor ingrese un color válido";
				}
				$respuesta["respuesta"] = false;
				return $respuesta;
			}
			return $respuesta;
		}

		function html2rgb($color){
			if (isset($color[0])){
			    if ($color[0] == '#')
					$color = substr($color, 1);
			}

		    if (strlen($color) == 6)
			list($r, $g, $b) = array($color[0].$color[1],
				                 $color[2].$color[3],
				                 $color[4].$color[5]);
		    elseif (strlen($color) == 3)
			list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
		    else
			return false;

		    $r = hexdec($r); $g = hexdec($g); $b = hexdec($b);

		    return array($r, $g, $b);
		}

		function getR(){return $this->R;}
		function getG(){return $this->G;}
		function getB(){return $this->B;}

		function setR($r){$this->R=$r;}
		function setG($g){$this->G=$g;}
		function setB($b){$this->B=$b;}

		function toHTML(){
			$total="";
			$atributos="";
			$propiedades=$this->getPropiedades();
			$estilo="";
			if (isset($propiedades["error"])){
				$estilo.=($propiedades["error"]==true?"error ":"");
			}
			$estilo.="contenedorCampoFormulario".$propiedades["tipo"]." campoFormulario";
			$otrosAtributos="";
			foreach ($propiedades as $a => $i){
				if (strcmp($a,"autocomplete")==0 && strcmp($i,"off")==0){
					$atributos=$a."=".$i;
				}else{
					switch($a){
						//TODO Falta revisar cuando el campo color es requerido
						case "errorMensaje":case "tipo":case "valorPorDefecto":case "id":case "titulo":case "error":break;
						default: $otrosAtributos.=" ".$a."='".$i."'";
					}
				}
			}

				$total="

				<script type='text/javascript'>

					function crearComponente_".$propiedades["tipo"]."(id){
				
					    $('#'+id).change(function () {
						    $('#'+id+'Div div').css('backgroundColor', this.value);
						    $('#'+id+'Div').ColorPickerSetColor(this.value);
					    }).change();
						$('#'+id+'Div').ColorPicker({
							color: '".$propiedades["valorPorDefecto"]."',
							onShow: function (colpkr) {
								$(colpkr).fadeIn(500);
								return false;
							},
							onHide: function (colpkr) {
								$(colpkr).fadeOut(500);
								return false;
							},
							onChange: function (hsb, hex, rgb) {
								//alert('cambio a '+hex);
								$('#'+id+'Div div').css('backgroundColor', '#' + hex);
								$('#'+id).val('#'+hex);
							}
						});
					}	
					function borrarComponentesDinamicos_".$propiedades["tipo"]."(id){
						//alert('Toca borrar los elementos '+id);
						var parametros=id.split('Contenedor');
						$('#'+parametros[0]+'Div').ColorPicker('destroy');
						
					}
					$(function() {
						//setIdentificadorComponente_".$propiedades["tipo"]."('".$propiedades["id"]."');
						crearComponente_".$propiedades["tipo"]."('".$propiedades["id"]."');
					});

				</script>";

				
				$total.="";
				if (isset($propiedades["titulo"]))
					$total.="	<div id='".$this->getIdObjeto()."Titulo' class='".$estilo."Titulo  etiquetaFormulario'>".$propiedades["titulo"]."</div>\n";
				$total.="		<input id='".$this->getIdObjeto()."' class='".$estilo."Color' type='text' name='".$this->getNombre()."' value='".$propiedades["valorPorDefecto"]."' ".$atributos." $otrosAtributos />\n";
				$error=isset($propiedades["error"])?$propiedades["error"]:false;
				$total.=$error?"					<label class='error'>".(isset($propiedades["errorMensaje"])?$propiedades["errorMensaje"]:"")."</label>":"";
				$total.="					<div id='".$this->getIdObjeto()."Div' class='colorSelector'>
						<div style='background-color: ".$propiedades["valorPorDefecto"].";' ></div>
					</div>\n";
			return $total;
		}
	}

?>
