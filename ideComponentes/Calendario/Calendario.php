<?php

	class Calendario extends ComponentePadre implements componente{
		
		function Calendario(){
			$this->js[]="Externos/jquery/jquery.weekcalendar/jquery.weekcalendar.js";
			$this->css[]="Externos/jquery/jquery.weekcalendar/jquery.weekcalendar.css";
		}
	
		function obtenerResultado($xml, $principal=true){
			static $numeroCalendario=0;
			$numeroCalendario++;
			$html="";
	
			$id="calendario".$numeroCalendario;
			if (strcmp($xml["id"], "")!=0){
				$id=$xml["id"];
			}
	

			$eventos="";
			$colores="var colores=Array();\n";
			$colores.="var coloresFuente=Array();\n";
			$propiedades="";
			$contadorEventos=0;
			foreach($xml->children() as $hijo){
				switch($hijo->getName()){
					case "Nodo":
						$contadorEventos++;
						$desde=strtotime((string)$hijo["desde"]);
						$hasta=strtotime((string)$hijo["hasta"]);
						$eventos.='{"id":'.$contadorEventos.', "start": new Date('.date("Y",$desde).', '.date("m",$desde).'-1, '.date("d",$desde).'-1, '.date("H",$desde).', '.date("i",$desde).'), "end": new Date('.date("Y",$hasta).', '.date("m",$hasta).'-1, '.date("d",$hasta).'-1, '.date("H",$hasta).', '.date("i",$hasta).'),"title":"'.(string)$hijo["titulo"].'"},'."\n";
						
						if (isset($hijo["colorFondo"])){
							$colores.="colores[".$contadorEventos."]='".(string)$hijo["colorFondo"]."';\n";
						}else{
							$colores.="colores[".$contadorEventos."]='#FFF';\n";
						}
						if (isset($hijo["colorFuente"])){
							$colores.="coloresFuente[".$contadorEventos."]='".(string)$hijo["colorFuente"]."';\n";
						}else{
							$colores.="coloresFuente[".$contadorEventos."]='#000';\n";
						}
						
						break;
					case 'Propiedad':
						$propiedades.=$hijo["nombre"]." : ".$hijo.",";
						break;
					default:
						$html.=$this->llamarClaseGenerica($hijo);
				}
			}
			
				$html.='
				<script >
				
				'.$colores.'
				
				$(function() {	 				
					var eventData = {
						events : [
						   	'.$eventos.'
						]
					};				
				
					$("#'.$id.'").weekCalendar({

						firstDayOfWeek : 1,
						daysToShow : 6,
						timeslotsPerHour: 2,
						height : function($calendar) {
							return $(window).height() - $("h1").outerHeight() - 1;
						},

						eventRender : function(calEvent, event) {
							event.css({"backgroundColor": colores[calEvent.id], "border":"1px solid #000"});
							event.css({"color": coloresFuente[calEvent.id]});
						},

						data:eventData,
						shortMonths : ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
						longMonths : ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
						shortDays : ["Dom", "Lun", "Mar", "Mie", "Jue", "Vie", "Sab"],
						longDays : ["Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado"],
						buttonText : {
							today : "Hoy",
							lastWeek : "&nbsp;&lt;&nbsp;",
							nextWeek : "&nbsp;&gt;&nbsp;"
						},
						timeSeparator : "/",
						businessHours : {start: 7, end: 18, limitDisplay : true},
						draggable : function(calEvent, element) {
							return false;
						},
						resizable : function(calEvent, element) {
							return false;
						},
						'.$propiedades.'

					});
				});
				</script>
				<div id="'.$id.'"></div>';
				
			/*
			static $ultimoHijo="";
			foreach($xml->children() as $hijo){
				switch($hijo->getName()){
					case "Nodo":
						$html.='<h3><a href="#">'.$hijo['titulo'].'</a></h3>'.
							'<div>'.
								$this->obtenerResultado($hijo, false).
							'</div>';
						break;
					default:
						$html.=$this->llamarClaseGenerica($hijo);
				}
			}
			*/
			return $html;
		}
		
	}
	
	
	
?>
