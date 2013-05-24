<?php

class ConfiguracionGeneral{
	var $autor="ideSoluciones";
	var $descripcion="";
	var $keywords="";
	var $titulo="ideSoluciones";
	var $cliente="ideSoluciones";

	var $configuracion="general";
	var $cantidadImec=15;
	var $temaLibrerias="smoothness";
	
	var $destinoDefecto="nodo";
	var $destinoAuxDefecto="Principal";
	var $favicon="../Librerias/img/ideNegro.png";
	var $logo="";

	var $menus=array();
	var $codigoAnalytics="";
	
	var $configuracionEnvioCorreo=array(
		"host"=>"ssl://box307.bluehost.com",
		"puerto"=>"465",
		"user"=>"mensajero@a.idesoluciones.com",
		"pass"=>"C$$$.7Qu@gtI",
		"desde"=>"info@idesoluciones.com",
		"nombreDesde"=>"ideSoluciones",
		"responder"=>"info@idesoluciones.com"
	);
}

?>
