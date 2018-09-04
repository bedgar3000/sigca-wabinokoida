<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
list($CodRequerimiento, $Secuencia, $Numero) = explode("_", $sel_desconfirmar);
//---------------------------------------------------
//	consulto
$sql = "SELECT
			ccc.NroConfirmacion,
			ccc.FechaConfirmadaPor As FechaConfirmacion,
			ccc.CantidadRecibida,
			p.NomCompleto AS NomConfirmadoPor,
			r.FechaAprobacion AS FechaDocumento,
			r.CodInterno AS NroInterno,
			rd.Descripcion,
			o.Organismo,
			o.DocFiscal,
			o.Telefono1,
			o.Fax1,
			o.Direccion,
			d.Dependencia
		FROM
			lg_cajachicaconfirmacion ccc
			INNER JOIN lg_requerimientos r ON (r.CodRequerimiento = ccc.CodRequerimiento)
			INNER JOIN lg_requerimientosdet rd ON (rd.CodRequerimiento = ccc.CodRequerimiento AND
												   rd.Secuencia = ccc.Secuencia)
			INNER JOIN mastpersonas p ON (p.CodPersona = ccc.ConfirmadaPor)

			INNER JOIN mastorganismos o ON (o.CodOrganismo = r.CodOrganismo)
			INNER JOIN mastdependencias d On (d.CodDependencia = r.CodDependencia)
		WHERE
			ccc.CodRequerimiento = '".$CodRequerimiento."' AND
			ccc.Secuencia = '".$Secuencia."' AND
			ccc.Numero = '".$Numero."'";
$field = getRecord($sql);
//---------------------------------------------------

