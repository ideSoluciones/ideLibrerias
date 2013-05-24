<?php
	class ProcesadorAjax extends generalXML{
		var $xml;

		function setRespuestaAjax($respuestaAjax, $xml){
			if (!is_null($respuestaAjax)){
				$xmltmp=$xml->xpath('//Complementar');
				$this->extraerNodo1p($xmltmp, "complementar", $respuestaAjax);
				$xmltmp=$xml->xpath('//Remplazar');
				$this->extraerNodo1p($xmltmp, "remplazar", $respuestaAjax);
				return true;
			}
			return false;
		}
		function complementar($data, $respuestaAjax){
			$total="";
			foreach ($data->children() as $contenido) {
				$total.=$contenido->asXml();
			}
			$respuestaAjax->append($data["id"], 'innerHTML', $total);			
		}
		function remplazar($data, $respuestaAjax){
			$total="";
			foreach ($data->children() as $contenido) {
				$total.=$contenido->asXml();
			}
			$respuestaAjax->assign($data["id"], 'innerHTML', $total);			
		}
	}
?>
