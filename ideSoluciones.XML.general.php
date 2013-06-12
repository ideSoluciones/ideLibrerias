<?php
/*http://www.rafachacon.com/programatik/2007/10/29/ejemplo-de-uso-de-eval-y-objetos-en-php/
 function hola($mensaje){
 echo "Hola $mensaje<br>";
 }
 $temp="\$this->".$nodo."("."hola1".");";
 eval($temp);
 $temp="\$this->".$nodo."("."hola2".");";
 eval($temp);
 $temp="\$this->".$nodo."("."hola3".");";
 eval($temp);
 */

require_once("ideSoluciones.php.general.php");


class generalXML extends generalPhp{
	/**
	* Funcion que recorre un retorno de un xpath llamando a una
	* función establecida con los datos del nodo correspontiende
	*/
	function extraerNodo($xmltmp, $funcion){
		$temp="";
		if(is_array($xmltmp)){
			foreach($xmltmp as $nodo){
				$temp.=$this->$funcion($nodo);
			}
		}else{
			if(is_object($xmltmp)){
				$temp=$this->$funcion($nodo);
			}
		}
		return $temp;
	}

	function extraerNodoArreglo($xmltmp, $funcion){
		$temp=array();
		if(is_array($xmltmp)){
			foreach($xmltmp as $nodo){
				$temp[]=$this->$funcion($nodo);
			}
		}else{
			if(is_object($xmltmp)){
				$temp[]=$this->$funcion($nodo);
			}
		}
		return $temp;
	}

	function extraerNodo1p($xmltmp, $funcion, $param){
		$temp="";
		if(is_array($xmltmp)){
			foreach($xmltmp as $nodo){
				$temp.=$this->$funcion($nodo,$param);
			}
		}else{
			if(is_object($xmltmp)){
				$temp=$this->$funcion($nodo,$param);
			}
		}
		return $temp;
	}
	function extraerNodo1pArray($xmltmp, $funcion, $param){
		$temp="";
		if(is_array($xmltmp)){
			$temp=array();
			foreach($xmltmp as $nodo){
				$temp[]=$this->$funcion($nodo,$param);
			}
		}else{
			if(is_object($xmltmp)){
				$temp=$this->$funcion($nodo,$param);
			}
		}
		return $temp;
	}


	/**
		*
		*/
	function numeroCaracteres($n, $t){
		$ts="";
		for ($i=0;$i<$n;$i++){
			$ts.=$t;
		}
		return $ts;
	}

	/**
		*
		*/
	function identarTextoXML($texto){
		//echo "Identando[";
		$buffer="";
		$ident=0;
		for ($i=0;$i<strlen($texto);$i++){
			if ($i>0){
				if ($texto{$i-1}!='/' && $texto{$i}=='>'){
					$ident++;
				}
			}
			if ($texto{$i}=='<' && $texto{$i+1}=='/'){
				$ident--;
				$ident--;
				$buffer=substr($buffer, 0, strlen($buffer))."\n";
			}
			$buffer.=$texto{$i};
			if ($texto{$i}=='>' && ($i+1)!=strlen($texto)){
				$buffer.="\n".numeroCaracteres($ident, "\t");
			}
		}
		//echo $buffer."]";
		return $buffer;
	}
	/**
		* Funcion para convertir un xml en texto a html con resalto de sintaxis
		*/
	function geshiTexto($texto){

			$geshi = new GeSHi(identarTextoXML($texto), "xml");
			return "<div style='overflow:auto;border:3px double; background:#E7FFA6; font-style:italic;'>".$geshi->parse_code()."</div>";

		//return "geshiTexto[$texto]";
	}

	/**
		* Funcion para guardar un xml en un archivo
		*/
	function guardarXML($xml,$file){
		$filexml = @fopen($file, "w");
		if($filexml){
			fwrite($filexml,$xml->asXML());
			fclose($filexml);
			return true;
		}
		return false;
	}
	/**
		* Funcion para convertir un xml a html con resalto de sintaxis
		* @deprec esta funcion fue remplazada por geshiXML
		*/
	function textoXML($xml){
		return $this->geshiXML($xml);
	}
	/**
		* Funcion para convertir un xml a html con resalto de sintaxis
		*/
	public static function geshiXML($xml){
		/*OJO VER SI AQUI ESTA EL PROBLEMA*/
		//throw new Exception("error");
		try{
			$geshi = new GeSHi(identarTextoXML($xml->asXML()), "xml");
			return "<div style='overflow:auto;border:3px double; background:#E7FFA6; font-style:italic;'>".$geshi->parse_code()."</div>";
		}catch(Exception $e){
			echo $e->getMessage();
			exit();
		}

		//return "geshiXML[".$xml->asXML()."]";
	}
	function geshiHTML($html){
		$geshi = new GeSHi($html, "html");
			return "<label>Debug</label><div style='overflow:auto;border:3px groove blue; background:#ffe8e8ff;'>".$geshi->parse_code()."</div>";

		//return "geshiHTML[$html]";
	}

