<?php

	/**
	*    @name FIntervalo2
	*    @abstract	
	*    @author Jorge Gonzalez <jgonzalez@idesoluciones.com >
	*    @version 1.0
	*/
	
	class FIntervalo2 extends ObjetoHTML{

		function FIntervalo2($idObjeto, $nombre, $propiedades, $tipoSalida, $accionPlus){
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
				
				$total = "<style>
						.intervalo{
							overflow:hidden;
						}
						.opcionIntervalo{
							float:left;
						}
						.comandosIntervalo{
							cursor: pointer;
							float: left;
							font-size: 30px;
							margin: 0 20px;
						}
						.comandosIntervalo i{
							margin: 0 5px;
						}
					</style>";
					
				//$total .= "hola mundo: desde ".$propiedades["minimo"]." hasta ".$propiedades["maximo"];
				
				
				//$propiedades["maximo"]
				$total .= '<div class="intervalo"><div class="tituloIntervalo">'.$propiedades["titulo"].':</div>';
				$intermedio = "";
				foreach($propiedades["opciones"] as $nom=>$valor){
					$total .= $intermedio.'<div class="opcionIntervalo" >'.$nom.'<br/><input id="'.$this->getNombre().'_'.$nom.'" name="'.$nom.'" value="'.$valor.'"/></div>';
					$total .= '<script type="text/javascript">
						$(function() {
							$("#'.$this->getNombre().'_'.$nom.'").focusout(function (){
								normalizar_'.$this->getNombre().'("'.$nom.'");
							});
						});
					</script>';

					$intermedio = '<div class="comandosIntervalo">
						<i id="todo_izquierda_'.$this->getNombre().'" class="icon-fast-backward"></i>
						<i id="paso_izquierda_'.$this->getNombre().'" class="icon-step-backward"></i>
						<i id="paso_derecha_'.$this->getNombre().'"   class="icon-step-forward"></i>
						<i id="todo_derecha_'.$this->getNombre().'"   class="icon-fast-forward"></i>
						</div>
						';
					$valores[]=$valor;
					$nombres[]=$nom;
				}

				$total .= '
					<script type="text/javascript">
						var valorMaximo_'.$this->getNombre().' = '.$propiedades["maximo"].';
						var valorMinimo_'.$this->getNombre().' = '.$propiedades["minimo"].';
						var paso = '.$propiedades["paso"].';
						var elementos_'.$this->getNombre().' = '.json_encode($nombres, false).';
						function normalizar_'.$this->getNombre().'(id){
							var valorIngresado=0;
							elementos_'.$this->getNombre().'.forEach(function(nombre) {
								if (nombre == id){
									valorIngresado = 0+parseFloat($("#'.$this->getNombre().'_"+nombre).val());
									if (valorIngresado>valorMinimo_'.$this->getNombre().'){
										if (valorIngresado<valorMaximo_'.$this->getNombre().'){
											$("#'.$this->getNombre().'_"+nombre).val(valorIngresado.toFixed(2));
										}else{
											valorIngresado = valorMaximo_'.$this->getNombre().';
											$("#'.$this->getNombre().'_"+nombre).val(valorMaximo_'.$this->getNombre().'.toFixed(2));
										}
									}else{
										valorIngresado = 0;
										$("#'.$this->getNombre().'_"+nombre).val(valorIngresado.toFixed(2));
									}
								}
							});
							elementos_'.$this->getNombre().'.forEach(function(nombre) {
								if (nombre != id){
									valorRestante = valorMaximo_'.$this->getNombre().'-valorIngresado;
									$("#'.$this->getNombre().'_"+nombre).val(valorRestante.toFixed(2));
								}
							});
							
						}
						$(function() {
							$("#todo_izquierda_'.$this->getNombre().'").click(function(){
								$("#'.$this->getNombre().'_'.$nombres[0].'").val(valorMaximo_'.$this->getNombre().');
								$("#'.$this->getNombre().'_'.$nombres[1].'").val(valorMinimo_'.$this->getNombre().');
							});
							$("#paso_izquierda_'.$this->getNombre().'").click(function(){
								if ($("#'.$this->getNombre().'_'.$nombres[1].'").val()-paso>0){
									$("#'.$this->getNombre().'_'.$nombres[0].'").val( paso+parseFloat($("#'.$this->getNombre().'_'.$nombres[0].'").val()));
									$("#'.$this->getNombre().'_'.$nombres[1].'").val(-paso+parseFloat($("#'.$this->getNombre().'_'.$nombres[1].'").val()));
								}else{
									$("#'.$this->getNombre().'_'.$nombres[0].'").val(valorMaximo_'.$this->getNombre().');
									$("#'.$this->getNombre().'_'.$nombres[1].'").val(valorMinimo_'.$this->getNombre().');
								}
							});
							$("#paso_derecha_'.$this->getNombre().'").click(function(){
								if ($("#'.$this->getNombre().'_'.$nombres[0].'").val()-paso>0){
									$("#'.$this->getNombre().'_'.$nombres[0].'").val(-paso+parseFloat($("#'.$this->getNombre().'_'.$nombres[0].'").val()));
									$("#'.$this->getNombre().'_'.$nombres[1].'").val( paso+parseFloat($("#'.$this->getNombre().'_'.$nombres[1].'").val()));
								}else{
									$("#'.$this->getNombre().'_'.$nombres[0].'").val(valorMinimo_'.$this->getNombre().');
									$("#'.$this->getNombre().'_'.$nombres[1].'").val(valorMaximo_'.$this->getNombre().');
								}
							});
							$("#todo_derecha_'.$this->getNombre().'").click(function(){
								$("#'.$this->getNombre().'_'.$nombres[0].'").val(valorMinimo_'.$this->getNombre().');
								$("#'.$this->getNombre().'_'.$nombres[1].'").val(valorMaximo_'.$this->getNombre().');
							});

						});
					</script>
				';
				
				$total .= '</div>';
				
				
				
				
			/*	
			
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
							pasos=parseFloat($("#'.$id.'_paso").val());
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
							$("#todo_izquierda_'.$this->getNombre().'").click(function(){
								uiSlider'.$id.'.slider( "value", '.$propiedades["minimo"].');
								actualizarSlider'.$id.'(uiSlider'.$id.');
							});
							$("#paso_izquierda_'.$this->getNombre().'").click(function(){
								uiSlider'.$id.'.slider( "value", uiSlider'.$id.'.slider( "value")-'.$pasos[0].');
								actualizarSlider'.$id.'(uiSlider'.$id.');
							});
							$("#paso_derecha_'.$this->getNombre().'").click(function(){
								uiSlider'.$id.'.slider( "value", uiSlider'.$id.'.slider( "value")+'.$pasos[0].');
								actualizarSlider'.$id.'(uiSlider'.$id.');
							});
							$("#todo_derecha_'.$this->getNombre().'").click(function(){
								uiSlider'.$id.'.slider( "value", '.$propiedades["maximo"].');
								actualizarSlider'.$id.'(uiSlider'.$id.');
							});
							
						});
						</script>

						<p>
							<label for="'.$this->getNombre().'">'.$propiedades["titulo"].':</label>
							'.$htmlPasos.'
							<input name="'.$this->getNombre().'" value="'.$textosInput_valoresIniciales.'"  readonly type="text" class="'.$estilo.'Slider" id="'.$id.'_valor" style="width: 300px; border:0; color:#f6931f; font-weight:bold;" />
						</p>
						<div id="'.$id.'"></div>
';
$intermedio = '';
foreach($propiedades["opciones"] as $nom=>$valor){
					if ($propiedades["opciones"][$nomOld]==$propiedades["opciones"][$nom]){
						$valor=$maximoValor;
					}
					$total.=$intermedio.'
					'.$nom.' $<input name="'.$this->getNombre().'_'.$nom.'" value="'.$valor.'"  type="text" class="'.$estilo.'Slider" id="'.$id.'_'.$nom.'" style="width: 200px;"/>';
					
					$intermedio = '
						<i id="todo_izquierda_'.$this->getNombre().'" class="icon-fast-backward"></i>
						<i id="paso_izquierda_'.$this->getNombre().'" class="icon-step-backward"></i>
						<i id="paso_derecha_'.$this->getNombre().'"   class="icon-step-forward"></i>
						<i id="todo_derecha_'.$this->getNombre().'"   class="icon-fast-forward"></i>
						';
}
					$total.='';
			*/
			return $total;
		}
	}
	
?>
