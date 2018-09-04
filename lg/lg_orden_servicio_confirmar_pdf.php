<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
$foot = false;
//---------------------------------------------------
list($Anio, $CodOrganismo, $NroOrden, $Secuencia, $CodProveedor, $DocumentoClasificacion, $DocumentoReferencia) = explode('_', $confirmadas[0]);
//---------------------------------------------------
//	consulto
$sql = "SELECT
			dc.Anio,
			dc.CodProveedor,
			dc.DocumentoClasificacion,
			dc.DocumentoReferencia,
			dc.TransaccionNroDocumento,
			(dc.MontoAfecto + dc.MontoNoAfecto) AS MontoServicio,
			dc.MontoImpuestos,
			dc.MontoTotal,
			dc.Comentarios AS Descripcion,
			osd.FechaConfirmacion,
			osd.FechaTermino,
			osd.NroActivo,
			os.FechaDocumento,
			os.Estado,
			os.NomProveedor,
			os.CodCentroCosto,
			os.NroInterno,
			ocs.FechaConfirmacion,
			ocs.NroInterno AS NroConfirmacion,
			mp.DocFiscal AS DocFiscalProveedor,
			mp.Direccion,
			mp.Telefono1,
			fp.Descripcion AS NomFormaPago,
			d.Dependencia,
			o.Organismo,
			o.DocFiscal,
			o.Telefono1,
			o.Fax1,
			o.Direccion,
			pr.NroInscripcionSNC,
			cc.Descripcion AS CentroCosto,
			cc.Abreviatura As NomCentroCosto,
			mp2.NomCompleto As NomConfirmadoPor,
			afa.Descripcion AS NomActivo
		FROM
			ap_documentos dc
			INNER JOIN lg_ordenservicio os ON (os.Anio = dc.Anio AND
											   os.CodOrganismo = dc.CodOrganismo AND
											   os.CodProveedor = dc.CodProveedor AND
											   os.NroOrden = dc.ReferenciaNroDocumento)
			INNER JOIN lg_ordenserviciodetalle osd ON (osd.Anio = os.Anio AND
											   		   osd.CodOrganismo = os.CodOrganismo AND
													   osd.NroOrden = os.NroOrden)
			INNER JOIN lg_confirmacionservicio ocs ON (ocs.Anio = os.Anio AND
											   		   ocs.CodOrganismo = os.CodOrganismo AND
													   ocs.NroOrden = os.NroOrden AND
													   ocs.DocumentoReferencia = dc.DocumentoReferencia)
			INNER JOIN mastpersonas mp ON (os.CodProveedor = mp.CodPersona)
			INNER JOIN mastproveedores pr ON (mp.CodPersona = pr.CodProveedor)
			INNER JOIN mastorganismos o ON (os.CodOrganismo = o.CodOrganismo)
			INNER JOIN mastdependencias d ON (os.CodDependencia = d.CodDependencia)
			INNER JOIN mastformapago fp ON (pr.CodFormaPago = fp.CodFormaPago)
			INNER JOIN ac_mastcentrocosto cc ON (osd.CodCentroCosto = cc.CodCentroCosto)
			INNER JOIN mastpersonas mp2 ON (osd.ConfirmadoPor = mp2.CodPersona)
			LEFT JOIN af_activo afa ON (afa.Activo = osd.NroActivo)
		WHERE
			dc.Anio = '".$Anio."' AND
			dc.CodProveedor = '".$CodProveedor."' AND
			dc.DocumentoClasificacion = '".$DocumentoClasificacion."' AND
			dc.DocumentoReferencia = '".$DocumentoReferencia."'
		GROUP BY dc.Anio, dc.CodProveedor, dc.DocumentoClasificacion, dc.DocumentoReferencia";
$field_documento = getRecord($sql);

