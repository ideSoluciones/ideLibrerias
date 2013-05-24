<?php

	include_once("Text/Wiki/Mediawiki.php");


	class Wiki extends ComponentePadre implements componente{

		function Wiki(){
			//$this->js[]="Librerias/ideComponentes/Navegador/navegador.js";
			//$this->css[]="Librerias/ideComponentes/Navegador/navegador.css";
		}

		function obtenerResultado($xml){
			$clase="class='wiki ";
			if (isset($xml['clase'])){
				$clase.=$xml['clase'];
			}
			$clase.="'";

			$estilo="";
			if (isset($xml['estilo'])){
				$estilo="style='".$xml['estilo']."'";
			}
			$id="";
			if (isset($xml['id'])){
				$estilo="id='".$xml['id']."'";
			}
			$onclick="";
			if (isset($xml['onclick'])){
				$estilo="onclick='".$xml['onclick']."'";
			}
		
			$html="<div ".$estilo." ".$clase." ".$id."  ".$onclick.">";
			$rules = array(
				'Prefilter',
				'Delimiter',
				'Code',
				'Function',
				'Html',
				'Raw',
				'Include',
				'Embed',
				'Anchor',
				'Heading',
				'Toc',
				'Horiz',
				'Break',
				'Blockquote',
				'List',
				'Deflist',
				'Table',
				'Image',
				'Phplookup',
				'Center',
				'Newline',
				'Paragraph',
				'Url',
				'Freelink',
				'Interwiki',
				'Wikilink',
				//'WikilinkIde',
				'Colortext',
				'Strong',
				'Bold',
				'Emphasis',
				'Italic',
				'Underline',
				'Tt',
				'Superscript',
				'Subscript',
				'Revise',
				'Tighten'
			    );
			$wiki = Text_Wiki::factory('Mediawiki', $rules);



            // Partimos la cadena por los caracteres [[ o ]]
            $links=preg_split("/\]\]|\[\[/", $xml);

            $pages=array();
            for ($i=0;$i<count($links);$i++){
                if ($i%2){
                    // Agregamos solamente los links impares
					$partes=explode('|', $links[$i]);
                    $pages[]=str_replace(" ","",$partes[0]);
                }
            }


			$wiki->setRenderConf('xhtml', 'wikilink', 'pages', $pages);
			$wiki->setRenderConf('xhtml', 'wikilink', 'view_url', "http://".$_SERVER["SERVER_NAME"].resolverPath().'/%s');
			$cadena="\n".$xml;
			$html.= str_replace("%2F", "/", html_entity_decode( $wiki->transform($cadena, 'Xhtml'), ENT_COMPAT, "ISO-8859-1"));

			$html.="</div>";
			return $html;
		}

	}



?>
