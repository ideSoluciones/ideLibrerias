<?php
class ControlUtilidades{
	function ControlUtilidades(){
	}
	// Genera una cadena aleatoria de tamaño = $tamano
	public static function generarCadenaAleatorio($tamano=64){
		$vocales = 'aeuyAEUY';
		$consonantes = 'bdghjmnpqrstvzBDGHJLMNPQRSTVWXZ23456789';
		$clave = '';
		$alt = time() % 2;
		while($tamano>=0)
		{
			if ($alt == 1) {
				$clave .= $consonantes[(rand() % strlen($consonantes))];
				$alt = 0;
			} else {
				$clave .= $vocales[(rand() % strlen($vocales))];
				$alt = 1;
			}
			$tamano--;
		}
		return $clave;
	}
	// Dado el resultado de un ideComponente lo renderiza a HTML
	public static function renderizarHTML($cadena){
		$xml= new SimpleXMLElement($cadena);
		$componentePadre=new ComponentePadre();
		return $componentePadre->llamarClaseGenerica($xml);
	}
	// Recibe un JSON cifrado en base 64 y lo intenta transformar en un arreglo
	public static function descifrarDecodificarJson(&$jsonCifrado){
		$arreglo = json_decode(base64_decode($jsonCifrado),true);
		return is_null($arreglo)? array() : $arreglo;
	}
	
	public static function metodoXSD($path){

		$xsdstring = $path."/example_schema.xsd";
		$XSDDOC = new DOMDocument();
		$XSDDOC->preserveWhiteSpace = false;
		if ($XSDDOC->load($xsdstring))
		{
			$xsdpath = new DOMXPath($XSDDOC);
			$attributeNodes = $xsdpath->query('//xs:simpleType[@name="title"]')->item(0);
			foreach ($attributeNodes->childNodes as $attr)
			{
				var_dump($attr->tagName);
				var_dump($attr->getAttribute('base'));
			}

		}
	}
}
?>