$sql = "SELECT
			dc.Anio,
			dc.CodProveedor,
			dc.DocumentoClasificacion,
			dc.DocumentoReferencia,
			dc.TransaccionNroDocumento,
			dd.Cantidad AS CantidadRecibida,
			dd.Total AS MontoServicio,
			dc.MontoImpuestos,
			dc.MontoTotal,
			dd.Descripcion,
			osd.FechaConfirmacion,
			osd.FechaTermino,
			osd.NroActivo,
			os.FechaDocumento,
			os.Estado,
			os.NomProveedor,
			os.NroInterno,
			ocs.NroInterno AS NroConfirmacion,
			mp.DocFiscal AS DocFiscalProveedor,
			mp.Direccion,
			mp.Telefono1,
			fp.Descripcion AS NomFormaPago,
			d.Dependencia,
			o.Organismo,
			o.DocFiscal,
			o.Telefono1,
			o.Fax1,
			o.Direccion,
			pr.NroInscripcionSNC,
			cc.Abreviatura As NomCentroCosto,
			mp2.NomCompleto As NomConfirmadoPor,
			afa.Descripcion AS NomActivo
		FROM
			ap_documentos dc
			INNER JOIN ap_documentosdetalle dd ON (dd.Anio = dc.Anio AND
												   dd.CodProveedor = dc.CodProveedor AND
												   dd.DocumentoClasificacion = dc.DocumentoClasificacion AND
												   dd.DocumentoReferencia = dc.DocumentoReferencia)
			INNER JOIN lg_ordenservicio os ON (os.Anio = dc.Anio AND
											   os.CodOrganismo = dc.CodOrganismo AND
											   os.CodProveedor = dc.CodProveedor AND
											   os.NroOrden = dc.ReferenciaNroDocumento)
			INNER JOIN lg_ordenserviciodetalle osd ON (osd.Anio = os.Anio AND
											   		   osd.CodOrganismo = os.CodOrganismo AND
													   osd.NroOrden = os.NroOrden)
			INNER JOIN lg_confirmacionservicio ocs ON (ocs.Anio = os.Anio AND
											   		   ocs.CodOrganismo = os.CodOrganismo AND
													   ocs.NroOrden = os.NroOrden AND
													   ocs.DocumentoReferencia = dc.DocumentoReferencia)
			
			INNER JOIN mastpersonas mp ON (os.CodProveedor = mp.CodPersona)
			INNER JOIN mastproveedores pr ON (mp.CodPersona = pr.CodProveedor)
			INNER JOIN mastorganismos o ON (os.CodOrganismo = o.CodOrganismo)
			INNER JOIN mastdependencias d ON (os.CodDependencia = d.CodDependencia)
			INNER JOIN mastformapago fp ON (pr.CodFormaPago = fp.CodFormaPago)
			INNER JOIN ac_mastcentrocosto cc ON (osd.CodCentroCosto = cc.CodCentroCosto)
			INNER JOIN mastpersonas mp2 ON (osd.ConfirmadoPor = mp2.CodPersona)
			LEFT JOIN af_activo afa ON (afa.Activo = osd.NroActivo)
		WHERE
			dc.Anio = '".$Anio."' AND
			dc.CodProveedor = '".$CodProveedor."' AND
			dc.DocumentoClasificacion = '".$DocumentoClasificacion."' AND
			dc.DocumentoReferencia = '".$DocumentoReferencia."'
		GROUP BY dc.Anio, dc.CodProveedor, dc.DocumentoClasificacion, dc.DocumentoReferencia, dd.Secuencia";
$field_documento_detalle = getRecords($sql);
//---------------------------------------------------

