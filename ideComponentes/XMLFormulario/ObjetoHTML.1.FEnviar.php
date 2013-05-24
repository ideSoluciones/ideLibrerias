<?php

	/**
	*    @name FEnviar
	*    @abstract	
	*    @author 
	*    @version 1.0
	*/
	
	class FEnviar extends ObjetoHTML{

		function FEnviar($idObjeto, $nombre, $propiedades, $tipoSalida, $accionPlus){
			$this->setIdObjeto($idObjeto);
			$this->setNombre($nombre);
			$this->setPropiedades($propiedades);
			$this->setTipoSalida($tipoSalida);
			$this->setAccionPlus($accionPlus);
		}
		
		function toHTML(){
			$total="";
			$propiedades=$this->getPropiedades();
			$estilo="";
			if (isset($propiedades["error"])){
				if (strcmp(strtolower($propiedades["error"]),"true")==0){
					$estilo.=($propiedades["error"]==true?"error ":"");
				}
			}
			$estilo.="contenedorCampoFormulario".$propiedades["tipo"]." campoFormulario ";
			if (isset($propiedades["estilo"])){
				$estilo.=$propiedades["estilo"];
			}

			$titulo="";
			if(isset($propiedades["titulo"])){
				$titulo=$propiedades["titulo"];
			}
			
			static $botonesEnviar=0;
			if(isset($propiedades["id"])){
				$id=$propiedades["id"];
			}else{
				$id="bEnviar$botonesEnviar";
				$botonesEnviar++;
			}

			$scriptFocoAntes="";
			$tipo="submit";
			if (isset($propiedades["focoAntes"])){
				$scriptFocoAntes="this.focus();this.form.submit();";
				$tipo="button";
			}
			if (isset($propiedades["click"])){
				$total.="
					<script>
						$(function() {
							$('#".$id."').click(function() {
								".$propiedades["click"]."
								$scriptFocoAntes
							});
						});
						
					</script>
				";
			}else{
				if(isset($propiedades["focoAntes"])){
					$total.="
						<script>
							$(function() {
								$('#".$id."').click(function() {
									$scriptFocoAntes
								});
							});
						
						</script>
					";
				}
			}

			$total.="<input type='$tipo' name='".$this->getNombre()."' 
					value='".$titulo."' 
					style='".$estilo."' 
					id='".$id."' />
			";
		
			return $total;
		}
	}
	
?>
