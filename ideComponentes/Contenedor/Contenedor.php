<?php


	class Contenedor extends ComponentePadre implements componente{

		function Contenedor(){
			//$this->js[]="../Librerias/ideComponentes/Navegador/navegador.js";
			//$this->css[]="../Librerias/ideComponentes/Navegador/navegador.css";
		}

		function obtenerResultado($xml){

			$html="";

			static $contenedores=0;
			$contenedores++;

			$atributos="";
			foreach($xml->attributes() as $nombre=>$valor){
				switch($nombre){case "id":case "titulo":case "modal":case "estilo":case "escalable":case "clase":case "textoModal":case "icono":case "ancho":case "alto":case "propiedadesIcono":break;default:
					$atributos.=$nombre."=\"".$valor."\" ";
				}
			}

			

			if (!isset($xml['id'])){
				$xml['id']="Contenedor".$contenedores;
			}
			
			$etiqueta="";
			if (strcmp((string)$xml["titulo"], "")==0 || strcmp($xml["modal"], "true")==0){
				$contenedor="div";
			}else{
				$contenedor="fieldset";
				$etiqueta="<legend>".$xml["titulo"]."</legend>";
			}

			/*
			 * Ojo si se colocan efectos los estilos son sobreescritos
			 */
			$estilo="";
			if (isset($xml["estilo"])){
				$estilo="style='".$xml["estilo"]."'";
			}
			
			$estiloBoton="";
			if (strcmp((string)$xml["estiloBoton"], "")!=0){
				$estiloBoton="style='".$xml["estiloBoton"]."'";
			}
			
			$otroCodigoJavascript="";
			if (isset($xml["escalable"])){
				if (strcmp((string)$xml["escalable"], true)==0){
					$otroCodigoJavascript="$('#".$xml['id']."').resizable();\n";
				}
			}
			
		
			$cadenas=array();
			$contenido="";
			foreach($xml->children() as $hijo){
				switch($hijo->getName()){
					case "Efecto":
						$parametros= array();
						foreach($hijo->attributes() as $s=>$params){
							if(strcmp((string)$s,"nombre")!=0){
								$parametros[]=$params;
							}
						}
						$cadenas[]=$hijo["nombre"]."(".implode(",", $parametros).")";
						break;
					case "Nodo":
						$contenido.='<h3><a href="#">'.$hijo['titulo'].'</a></h3>'.
							$this->obtenerResultado($hijo, false);
						break;
					default:
						$contenido.=$this->llamarClaseGenerica($hijo);
				}
			}
			$htmlCodigo="";
			if(count($cadenas)>0){
				$htmlCodigo="
					<script type='text/javascript'>
						$(function() {
							$('#".$xml['id']."').".implode(".", $cadenas).";"."
						});
					</script>";
			}
			if(strlen($otroCodigoJavascript)>0){
				$htmlCodigo="
					<script type='text/javascript'>
						$(function() {
							".$otroCodigoJavascript."
						});
					</script>";
			}


			$clase="";
			$id="";
			$estilo="";
			$titulo="";
			if(isset($xml['clase'])){
				$clase="class='".$xml['clase']."'";
			}else{
				$clase="class='contenedor'";
			}
			if(isset($xml['id'])){
				$id="id='".$xml['id']."'";
			}
			if(isset($xml['estilo'])){
				$estilo="style='".$xml['estilo']."'";
			}
			if(isset($xml['titulo'])){
				$titulo="title='".$xml['titulo']."'";
			}
			$html="".$htmlCodigo."<".$contenedor." ".$id." ".$clase." ".$estilo." ".$titulo." $atributos>\n".$etiqueta."\n";
			$html.=$contenido;
			$html.="</".$contenedor.">";
			
			
			if (strcmp($xml["modal"], "true")==0){
				if (strcmp($xml["textoModal"], "")==0 && strcmp($xml["icono"], "")==0){
					asercion("Los dialogos modal deben tener textoModal o imagen<br>".generalXML::geshiXML($xml));
				}
				$ancho="width:500,";
				$alto="";
				if (strcmp($xml["ancho"], "")!=0){
					$ancho="width:".$xml["ancho"].",";
				}
				if (strcmp($xml["alto"], "")!=0){
					$alto="height:".$xml["alto"].",";
				}
				$htmlCodigo="
					<script type='text/javascript'>
						function cerrarDialogo".$xml['id']."(){
							$('#".$xml['id']."').dialog('close');
						}
						$(function() {
							$('#".$xml['id']."_Boton').click(function () {
								$('#".$xml['id']."').dialog('open');
							});
							$('#".$xml['id']."').dialog({
								autoOpen: false,
								bgiframe: true,
								modal: true,
								show: 'blind',
								hide: 'blind',
								".$ancho.' '.$alto."
								"
								/*
								//@ToDo: parametrizar los botones
								buttons: {
									Cerrar: function() {
										$(this).dialog('close');
									}
								}*/."
							});
						});
					</script>";
//				$html=$htmlCodigo."<div id='".$xml['id']."_Boton' class='botonModal' >".$xml["textoModal"]."</div>".$html;
				$icono="";
				if (strcmp($xml["icono"], "")!=0){
					$icono="<img src='".$xml["icono"]."' ".$xml["propiedadesIcono"]."/>";
				}
				$html=$htmlCodigo."<button id='".$xml['id']."_Boton'  type='button' $estiloBoton class='ui-state-default ui-corner-all'>".$icono.$xml["textoModal"]."</button>".$html;
			}
			
			
			return $html;
		}
	}


?>