//---------------------------------------------------
class PDF extends FPDF {
	//	Cabecera de página.
	function Header() {
		global $_PARAMETRO;
		global $field;
		//	imprimo la cabecera
		$this->Image($_PARAMETRO["PATHLOGO"].'logo-alcaldia.jpg', 15, 11, 11, 12);	
		$this->SetFont('Arial', 'B', 8);
		$this->SetXY(25, 10); $this->Cell(195, 5, $field['Organismo'], 0, 0, 'L');
		$this->SetXY(25, 13); 
		$this->Cell(30, 5, utf8_decode('R.I.F. '.$field['DocFiscal']), 0, 0, 'L');
		$this->Cell(35, 5, utf8_decode('Telefono: '.$field['Telefono1']), 0, 0, 'L'); 
		$this->Cell(35, 5, utf8_decode('Fax: '.$field['Fax1']), 0, 0, 'L');		
		$this->SetXY(25, 16); $this->Cell(195, 5, utf8_decode($field['Direccion']), 0, 0, 'L');		
		$this->SetXY(25, 19); $this->Cell(195, 5, utf8_decode(getValorCampo("mastdependencias", "CodDependencia", "Dependencia", $_PARAMETRO['DEPLOGCXP'])), 0, 0, 'L');
		//------
		$this->SetFont('Arial', 'B', 12);
		$this->SetXY(10, 30); 
		$this->Cell(195, 10, utf8_decode('CONFIRMACIÓN DE SERVICIOS Nº '.$field['NroConfirmacion']), 0, 1, 'C');		
		//------
		$this->Ln(10);
		$this->SetFillColor(255, 255, 255);
		$this->SetX(20); $this->SetFont('Arial', 'B', 12); $this->Cell(15, 8, 'De: ', 0, 0, 'L', 1);
		$this->SetFont('Arial', '', 12); $this->Cell(15, 8, utf8_decode($field['Dependencia']), 0, 1, 'L', 1);
		$this->SetX(20); $this->SetFont('Arial', 'B', 12); $this->Cell(15, 8, 'Para: ', 0, 0, 'L', 1);
		$this->SetFont('Arial', '', 12); $this->Cell(15, 8, utf8_decode($field['Dependencia']), 0, 1, 'L', 1);
		$this->SetX(20); $this->SetFont('Arial', 'B', 12); $this->Cell(15, 8, 'Fecha: ', 0, 0, 'L', 1);
		$this->SetFont('Arial', '', 12); $this->Cell(15, 8, formatFechaDMA($field['FechaConfirmacion']), 0, 1, 'L', 1);
		$this->Ln(10);
		$this->SetX(20); 
		##	############
		list($Anio, $Mes, $Dia) = explode("-", $field['FechaConfirmacion']);
		$DiaLetras = convertir_a_letras($Dia, 'entero');
		$MesLetras = getNombreMes($field['FechaConfirmacion']);
		//$Texto = "Por medio de la presente se hace constar que el dia $DiaLetras ($Dia) de $MesLetras del $Anio, la Empresa: $field[NomProveedor], RIF. $field[DocFiscalProveedor]; prestó los Servicios de:";
		$Texto = "Por medio de la presente se hace constar que el dia $DiaLetras ($Dia) de $MesLetras del $Anio se confirmó los Servicios de:";
		$this->MultiCell(175, 8, utf8_decode($Texto), 0, 'J', 1);
		##	############
		$this->Ln(5);			
		$this->SetDrawColor(255, 255, 255);
		$this->SetFillColor(255, 255, 255);
		$this->SetFont('Arial', 'B', 12);
		$this->SetWidths(array(20, 5, 140));
		$this->SetAligns(array('L', 'C', 'L'));
		$this->SetX(30);
		$this->Row(array(number_format($field['CantidadRecibida'], 2, ',', '.'),
						 '', 					
						 utf8_decode($field['Descripcion'])));
		$this->Ln(10);
		$this->SetFont('Arial', '', 12);
		$this->SetX(20); 
		$Texto = "Según se detalla en Requerimiento Nro. $field[NroInterno] de fecha ".formatFechaDMA($field['FechaDocumento']).".";
		$this->MultiCell(175, 8, utf8_decode($Texto), 0, 'J', 1);
		/*$this->Ln(10);
		$this->SetFont('Arial', 'B', 12);
		$this->SetX(30); $this->Cell(60, 8, 'Monto Servicio: ', 0, 0, 'L', 1); $this->Cell(10, 8, 'Bs.', 0, 0, 'L', 1);
		$this->Cell(30, 8, number_format($field['MontoServicio'], 2, ',', '.'), 0, 1, 'R', 1);
		$this->SetX(30); $this->Cell(60, 8, 'I.V.A: ', 0, 0, 'L', 1); $this->Cell(10, 8, 'Bs.', 0, 0, 'L', 1);
		$this->Cell(30, 8, number_format($field['MontoImpuestos'], 2, ',', '.'), 0, 1, 'R', 1);
		$this->SetX(30); $this->Cell(60, 8, 'Monto Total a Pagar: ', 0, 0, 'L', 1); $this->Cell(10, 8, 'Bs.', 0, 0, 'L', 1);
		$this->Cell(30, 8, number_format($field['MontoTotal'], 2, ',', '.'), 0, 1, 'R', 1);*/
		$this->Ln(10);
		$this->SetFont('Arial', '', 12);
		//$Texto = "Por lo tanto, solicito se proceda con el pago respectivo.";
		//$this->SetX(20); $this->MultiCell(175, 8, utf8_decode($Texto), 0, 'J', 1);
	}
	//	Pie de página.
	function Footer() {
		global $field;
		//------
		$this->SetDrawColor(0, 0, 0);
		$this->SetFillColor(255, 255, 255);
		$this->Rect(115, 239, 85, 0.1, "DF");
		$this->SetFont('Arial', 'B', 10);
		$this->SetXY(125, 240); $this->Cell(65, 5, 'El presente documento fue confirmado por', 0, 0, 'C');
		$this->SetXY(125, 245); $this->Cell(65, 5, utf8_decode($field['NomConfirmadoPor']), 0, 0, 'C');
		$this->SetXY(125, 250); $this->Cell(65, 5, formatFechaDMA($field['FechaConfirmacion']), 0, 0, 'C');
	}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada.
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(10, 5, 10);
$pdf->SetAutoPageBreak(5, 1);
//---------------------------------------------------

//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>