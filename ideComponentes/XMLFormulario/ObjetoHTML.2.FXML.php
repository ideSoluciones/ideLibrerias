<?php

	class FXml extends FTexto {
	    var $ideMotorXML;
		function FTexto($idObjeto, $nombre, $propiedades, $tipoSalida, $accionPlus, $xml){
			parent::FCampo($idObjeto, $nombre, $propiedades, $tipoSalida, $accionPlus, $xml);
			$ideMotorXML=false;
		}

        function toHTML(){
			$total="";
			$propiedades=$this->getPropiedades();
			$estilo="";
			if (isset($propiedades["error"])){
				if (strcmp(strtolower($propiedades["error"]),"true")==0){
					$estilo.=($propiedades["error"]==true?"error ":"");
				}
			}

			$otrosAtributos="";
			foreach($propiedades as $a => $i){
				switch($a){
					case "formatoSalida":case "errorMensaje":case "estilo":case "columnas":case "filas":case "opciones":case "tipo":case "campo":case "id":case "nombre":case "titulo":case "requerido":case "valorPorDefecto":case "error":break;
					default: $otrosAtributos.=" ".$a."='".$i."'";
				}
			}
			
			$estilo.="contenedorCampoFormulario".$propiedades["tipo"]." campoFormulario";
			if (strcmp($this->getTipoSalida(), "html")==0){
                $this->motor=isset($propiedades["formatoSalida"])?$propiedades["formatoSalida"]:"";
                $alto=(!isset($propiedades["alto"])?"300":$propiedades["alto"]);
                $ancho=(!isset($propiedades["ancho"])?"":$propiedades["ancho"]);

                $xml=$this->getXML();
                $parametros=array();
                foreach($xml->children() as $hijo){
                    $parametros[(string)$hijo->getName()]=$hijo;
                }

                $valor=$propiedades["valorPorDefecto"];
                $valorInterno="";


                $input="input";
                $tipoInput="hidden";

                if (!isset($propiedades["formatoSalida"])){
                	$valor="";
					$valorInterno=$propiedades["valorPorDefecto"];
					if (strcmp((string)$propiedades["valorPorDefecto"], "")==0){
						//var_dump();
						$xml=$this->getXml();
						$valorInterno.=$xml;
						foreach ($xml->children() as $hijo) {
							$valorInterno.=$hijo->asXML();
						}
					}
                    $input="";
                    $tipoInput="textarea";
                    $javascript="

                    <script type='text/javascript'>
                        $(function() {
                            var editor = CodeMirror.fromTextArea('".$this->getIdObjeto()."', {
                                height: '350px',
                                parserfile: 'parsexml.js',
                                stylesheet: '".resolverPath()."/../Externos/codemirror/css/xmlcolors.css',
                                path: '".resolverPath()."/../Externos/codemirror/js/',
                                continuousScanning: 500,
                                lineNumbers: true
                            });
                        });
                    </script>";
                }else if (strcmp($propiedades["formatoSalida"], "javascript")==0){
                    $estiloInterno="height:".$alto."px; width:".$alto."px; background-color:#006699;";
                    $javascript="
                    <script type='text/javascript'>
					    $(function() {
						    $('#".$this->getIdObjeto()."Motor').generarEntornoXML('".$parametros["Especificacion"]->asXML()."');
					    });
					</script>";
				}else if (strcmp($propiedades["formatoSalida"], "actionscript")==0){

			        $this->arrayReglas["reglas"][]="\n".str_repeat("\t", 3).'idexml: true';

                    $this->ideMotorXML=true;
                    $estiloInterno="height:".$alto."px; width:".$ancho."px; background-color:#669900;";
					$contador=0;
					foreach($parametros["Datos"]->attributes() as $b) {
						$contador++;
					}
					foreach($parametros["Datos"]->children() as $b) {
						$contador++;
					}
                    if ($contador==0){
                        $parametros["Datos"]=new SimpleXMLElement("<Datos/>");
                    }

					$sesion=Sesion::getInstancia();
					

//                                swf:    '".resolverPath()."/../Librerias/as/ideMotorXML.swf',
                    $javascript="
                    <script type='text/javascript'>
                        $(function() {
                            $('#".$this->getIdObjeto()."Motor').flash(
                            {
                                id:     '".$this->getIdObjeto()."Swf',
                                swf:    '".resolverPath()."/../Librerias/as/ideMotor.swf',
                                width:  '".$ancho."',
                                height: '".$alto."',
                                allowScriptAccess: 'always',

                                flashvars: {
                                    especificacion: '".str_replace("\n", '', $parametros["Especificacion"]->asXML())."',
                                    datos:          '".str_replace("\n", '', $parametros["Datos"]->asXML())."',
                                    ancho:          '".$ancho."',
                                    alto:           '".$alto."',
                                    idCasoUso:      '".$sesion->leerParametro("idCasoUso")."',
                                    nombreCasoUso:  '".$sesion->leerParametro("nombreCasoUso")."',
							        idContenedor:	'".$this->getIdObjeto()."Ajax',
							        idSwf:			'".$this->getIdObjeto()."Swf',
                                }
                            });
                        });
                        function actualizarVariable(valor){
                            $('#".$this->getIdObjeto()."').val(valor);
                        }
						function thisMovie(movieName) {
							if (navigator.appName.indexOf('Microsoft') != -1) {
								return window[movieName];
							} else {
								return document[movieName];
							}
						}
						function sendToActionScript".$this->getIdObjeto()."Swf(formulario) {
							//alert('Agregando el formulario '+formulario);
							datosFormulario = $(formulario).serialize();
							//alert('Los datos del formularios son: '+datosFormulario);
							thisMovie('".$this->getIdObjeto()."Swf').sendToActionScript(datosFormulario);
						}

                    </script>
					<div
						id='".$this->getIdObjeto()."Ajax'
					>
					</div>

                    ";
				}

		        if (strcmp($this->motor, "javascript")==0){
	   			    $this->agregarJs("Externos/jquery/jquery.ideMotorXML.js");
		        }else if (strcmp($this->motor, "actionscript")==0){
	   			    $this->agregarJs("Externos/jquery/jquery.swfobject.js");
		        }else{
	   			    $this->agregarJs("Externos/codemirror/js/codemirror.js");
	   			    $this->agregarCss("Externos/codemirror/css/codemirror.css");
		        }

/*
				<script type='text/javascript'>
					$(function() {
						$('#".$this->getIdObjeto()."Redimension').resizable(
//							Si se necesita redimencionar los componentes internos se puede hacer de esta manera
//							{ alsoResize: '#".$this->getIdObjeto()."Contenedor>div#".$this->getIdObjeto().">div'}
						);
					});
				</script>
*/
				$total=$javascript."
				<label>".$propiedades["titulo"]."</label>

				<div id='".$this->getIdObjeto()."Redimension' style='border:1px solid;width:{$ancho}px;height:".($alto+55)."px;overflow:hidden;'>
				<div
					id='".$this->getIdObjeto()."Contenedor'
					class='".$estilo."_Contenedor'
					name='".$this->getNombre()."_Contenedor'
				>";

				$campoInput="<".$tipoInput." $otrosAtributos name='".$this->getNombre()."' value='".$valor."'  type='".$tipoInput."' id='".$this->getIdObjeto()."' >".$valorInterno."</".$tipoInput.">";

				$total.="
				
					<div
						id='".$this->getIdObjeto()."Motor' 
						name='".$this->getNombre()."Motor'
					 ></div>\n
					".$campoInput."
				</div></div>\n";
			}
			return $total;
		}


	}
?>
