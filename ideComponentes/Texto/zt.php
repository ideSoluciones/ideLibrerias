<?php
class T extends Texto{

	public function obtenerResultado($dato){
		$this->setXMLNoticia($dato);
		return $this->toHTML();
	}
	
	function toHTML(){
		$total=$this->xml;
		foreach($this->matrizReemplazos as $remplazo){
			$total= ereg_replace($remplazo["instruccion"], $remplazo["html"], $total);
		}
		return $total;
	}
}
?>
