<?php

	class Estadistica extends ComponentePadre implements componente{

		var $propiedades=array();
		
		function Estadistica(){
			
			$this->propiedades["tipo"]="histograma";
			$this->propiedades["ancho"]=500;
			
			//<!--[if IE]><script language="javascript" type="text/javascript" src="excanvas.js"></script><![endif]-->
			
			//$this->js[]="Librerias/ideComponentes/Navegador/navegador.js";
			//$this->css[]="Librerias/ideComponentes/Navegador/navegador.css";
		}

		function obtenerResultado($xml, $principal=true, $id=0){
		
			static $contadorEstadisticas=0;
			if (strcmp($xml["id"], "")==0){
				$xml["id"]="Estadistica".$contadorEstadisticas;
			}
			$contadorEstadisticas++;
		
			$datos=array();
			$mayor=0;
			$datosParaArray=array();
		
			foreach($xml->children() as $hijo){
				switch($hijo->getName()){
					case "Propiedad":
						$nombre=(string)$hijo["nombre"];
						$valor=(string)$hijo["valor"];
						$this->propiedades[$nombre]=$valor;
						
						break;
					case "Valores":
						foreach($hijo->children() as $dato){
							//var_dump($dato);
							$datos["".$dato['etiqueta'].""]="".$dato['valor'];
							if ($mayor<intval($dato['valor']))
								$mayor=$dato['valor'];
								
							$datosParaArray[]="['".$dato['etiqueta']."', ".$dato['valor']."]";
							//$datosParaArray[]=$dato['valor'];
								
						}
						break;
					default:
						$html.=$this->llamarClaseGenerica($hijo);
				}
			}
			//var_dump($datos);
			$ancho="300";
			$alto="500";
			if (strcmp($xml["ancho"], "")!=0){
				$ancho=$xml["ancho"];
			}
			if (strcmp($xml["alto"], "")!=0){
				$alto=$xml["alto"];
			}
			if (!isset($this->propiedades["titulo"])){
				$this->propiedades["titulo"]="";
			}
			$html="<div id='".$xml["id"]."' style='height:".$ancho."px;width:".$alto."px;'></div>";
			$this->js[]="Externos/jquery/jqplot/jquery.jqplot.min.js";
			$this->css[]="Externos/jquery/jqplot/jquery.jqplot.css";

			if (strcmp($this->propiedades["tipo"], "histograma")==0
				||
				strcmp($this->propiedades["tipo"], "linea")==0){
				
				$this->js[]="Externos/jquery/jqplot/plugins/jqplot.dateAxisRenderer.min.js";
				$this->js[]="Externos/jquery/jqplot/plugins/jqplot.canvasTextRenderer.min.js";
				$this->js[]="Externos/jquery/jqplot/plugins/jqplot.canvasAxisTickRenderer.min.js";
				$this->js[]="Externos/jquery/jqplot/plugins/jqplot.categoryAxisRenderer.min.js";
				$this->js[]="Externos/jquery/jqplot/plugins/jqplot.barRenderer.min.js";
				
				$this->js[]="Externos/jquery/jqplot/plugins/jqplot.highlighter.min.js";
				$this->js[]="Externos/jquery/jqplot/plugins/jqplot.cursor.min.js";

				$infoSobreSerie="";
				$serie="";
				if (isset($this->propiedades["serie"])){
					$infoSobreSerie="legend:{show:true},";
					$serie=$this->propiedades["serie"];
				}
				
				$tipoGrafica="";
				if (strcmp($this->propiedades["tipo"], "histograma")==0){
					$tipoGrafica="renderer:$.jqplot.BarRenderer";
				}
				
				$javascript="
				<script >
				$(function() {
					var ".$xml["id"]."_datos=[".implode(',', $datosParaArray)."];
					$.jqplot(
						'".$xml["id"]."',
						[".$xml["id"]."_datos],
						{
							title: '".$this->propiedades["titulo"]."',
							".$infoSobreSerie."
							series:[
								{
									//showLabel:true,
									label:'".$serie."',
									".$tipoGrafica."
								},
							],
							axesDefaults: {
								tickRenderer: $.jqplot.CanvasAxisTickRenderer ,
								tickOptions: {
									angle: -30,
									fontSize: '10pt'
								}
							},
							axes: {
								xaxis: {
									renderer: $.jqplot.CategoryAxisRenderer
								},
								yaxis: {
									autoscale:true
								}
							},
							cursor: {show: false}

						}
					);
					
				});
				</script>
					";
			
			}else if (strcmp($this->propiedades["tipo"], "torta")==0){
			
				$this->js[]="Externos/jquery/jqplot/plugins/jqplot.pieRenderer.min.js";
				$this->js[]="Externos/jquery/jqplot/plugins/jqplot.highlighter.min.js";
			

				$javascript="
					<script >
					
						$(function() {
						
							var ".$xml["id"]."_datos=[".implode(',', $datosParaArray)."];
							$.jqplot(
								'".$xml["id"]."',
								[".$xml["id"]."_datos],
								{
									title: '".$this->propiedades["titulo"]."',
									seriesDefaults:{
										renderer:$.jqplot.PieRenderer,
										rendererOptions:{sliceMargin:4, lineWidth:5}
									},
									legend:{
										show:true, 
										location: 'w'
									},
									cursor: {show: false}
								}
							);
					
						});
						
					</script>
						";
			
			}else if (strcmp($this->propiedades["tipo"], "candelas")==0){
				$this->js[]="Externos/jquery/jqplot/plugins/jqplot.ohlcRenderer.min.js";
				$this->js[]="Externos/jquery/jqplot/plugins/jqplot.dateAxisRenderer.min.js";
				$this->js[]="Externos/jquery/jqplot/plugins/jqplot.highlighter.min.js";
				


				$javascript="
				<script >
					$(function() {
						var ".$xml["id"]."_datos=[".implode(',', $datosParaArray)."];
						$.jqplot(
							'".$xml["id"]."',
							[".$xml["id"]."_datos],
							{
								title: '".$this->propiedades["titulo"]."',
								series: [{renderer:$.jqplot.OHLCRenderer, rendererOptions:{candleStick:true}}],
								axesDefaults:{},
								axes: {
									xaxis: {
										renderer:$.jqplot.DateAxisRenderer,
										tickOptions:{formatString:'%Y.%m'}
									},
									yaxis: {
										tickOptions:{formatString:'$%.2f'}
									}
								},
								highlighter: {
									showMarker:false,
									tooltipAxes: 'y',
									tooltipLocation: 'nw',
									yvalues: 4,
									formatString:
									   '<table class=\"jqplot-highlighter\">".
										"<tr><td>open:</td><td>%s</td></tr>".
										"<tr><td>hi:</td><td>%s</td></tr>".
										"<tr><td>low:</td><td>%s</td></tr>".
										"<tr><td>close:</td><td>%s</td></tr>".
										"</table>'
								},
								cursor: {show: false}

							}
						);
					});
					</script>
					";

				
			}
			
			return $javascript.$html;
		}
		
	}
	
	
	
?>
