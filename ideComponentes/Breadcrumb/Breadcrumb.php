<?php
	class Breadcrumb extends ComponentePadre implements componente{

		function Breadcrumb(){
		}

		function obtenerResultado($xml){

			// Se incrementa y se settea el identificador de breadcrumbs
			static $contadorBreadcrumb = 0;
			$contadorBreadcrumb++;

			$this->setAtributoInexistente($xml, 'id', "Breadcrumb".$contadorBreadcrumb);
			$this->setAtributoInexistente($xml, 'clase', "xbreadcrumbs");
			$this->setAtributoInexistente($xml, 'estilo', "");

			$this->js[]="Externos/jquery/jquery.xBreadcrumbs/xbreadcrumbs.js";
			$this->css[]="Externos/jquery/jquery.xBreadcrumbs/xbreadcrumbs.css";

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
						background: url(".resolverPath()."/../Externos/iconos/tango/16x16/actions/go-home.png) no-repeat left center;
						padding-left: 20px;
					}
					.xbreadcrumbs#".$xml['id']." li ul li a:hover { background: none; padding: 4px;}
				</style>";
			}

			$html .= "<div style='overflow:hidden;'><ul class='".$xml['clase']."' id='".$xml['id']."' style='".$xml['estilo']."'>";

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
			$html .= "\n</ul></div>";

			return $html;
		}

	}
?>
