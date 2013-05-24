<?php
/*

<Filetree>

<Carpeta nombre='carpeta 1' clase='' estilo='' id='' enlace='' cerrado='true'/>
<Carpeta nombre='carpeta 2' clase='' estilo='' id='' enlace='' cerrado='true'/>
<Carpeta nombre='carpeta 3' clase='' estilo='' id='' enlace='' cerrado='true'/>
<Carpeta nombre='carpeta 4' clase='' estilo='' id='' enlace='' cerrado='true'/>

<Archivo nombre='archivo 1' clase='' estilo='' id='' enlace=''/>

</Filetree>

*/
	class Filetree extends ComponentePadre implements componente{

		function Filetree(){
		}

		private function concatenarHijos(&$xml){
			$this->setAtributoInexistente($xml, 'id', "");
			$this->setAtributoInexistente($xml, 'clase', "");
			$this->setAtributoInexistente($xml, 'estilo', "");
//var_dump(count($xml->children()));
			$html = "";
			if(count($xml->children())>0){		
				$html .= "<ul id='".$xml["id"]."' class='".$xml["clase"]."' style='".$xml["estilo"]."'>";
				foreach($xml->children() as $nodo){
					$this->setAtributoInexistente($xml, 'nombre', "no hay nombre");
					if(strcasecmp((string)$nodo->getName(),"Carpeta")==0){
						$html .= "<li class='closed'><span class='folder'>".$nodo["nombre"]."</span>";
						$html .= $this->concatenarHijos($nodo);
						$html .= "</li>";
					}
					if(strcasecmp((string)$nodo->getName(),"Archivo")==0){
						$html .= "<li><span class='file'>".$nodo["nombre"]."</span></li>";
					}
				}
				$html .= "</ul>";
			}
			return $html;
		}

		function obtenerResultado($xml){

			$this->js[]="Externos/jquery/jquery.treeview/jquery.treeview.min.js";
			$this->js[]="Externos/jquery/jquery.treeview/jquery.cookie.js";
			$this->css[]="Externos/jquery/jquery.treeview/jquery.treeview.css";
			$this->css[]="Externos/jquery/jquery.treeview/screen.css";

			// Se incrementa y se settea el identificador de breadcrumbs
			static $contadorFiletree = 0;
			$contadorFiletree++;

			$this->setAtributoInexistente($xml, 'id', "Filetree".$contadorFiletree);
			$this->setAtributoInexistente($xml, 'clase', "filetree");
			$this->setAtributoInexistente($xml, 'estilo', "");

			$html .="
			<script type='text/javascript'> 
				$(function() {
					$('#".$xml['id']."').treeview();
				});
			</script>";

			$html = $this->concatenarHijos($xml);

/*
			$this->js[]="Externos/jquery/jquery.xBreadcrumbs/xbreadcrumbs.js";
			$this->css[]="Externos/jquery/jquery.xBreadcrumbs/xbreadcrumbs.css";
*/

/*
			$html ="";

			if(strcmp($xml['clase'],"xbreadcrumbs")==0)
			{
				$html .="
				<script type='text/javascript'>
					 $(document).ready(function(){
						  $('#".$xml['id']."').xBreadcrumbs();
					 });
				</script>".
				"<style type='text/css'>
					.xbreadcrumbs#".$xml['id']." li a.home {
						background: url(/ide/Externos/iconos/tango/16x16/actions/go-home.png) no-repeat left center;
						padding-left: 20px;
					}
					.xbreadcrumbs#".$xml['id']." li ul li a:hover { background: none; padding: 4px;}
				</style>";
			}

			$html .= "<ul class='".$xml['clase']."' id='".$xml['id']."' style='".$xml['estilo']."'>";

			// Se recorre el XML en busca de tags categoria
			foreach($xml->children() as $categoria){
				if(strcasecmp($categoria->getName(),"Categoria")==0){			
					$this->setAtributoInexistente($categoria, "clase", "");
					$this->setAtributoInexistente($categoria, "claseEnlace", "");
					$this->setAtributoInexistente($categoria, "enlace", "#");
					$this->setAtributoInexistente($categoria, "titulo", "No hay titulo categoria");
					$this->setAtributoInexistente($categoria, "estiloListaDesplegable", "display: none;");
					$html .= "\n<li class='".$categoria['clase']."'>
						<a class='".$categoria['claseEnlace']."' href='".$categoria['enlace']."' >".$categoria['titulo']."</a>";
						// Se recorre el XML de la categoria buscando tags item
						if(count($categoria)>0){
							$html .= "\n<ul style='".$categoria['estiloListaDesplegable']."'>";
							// Se recorre el XML de la categoria buscando tags item
							foreach($categoria->children() as $item){
								if(strcasecmp($item->getName(),"Item")==0){
									$this->setAtributoInexistente($item, "enlace", "#");
									$this->setAtributoInexistente($item, "titulo", "No hay titulo item");
									$html .= "\n<li><a href='".$item['enlace']."'>".$item["titulo"]."</a></li>";
								}else{
									// El XML enviado no cumple la estructura esperada							
								}
							}
							$html .= "\n</ul>";
						}
					$html .= "\n</li>";
				} else{
					// El XML enviado no cumple la estructura esperada				
				}
			}
			$html .= "\n</ul>";
*/
			return $html;
		}

	}
?>