//---------------------------------------------------
class PDF extends FPDF {
	//	Cabecera de página.
	function Header() {
		global $_PARAMETRO;
		global $field_documento;
		global $field_documento_detalle;
		//	imprimo la cabecera
		$this->Image($_PARAMETRO["PATHLOGO"].'logo-alcaldia.jpg', 15, 11, 11, 12);	
		$this->SetFont('Arial', 'B', 8);
		$this->SetXY(25, 10); $this->Cell(195, 5, $field_documento['Organismo'], 0, 0, 'L');
		$this->SetXY(25, 13); 
		$this->Cell(30, 5, utf8_decode('R.I.F. '.$field_documento['DocFiscal']), 0, 0, 'L');
		$this->Cell(35, 5, utf8_decode('Telefono: '.$field_documento['Telefono1']), 0, 0, 'L'); 
		$this->Cell(35, 5, utf8_decode('Fax: '.$field_documento['Fax1']), 0, 0, 'L');		
		$this->SetXY(25, 16); $this->Cell(195, 5, utf8_decode($field_documento['Direccion']), 0, 0, 'L');		
		$this->SetXY(25, 19); $this->Cell(195, 5, utf8_decode(getValorCampo("mastdependencias", "CodDependencia", "Dependencia", $_PARAMETRO['DEPLOGCXP'])), 0, 0, 'L');
		//------
		$this->SetFont('Arial', 'B', 12);
		$this->SetXY(10, 30); 
		$this->Cell(195, 10, utf8_decode('CONFIRMACIÓN DE SERVICIOS Nº '.$field_documento['NroConfirmacion']), 0, 1, 'C');
		//------
		$this->Ln(10);
		$this->SetX(30);
		/*$this->SetFillColor(255, 255, 255);
		$this->SetX(20); $this->SetFont('Arial', 'B', 12); $this->Cell(15, 8, 'De: ', 0, 0, 'L', 1);
		$this->SetFont('Arial', '', 12); $this->Cell(15, 8, utf8_decode($field_documento['CentroCosto']), 0, 1, 'L', 1);
		$this->SetX(20); $this->SetFont('Arial', 'B', 12); $this->Cell(15, 8, 'Para: ', 0, 0, 'L', 1);
		$this->SetFont('Arial', '', 12); $this->Cell(15, 8, utf8_decode($field_documento['CentroCosto']), 0, 1, 'L', 1);
		$this->SetX(20); $this->SetFont('Arial', 'B', 12); $this->Cell(15, 8, 'Fecha: ', 0, 0, 'L', 1);
		$this->SetFont('Arial', '', 12); $this->Cell(15, 8, formatFechaDMA($field_documento['FechaConfirmacion']), 0, 1, 'L', 1);
		$this->Ln(10);
		$this->SetX(20); 
		##	############
		list($Anio, $Mes, $Dia) = explode("-", $field_documento['FechaConfirmacion']);
		$DiaLetras = convertir_a_letras($Dia, 'entero');
		$MesLetras = getNombreMes($field_documento['FechaConfirmacion']);
		$Texto = "Por medio de la presente se hace constar que el dia $DiaLetras ($Dia) de $MesLetras del $Anio, la Empresa: $field_documento[NomProveedor], RIF. $field_documento[DocFiscalProveedor]; prestó los Servicios de:";
		$this->MultiCell(175, 8, utf8_decode($Texto), 0, 'J', 1);
		##	############
		$this->Ln(5);			
		$this->SetDrawColor(255, 255, 255);
		$this->SetFillColor(255, 255, 255);
		$this->SetFont('Arial', 'B', 12);
		$this->SetWidths(array(20, 5, 140));
		$this->SetAligns(array('L', 'C', 'L'));
		foreach ($field_documento_detalle as $fdd) {
			$this->SetX(30);
			$this->Row(array(number_format($fdd['CantidadRecibida'], 2, ',', '.'),
							 '', 					
							 utf8_decode($fdd['Descripcion'])));
		}
		$this->Ln(10);
		if ($field_documento['NroActivo']) {
			$this->SetFont('Arial', '', 12);
			$this->SetX(20); $this->Cell(195, 8, utf8_decode('correspondientes al activo '), 0, 1, 'L', 1);
			$this->Ln(5);			
			$this->SetDrawColor(255, 255, 255);
			$this->SetFillColor(255, 255, 255);
			$this->SetFont('Arial', 'B', 12);
			$this->SetWidths(array(26, 5, 134));
			$this->SetAligns(array('L', 'C', 'L'));
			$this->SetX(30);
			$this->Row(array($field_documento['NroActivo'],
							 '', 					
							 utf8_decode($field_documento['NomActivo'])));
			$this->Ln(10);
		}
		$this->SetFont('Arial', '', 12);
		$this->SetX(20); 
		$Texto = "Según se detalla en Orden de Servicio O/S $field_documento[NroInterno] de fecha ".formatFechaDMA($field_documento['FechaDocumento']).".";
		$this->MultiCell(175, 8, utf8_decode($Texto), 0, 'J', 1);
		$this->Ln(10);
		$this->SetFont('Arial', 'B', 12);
		$this->SetX(30); $this->Cell(60, 8, 'Monto Servicio: ', 0, 0, 'L', 1); $this->Cell(10, 8, 'Bs.', 0, 0, 'L', 1);
		$this->Cell(30, 8, number_format($field_documento['MontoServicio'], 2, ',', '.'), 0, 1, 'R', 1);
		$this->SetX(30); $this->Cell(60, 8, 'I.V.A: ', 0, 0, 'L', 1); $this->Cell(10, 8, 'Bs.', 0, 0, 'L', 1);
		$this->Cell(30, 8, number_format($field_documento['MontoImpuestos'], 2, ',', '.'), 0, 1, 'R', 1);
		$this->SetX(30); $this->Cell(60, 8, 'Monto Total a Pagar: ', 0, 0, 'L', 1); $this->Cell(10, 8, 'Bs.', 0, 0, 'L', 1);
		$this->Cell(30, 8, number_format($field_documento['MontoTotal'], 2, ',', '.'), 0, 1, 'R', 1);
		$this->Ln(10);
		$this->SetFont('Arial', '', 12);
		$Texto = "Por lo tanto, solicito se proceda con el pago respectivo.";
		$this->SetX(20); $this->MultiCell(175, 8, utf8_decode($Texto), 0, 'J', 1);*/
	}
	//	Pie de página.
	function Footer() {
		global $field_documento;
		global $foot;

		if ($foot) {
			$CategoriaProg = getCategoriaXCentroCosto($field_documento['CodCentroCosto']);
			$Unidad = getUnidadEjecutora($CategoriaProg);
			//------
			$this->SetDrawColor(0, 0, 0);
			$this->SetFillColor(255, 255, 255);
			$this->Rect(115, 239, 85, 0.1, "DF");
			$this->SetFont('Arial', 'B', 10);
			$this->SetXY(125, 240); $this->Cell(65, 5, 'El presente documento fue confirmado por', 0, 0, 'C');
			$this->SetXY(125, 245); $this->Cell(65, 5, utf8_decode($Unidad), 0, 0, 'C');
			$this->SetXY(125, 250); $this->Cell(65, 5, formatFechaDMA($field_documento['FechaConfirmacion']), 0, 0, 'C');
		}
	}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada.
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(1, 30);
$pdf->AddPage();
//---------------------------------------------------
$pdf->SetFillColor(255, 255, 255);
$pdf->SetX(20); $pdf->SetFont('Arial', 'B', 12); $pdf->Cell(15, 8, 'De: ', 0, 0, 'L', 1);
$pdf->SetFont('Arial', '', 12); $pdf->Cell(15, 8, utf8_decode($field_documento['CentroCosto']), 0, 1, 'L', 1);
$pdf->SetX(20); $pdf->SetFont('Arial', 'B', 12); $pdf->Cell(15, 8, 'Para: ', 0, 0, 'L', 1);
$pdf->SetFont('Arial', '', 12); $pdf->Cell(15, 8, utf8_decode($field_documento['CentroCosto']), 0, 1, 'L', 1);
$pdf->SetX(20); $pdf->SetFont('Arial', 'B', 12); $pdf->Cell(15, 8, 'Fecha: ', 0, 0, 'L', 1);
$pdf->SetFont('Arial', '', 12); $pdf->Cell(15, 8, formatFechaDMA($field_documento['FechaConfirmacion']), 0, 1, 'L', 1);
$pdf->Ln(10);
$pdf->SetX(20); 
##	############
list($Anio, $Mes, $Dia) = explode("-", $field_documento['FechaConfirmacion']);
$DiaLetras = convertir_a_letras($Dia, 'entero');
$MesLetras = getNombreMes($field_documento['FechaConfirmacion']);
$Texto = "Por medio de la presente se hace constar que el dia $DiaLetras ($Dia) de $MesLetras del $Anio, la Empresa: $field_documento[NomProveedor], RIF. $field_documento[DocFiscalProveedor]; prestó los Servicios de:";
$pdf->MultiCell(175, 8, utf8_decode($Texto), 0, 'J', 1);
##	############
$pdf->Ln(5);			
$pdf->SetDrawColor(255, 255, 255);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetWidths(array(20, 5, 140));
$pdf->SetAligns(array('L', 'C', 'L'));
foreach ($field_documento_detalle as $fdd) {
	$pdf->SetX(30);
	$pdf->Row(array(number_format($fdd['CantidadRecibida'], 2, ',', '.'),
					 '', 					
					 utf8_decode($fdd['Descripcion'])));
}
$pdf->Ln(10);
if ($field_documento['NroActivo']) {
	$pdf->SetFont('Arial', '', 12);
	$pdf->SetX(20); $pdf->Cell(195, 8, utf8_decode('correspondientes al activo '), 0, 1, 'L', 1);
	$pdf->Ln(5);			
	$pdf->SetDrawColor(255, 255, 255);
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', 'B', 12);
	$pdf->SetWidths(array(26, 5, 134));
	$pdf->SetAligns(array('L', 'C', 'L'));
	$pdf->SetX(30);
	$pdf->Row(array($field_documento['NroActivo'],
					 '', 					
					 utf8_decode($field_documento['NomActivo'])));
	$pdf->Ln(10);
}
$pdf->SetFont('Arial', '', 12);
$pdf->SetX(20); 
$Texto = "Según se detalla en Orden de Servicio O/S $field_documento[NroInterno] de fecha ".formatFechaDMA($field_documento['FechaDocumento']).".";
$pdf->MultiCell(175, 8, utf8_decode($Texto), 0, 'J', 1);
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetX(30); $pdf->Cell(60, 8, 'Monto Servicio: ', 0, 0, 'L', 1); $pdf->Cell(10, 8, 'Bs.', 0, 0, 'L', 1);
$pdf->Cell(30, 8, number_format($field_documento['MontoServicio'], 2, ',', '.'), 0, 1, 'R', 1);
$pdf->SetX(30); $pdf->Cell(60, 8, 'I.V.A: ', 0, 0, 'L', 1); $pdf->Cell(10, 8, 'Bs.', 0, 0, 'L', 1);
$pdf->Cell(30, 8, number_format($field_documento['MontoImpuestos'], 2, ',', '.'), 0, 1, 'R', 1);
$pdf->SetX(30); $pdf->Cell(60, 8, 'Monto Total a Pagar: ', 0, 0, 'L', 1); $pdf->Cell(10, 8, 'Bs.', 0, 0, 'L', 1);
$pdf->Cell(30, 8, number_format($field_documento['MontoTotal'], 2, ',', '.'), 0, 1, 'R', 1);
$pdf->Ln(10);
$pdf->SetFont('Arial', '', 12);
$Texto = "Por lo tanto, solicito se proceda con el pago respectivo.";
$pdf->SetX(20); $pdf->MultiCell(175, 8, utf8_decode($Texto), 0, 'J', 1);
$foot = true;
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>