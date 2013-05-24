<?php
	class Campo extends ComponentePadre implements componente{

		var $propiedades;
		public $clase;
		public $arrayReglas;

		function Campo(){
			$this->propiedades=array(
				"tipo"=>"",

				"campo"=>true,/*
				"id"=>"",
				"titulo"=>"",
				"valor"=>"",
				"minimo"=>"",
				"maximo"=>"",
				"filas"=>"",
				"columnas"=>"",
				"evento"=>"",
				"multiseleccion"=>"",
				"numeroCaracteresMin"=>"",
				"numeroCaracteresMax"=>"",
				"valorPorDefecto"=>"",
				"requerido"=>"",
				"error"=>"",
				"autocomplete"=>"",
				"opciones"=>array()*/
			);
		}

		function generarReglas($xml, $pref="", $tipoSalida="html", $accionPlus=""){
			static $contadorCampos=0;
			$html="";
			$htmlInterno="";
			foreach($xml->children() as $hijo){
				switch($hijo->getName()){
					case "Opcion":
						$nombre="".$hijo["nombre"];
						$valor="".$hijo["valor"];
						$this->propiedades["opciones"][$nombre]=$valor;
						break;
					//default:
					case "Ayuda":
						$htmlInterno.="<div class='floatLeft'>".$this->llamarClaseGenerica($hijo)."</div>";
				}
			}

			if(isset($this->propiedades["id"])){
				if(strcmp($this->propiedades["id"],"")==0){
					$this->propiedades["id"]="Campo".$contadorCampos;
				}
			}else{
				$this->propiedades["id"]="Campo".$contadorCampos;
			}

			if(isset($this->propiedades["nombre"])){
				if(strcmp($this->propiedades["nombre"],"")==0){
					$this->propiedades["nombre"]="Campo".$contadorCampos;
				}
			}else{
				$this->propiedades["nombre"]="Campo".$contadorCampos;
			}


			foreach($xml->attributes() as $nombre => $valor) {
				//echo $nombre." - ".$valor,"<br>";
				$this->propiedades[(string)$nombre]=(string)$valor;
			}
			//echo "<hr>";
			$nombre=$this->propiedades["nombre"];


			if (strcmp($this->propiedades["tipo"], "")==0){
				$this->propiedades["tipo"]="cadena";
			}
			$nombreClase="F".strtolower($this->propiedades["tipo"]);
			$nombreClase[1]=strtoupper($nombreClase[1]);
			$this->clase= new $nombreClase($this->propiedades["id"], $nombre, $this->propiedades, $tipoSalida, $accionPlus, $xml);
			$this->clase->setReglasPorDefecto();

		}

		function obtenerResultado($xml, $pref="", $tipoSalida="html", $accionPlus=""){
			static $contadorCampos=0;
			$contadorCampos++;
			$sesion=Sesion::getInstancia();
			$contadorRecargas=(string)$sesion->leerParametro("ContadorRecargas");
			//echo "El conteador de campos es: ".$contadorRecargas;
			if (strcmp($contadorRecargas, "")==0){
				$contadorRecargas=0;
			}
			$contadorRecargas++;
			$sesion->escribirParametro("ContadorRecargas", $contadorRecargas);
			$html="";
			$htmlInterno="";
			foreach($xml->children() as $hijo){
				switch($hijo->getName()){
					case "Opcion":
						$nombre="".$hijo["nombre"];
						$valor="".$hijo["valor"];
						$this->propiedades["opciones"][$nombre]=$valor;
						break;
					//default:
					case "Ayuda":
						$htmlInterno.="<div class='floatLeft'>".$this->llamarClaseGenerica($hijo)."</div>";
				}
			}

			if(isset($this->propiedades["id"])){
				if(strcmp($this->propiedades["id"],"")==0){
					$this->propiedades["id"]="Campo".$contadorRecargas.$contadorCampos;
				}
			}else{
				$this->propiedades["id"]="Campo".$contadorRecargas.$contadorCampos;
			}

			if(isset($this->propiedades["nombre"])){
				if(strcmp($this->propiedades["nombre"],"")==0){
					$this->propiedades["nombre"]="Campo".$contadorRecargas.$contadorCampos;
				}
			}else{
				$this->propiedades["nombre"]="Campo".$contadorRecargas.$contadorCampos;
			}


			foreach($xml->attributes() as $nombre => $valor) {
				//echo $nombre." - ".$valor,"<br>";
				$this->propiedades[(string)$nombre]=(string)$valor;
			}
			//echo "<hr>";
			$nombre=$this->propiedades["nombre"];


			if (strcmp($this->propiedades["tipo"], "")==0){
				$this->propiedades["tipo"]="cadena";
			}
			$nombreClase="F".strtolower($this->propiedades["tipo"]);
			$nombreClase[1]=strtoupper($nombreClase[1]);
			$this->clase= new $nombreClase($this->propiedades["id"], $nombre, $this->propiedades, $tipoSalida, $accionPlus, $xml);
			$this->clase->setReglasPorDefecto();
			$html.=$this->clase->pre2HTML();
			$html.=$this->clase->toHTML();
			$html.=$htmlInterno;
			$html.=$this->clase->post2HTML();
			$this->css=array_merge_recursive($this->css, $this->clase->getCss());
			$this->js=array_merge_recursive($this->js, $this->clase->getJs());




//var_dump($this);
//var_dump($this->clase->arrayReglas);
			return $html;
		}
	}

?>
