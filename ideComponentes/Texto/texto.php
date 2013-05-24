<?php
class Texto extends ComponentePadre{

	protected $xml;
	protected $campos;
	protected $matrizReemplazos=array();
	
	public function obtenerResultado($dato){
		$this->setXMLNoticia($dato);
		return $this->toHTML();
	}
	
	function XMLNoticia($xml=null){/*Colocados por los logs de php function XMLNoticia($xlm=null){*/
		if(!is_null($xml)){
			$this->setXMLNoticia($xml);
		}
	}

	function setXMLNoticia($xml){
		$this->campos=array();
		$this->xml=$xml;
		//$this->XMLNoticia_analizarXML();
/*
*/		
		$this->matrizReemplazos=array(
			array("instruccion" => "_nuevaLinea_", "html"=>"<br>"),
			array("instruccion" => "_linea_", "html"=>"<hr>"),

			array("instruccion" => "_negrilla([^\_]*)_", "html"=>"<strong\\1>"),
			array("instruccion" => "_finNegrilla_", "html"=>"</strong>"),


			/*
			Tablas
			
			http://www.w3schools.com/tags/tryit.asp?filename=tryhtml_tbody
			<table>
			  <thead>
			    <tr>
			      <th>Month</th>
			      <th>Savings</th>
			    </tr>
			  </thead>
			  <tfoot>
			    <tr>
			      <td>Sum</td>
			      <td>$180</td>
			    </tr>
			  </tfoot>
			  <tbody>
			    <tr>
			      <td>January</td>
			      <td>$100</td>
			    </tr>
			    <tr>
			      <td>February</td>
			      <td>$80</td>
			    </tr>
			  </tbody>
			</table>
			*/
			array("instruccion" => "_tabla([^\_]*)_", "html"=>"<table\\1>"),
			array("instruccion" => "_tCabecera([^\_]*)_", "html"=>"<thead\\1>"),
			array("instruccion" => "_tCabeceraFila([^\_]*)_", "html"=>"<tr\\1>"),
			array("instruccion" => "_tCabeceraCelda([^\_]*)_", "html"=>"<th\\1>"),
			array("instruccion" => "_tablaCuerpo([^\_]*)_", "html"=>"<tbody\\1>"),
			array("instruccion" => "_fila([^\_]*)_", "html"=>"<tr\\1>"),
			array("instruccion" => "_celda([^\_]*)_", "html"=>"<td\\1>"),
			array("instruccion" => "_tPie([^\_]*)_", "html"=>"<tfoot\\1>"),
			array("instruccion" => "_tPieFila([^\_]*)_", "html"=>"<tr\\1>"),
			array("instruccion" => "_tPieCelda([^\_]*)_", "html"=>"<td\\1>"),
			
			array("instruccion" => "_titulo([^\_]*)_", "html"=>"<h1\\1>"),
			array("instruccion" => "_finTitulo_", "html"=>"</h1>"),
			
			array("instruccion" => "_finTabla_", "html"=>"</table>"),
			array("instruccion" => "_finTCabecera_", "html"=>"</thead>"),
			array("instruccion" => "_finTCabeceraFila_", "html"=>"</tr>"),
			array("instruccion" => "_finTCabeceraCelda_", "html"=>"</th>"),
			array("instruccion" => "_finTablaCuerpo_", "html"=>"</tbody>"),
			array("instruccion" => "_finFila_", "html"=>"</tr>"),
			array("instruccion" => "_finCelda_", "html"=>"</td>"),
			array("instruccion" => "_finTPie_", "html"=>"</tfoot>"),
			array("instruccion" => "_finTPieFila_", "html"=>"<tr>"),
			array("instruccion" => "_finTPieCelda_", "html"=>"<td>"),
			
			array("instruccion" => "_boton([^\_]*)_", "html"=>"<button\\1>"),
			array("instruccion" => "_finBoton_", "html"=>"</button>"),
			
			/// Listas			
			

			array("instruccion" => "_listaDesordenada([^\_]*)_", "html"=>"<ul\\1>"),
			array("instruccion" => "_listaOrdenada([^\_]*)_", "html"=>"<ol\\1>"),
			array("instruccion" => "_elementoLista([^\_]*)_", "html"=>"<li\\1>"),
			array("instruccion" => "_finListaDesordenada_", "html"=>"</ul>"),
			array("instruccion" => "_finListaOrdenada_", "html"=>"</ol>"),
			array("instruccion" => "_finElementoLista_", "html"=>"</li>"),
			
			//Anclas
								
			array("instruccion" => "_ancla([^\_]*)_", "html"=>"<a\\1>"),
			array("instruccion" => "_finAncla_", "html"=>"</a>"),
			
			//Cajas / Divs

			array("instruccion" => "_caja([^\_]*)_", "html"=>"<div\\1>"),
			array("instruccion" => "_finCaja_", "html"=>"</div>"),

			array("instruccion" => "_recuadro([^\_]*)_", "html"=>"<fieldset\\1>"),
			array("instruccion" => "_finRecuadro_", "html"=>"</fieldset>"),
			
			array("instruccion" => "_leyenda([^\_]*)_", "html"=>"<legend\\1>"),
			array("instruccion" => "_finLeyenda_", "html"=>"</legend>"),
			
			array("instruccion" => "_espacio_", "html"=>"&nbsp;"),
			array("instruccion" => "_tab_", "html"=>"&#09;"),
			array("instruccion" => "_linea_", "html"=>"<hr>;"),
			
			array("instruccion" => "_entrada([^\_]*)_", "html"=>"<input\\1/>"),
			
			array("instruccion" => "_seleccion([^\_]*)_", "html"=>"<select\\1>"),
			array("instruccion" => "_opcion([^\_]*)_", "html"=>"<option\\1/>"),
			array("instruccion" => "_finOpcion_", "html"=>"</option>"),
			array("instruccion" => "_op([^\_]*)_", "html"=>"<option\\1/>"),
			array("instruccion" => "_finOp_", "html"=>"</option>"),
			array("instruccion" => "_finSeleccion_", "html"=>"</select>"),

			array("instruccion" => "_barraPorcentaje([^[\:]*)[:| :]([^\:]*)[:| :]([^\:]*):([^\_]*)_", "html"=>"<div \\1><div class=barraPorcentajeExterno style='width:\\3'><div class=barraPorcentajeInterno style='width: \\2;'>\\4</div></div></div>"),
			array("instruccion" => "_barraPorcentaje([^[\:]*)[:| :]([^\:]*):([^\_]*)_", "html"=>"<div \\1><div class=barraPorcentajeExterno style='width:\\3'><div class=barraPorcentajeInterno style='width: \\2;'>\\2</div></div></div>"),

			array("instruccion" => "_imagen([^\_]*)_", "html"=>"<img src=\\1>"),
			array("instruccion" => "_video([^\_]*)_", "html"=>"<video src=\\1>Su navegador no soporta video en html5</video>"),


			array("instruccion" => "_flashRefresco([^[\:]*)[:| :]([^\:]*):([^\:]*):([^\:]*):([^\_]*)_", "html"=>
				'<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
					codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0"
					width="\\3" height="\\4">
					<param
						name="movie" 
						value="\\2?nada='.rand().'">
					<param name="quality" value="high">
					<param name="FlashVars" value="\\5">										
					<embed src="\\2?nada='.rand().'" 
						quality="high" 
						pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash"
						type="application/x-shockwave-flash"
						width="\\3"
						height="\\4"
						FlashVars="\\5" >
					</embed>
				</object>'),

			array("instruccion" => "_flashPrecarga([^[\:]*)[:| :]([^\:]*):([^\:]*):([^\:]*):([^\_]*)_", "html"=>
				'<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
					codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0"
					width="\\3" height="\\4">
					<param
						name="movie" 
						value="../Librerias/as/Cargador.swf?nada='.rand().'">
					<param name="quality" value="high">
					<param name="FlashVars" value="idePelicula=\\2&\\5">										
					<embed src="../Librerias/as/Cargador.swf?nada='.rand().'" 
						quality="high" 
						pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash"
						type="application/x-shockwave-flash"
						width="\\3"
						height="\\4"
						FlashVars="idePelicula=\\2&\\5" >
					</embed>
				</object>'),

			array("instruccion" => "_flash([^[\:]*)[:| :]([^\:]*):([^\:]*):([^\:]*):([^\_]*)_", "html"=>
				'<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
					codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0"
					width="\\3" height="\\4">
					<param
						name="movie" 
						value="'.resolverPath().'/\\2">
					<param name="quality" value="high">
					<param name="FlashVars" value="\\5">										
					<embed src="'.resolverPath().'/\\2" 
						quality="high" 
						pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash"
						type="application/x-shockwave-flash"
						width="\\3"
						height="\\4"
						FlashVars="\\5" >
					</embed>
				</object>'),
				
			array("instruccion" => "_ventanaPopUp[:| :]([^[\:]*)[:| :]([^[\:]*)[:| :]([^[\:]*)[:| :]([^\_]*)_", "html" =>
				'<script >
					$(document).ready( function() {
						window.open("'.resolverPath().'/\\1" , null, "width=\\2,height=\\3,\\4");
					}); 
				</script >'),
			array("instruccion" => "_ventanaPopUp[:| :]([^[\:]*)[:| :]([^[\:]*)[:| :]([^\_]*)_", "html" =>
				'<script >
					$(document).ready( function() {
						window.open("'.resolverPath().'/\\1" , null, "width=\\2,height=\\3");
					}); 
				</script >'),

			array("instruccion" => "_ventanaPopUpLimpia[:| :]([^[\:]*)[:| :]([^[\:]*)[:| :]([^\_]*)_", "html" =>
				'<script >
					$(function(){ 
						window.open("'.resolverPath().'/\\1" , null, "width=\\2,height=\\3,status=no,toolbar=no,menubar=no,location=no");
						return true;
					}); 
				</script >'),


		);
	}

	/*function XMLNoticia_analizarXML(){
		$xmltmp=$this->xml->xpath('Campo');
		$this->extraerNodo($xmltmp, "XMLNoticia_Campo");
	}

	function XMLNoticia_Campo($datos){
		$this->campos[]=array($datos["nombre"], $datos["valor"], $datos["nivel"], $datos["id"]);
	}*/

	function toHTML(){
		static $contador=0;
		$total="<div class='Noticia'>\n";
		foreach($this->xml->children() as $hijo){
			//echo revisarArreglo($campo, "campo");
			switch($hijo["nombre"]){
				case "titulo":
					$total.="<h".$hijo["nivel"].">".$hijo["valor"]."</h".$hijo["nivel"].">\n";
					break;
				case "contenido":
					$general=$hijo["valor"];
					foreach($this->matrizReemplazos as $remplazo){
						$general= ereg_replace($remplazo["instruccion"], $remplazo["html"], $general);
					}
					$nombreObjeto="";
					$totalEfecto="";
					if (strcmp($hijo["efecto"], "")!=0){
					/**
					 * Se pueden aplicar cualquiera de estos efectos y otros mas
					 * http://docs.jquery.com/Effects
					 */
						$datos=explode(",",$hijo["efecto"]);
						$nombreFuncion=$datos[0];
						array_splice($datos, 0, 1);
						$parametros=implode(",", $datos);
						$nombreObjeto="Efecto".$contador;
						//$totalEfecto.="efecto [".$hijo["efecto"]."]";
						$totalEfecto.="
							<script type='text/javascript'>
								$(function() {
									setInterval(efecto".$contador.",3000);
									function efecto".$contador."(){
										$('.Efecto".$contador."').".$nombreFuncion."(".$parametros.");
									}
								});
							</script>";
						$contador++;
					}
					$total.="<div class='contenidoNoticia ".$nombreObjeto."' id='".$hijo["id"]."'>".$general."</div>\n".$totalEfecto;
					break;

			}
		}
		$total.="</div>\n";
		return $total;
	}
}
?>