	/**
		* Remove node/nodes xml with xpath
		*
		* @param SimpleXMLElement                 $xml
		* @param string XPath                     $path
		* @param string ('one'|'child'|'all')     $multi
		*
		* Use:
		*
		* Example xml file - http://ru2.php.net/manual/ru/ref.simplexml.php
		*
		* $xml = simplexml_load_file($xmlfile);
		*
		* //1. remove only 1 node (without child nodes)
		* //   $path must return only 1 (unique) node without child nodes
		* removeNode($xml, '//movie/rating[@type="thumbs"]');
		*
		* //2. remove 1 node (with 1 child nodes)
		* //    $path can return any nodes - will be removed only first node
		* //   with all child nodes
		* removeNode($xml, '//characters', 'child')
		*
		* //3. remove all nodes (with child nodes)
		* //   $path can return any nodes - will be removed all
		* //   with child nodes
		* removeNode($xml, '//rating', 'all')
		*
		* $xml->asXML($xmlfile);
		*
		*/
	function removeNode($xml, $path, $multi='one')
	{
		$result = $xml->xpath($path);
		//echo "Voy a remover los nodos<br>";
		# for wrong $path
		if (!isset($result[0])){
			//echo "Error Borrando no se encontro el path";
			return false;
		}

		switch ($multi) {
			case 'all': case 'All':
				$errlevel = error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
				foreach ($result as $r){
					//echo "borrando ", generalXML::geshiXML($r[0]);
					unset ($r[0]);
				}
				error_reporting($errlevel);
				return true;

			case 'child':
				unset($result[0][0]);
				return true;

			case 'one':
				if (count($result[0]->children())==0 && count($result)==1) {
					unset($result[0][0]);
					return true;
				}

			default:
				return false;
		}
	}

	/**
		* Pumps all child elements of second SimpleXML object into first one.
		*
		* @param    object      $xml1   SimpleXML object
		* @param    object      $xml2   SimpleXML object
		* @return   void
		*/
	function simplexml_merge (SimpleXMLElement &$xml1, SimpleXMLElement $xml2)
	{
		// convert SimpleXML objects into DOM ones
		$dom1 = new DomDocument();
		$dom2 = new DomDocument();
		$dom1->loadXML($xml1->asXML());
		$dom2->loadXML($xml2->asXML());

		// pull all child elements of second XML
		$xpath = new domXPath($dom2);
		$xpathQuery = $xpath->query('/*/*');
		for ($i = 0; $i < $xpathQuery->length; $i++)
		{
			// and pump them into first one
			$dom1->documentElement->appendChild(
			$dom1->importNode($xpathQuery->item($i), true));
		}
		$xml1 = simplexml_import_dom($dom1);
	}
}


function append_simplexml(&$simplexml_to, &$simplexml_from)
{

	static $firstLoop=true;

	//Here adding attributes to parent           
	if( $firstLoop )
	{
		foreach( $simplexml_from->attributes() as $attr_key => $attr_value )
		{
			if (isset($simplexml_to[$attr_key])){
				$simplexml_to[$attr_key]=$attr_value;
			}else{
				$simplexml_to->addAttribute($attr_key, $attr_value);
			}	
		}
	}

	foreach ($simplexml_from->children() as $simplexml_child)
	{
		$simplexml_temp = $simplexml_to->addChild($simplexml_child->getName(), (string) $simplexml_child);
		foreach ($simplexml_child->attributes() as $attr_key => $attr_value)
		{
			$simplexml_temp->addAttribute($attr_key, $attr_value);
		}

		$firstLoop=false;

		append_simplexml($simplexml_temp, $simplexml_child);
	}

	unset( $firstLoop );
}

function removeNode($xml, $path, $multi='one')
{
	$result = $xml->xpath($path);
	# for wrong $path
	if (!isset($result[0])){
		return false;
	}

	switch ($multi) {
		case 'all': case 'All':
			$errlevel = error_reporting(E_ALL & ~E_WARNING);
			foreach ($result as $r){
				unset ($r[0]);
			}
			error_reporting($errlevel);
			return true;

		case 'child':
			unset($result[0][0]);
			return true;

		case 'one':
			if (count($result[0]->children())==0 && count($result)==1) {
				unset($result[0][0]);
				return true;
			}

		default:
			return false;
	}
}

/**
 * Pumps all child elements of second SimpleXML object into first one.
 *
 * @param    object      $xml1   SimpleXML object
 * @param    object      $xml2   SimpleXML object
 * @return   void
 */
function simplexml_merge (SimpleXMLElement &$xml1, SimpleXMLElement $xml2)
{
	// convert SimpleXML objects into DOM ones
	$dom1 = new DomDocument();
	$dom2 = new DomDocument();
	$dom1->loadXML($xml1->asXML());
	$dom2->loadXML($xml2->asXML());

	// pull all child elements of second XML
	$xpath = new domXPath($dom2);
	$xpathQuery = $xpath->query('/*/*');
	for ($i = 0; $i < $xpathQuery->length; $i++)
	{
		// and pump them into first one
		$dom1->documentElement->appendChild(
		$dom1->importNode($xpathQuery->item($i), true));
	}
	$xml1 = simplexml_import_dom($dom1);
}
/**
 *
 */
function numeroCaracteres($n, $t){
	$ts="";
	for ($i=0;$i<$n;$i++){
		$ts.=$t;
	}
	return $ts;
}
/**
 *
 */
function identarTextoXML($texto){
	//echo "Identando[";
	$buffer="";
	$ident=0;
	for ($i=0;$i<strlen($texto);$i++){
		if ($i>0){
			if ($texto{$i-1}!='/' && $texto{$i}=='>'){
				$ident++;
			}
		}
		if ($texto{$i}=='<' && $texto{$i+1}=='/'){
			$ident--;
			$ident--;
			$buffer=substr($buffer, 0, strlen($buffer))."\n";
		}
		$buffer.=$texto{$i};
		if ($texto{$i}=='>' && ($i+1)!=strlen($texto)){
			$buffer.="\n".numeroCaracteres($ident, "\t");
		}
	}
	//echo $buffer."]";
	return $buffer;
}
?>
