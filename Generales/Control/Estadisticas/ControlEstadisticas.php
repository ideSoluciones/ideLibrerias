<?php
	class ControlEstadisticas{
		static function generarGrafica($tipo, $xml, $datos, $parametros=array()){
			$estadistica=$xml->addChild("Estadistica");
			$propiedad=$estadistica->addChild("Propiedad");
			$propiedad->addAttribute("nombre", "tipo");
			$propiedad->addAttribute("valor", strtolower($tipo));
			
			foreach($parametros as $p=>$v){
				$propiedad=$estadistica->addChild("Propiedad");
				$propiedad->addAttribute("nombre", $p);
				$propiedad->addAttribute("valor", $v);
			}
			
			$valores=$estadistica->addChild("Valores");
			foreach($datos as $dato){
				$valor=$valores->addChild("Valor");
				foreach($dato as $id=>$propiedad){
					$valor->addAttribute($id, $propiedad);
				}
			}
		}
	}
?>
