<?php

	/**
	*    @name asercion
	*    @abstract	Funcion que es llamada cuando ocurre una asercion.
	*				$mensaje: Es el mensaje de error.
	*    @author Felipe Cano <felipe.cano@idesoluciones.com >
	*    @param string $mensaje
	*    @version 1.0
	*/
	// Activar las aserciones y hacerlas silenciosas
	assert_options(ASSERT_ACTIVE,     1);
	assert_options(ASSERT_WARNING,    0);
	assert_options(ASSERT_BAIL,       1);
	assert_options(ASSERT_QUIET_EVAL, 1);


	/**
	 * Función que carga las librerias de un directorio recursivamente
	 **/
	function cargarLibreriasDirectorio($classpath){
		$dir=opendir($classpath);
		$lista=array();
		while ($archivo=readdir($dir)){
			if ($archivo{0}!=".")
				$lista[]=$archivo;
		}
		//var_dump($lista);
		sort($lista);
		//var_dump($lista);

		foreach($lista as $archivo){
			if (substr($archivo,-4)=='.php'){
				//echo "<br>Agregando Archivos".$archivo;
				$archivoc=$classpath.'/'.$archivo;
				require_once($archivoc);
			}
		}
		closedir($dir);
		$dir=opendir($classpath);
		while ($archivo=readdir($dir)){
			if (substr($archivo,0,1)!="."){
				if (is_dir($classpath.'/'.$archivo)) {
					cargarLibreriasDirectorio($classpath.'/'.$archivo);
				}
			}
		}
		closedir($dir);
	}

	
	function asercion( $mensaje){
		$e = new Exception($mensaje);
		echo "
		<html><head>
		<script>
		function CentrarObjetoEfecto(id){
			var obj = document.getElementById(id);
			if(window.innerHeight) { altoTotalActual = window.innerHeight/2; } else { altoTotalActual = document.body.offsetHeight/2; }
			if(window.innerWidth) { anchoTotalActual = window.innerWidth/2; } else { anchoTotalActual = document.body.offsetWidth/2; }
			anchoVentana=obj.offsetWidth/2;
			altoVentana=obj.offsetHeight/2;
			var a= obj.style.top.split(\"px\");
			a[0]=a[0]*1;
			var b= obj.style.left.split(\"px\");
			b[0]=b[0]*1;
			if(a[0]!=altoTotalActual) obj.style.top=(a[0]+((altoTotalActual-a[0]-parseFloat(altoVentana))*.5))+\"px\";
			if(b[0]!=anchoTotalActual) obj.style.left=(b[0]+((anchoTotalActual-b[0]-parseFloat(anchoVentana))*.5))+\"px\";
			if(a[0]!=altoTotalActual||b[0]!=anchoTotalActual) setTimeout( \"CentrarObjetoEfecto('\"+id+\"');\", 10);
		}
		</script></head><body onLoad='CentrarObjetoEfecto(\"total\");'>
		<div id='total' style='height:500px; overflow:auto; font-family:sans-serif;border:10px outset blue;background:black; opacity:0.8;position:fixed;'>
			<h1 style='color:gold;text-align:center;'>Aserci&oacute;n</h1>
			<fieldset style='border:4px inset red;'>
				<h2 style='color:gold;text-align:center;'>".$e->getMessage()."</h2>
				<div style='color:white;'>";
				
				if(class_exists("ConfiguracionLocal")){
					$configtemp= new ConfiguracionLocal();
				}else{
					$configtemp= new ConfiguracionGeneral();
				}

				
				var_dump($e);
				$mensaje=var_export($e, true);
				$xml = ControlXML::nuevo("Parametros");
				ControlXML::agregarNodo($xml, "Parametro", array("nombre"=>"asunto", "valor"=>"[".$configtemp->titulo."][Asercion] ".strftime("%Y-%m-%d %H:%i")));
				ControlXML::agregarNodo($xml, "Parametro", array("nombre"=>"correo", "valor"=>"info@idesoluciones.com"));
				ControlXML::agregarNodo($xml, "Parametro", array("nombre"=>"smtpHost", "valor"=>"ssl://smtp.gmail.com"));
				ControlXML::agregarNodo($xml, "Parametro", array("nombre"=>"smtpPort", "valor"=>"465"));
				ControlXML::agregarNodo($xml, "Parametro", array("nombre"=>"smtpUser", "valor"=>"bot@idesoluciones.com"));
				ControlXML::agregarNodo($xml, "Parametro", array("nombre"=>"smtpPass", "valor"=>"Z6M/wnZ(dyB,"));
				ControlXML::agregarNodo($xml, "Parametro", array("nombre"=>"nombreDesde", "valor"=>"ideSoluciones"));
				ControlXML::agregarNodo($xml, "Parametro", array("nombre"=>"responder", "valor"=>"info@idesoluciones.com"));

				$msg=ControlXML::agregarNodo($xml, "Mensaje");
				ControlXML::agregarNodoTexto($msg, "Wiki", "<pre>".$mensaje."</pre>");
				
				$mensajero= new ControlMensajero();
				$mensajero->enviarCorreo($xml);
				$notificacion=$mensajero->getNotificacion();
		
		
		echo "</div>
			</fieldset>
		</div>
		</body>
		</html>
		";
		assert("");
	}


	/**
	*    @name revisarArreglo
	*    @abstract	Funcion que examina un array y lo desglosa en un mensaje de drupal.
	*				$value: Es el arreglo a examinar.
	*				$nom: Es el titulo con el que se muestra el resultado.
	*    @author Felipe Cano <felipe.cano@idesoluciones.com >
	*    @param array,string $value,$nom
	*    @version 1.0
	*/
	function revisarArreglo($value,$nom='Sin Nombre',$tipo='html'){
		//return "";
		if(strcmp($tipo,'html')==0){
			return IDESolGFAA($value,$nom);
		}else{
			if(strcmp($tipo,'ideProyecto')==0){
				return IDESolGFAAIDE($value,$nom);
			}
		}
	}
	function rgbhex($red, $green, $blue)
	{
		return sprintf('#%02X%02X%02X', $red, $green, $blue);
	}

	function contieneNoNumeros($cadena){
		//echo "Analizando cadena : ".$cadena."<br>";
		for ($i=0; $i < strlen($cadena);$i++){
			//echo "caracter:".$cadena[$i]."<br>";
			if(!is_int($cadena[$i])){
				//echo "retornando false ".$cadena[$i]."<br>";
				return false;
			}
		}
		//echo "retornando true ".$cadena."<br>";
		return true;
	}


	function IDESolGFAAIDE($value,$nom, $i=0){
		if(is_array($value)){
			$ret="_recuadro style=\"padding:5px; border:1px solid black; background:".rgbhex(255-$i*10,255-$i*5,255-$i*5).";\"__leyenda__negrilla_".$nom."_finNegrilla__finLeyenda_\n";
			foreach($value as $treg=>$dreg){
				$ret.=IDESolGFAAIDE($dreg,$treg, $i+1);
			}
			$ret.='_finRecuadro_'."\n";
			return $ret;
		}else if(is_object ($value)){
			settype($value, "array");
			return IDESolGFAAIDE($value,'object('.$nom.')', $i+1);
		}
		$color=array(
			"boolean" => 'rgb(255, 0, 0)',
			"integer"  => '#00aa00',
			"double"  => '#00a000',
			"string" => 'f95800',
			"array" => '#aa00aa',
			"object" => '#ffaa00',
			"resource" => '#00aaff',
			"NULL" => '#7ec2c4',
			"user function" => '#aa0000',
			"unknown type" => '#000000',);
		$type= gettype($value);
		//$value = str_replace('<', '<', $value);
		//$value = str_replace('>', '>', $value);
		if ($type=="boolean"){
			if ($value){
				return "_caja style=\"padding:0 5px; color: ".$color[$type].";\"_ ($type) $nom = TRUE _nuevaLinea__finCaja_";
			}else{
				return "_caja style=\"padding:0 5px; color: ".$color[$type].";\"_ ($type) $nom = FALSE_nuevaLinea__finCaja_";
			}
		}else{
			return  "_caja style=\"padding:0 5px; color: {$color[$type]};\"_ ($type) $nom = $value _nuevaLinea__finCaja_";
		}
	}

	function IDESolGFAA($value,$nom, $i=0){
		if(is_array($value)){
			$ret="<fieldset style='padding:0 10px; border:3px groove gold; background:".rgbhex(200,200,100+$i*30)."'><legend><strong>".$nom."</strong></legend>\n";
			foreach($value as $treg=>$dreg){
				$ret.=IDESolGFAA($dreg,$treg, $i+1);
			}
			$ret.='</fieldset>'."\n";
			return $ret;
		}else if(is_object ($value)){
			settype($value, "array");
			return IDESolGFAA($value,'object('.$nom.')', $i+1);
		}
		$color=array(
			"boolean" => 'rgb(255, 0, 0)',
			"integer"  => '#00aa00',
			"double"  => '#00a000',
			"string" => 'f95800',
			"array" => '#aa00aa',
			"object" => '#ffaa00',
			"resource" => '#00aaff',
			"NULL" => '#7ec2c4',
			"user function" => '#aa0000',
			"unknown type" => '#000000',);
		$type= gettype($value);
		$value = str_replace('<', '<', $value);
		$value = str_replace('>', '>', $value);
		if ($type=="boolean"){
			if ($value)
				return "<div style='padding:0 5px; color: ".$color[$type].";'>($type) $nom = TRUE<br>"/*.var_dump($value)*/."</div>";
			else
				return "<div style='padding:0 5px; color: ".$color[$type].";'>($type) $nom = FALSE<br>"/*.var_dump($value)*/."</div>";
		}else
			return  "<div style='padding:0 5px; color: ".$color[$type].";'>($type) $nom = $value<br>"/*.var_dump($value)*/."</div>";
	}

	class generalPhp{
		static function geshi($texto, $lenguaje){
			$geshi = new GeSHi($texto, $lenguaje);
			return "<div style='border:3px groove blue; background:#dddddd;overflow:auto;width:100%'>".$geshi->parse_code()."</div>";
		}
	}

	function registrarlog($stringData="", $ourFileName = "/tmp/log1.html", $borrar=false){
		/*
		if (!is_file($ourFileName) || $borrar){
			$fh = fopen($ourFileName, "w") or die("Can't open file");
		}else{
			$fh = fopen($ourFileName, "a+") or die("Can't open file");
		}
		fwrite($fh, $stringData."\n");
		fclose($fh);
		*/
	}
	function resolverPath($extra=""){
		$dirs = explode("/", $_SERVER['SCRIPT_NAME']);
		unset($dirs[count($dirs)-1]);
		return implode("/", $dirs).$extra;
	}
	function resolverDirectorio($extra=""){
		/*
			$script_filename = getenv('PATH_TRANSLATED');
			if (empty($script_filename)) {
				$script_filename = getenv('SCRIPT_FILENAME');
			}
			$script_filename = str_replace('', '/', $script_filename);
			$script_filename = str_replace('//', '/', $script_filename);
			$dir_fs_www_root_array = explode('/', dirname($script_filename));
			$dir_fs_www_root = array();
			for ($i=0, $n=sizeof($dir_fs_www_root_array); $i<$n; $i++) {
				$dir_fs_www_root[] = $dir_fs_www_root_array[$i];
			}


			$classpath = implode('/', $dir_fs_www_root) .$extra;
			return $classpath;
		*/
		$root = $_SERVER['DOCUMENT_ROOT'] ;
		$self = $_SERVER['PHP_SELF'] ;
		return $root.mb_substr($self,0,-mb_strlen(strrchr($self,"/"))).$extra ;
	}



	function tildes($texto,$tipo="utf"){
		//echo $texto;
		$textos=array(
			'á'=>array("utf"=>'á',"html"=>'&aacute;'),
			'é'=>array("utf"=>'é',"html"=>'&eacute;'),
			'í'=>array("utf"=>'í',"html"=>'&iacute;'),
			'ó'=>array("utf"=>'ó',"html"=>'&oacute;'),
			'ú'=>array("utf"=>'ú',"html"=>'&uacute;'),
			'ñ'=>array("utf"=>'ñ',"html"=>'&ntilde;'),
			'Á'=>array("utf"=>'Á',"html"=>'&Aacute;'),
			'É'=>array("utf"=>'É',"html"=>'&Eacute;'),
			'Í'=>array("utf"=>'Í',"html"=>'&Iacute;'),
			'Ó'=>array("utf"=>'Ó',"html"=>'&Oacute;'),
			'Ú'=>array("utf"=>'Ú',"html"=>'&Uacute;'),
			'Ñ'=>array("utf"=>'Ñ',"html"=>'&Ntilde;'),

			'Ã¡'=>array("utf"=>'á',"html"=>'&aacute;'),
			'Ã©'=>array("utf"=>'é',"html"=>'&eacute;'),
			'Ã­'=>array("utf"=>'í',"html"=>'&iacute;'),
			'Ã³'=>array("utf"=>'ó',"html"=>'&oacute;'),
			'Ãº'=>array("utf"=>'ú',"html"=>'&uacute;'),
			'Ã±'=>array("utf"=>'ñ',"html"=>'&ntilde;'),
			'Ã'=>array("utf"=>'Á',"html"=>'&Aacute;'),
			'Ã‰'=>array("utf"=>'É',"html"=>'&Eacute;'),
			'Ã'=>array("utf"=>'Í',"html"=>'&Iacute;'),
			'Ã“'=>array("utf"=>'Ó',"html"=>'&Oacute;'),
			'Ãš'=>array("utf"=>'Ú',"html"=>'&Uacute;'),
			'Ã‘'=>array("utf"=>'Ñ',"html"=>'&Ntilde;')

		);
		foreach($textos as $text=>$remp){
			//$texto=str_replace($text,$remp["$tipo"],$texto);
		}
		return utf8_encode($texto);
		//return $texto;
	}

function UTF_to_Unicode($input, $array=False) {

 $bit1  = pow(64, 0);
 $bit2  = pow(64, 1);
 $bit3  = pow(64, 2);
 $bit4  = pow(64, 3);
 $bit5  = pow(64, 4);
 $bit6  = pow(64, 5);

 $value = '';
 $val   = array();

 for($i=0; $i< strlen( $input ); $i++){

     $ints = ord ( $input[$i] );

     $z     = ord ( $input[$i] );
     $y     = ord ( $input[$i+1] ) - 128;
     $x     = ord ( $input[$i+2] ) - 128;
     $w     = ord ( $input[$i+3] ) - 128;
     $v     = ord ( $input[$i+4] ) - 128;
     $u     = ord ( $input[$i+5] ) - 128;

     if( $ints >= 0 && $ints <= 127 ){
        // 1 bit
        $value .= '&#'.($z * $bit1).';';
        $val[]  = $value;
     }
     if( $ints >= 192 && $ints <= 223 ){
        // 2 bit
        $value .= '&#'.(($z-192) * $bit2 + $y * $bit1).';';
        $val[]  = $value;
     }
     if( $ints >= 224 && $ints <= 239 ){
        // 3 bit
        $value .= '&#'.(($z-224) * $bit3 + $y * $bit2 + $x * $bit1).';';
        $val[]  = $value;
     }
     if( $ints >= 240 && $ints <= 247 ){
        // 4 bit
        $value .= '&#'.(($z-240) * $bit4 + $y * $bit3 +
$x * $bit2 + $w * $bit1).';';
        $val[]  = $value;
     }
     if( $ints >= 248 && $ints <= 251 ){
        // 5 bit
        $value .= '&#'.(($z-248) * $bit5 + $y * $bit4
+ $x * $bit3 + $w * $bit2 + $v * $bit1).';';
        $val[]  = $value;
     }
     if( $ints == 252 && $ints == 253 ){
        // 6 bit
        $value .= '&#'.(($z-252) * $bit6 + $y * $bit5
+ $x * $bit4 + $w * $bit3 + $v * $bit2 + $u * $bit1).';';
        $val[]  = $value;
     }
     if( $ints == 254 || $ints == 255 ){
       echo 'Wrong Result!<br>';
     }

 }

 if( $array === False ){
    return $unicode = $value;
 }
 if($array === True ){
     $val     = str_replace('&#', '', $value);
     $val     = explode(';', $val);
     $len = count($val);
     unset($val[$len-1]);

     return $unicode = $val;
 }

}


function Unicode_to_UTF( $input, $array=TRUE){

     $utf = '';
    if(!is_array($input)){
       $input     = str_replace('&#', '', $input);
       $input     = explode(';', $input);
       $len = count($input);
       unset($input[$len-1]);
    }
    for($i=0; $i < count($input); $i++){

    if ( $input[$i] <128 ){
       $byte1 = $input[$i];
       $utf  .= chr($byte1);
    }
    if ( $input[$i] >=128 && $input[$i] <=2047 ){

       $byte1 = 192 + (int)($input[$i] / 64);
       $byte2 = 128 + ($input[$i] % 64);
       $utf  .= chr($byte1).chr($byte2);
    }
    if ( $input[$i] >=2048 && $input[$i] <=65535){

       $byte1 = 224 + (int)($input[$i] / 4096);
       $byte2 = 128 + ((int)($input[$i] / 64) % 64);
       $byte3 = 128 + ($input[$i] % 64);

       $utf  .= chr($byte1).chr($byte2).chr($byte3);
    }
    if ( $input[$i] >=65536 && $input[$i] <=2097151){

       $byte1 = 240 + (int)($input[$i] / 262144);
       $byte2 = 128 + ((int)($input[$i] / 4096) % 64);
       $byte3 = 128 + ((int)($input[$i] / 64) % 64);
       $byte4 = 128 + ($input[$i] % 64);
       $utf  .= chr($byte1).chr($byte2).chr($byte3).
chr($byte4);
    }
    if ( $input[$i] >=2097152 && $input[$i] <=67108863){

       $byte1 = 248 + (int)($input[$i] / 16777216);
       $byte2 = 128 + ((int)($input[$i] / 262144) % 64);
       $byte3 = 128 + ((int)($input[$i] / 4096) % 64);
       $byte4 = 128 + ((int)($input[$i] / 64) % 64);
       $byte5 = 128 + ($input[$i] % 64);
       $utf  .= chr($byte1).chr($byte2).chr($byte3).
chr($byte4).chr($byte5);
    }
    if ( $input[$i] >=67108864 && $input[$i] <=2147483647){

       $byte1 = 252 + ($input[$i] / 1073741824);
       $byte2 = 128 + (($input[$i] / 16777216) % 64);
       $byte3 = 128 + (($input[$i] / 262144) % 64);
       $byte4 = 128 + (($input[$i] / 4096) % 64);
       $byte5 = 128 + (($input[$i] / 64) % 64);
       $byte6 = 128 + ($input[$i] % 64);
       $utf  .= chr($byte1).chr($byte2).chr($byte3).
chr($byte4).chr($byte5).chr($byte6);
    }
   }
   return $utf;
}

	function siEsta($valor,$siNo=""){
		return (isset($valor)?$valor:$siNo);
	}
	function siEstaArreglo($valor, $indice,$siNo=""){
		return (isset($valor[$indice])?$valor[$indice]:$siNo);
	}

	function siNoVacio($valor,$sino=""){
		$retorno=$sino;
		if(strcmp($valor,"")!=0){
			$retorno=$valor;
		}
		return $retorno;
	}
	function siEstaYNoVacio($valor,$siNo=""){
		$dato=siEsta($valor);
		return siNoVacio($dato,$siNo);
	}
	function agregarPropiedadesDeNodo($nodo,$CPropiedades=null){
		if(is_null($CPropiedades)){
			$campo=new CPropiedades();
		}else{
			$campo=$CPropiedades;
		}
		//Se recorren todos los atributos del filtro
		foreach($nodo->attributes() as $nombre => $valor) {
			//Se agrega la propiedad al objeto CPropiedades
			$campo->addPropiedad($nombre,$valor);
		}
		return $campo;
	}
	
	
	function file_size($size){
		$filesizename = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
		return $size ? round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i] : '0 Bytes';
	}
	
	function nombreSinNumerosAlInicio($nombre){
		$nombreRetorno=$nombre;
		while (
				$nombreRetorno{0}=='0' || $nombreRetorno{0}=='1' || 
				$nombreRetorno{0}=='2' || $nombreRetorno{0}=='3' || 
				$nombreRetorno{0}=='4' || $nombreRetorno{0}=='5' || 
				$nombreRetorno{0}=='6' || $nombreRetorno{0}=='7' || 
				$nombreRetorno{0}=='8' || $nombreRetorno{0}=='9'){
			$nombreRetorno=substr($nombreRetorno, 1);
		}
		return $nombreRetorno;
	}
	function tick() {
			list($sec, $mic, $now) = sscanf(microtime(), "%d.%d %d");
			return ((float)($now+$sec).'.'.$mic);
	}
	// Función que serializa SimpleXML
	function serializemmp($toserialize){
		if(is_a($toserialize, "SimpleXMLElement")){
			$stdClass = new stdClass();
			$stdClass->type = get_class($toserialize);
			$stdClass->data = $toserialize->asXml();
		}
		return serialize($stdClass);
	}
	// Función que deserializa SimpleXML
	function unserializemmp($tounserialize){
		$tounserialize = unserialize($tounserialize);
		if(is_a($tounserialize, "stdClass")){
			if($tounserialize->type == "SimpleXMLElement"){
				$tounserialize = simplexml_load_string($tounserialize->data);
			}
		}
		return $tounserialize;
	}
	function getRealIpAddr()
	{
		if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
		{
		  $ip=$_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
		{
		  $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else
		{
		  $ip=$_SERVER['REMOTE_ADDR'];
		}
		/*echo $ip;*/
		return $ip;
	}	
	
	

	function tipo_mime($filename) {

        $mime_types = array(

            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        $ext = strtolower(array_pop(explode('.',$filename)));
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        }
        else if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        }
        else {
            return 'application/octet-stream';
        }
    }
    
    function limpiarJson($t){
	    $t2 = str_replace("\n", "", $t);
		$t2 = str_replace("\r", "", $t2);
		$j =  json_decode($t2, true);
		if (is_null($j)){
			$j = json_decode("{}", true);
		}
    	return $t2;
    }
    	
	/*
    neat_r works like print_r but with much less visual clutter.
    By Jake Lodwick. Copy freely.
	*/
	function neat_r($arr, $return = false) {
		$out = array();
		$oldtab = "    ";
		$newtab = "  ";
	   
		$lines = explode("\n", print_r($arr, true));
	   
		foreach ($lines as $line) {

		    //remove numeric indexes like "[0] =>" unless the value is an array
		    if (substr($line, -5) != "Array") {    $line = preg_replace("/^(\s*)\[[0-9]+\] => /", "$1", $line, 1); }
		   
		    //garbage symbols
		    foreach (array(
		        "Array"        => "",
		        "["            => "",
		        "]"            => "",
		        " =>"        => ":",
		    ) as $old => $new) {
		        $out = str_replace($old, $new, $out);
		    }

		    //garbage lines
		    if (in_array(trim($line), array("Array", "(", ")", ""))) continue;

		    //indents
		    $indent = "";
		    $indents = floor((substr_count($line, $oldtab) - 1) / 2);
		    if ($indents > 0) { for ($i = 0; $i < $indents; $i++) { $indent .= $newtab; } }

		    $out[] = $indent . trim($line);
		}

		$out = implode("\n", $out) . "\n";
		if ($return == true) return $out;
		echo $out;
	}
	
?>
