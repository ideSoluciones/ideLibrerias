<?php

	/**
	*    @name FEtiqueta
	*    @abstract	
	*    @author Felipe Cano <fcano@idesoluciones.com >
	*    @version 1.0
	*/
	
	class FEtiqueta extends ObjetoHTML{

		function FEtiqueta($idObjeto, $nombre, $propiedades, $tipoSalida, $accionPlus){
			$this->setIdObjeto($idObjeto);
			$this->setNombre($nombre);
			$this->setPropiedades($propiedades);
			$this->setTipoSalida($tipoSalida);
			$this->setAccionPlus($accionPlus);
		}

		function toHTML(){
			$propiedades=$this->getPropiedades();
			$estilo="";
			if (isset($propiedades["error"])){
				if (strcmp(strtolower($propiedades["error"]),"true")==0){
					$estilo.=($propiedades["error"]==true?"error ":"");
				}
			}
			$estilo.="contenedorCampoFormulario".$propiedades["tipo"]." campoFormulario";
			
			$total="";
				if (strlen($propiedades["titulo"])>0)
				$total.="	<div id='".$this->getIdObjeto()."Titulo' class='".$estilo."Titulo  etiquetaFormulario'>".$propiedades["titulo"]."</div>\n";
				$total.="		<label id='".$this->getIdObjeto()."Etiqueta' class='".$estilo."Etiqueta'>".$propiedades["valor"]."</label>\n";
			return $total;
		}
	}
	
?>
