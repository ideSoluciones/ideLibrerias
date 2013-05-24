<?php

class ControlVistaCarrousel{
	//static function generarCarrouselImagenesDirectorio($directorio){
	static function generarCarrouselImagenesDirectorio($xml, $directorio=null){
		if (is_null($directorio)){
			$carrousel = ControlXML::nuevo("Carrousel");
			$directorio=$xml;
		}else{
			$carrousel = xml::add($xml, "Carrousel");
		}
	
		$sesion = Sesion::getInstancia();
		$archivos=ControlArchivo::getArchivosDirectorio($sesion->leerParametro("pathServidor").$directorio);
		sort($archivos);

		//echo "En el directorio".$directorio;
		//var_dump($archivos);
		foreach($archivos as $archivo){		
			$nodo = ControlXML::agregarNodo($carrousel, "Nodo");
			ControlXML::agregarNodoTexto($nodo, "Wiki", "http://".rawurlencode($_SERVER["HTTP_HOST"].$sesion->leerParametro("pathCliente").$directorio."/".$archivo));
		}
		return $carrousel;// $directorio
	}

}
?>
