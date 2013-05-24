<?php

	class Columnas extends ComponentePadre implements componente{
		

		function obtenerResultado($xml){
		
			static $contadorColumnas=0;
			$contadorColumnas++;
			$html="";


	
			
			if ($contadorColumnas==1){
				$html.="
				<style>
					.sinPadding{
						padding: 0px !important;
					}
					.ui-layout-resizer{
						background-color:#555555;
					}
					.ui-layout-toggler{
						background-color:#AAAAAA;
					}
					.ui-layout-pane {
						overflow:auto;
					}

				</style>";
			
			}
					
			
			
			$this->js[]="Externos/jquery/jquery.layout/jquery.layout-latest.js";
			$this->js[]="Externos/jquery/jquery.layout/jquery.layout.state.js";
			$this->js[]="Externos/jquery/jquery.layout/json2.js";
		
			$this->setAtributoInexistente($xml, 'var', "");
			$this->setAtributoInexistente($xml, 'tipo', "v");
			$this->setAtributoInexistente($xml, 'altura', "100%");
			$this->setAtributoInexistente($xml, 'id', "Columna".$contadorColumnas);
		


			if (isset($xml["idPadre"])){
				//echo "está seteado idPadre";
				$contenedor="";			
				$finContenedor="";
				$id=$xml["idPadre"];
			}else{
				$contenedor="<div id='".$xml["id"]."' style='height:".$xml["altura"]."; padding:0px; min-height:500px'>";			
				$finContenedor="</div>";
				$id=$xml["id"];
			}		
			

			
			
			if (strcmp($xml["tipo"], "h")==0){
				$nombres=array(0=>"ui-layout-west", 1=>"ui-layout-center", 2=>"ui-layout-east");
			}else{
				$nombres=array(0=>"ui-layout-north", 1=>"ui-layout-center", 2=>"ui-layout-south");
			}
			
			$contenidos=array();
			
			$contador=0;
			foreach($xml->children() as $hijo){
				//echo "Agregando: ".$hijo->getName()."<br>";
				if (strcmp($hijo->getName(), "Columnas")==0){
					if (!isset($hijo["idPadre"])){
						$hijo->addAttribute("idPadre", $id."_".$contador);
					}else{
						$hijo["idPadre"]=$id."_".$contador;
					}
				
				}
				$contenidos[$contador]=$this->llamarClaseGenerica($hijo);
				$contador++;
			}
			
		
		
			$sesion=Sesion::getInstancia();
			$html.="
			<script>
			
				var configuracionLayout".$id." = {
						applyDefaultStyles: true,
						useStateCookie: true,
						north: {
							size : '20%'
						},
						south: {
							size : '20%'
						},
						east: {
							size : '20%'
						},
						west: {
							size : '20%'
						},
				}
				var layout".$id.";
			
				$(function () {
					var id=$('#".$id."');
					layout".$id." = $('#".$id."').layout(
						 $.extend(
						 	configuracionLayout".$id.", 
						 	layoutState.load('".$sesion->leerParametro("identificadorSesion").$id."')
						)
					 );
				});
				layoutState.save(configuracionLayout".$id.");

				$(window).unload( layoutState.save );
				$.extend( configuracionLayout".$id.", layoutState.load() );

			</script>
			
			";
			
			
			
			
			if ($contador==2){
				if (strcmp($xml["inverso"], "true")!=0){
					$html.=$contenedor.
							"<div id='".$id."_0' class='".$nombres[0]." sinPadding'>".$contenidos[0]."</div>".
							"<div id='".$id."_1' class='".$nombres[1]." sinPadding'>".$contenidos[1]."</div>".
						$finContenedor;
				}else{
					$html.=$contenedor.
							"<div id='".$id."_1' class='".$nombres[1]." sinPadding'>".$contenidos[0]."</div>".
							"<div id='".$id."_2' class='".$nombres[2]." sinPadding'>".$contenidos[1]."</div>".
						$finContenedor;
				}
			}else if ($contador==3){
				$html.=$contenedor.
						"<div id='".$id."_0' class='".$nombres[0]." sinPadding' >".$contenidos[0]."</div>".
						"<div id='".$id."_1' class='".$nombres[1]." sinPadding' >".$contenidos[1]."</div>".
						"<div id='".$id."_2' class='".$nombres[2]." sinPadding' >".$contenidos[2]."</div>".
					$finContenedor;
			}else{
				$html="Error por el momento 2 o 3 columnas unicamente";
			}
			
			return $html;	
		}
		
	
/*		
		function Columnas(){
			$this->js[]="Externos/jquery/jquery.splitter/splitter.js";
			//$this->css[]="../Librerias/ideComponentes/Navegador/navegador.css";
//				$('#pane2').jScrollPane({showArrows:true});
		}
	

		function obtenerResultado($xml){
		
			static $contadorColumnas=0;
			$contadorColumnas++;
			
			
			$id="Columnaslides".$contadorColumnas;
			
			
			$this->setAtributoInexistente($xml, 'tipo', "v");
			
			if (strcmp($xml["tipo"], "h")==0){
				$division='splitHorizontal: true,
							minTop: 0, sizeTop: 200, maxTop: 500,';
				$division1='splitHorizontal: true,
							minTop: 0, sizeTop: 100, maxTop: 500,';
				$division2='splitHorizontal: true,
							minTop: 0, sizeTop: 200, maxTop: 500,';
				$css='
					.hsplitbar {
						background: #b9b9b9 url('.resolverPath().'/../Externos/jquery/jquery.splitter/img/hgrabber.gif) no-repeat center;
						height: 6px;
					}
					.hsplitbar.active, .hsplitbar:hover {
						background: #b9b9b9 url('.resolverPath().'/../Externos/jquery/jquery.splitter/img/hgrabber.gif) no-repeat center;
						opacity: 0.7;
						filter: alpha(opacity=70); 
					}
				';
			}else{
			
				$division='splitVertical: true, 
							minLeft: 0, sizeLeft: 200, maxLeft: 500,';
				$division1='splitVertical: true,
							minLeft: 0, sizeLeft: 200, maxLeft: 500, ';
				$division2='splitVertical: true,
							minRight: 0, sizeRight: 200, maxRight: 500, ';
				$css='
					.vsplitbar {
						background: #b9b9b9 url('.resolverPath().'/../Externos/jquery/jquery.splitter/img/vgrabber.gif) no-repeat center;
						width: 6px;
					}
					.vsplitbar.active, .vsplitbar:hover {
						background: #b9b9b9 url('.resolverPath().'/../Externos/jquery/jquery.splitter/img/vgrabber.gif) no-repeat center;
						width: 6px;
						opacity: 0.5;
						filter: alpha(opacity=50);
					}';
			}
			
			
			$css.='';
			$contenidos=array();
			
			$contador=0;
			foreach($xml->children() as $hijo){
				$contenidos[$contador]=$this->llamarClaseGenerica($hijo);
				$contador++;
			}
			$html= 
				'
		
				<style>
				
									
					'.$css.'		
				</style>
				';			
			if ($contador==2){
				$html.= 
					'
					<script>
					$(function() {
						$("#'.$id.'").splitter({
							'.$division.'
							outline: true,
							anchorToWindow: true,
							accessKey: "L"
						});
					});
					</script>

					<div id="'.$id.'" class="splitter">
						<div class="SplitterPane">
							'.$contenidos[0].'
						</div> 
						<div class="SplitterPane">
							'.$contenidos[1].'
						</div>
					</div>
					';
			}else if ($contador==3){
				$html.= 
					'
					<script>
					$(function() {
						$("#'.$id.'_Izquierda").splitter({
							'.$division1.'
							outline: true,
							anchorToWindow: true,
							accessKey: "L"
						});
						$("#'.$id.'_CentroDerecha").splitter({
							'.$division2.'
							outline: true,
							accessKey: "R"
							});
					});
					</script>

					<div id="'.$id.'_Izquierda" class="splitter">
						<div class="SplitterPane">
							'.$contenidos[0].'
						</div> 
						<div id="'.$id.'_CentroDerecha">
							<div class="SplitterPane">
								'.$contenidos[1].'
							</div>
							<div class="SplitterPane">
								'.$contenidos[2].'
							</div>
						</div>
					</div>
					';
			}else{
				msg::add("Columna != 2 o 3 ");
			}


			return $html;
		}
		*/
	}
?>
