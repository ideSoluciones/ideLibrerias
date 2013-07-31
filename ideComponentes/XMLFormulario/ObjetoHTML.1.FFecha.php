<?php

	/**
	*    @name FTexto
	*    @abstract
	*    @author Felipe Cano <fcano@idesoluciones.com >
	*    @version 1.0
	*/

	class FFecha extends ObjetoHTML{

		function FFecha($idObjeto, $nombre, $propiedades, $tipoSalida, $accionPlus){
			$this->setIdObjeto($idObjeto);
			$this->setNombre($nombre);
			$this->setPropiedades($propiedades);
			$this->setTipoSalida($tipoSalida);
			$this->setAccionPlus($accionPlus);

			$this->arrayReglas["reglas"]=array("\n".str_repeat("\t", 3).'idefecha: true');

			if (!isset($this->arrayReglas["mensajes"])){
				$this->arrayReglas["mensajes"]=array();
			}
			
			if(isset($propiedades["idefecha_msj"])){
				$this->arrayReglas["mensajes"][]="\n".str_repeat("\t", 3).'idefecha: "'.$propiedades["idefecha_msj"].'"';
			}else{
				if(isset($propiedades["hora"])){
					if (strcmp($propiedades["hora"],"false")!=0){
						$this->arrayReglas["mensajes"][]="\n".str_repeat("\t", 3).'idefecha: "Por favor ingrese una fecha válida en el formato y-m-d h:m:s"';
					}else{
						$this->arrayReglas["mensajes"][]="\n".str_repeat("\t", 3).'idefecha: "Por favor ingrese una fecha válida en el formato y-m-d"';
					}
				}else{
					$this->arrayReglas["mensajes"][]="\n".str_repeat("\t", 3).'idefecha: "Por favor ingrese una fecha válida en el formato y-m-d h:m:s"';
				}
			}
			$this->agregarJs("Externos/jquery/ui.datepicker-es.js");
			$sesion = Sesion::getInstancia();
			$this->agregarCss("Externos/jquery/jquery.timepickr/jquery.timepicker.css");
			$this->agregarJs("Externos/jquery/jquery.timepickr/jquery.timepicker.min.js");
		}

		public static function validar($campo,$valorDato){
			$respuesta = array("respuesta"=>true,"mensaje"=>"");
			//ideFecha
			if(!preg_match("/^(((1[6-9]|[2-9]\d)?\d{2}\-(((0?[13578]|1[02])\-31)|((0?[1,3-9]|1[0-2])\-(29|30))))|((((1[6-9]|[2-9]\d)?(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00)))\-0?2\-29)|((1[6-9]|[2-9]\d)?\d{2})\-((0?[1-9])|(1[0-2]))\-(0?[1-9]|1\d|2[0-8]))\s(20|21|22|23|[0-1]?\d):[0-5]?\d(:[0-5]?\d)?$/",$valorDato)){
				if(strcmp($campo["idefecha_msj"],"")!=0){
					$respuesta["mensaje"] = (string)$campo["idefecha_msj"];
				}else{
					if(strcmp($campo["hora"],"false")!=0){
						$respuesta["mensaje"] = "Por favor ingrese una fecha válida en el formato y-m-d h:m:s";
					}else{
						$respuesta["mensaje"] = "Por favor ingrese una fecha válida en el formato y-m-d";
					}
				}
				$respuesta["respuesta"] = false;
				return $respuesta;
			}
			return $respuesta;
		}

		function toHTML(){
			$total="";
			$atributos="";
			$propiedades=$this->getPropiedades();
			$estilo="";
			if (isset($propiedades["error"])){
				if (strcmp(strtolower($propiedades["error"]),"true")==0){
					$estilo.=($propiedades["error"]==true?"error ":"");
				}
			}
			$estilo.="contenedorCampoFormulario".$propiedades["tipo"]." campoFormulario";

			if (!isset($propiedades["valorPorDefecto"])){
				$propiedades["valorPorDefecto"]=" ";
			}
			if (!isset($propiedades["hora"])){
				$propiedades["hora"]="";
			}
			if (!isset($propiedades["saltoFecha"])){
				$saltoFecha=50;
			}

			static $fechas=0;
			$otrosAtributos="";
			foreach ($propiedades as $a => $i){
				if (strcmp($a,"autocomplete")==0 && strcmp($i,"off")==0){
					$atributos=$a."=".$i;
				}else{
					switch($a){
						case "tipo":case "campo":case "id":case "nombre":case "titulo":case "requerido":case "valorPorDefecto":case "error": case "hora":break;
						default: $otrosAtributos.=" ".$a."='".$i."'";
					}
				}
			}

				$defaults = explode(" ",$propiedades["valorPorDefecto"]);
				if (!isset($defaults[1])){
					$defaults[1]="00:00";
				}
				$total="";

				/*

				Se deshabilita la duplicación de los campos
				
				if($fechas==0){

				
					$total.="

						<script type='text/javascript'>

							function concatenarFechas(objeto){
								var nombres = objeto.currentTarget.id.split('_');
						
								//alert('Esta cambiando {'+objeto.currentTarget.id+'} '+nombres[0]+', '+nombres[1]+', '+nombres[2]);
								$('#'+nombres[0]).val( $('#'+nombres[0]+'_fecha').val()+' '+$('#'+nombres[0]+'_hora').val() );
							}
					
							var identificadorComponente".$propiedades["tipo"]."= new Array();

							function borrarComponentesDinamicos_".$propiedades["tipo"]."(id){
								var parametros=id.split('Contenedor');
								$('#'+parametros[0]+'_fecha').datepicker('destroy');";	
if(strcmp($propiedades["hora"],"false")!=0){
$total.="
								$('#'+parametros[0]+'_hora').timePicker('destroy');";					
}
$total.="
						
							}
							function crearComponenteDinamico_".$propiedades["tipo"]."(id){
								setIdentificadorComponente_".$propiedades["tipo"]."(id);
								crearComponente_".$propiedades["tipo"]."(id);
								var parametros=id.split('Contenedor');
								setIdentificadorComponente_".$propiedades["tipo"]."(parametros[0]);
								crearComponente_".$propiedades["tipo"]."(parametros[0]);
							}
							function setIdentificadorComponente_".$propiedades["tipo"]."(id){
								var parametros=id.split('Contenedor');
								if(parametros[1]==undefined)
									 parametros[1]='';
								identificadorComponente".$propiedades["tipo"]."[0]=parametros[0];
								identificadorComponente".$propiedades["tipo"]."[1]=parametros[1];							 
							}
					
							function crearComponente_".$propiedades["tipo"]."(id){
								var parametros=new Array();

								if(identificadorComponente".$propiedades["tipo"]."[0]==undefined)
									identificadorComponente".$propiedades["tipo"]."[0]=id;
								if(identificadorComponente".$propiedades["tipo"]."[1]==undefined)
									identificadorComponente".$propiedades["tipo"]."[1]='';
								parametros[0]=identificadorComponente".$propiedades["tipo"]."[0];
								parametros[1]=identificadorComponente".$propiedades["tipo"]."[1];
					
						

								$('#'+parametros[0]+'_hora'+parametros[1]).timePicker();
								$('#'+parametros[0]+'_fecha'+parametros[1]).datepicker({
									showOn: 'button', 
									buttonImage: '".resolverPath()."/../Librerias/img/calendar.gif', 
									buttonImageOnly: true
								});
						
								$('#'+parametros[0]+'_fecha'+parametros[1]).change(concatenarFechas);
								$('#'+parametros[0]+'_hora'+parametros[1]).change(concatenarFechas);
							}

						</script>";

				}
				
				$total.="	
				<script>
					$(function() {
						crearComponente_".$propiedades["tipo"]."('".$propiedades["id"]."');
					});
				</script>			
				*/
				$total.="	
				<script type='text/javascript'>
					function concatenarFechas".$propiedades["id"]."(){
						$('#".$propiedades["id"]."').val( $('#".$propiedades["id"].
						"_fecha').val()+' '+$('#".$propiedades["id"]."_hora').val() );
					}

					$(function() {";


				$sesion = Sesion::getInstancia();
				$total.="$('#".$propiedades["id"]."_hora').timepicker( {'timeFormat':'H:i', 'step':15 });";


				$total.="	
						$('#".$propiedades["id"]."_fecha').datepicker({
							showOn: 'both', 
							buttonImage: '".resolverPath()."/../Librerias/img/calendar.gif', 
							buttonImageOnly: true,
							changeMonth: true,
							changeYear: true,
							shortYearCutoff: ".$saltoFecha.",
							yearRange: '1900:".date("Y")."'
						});
						$('#".$propiedades["id"]."_fecha').change(concatenarFechas".$propiedades["id"].");
						$('#".$propiedades["id"]."_hora').change(concatenarFechas".$propiedades["id"].");
					});
				</script>
				";
				$total.="";
				if (isset($propiedades["titulo"])){
					$total.="<div id='".$this->getIdObjeto()."Titulo' class='".$estilo."Titulo  etiquetaFormulario'>".$propiedades["titulo"];
				}
				if (isset($propiedades["ayuda"])){
					$ayuda=xml::add(null, "Ayuda");
					xml::add($ayuda, "Wiki", $propiedades["ayuda"]);
					$total.=ide::renderizar($ayuda);
				}
				if (isset($propiedades["titulo"])){
					$total.="</div>\n";
				}
				$total.="		<input id='".$propiedades["id"]."_fecha' class='".$estilo."Fecha' type='text' name='".$this->getNombre()."-fecha' value='".$defaults[0]."' ".$atributos." />\n";

				// Se agrega el input de hora con ciertos parámetros dependiendo de si está setteado o no
				if(strcmp($propiedades["hora"],"false")!=0){
					$total.="
					<input id='".$propiedades["id"]."_hora' class='".$estilo."Fecha' type='text' name='".$this->getNombre()."-hora' value='".$defaults[1]."' ".$atributos." />\n
					<input id='".$propiedades["id"]."' $otrosAtributos class='".$estilo."Fecha' type='hidden' name='".$this->getNombre()."' value='".$propiedades["valorPorDefecto"]."' ".$atributos." />\n";

				}else{
					$total.="
					<input id='".$propiedades["id"]."_hora' class='".$estilo."Fecha' type='hidden' name='".$this->getNombre()."-hora' value='00:00' ".$atributos." />\n
					<input id='".$propiedades["id"]."' $otrosAtributos class='".$estilo."Fecha' type='hidden' name='".$this->getNombre()."' value='".$defaults[0]." 00:00' ".$atributos." />\n";
				}
				$propiedades["errorMensaje"]=isset($propiedades["errorMensaje"])?$propiedades["errorMensaje"]:"";
				$total.=isset($propiedades["error"])?"					<label class='error'>".$propiedades["errorMensaje"]."</label>":"";
				$total.="\n";

			$fechas++;
			return $total;
		}
	}

?>
