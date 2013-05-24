<?php

	class Navegador extends ComponentePadre implements componente{
		
		var $arregloDeNiveles;
		
		function Navegador(){
			//No se esta utilizando
			//$this->js[]="Librerias/ideComponentes/Navegador/navegador.js";
			$this->css[]="Librerias/ideComponentes/Navegador/navegador.css";
		}

		function obtenerResultado($xml){
			$html="";
			static $ultimoHijo="";
			foreach($xml->children() as $hijo){
				switch($hijo->getName()){
					case "Listas":
						$html.=$this->obtenerResultado($hijo);
						break;
					case "Lista":
						$ultimoHijo=$hijo["nombre"];
						$html.="<div class='contenedorListaNavegador' id='{$hijo["nombre"]}'>";
						$html.="<div class='contenedorTitulo'><div class='preTituloLista'></div><div class='tituloLista'>{$hijo["nombre"]}</div><div class='posTituloLista'></div></div>";
						$html.="<div class='listaNavegador'>";
						$html.=$this->obtenerResultado($hijo);
						$html.="</div>";
						$html.="</div>";
						break;
					case "Elementos":
						$html.="<div class='contenedorItems'>";
						$html.=$this->obtenerResultado($hijo);
						$html.="</div>";
						break;
					case "Elemento":
						$html.="<div class='elementoListaNavegador {$hijo["estilo"]}'><a href='{$hijo["ancla"]}#$ultimoHijo'>{$hijo["texto"]}</a></div>";
						break;
					case "Paginacion":
						$html.="<div class='contenedorPaginacion'>";
						$html.=$this->obtenerResultado($hijo);
						$html.="</div>";
						break;
					case "AccionPaginacion":
						$html.="<div class='{$hijo["estilo"]}'><a href='{$hijo["ancla"]}'>{$hijo["texto"]}</a></div>";
						break;
					case "MensajePaginacion":
						$html.="<div class='{$hijo["estilo"]}'>{$hijo["texto"]}</div>";
						break;
					case "Acciones":
						$html.="<div class='accionesListaNavegador'>";
						$html.=$this->obtenerResultado($hijo);
						$html.="</div>";
						break;
					case "Accion":
						$html.="<div class='{$hijo["estilo"]}'><a href='{$hijo["ancla"]}#contenidoNavegacion'>{$hijo["texto"]}</a></div><div class='{$hijo["estilo"]}Cierre'></div>";
						break;
					case "Grupo":
						$html.="<div class='grupoDeListas'>";
						$html.=$this->obtenerResultado($hijo);
						$html.="</div>";
						break;
					default:
						$html.=$this->llamarClaseGenerica($hijo);

				}
			}
			return $html;
//			return htmlspecialchars($xml->asXML());
		}
		
		function analizarListas(){
			
		}
		
		function extraerNivel($xml, $nivel){
			foreach($xml as $nodo){
				$this->arregloDeNiveles["$nivel"][]=array(
					"nombre"=>$nodo["nombre"],
					"valor"=>$nodo["valor"],
					"estilo"=>$nodo["estilo"],
					"titulo"=>$nodo["titulo"]
				);
				if(count($nodo)>0){
					$this->extraerNivel($nodo, $nivel+1);
				}
			}
		}
		
		function obtenerHtml(){
			$html="";
			foreach($this->arregloDeNiveles as $no=>$nivel){
				
				$htmlTmp="";
				$tituloNivel="Nivel $no";
				foreach($nivel as $dato){
					$htmlTmp.="<li class='".$dato["estilo"]."'><a href='#' onClick='alert(\"".$dato["valor"]."\");'>".$dato["nombre"]."</a></li>";
					if($dato["titulo"]!=""){
						$tituloNivel=$dato["titulo"];
					}
				}
				$html.="<div id='nivel_".$no."' class='nivel' style='float:left;'><ul>";
				$html.="<div id='tituloNivel_".$no."' class='titulo'>";
					$html.="<div class='pestana5' style='float:left;'></div>";
					$html.="<div class='pestana2' style='float:left;'>$tituloNivel</div>";
					$html.="<div class='pestana4' style='float:left;'></div>";
				$html.="</div>";
				$html.="<div class='contenedorLista' style='clear:both;'>";
				$html.="<ul>";
				$html.=$htmlTmp;
				$html.="</ul></div></div>";
			}
			return $html;
		}
	}

?>
