<?php
	abstract class ObjetoHTML extends generalXML{

		public $arrayReglas;
		private $idObjeto;
		private $nombre;
		private $propiedades;
		private $tipoSalida;
		private $accionPlus;
		private $xml;
		private $css=array();
		private $js=array();

		public function ObjetoHTML(){
			// Inicializa el arreglo de reglas para que las clases derivadas puedan concatenar sus propias reglas
			$this->arrayReglas=array("reglas"=> array(), "mensajes"=>array());
			if (!isset($this->propiedades["valorPorDefecto"])){
				$this->propiedades["valorPorDefecto"]="";
			}
			
		}
/*
		abstract protected function agregarCss(&$js);
		abstract protected function agregarJs(&$css);
*/
		public static function validarReglas($nombreClase, $campo, $valorDato){
			$respuesta = array("respuesta"=>true,"mensaje"=>"");
			//requerido
			if(strcmp($campo["requerido"],"true")==0 && !$valorDato){
				$respuesta["respuesta"] = false;
				$respuesta["mensaje"] = " El campo ".$campo["titulo"]." es requerido";
			}
			if($respuesta["respuesta"] == true){
				$respuesta = call_user_func(array($nombreClase,"validar"), $campo, $valorDato);
			}
			return $respuesta;
		}
		public static function validar($campo, $valorDato){
			
		
		
			return array("respuesta"=>true,"mensaje"=>"");
		}
		final public function setPropiedad($nombre,$valor){
			$funcion="set".$nombre;
			$this->$funcion($valor);
		}
		final public function getPropiedad($nombre){
			$funcion="get".$nombre;
			return $this->$funcion();
		}
		final public function setIdObjeto($idObjeto){$this->idObjeto=($idObjeto==null?"":$this->caracteresEspeciales($idObjeto));}
		final public function setNombre($nombre){$this->nombre=($nombre==null?time():$this->caracteresEspeciales($nombre));}
		final public function setPropiedades($propiedades){
			$this->propiedades=($propiedades==null?array():$propiedades);
			
			if (!isset($this->propiedades["valorPorDefecto"])){
				$this->propiedades["valorPorDefecto"]="";
			}
		}
		final public function setTipoSalida($tipoSalida){$this->tipoSalida=($tipoSalida==null?"":$tipoSalida);}
		final public function setAccionPlus($accionPlus){$this->accionPlus=($accionPlus==null?"":$accionPlus);}
		final public function setXML($xml){$this->xml=$xml;}
		final public function getIdObjeto(){return $this->idObjeto;}
		final public function getNombre(){return $this->nombre;}
		final public function getPropiedades(){return $this->propiedades;}
		final public function getTipoSalida(){return $this->tipoSalida;}
		final public function getAccionPlus(){return $this->accionPlus;}
		final public function getXML(){return $this->xml;}
		final public function caracteresEspeciales($datos){
			if($GLOBALS["debug"]>2){ registrarlog("->IndexGeneral::caracteresEspeciales() = Se quito la función htmlspecialchars.<br>"); return $datos; }
			return htmlspecialchars($datos, ENT_QUOTES);
		}
		final public function getCss(){return $this->css;}
		final public function getJs(){return $this->js;}
		final public function agregarCss($css){$this->css[]=$css;}
		final public function agregarJs($js){$this->js[]=$js;}
		
		public function pre2HTML(){
		
			$propiedades=$this->getPropiedades();
			
			$estilo="";
			if (isset($propiedades["error"])){
				$estilo.=($propiedades["error"]==true?"error ":"");
			}
			$estilo.="contenedorCampoFormulario".$propiedades["tipo"]." campoFormulario";
			$estiloContenedor=isset($propiedades["estiloContenedor"])?$propiedades["estiloContenedor"]:"";
			$total="<div id='".$this->getIdObjeto()."Contenedor' class='".$estilo."' style='".$estiloContenedor."'  >\n";
			/*  Se deshabilita el duplicador
			$propiedades=$this->getPropiedades();
			if (strcmp($propiedades["multiple"], "true")==0){
				$this->agregarJs("Externos/jquery/jquery.relcopy/jquery.relCopy.ide.js");
				$total.="<script type='text/javascript'>
				$(function() {
					$('#".$this->getIdObjeto()."Duplicador').relCopy(
							{
								funcionPre: 'borrarComponentesDinamicos_".$propiedades["tipo"]."',
								funcionPost: 'crearComponenteDinamico_".$propiedades["tipo"]."',
							}
						);
					//$('#".$this->getIdObjeto()."Duplicador').relCopy();
				});
				</script>
				<a id='".$this->getIdObjeto()."Duplicador' rel='#".$this->getIdObjeto()."Contenedor' href='#'>Duplicar ".$propiedades["titulo"]."</a>";
			}*/
			return $total;
		}

		public function post2HTML(){
			$total="</div>\n";
			return $total;
		}

		final public function setReglasPorDefecto(){

			$propiedades = $this->getPropiedad("Propiedades");

			if(isset($propiedades["requerido"])){
				if(strcmp($propiedades["requerido"],"true")==0){
					$this->arrayReglas["reglas"][]="\n".str_repeat("\t", 3).'required: true';
					if(isset($propiedades["requerido_msj"]))
						$this->arrayReglas["mensajes"][]="\n".str_repeat("\t", 3).'required: "'.$propiedades["requerido_msj"].'"';
					else
						$this->arrayReglas["mensajes"][]="\n".str_repeat("\t", 3).'required: "Este campo es requerido"';
				}else{
					$this->arrayReglas["reglas"][]="\n".str_repeat("\t", 3).'required: false';
				}
			}
		}
	}
?>
