<?php

	/**
	*    @name FTexto
	*    @abstract
	*    @author Felipe Cano <fcano@idesoluciones.com >
	*    @version 1.0
	*/

	class FTexto extends ObjetoHTML{

		function FTexto($idObjeto, $nombre, $propiedades, $tipoSalida, $accionPlus, $xml){
			$this->ObjetoHTML();
			$this->setIdObjeto($idObjeto);
			$this->setNombre($nombre);
			$this->setPropiedades($propiedades);
			$this->setTipoSalida($tipoSalida);
			$this->setAccionPlus($accionPlus);
			$this->setXML($xml);
			$this->agregarJs("Externos/jquery/jquery.disable.text.select.js");
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
					case "estilo":case "columnas":case "filas":case "opciones":case "tipo":case "campo":case "id":case "nombre":case "titulo":case "requerido":case "valorPorDefecto":case "error":break;
					default: $otrosAtributos.=" ".$a."='".$i."'";
				}
			}
			
			$estilo.="contenedorCampoFormulario".$propiedades["tipo"]." campoFormulario";
			$total="";
			if (!isset($propiedades["filas"])){
				$propiedades["filas"]="";
			}
			if (!isset($propiedades["columnas"])){
				$propiedades["columnas"]="";
			}
			if (!isset($propiedades["estilo"])){
				$propiedades["estilo"]="";
			}
			if (!isset($propiedades["clase"])){
				$propiedades["clase"]="";
			}
			if (isset($propiedades["titulo"])){
				$total.="	<div id='".$this->getIdObjeto()."Titulo' class='".$estilo."Titulo  etiquetaFormulario'>".$propiedades["titulo"]."</div>\n";
			}
			if (!isset($propiedades["codigo"])){
				$codigo="";
			}else{
				$this->agregarJs("Externos/ace/build/src/ace.js");
				$this->agregarJs("Externos/ace/build/src/theme-twilight.js");
				$this->agregarJs("Externos/ace/build/src/mode-javascript.js");
				$codigo="
					<script type='text/javascript'>
						$(function() {
							var editor = ace.edit('".$this->getIdObjeto()."');
							editor.setTheme('ace/theme/twilight');
							var JavaScriptMode = require('ace/mode/javascript').Mode;
							editor.getSession().setMode(new JavaScriptMode());
						});
					</script>
				";
			}
			/*
				<script type='text/javascript'>
					$(function() {
						$('#".$this->getIdObjeto()."').resizable();
					});
				</script>
			*/
			$total.=
				$codigo."
				<textarea
					id='".$propiedades["id"]."'
					class='".$estilo."AreaDeTexto ".$propiedades["clase"]."'
					rows='".$propiedades["filas"]."'
					cols='".$propiedades["columnas"]."'
					style='".$propiedades["estilo"]."'
					name='".$this->getNombre()."'
					 >".$propiedades["valorPorDefecto"]."</textarea>\n";
			return $total;
		}
	}

?>
