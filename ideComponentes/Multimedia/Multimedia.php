<?php


	class Multimedia extends ComponentePadre implements componente{

		var $anchoDefecto;
		var $altoDefecto;

		function Multimedia(){
			//$this->js[]="Librerias/ideComponentes/Navegador/navegador.js";
			//$this->css[]="Librerias/ideComponentes/Navegador/navegador.css";

			$this->anchoDefecto=425;
			$this->altoDefecto=344;
		}

		function obtenerResultado($xml){
			static $contadorFlash=0;
			$html="<div class='".$xml['clase']."'>";
			$entroFor=false;
			foreach($xml->children() as $hijo){
				switch($hijo->getName()){
					case "Video":
						$nombre=((strcmp($hijo['nombre'], "")!=0)?$hijo['nombre']:"SonidoFlash".$contadorFlash);
						$direccion=((strcmp($hijo['direccion'], "")!=0)?$hijo['direccion']:"");
						$ancho=((strcmp($hijo['ancho'], "")!=0)?$hijo['ancho']:$this->anchoDefecto);
						$alto=((strcmp($hijo['alto'], "")!=0)?$hijo['alto']:$this->altoDefecto);
						if (strcmp($direccion, "")!=0){
							$autoinicio=((strcmp($hijo['autoinicio'], "")!=0)?$hijo['autoinicio']:"false");
							$html.='<div class="'.$nombre.'">
									<embed
										src="'.$direccion.'"
										autostart="'.$autoinicio.'"
										width="'.$ancho.'"
										height="'.$alto.'"/>
								</div>';
						}else{
							$html.='<h1>ERROR no se tiene la dirección en el video</h1>';
						}
						break;
					case "Youtube":
						$nombre=((strcmp($hijo['nombre'], "")!=0)?$hijo['nombre']:"SonidoFlash".$contadorFlash);
					//@TODO: ver si se implementa creando el nodo Flash y mandandolo a renderizar
					//	 o ver si se utiliza directamente js (swfobject) para imprimir este contenido
						$ancho=((strcmp($hijo['ancho'], "")!=0)?$hijo['ancho']:$this->anchoDefecto);
						$alto=((strcmp($hijo['alto'], "")!=0)?$hijo['alto']:$this->altoDefecto);
						$pantallaCompleta=((strcmp($hijo['permitirPantallaCompleta'], "")!=0)?$hijo['alto']:"true");
						$clave=((strcmp($hijo['clave'], "")!=0)?$hijo['clave']:"");

						if(strcmp($clave, "")!=0){
							$html.='<div class="'.$nombre.'">
									<object width="'.$ancho.'" height="'.$alto.'">
										<param name="movie" value="http://www.youtube.com/v/'.$clave.'&hl=es&fs=1&rel=0">
										</param>
										<param name="allowFullScreen" value="'.$pantallaCompleta.'">
										</param>
										<param name="allowscriptaccess" value="always">
										</param>
										<embed
											src="http://www.youtube.com/v/'.$clave.'&hl=es&fs=1&rel=0"
											type="application/x-shockwave-flash"
											allowscriptaccess="always"
											allowfullscreen="'.$pantallaCompleta.'"
											width="'.$ancho.'"
											height="'.$alto.'">
										</embed>
									</object>
								</div>';
						}else{
							$html.='<h1>ERROR no se tiene la clave en el video de youtube</h1>';
						}
						break;
					case "Flash":
						$id=((strcmp($xml['id'], "")!=0)?$xml['id']:"Flash".$contadorFlash);
						$clase=((strcmp($xml['clase'], "")!=0)?$xml['clase']:"");
						$direccion=((strcmp($hijo['direccion'], "")!=0)?$hijo['direccion']:"");
						if (strcmp($direccion, "")!=0){
							$ancho=((strcmp($hijo['ancho'], "")!=0)?$hijo['ancho']:$this->anchoDefecto);
							$alto=((strcmp($hijo['alto'], "")!=0)?$hijo['alto']:$this->altoDefecto);
							$parametros=((strcmp($hijo['parametros'], "")!=0)?$hijo['parametros']:"");
							$listaParametros=explode("&", $parametros);
							$variablesFlashScript="";
							if (count($listaParametros)>0){
								$variablesFlashScript="var flashvars = {\n";
							}
							foreach($listaParametros as $parametro){

								$datosParametros=explode("=", $parametro);
								if (count($datosParametros)>=2)
									$variablesFlashScript.=$datosParametros[0].": '".$datosParametros[1]."',\n";
							}
							if (count($listaParametros)>0){
								$variablesFlashScript.="};";
							}
							//$this->js=array("Librerias/js/jquery/jquery.swfobject.js");
							if (isset($this->js)){
								$this->js[]="Externos/swfobject/swfobject/swfobject.js";
							}else{
								$this->js=array("Externos/swfobject/swfobject/swfobject.js");
							}
							$script='
								$(function() {
									var params = {};
									var attributes = {};
									'.$variablesFlashScript.'
									swfobject.embedSWF("'.$direccion.'", "'.$id.'", "'.$ancho.'", "'.$alto.'", "9.0.0","expressInstall.swf", flashvars, params, attributes);
								});';
							
							$html.='
									<script>
									'.$script.'
									</script>
									<div id="'.$id.'" class="'.$clase.'">
										<h1>Plugin de flash</h1>
										<p><a href="http://www.adobe.com/go/getflashplayer"><img src="'.resolverPath().'/../Externos/flowplayer/swfobject/get_flash_player.gif" border=0 alt="Get Adobe Flash player" /></a></p>
									</div>';
							$contadorFlash++;
						}else{
							$html.='<h1>ERROR no se tiene la dirección en el elemento flash</h1>';
						}
						break;
					case "SonidoFlash":
						$nombre=((strcmp($hijo['nombre'], "")!=0)?$hijo['nombre']:"SonidoFlash'.$contadorFlash.'");
						$direccion=((strcmp($hijo['direccion'], "")!=0)?$hijo['direccion']:"");
						if (strcmp($direccion, "")!=0){

							if (isset($this->js)){
								$this->js[]="Externos/flowplayer/flowplayer/flowplayer-3.1.4.min.js";
							}else{
								$this->js=array("Externos/flowplayer/flowplayer/flowplayer-3.1.4.min.js");
							}
							$autoinicio=((strcmp($hijo['autoinicio'], "")!=0)?$hijo['autoinicio']:"false");
							$imagen=((strcmp($hijo['imagen'], "")!=0)?$hijo['imagen']:"../Librerias/js/flowplayer/imagenSonidos.jpg");

							$ancho=((strcmp($hijo['ancho'], "")!=0)?$hijo['ancho']:$this->anchoDefecto);
							$alto=((strcmp($hijo['alto'], "")!=0)?$hijo['alto']:30);
							$id=((strcmp($hijo['id'], "")!=0)?$hijo['id']:'SonidoFlash'.$contadorFlash);
							

							$script='
								$(function() {
									$f("'.$id.'", "'.resolverPath().'/../Externos/flowplayer/flowplayer/flowplayer-3.1.3.swf",
										{
											clip: {
												autoPlay: '.$autoinicio.'
											},
											playlist: [

												// first entry in a playlist work as a splash image for the MP3 clip
												"'.resolverPath().'/'.$imagen.'",

												{
												    // our song
												    url: "'.$direccion.'",

												    // when music starts grab song"s metadata and display it using content plugin
												    onStart: function(song) {
												      /*
													var meta = song.metaData;
													this.getPlugin("content").setHtml(
													    "<p>Artist: <b>" + meta.TPE1 + "</b></p>" +
													    "<p>Album:   <b>" + meta.TALB + "</b></p>" +
													    "<p>Title:   <b>" + meta.TIT2 + "</b></p>"
													);*/
												    }
												}
											],
											plugins:  {
												/*
												// content plugin settings
												content: {
												    url: "'.resolverPath().'/../Externos/flowplayer/flowplayer/flowplayer.content-3.1.0.swf",
												    backgroundColor:"#002200",
												    top:25, right: 25, width: 160, height: 60
												},
												 */
												// and a bit of controlbar skinning
												controls: {
												    backgroundColor:"#CCCCCC",
												    height: 30,
												    fullscreen: false
												}
											}
										}
									);

								});';
							$html.='
									<script>
									'.$script.'
									</script>';

							$html.="<div class='".$nombre."'>
									<a
										 style='display:block;width:".$ancho."px;height:".$alto."px'
										 id='".$id."'>
									</a>
								</div>";

							$contadorFlash++;
						}else{
							$html.='<h1>ERROR no se tiene la dirección en el sonido(f)</h1>';
						}
						break;
					case "VideoFlash":
						$nombre=((strcmp($hijo['nombre'], "")!=0)?$hijo['nombre']:"SonidoFlash'.$contadorFlash.'");
						$direccion=((strcmp($hijo['direccion'], "")!=0)?$hijo['direccion']:"");
						if (strcmp($direccion, "")!=0){
							if (is_array($this->js)){
								$this->js[]="Externos/flowplayer/flowplayer/flowplayer-3.1.4.min.js";
							}else{
								$this->js=array("Externos/flowplayer/flowplayer/flowplayer-3.1.4.min.js");
							}
							$autoinicio=((strcmp($hijo['autoinicio'], "")!=0)?$hijo['autoinicio']:"false");

							$ancho=((strcmp($hijo['ancho'], "")!=0)?$hijo['ancho']:$this->anchoDefecto);
							$alto=((strcmp($hijo['alto'], "")!=0)?$hijo['alto']:$this->altoDefecto);
							$script='
								$(function() {
									flowplayer("VideoFlash'.$contadorFlash.'", "'.resolverPath().'/../Externos/flowplayer/flowplayer/flowplayer-3.1.3.swf",
										{
											clip: {
												autoPlay: '.$autoinicio.'
											},
										}

									);
								});';
							$html.='
									<script>
									'.$script.'
									</script>';
							$html.="<div class='".$nombre."'>
									<a
										 href='".$direccion."'
										 style='display:block;width:".$ancho."px;height:".$alto."px'
										 id='VideoFlash".$contadorFlash."'>
									</a>
								</div>
							";

							
							$contadorFlash++;
						}else{
							$html.='<h1>ERROR no se tiene la dirección en el video(f)</h1>';
						}
						break;
					default:
						$html.=$this->llamarClaseGenerica($hijo);
				}
			}
			$html.="</div>";
			return $html;
		}
	}


?>
