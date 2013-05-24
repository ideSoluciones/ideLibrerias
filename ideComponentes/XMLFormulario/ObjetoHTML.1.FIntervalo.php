<?php

	/**
	*    @name FIntervalo
	*    @abstract	
	*    @author Jorge Gonzalez <jgonzalez@idesoluciones.com >
	*    @version 1.0
	*/
	
	class FIntervalo extends ObjetoHTML{

		function FIntervalo($idObjeto, $nombre, $propiedades, $tipoSalida, $accionPlus){
			$this->setIdObjeto($idObjeto);
			$this->setNombre($nombre);
			$this->setTipoSalida($tipoSalida);
			$this->setAccionPlus($accionPlus);
			if (!isset($propiedades["minimo"]))
				$propiedades["minimo"]="0";
			if (!isset($propiedades["maximo"]))
				$propiedades["maximo"]="100";
			if (!isset($propiedades["paso"]))
				$propiedades["paso"]="10";
			$this->setPropiedades($propiedades);
			
		}

		function toHTML(){
			$total="";
			$atributos="";
			$propiedades=$this->getPropiedades();
			$estilo="";
			$id=$propiedades["id"];
			if (isset($propiedades["error"])){
				if (strcmp(strtolower($propiedades["error"]),"true")==0){
					$estilo.=($propiedades["error"]==true?"error ":"");
				}
			}
			$estilo.="contenedorCampoFormulario".$propiedades["tipo"]." campoFormulario";
			
			if (isset($propiedades["pasos"])){
				$pasos=explode(',',$propiedades["pasos"]);
			}else{
				$pasos=array(1,100,1000,10000);
			}

            if (count($pasos)>1) {
			    $htmlPasos='<Select id="'.$id.'_paso" style="float: right;">'.
						   '<option>'.implode('</option><option>', $pasos).'</option></Select>';
            } else {
			    $htmlPasos='<input type="hidden" id="'.$id.'_paso"></input>';
            }
                

			
			foreach ($propiedades as $a => $i){
				if (strcmp($a,"autocomplete")==0 && strcmp($i,"off")==0){
					$atributos=$a."=".$i;
				}
			}
				

			
				$valores=array();
				$nombres=array();
				$maximoValor=0;
				foreach($propiedades["opciones"] as $nom=>$valor){
					$valores[]=$valor;
					$nombres[]=$nom;
				}
				$nomOld=$nom;
				
				
			
				if (count($propiedades["opciones"])==2){
					$maximoValor=$valores[1]=$propiedades["maximo"]-$valores[0];
					$textos_javaScriptFuncionSlide='"'.$nombres[0].': " + ui.value + " -  '.$nombres[1].': " + ($("#'.$id.'").slider("option", "max")-ui.value)';
					$textosInput_valoresIniciales=$nombres[0].': '.$valores[0].' - '.$nombres[1].': '.$valores[1];

					$variablesOcultas='$("#'.$id.'_'.$nombres[0].'").val(ui.value);
							$("#'.$id.'_'.$nombres[1].'").val($("#'.$id.'").slider("option", "max")-ui.value);';

					$valoresIniciales='$("#'.$id.'_'.$nombres[0].'").val($("#'.$id.'").slider("option", "max"));
								$("#'.$id.'_'.$nombres[1].'").val(0);
							$("#'.$id.'").slider("option", "value", $("#'.$id.'").slider("option", "max"));';
					
					$textos_valoresIniciales='"'.$nombres[0].': " + $("#'.$id.'").slider("option", "max") + " -  '.$nombres[1].': 0"';

					$valor="value: ".$valores[0].",
								range: 'min',";
					$valorRecrear="value: maximo,
									range: 'min',";
				
				}else if (count($propiedades["opciones"])==3){
					$maximoValor=$valores[2]=$propiedades["maximo"]-$valores[1]-$valores[0];
					$textos_javaScriptFuncionSlide='"'.$nombres[0].': " + ui.values[0] + " -  '.$nombres[1].': " + (ui.values[1]-ui.values[0]) + " -  '.$nombres[2].': " + ($("#'.$id.'").slider("option", "max")-ui.values[1])';
					$textosInput_valoresIniciales=$nombres[0].': '.$valores[0].' - '.$nombres[1].': '.$valores[1].' - '.$nombres[2].': '.$valores[2];
					
					
					$variablesOcultas='$("#'.$id.'_'.$nombres[0].'").val(ui.values[0]);
								$("#'.$id.'_'.$nombres[1].'").val(ui.values[1]-ui.values[0]);
								$("#'.$id.'_'.$nombres[2].'").val($("#'.$id.'").slider("option", "max")-ui.values[1]);';

					$valoresIniciales='$("#'.$id.'_'.$nombres[0].'").val($("#'.$id.'").slider("option", "max"));
								$("#'.$id.'_'.$nombres[1].'").val(0);
								$("#'.$id.'_'.$nombres[2].'").val(0);
								$("#'.$id.'").slider("option", "values", [maximo,maximo,maximo]);';

					$textos_valoresIniciales='"'.$nombres[0].': " + $("#'.$id.'").slider("option", "max") + " -  '.$nombres[1].': 0 -  '.$nombres[2].': 0"';

					$valor="values: [".$valores[0].', '.($valores[1]+$valores[0])."], 
								range: true,";
					$valorRecrear="values: [maximo,maximo],
									range: true,";
				}
				
				
			
				$total='
						<script type="text/javascript">
						var uiSlider'.$id.';
						function actualizarSlider'.$id.'(ui){
							$("#'.$id.'_valor").val('.$textos_javaScriptFuncionSlide.');
							'.$variablesOcultas.'
						}
						function reiniciarValores'.$id.'(ui){
							maximo=$("#'.$id.'").slider("option", "max"); 
							$("#'.$id.'_valor").val('.$textos_valoresIniciales.');
							'.$valoresIniciales.'
							pasos=parseInt($("#'.$id.'_paso").val());
							$("#'.$id.'").slider("destroy");
							uiSlider'.$id.' = $("#'.$id.'").slider({
								animate: true,
								min: '.$propiedades["minimo"].',
								max: maximo,
								step: pasos,
								'.$valorRecrear.'
								slide: function(event, ui) {
									actualizarSlider'.$id.'(ui);
								}
							});
						}
						$(function() {
							$("#'.$id.'_paso").change(function() 
								{ 
									reiniciarValores'.$id.'("#'.$id.'");
								});
							uiSlider'.$id.' = $("#'.$id.'").slider({
								animate: true,
								min: '.$propiedades["minimo"].',
								max: '.$propiedades["maximo"].',
								step: '.$pasos[0].',
								'.$valor.'
								slide: function(event, ui) {
									actualizarSlider'.$id.'(ui);
								}
							});
							
						});
						</script>

						<p>
							<label for="'.$this->getNombre().'">'.$propiedades["titulo"].':</label>
							'.$htmlPasos.'
							<input name="'.$this->getNombre().'" value="'.$textosInput_valoresIniciales.'"  readonly type="text" class="'.$estilo.'Slider" id="'.$id.'_valor" style="border:0; color:#f6931f; font-weight:bold;" />
						</p>
						<div id="'.$id.'"></div>
';
foreach($propiedades["opciones"] as $nom=>$valor){
					if ($propiedades["opciones"][$nomOld]==$propiedades["opciones"][$nom]){
						$valor=$maximoValor;
					}
					$total.='
					<input name="'.$this->getNombre().'_'.$nom.'" value="'.$valor.'"  type="hidden" class="'.$estilo.'Slider" id="'.$id.'_'.$nom.'"/>';
}
					$total.='';
		
			return $total;
		}
	}
	
?>
