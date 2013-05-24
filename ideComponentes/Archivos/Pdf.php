<?php

	define('FPDF_FONTPATH','../Externos/fpdf/font/');
	require('../Externos/fpdf/fpdf.php');

	class idePDF extends FPDF
	{
		public $cabeza=null;
		function Header()
		{
			global $title;

			//Arial bold 15
			/*$this->SetFont('Arial','B',15);
			//Calculate width of title and position
			$w=$this->GetStringWidth($title)+6;
			$this->SetX((210-$w)/2);
			//Colors of frame, background and text
			$this->SetDrawColor(0,80,180);
			$this->SetFillColor(230,230,0);
			$this->SetTextColor(220,50,50);
			//Thickness of frame (1 mm)
			
			//Title
			/*
			$this->Cell($w,9,$title,1,1,'C',true);
			*/
			$this->SetLineWidth(1);
			//var_dump($this->cabeza);
			if (!is_null($this->cabeza)){
				//echo "No es null entonces lo imprimo";
				foreach($this->cabeza->children() as $xml){
					$this->procesarXML($xml);
				}
			}
			//Line break
			$this->Ln(30);
		}

		function Footer()
		{
		    //Position at 1.5 cm from bottom
		    $this->SetY(-15);
		    //Arial italic 8
		    $this->SetFont('Arial','I',8);
		    //Text color in gray
		    $this->SetTextColor(128);
		    //Page number
		    $this->Cell(0,10,'Pagina '.$this->PageNo(),0,0,'C');
		}
		function PrintChapter($num,$title,$file)
		{
		    $this->ChapterTitle($num,$title);
		    $this->ChapterBody($file);
		}
		
		function ImprimirTitulo($label)
		{
		    //Arial 12
		    $this->SetFont('Times','',12);
		    //Background color
		    $this->SetFillColor(200,220,255);
		    //Title
		    $this->Cell(0,6,utf8_decode($label),0,1,'L',true);
		    //Line break
		    $this->Ln(4);
		}
		
		function ImprimirTexto($texto)
		{
		    $this->SetFont('Times','',12);
		    $this->MultiCell(0,5,utf8_decode($texto));
		    $this->Ln();
		}	
		function ImprimirCadena($xml)
		{
			$fuente='Arial';
			if ($xml['fuente']!="")
				$fuente=$xml['fuente'];
			$estilo='';
			if ($xml['estilo']!="")
				$estilo=$xml['estilo'];
			$tamanho='tamaño';
			if ($xml['tamaño']!="")
				$tamanho=$xml['tamaño'];

			$x='';
			if ($xml['x']!="")
				$x=$xml['x'];
			$y='';
			if ($xml['y']!="")
				$y=$xml['y'];

			$this->SetFont($fuente,$estilo,$tamanho);
			$this->Cell($x,$y,utf8_decode($xml['texto']));
		}	
		
		function ImprimirSeccion($xml){
			$this->ImprimirTitulo($xml['titulo']);
			$this->ImprimirTexto($xml['texto']);
		}
		
		function procesarXML($xml){
			switch($xml->getName()){
				case "Texto":
					$this->ImprimirSeccion($xml);
					break;
				case "Cadena":
					$this->ImprimirCadena($xml);
					break;
				case "NuevaLinea":
					$this->Ln($xml['alto']);
					break;
				case "Imagen":
					//echo "Los parametros son: ",$xml['dirección'],",",$xml['x'],",",$xml['y'],",",$xml['alto']."<br>";
					$this->Image("".utf8_decode($xml['dirección']),$xml['x'],$xml['y'],$xml['alto']);
					break;
				default:
			}
		}
	}


	class Pdf extends ComponentePadre implements componente{
		
		function Pdf(){
			//$this->js[]="../Librerias/ideComponentes/Navegador/navegador.js";
			//$this->css[]="../Librerias/ideComponentes/Navegador/navegador.css";
		}
	
		function obtenerResultado($xml, $nombreClasePDF="idePDF", $area=""){
		
			ob_clean();
			$pdf=new $nombreClasePDF();

			
			$contador=1;

			foreach($xml->children() as $hijo){
				$fuente="Arial";
				$negrilla='';
				$tamanho=12;
				$x=20;
				$y=20*$contador;
				switch($hijo->getName()){
					case "Encabezado":
						//echo "agregando cabeza";
						$pdf->cabeza=$hijo;
						$pdf->AddPage();
						break;
					default:
						$pdf->procesarXML($hijo);
				}
				$contador++;
			}
			$pdf->Output($xml["nombre"], 'I');
			
			ob_clean();
			exit();
		}
		
	}
	
	
	
?>
