<?php
require('fpdf.php');
require('fphp_ap.php');
connect();
//---------------------------------------------------
$_PATHLOGO = getParametro("PATHLOGO");
list($flagcajachica, $periodo, $nrocajachica) = split("[_]", $sel_registros);
//---------------------------------------------------
//	consulto la informacion general
$sql = "SELECT
			cc.*,
			p1.NomCompleto AS NomBeneficiario,
			p2.NomCompleto AS NomPreparadoPor,
			p3.NomCompleto AS NomAprobadoPor,
			cg.Descripcion AS NomClasificacion
		FROM
			ap_cajachica cc
			INNER JOIN mastpersonas p1 ON (cc.CodBeneficiario = p1.CodPersona)
			INNER JOIN mastpersonas p2 ON (cc.PreparadoPor = p2.CodPersona)
			INNER JOIN mastpersonas p3 ON (cc.PreparadoPor = p3.CodPersona)
			INNER JOIN mastempleado e ON (p1.CodPersona = e.CodPersona)
			INNER JOIN mastpersonas p4 ON (e.CodPersona = p4.CodPersona)
			INNER JOIN ap_clasificaciongastos cg ON (cc.CodClasificacion = cg.CodClasificacion)
		WHERE
			cc.FlagCajaChica = '".$flagcajachica."' AND
			cc.Periodo = '".$periodo."' AND
			cc.NroCajaChica = '".$nrocajachica."'";
$query_cc = mysql_query($sql) or die ($sql.mysql_error());
if (mysql_num_rows($query_cc) != 0) $field_cc = mysql_fetch_array($query_cc);
//---------------------------------------------------

//---------------------------------------------------
class PDF extends FPDF {
	//	Cabecera de página.
	function Header() {
		global $_PATHLOGO;
		global $flagcajachica;
		global $periodo;
		global $nrocajachica;
		global $field_cc;
		global $Head1;
		$this->SetTextColor(0, 0, 0);
		$this->SetDrawColor(255, 255, 255);
		$this->SetFillColor(255, 255, 255);
		$this->Image($_PATHLOGO.'logo-alcaldia.jpg', 10, 5, 10, 10);		
		$this->SetFont('Arial', '', 8);
		$this->SetXY(20, 5); $this->Cell(100, 5, $_SESSION['NOMBRE_ORGANISMO_ACTUAL'], 0, 1, 'L');
		$this->SetXY(20, 10); $this->Cell(100, 5, utf8_decode('DIRECCIÓN DE ADMINISTRACIÓN'), 0, 0, 'L');
		$this->SetFont('Arial', '', 8);
		$this->SetXY(300, 5); $this->Cell(20, 5, utf8_decode('Fecha: '), 0, 0, 'R');
		$this->Cell(30, 5, date("d-m-Y"), 0, 1, 'L');
		$this->SetXY(300, 10); $this->Cell(20, 5, utf8_decode('Página: '), 0, 0, 'R'); 
		$this->Cell(30, 5, $this->PageNo().' de {nb}', 0, 1, 'L');
		$this->Ln(10);
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(20, 5, utf8_decode('Reporte de Caja Chica'), 0, 0, 'L');
		$this->Ln(5);
		//	imprimo datos generales
		$this->SetFillColor(245, 245, 245);
		$this->SetFont('Arial', 'B', 6); $this->Cell(20, 5, utf8_decode('Caja Chica: '), 0, 0, 'L'); 
		$this->SetFillColor(255, 255, 255);
		$this->SetFont('Arial', '', 6); $this->Cell(20, 5, $nrocajachica, 0, 0, 'L');
		$this->SetFillColor(245, 245, 245);
		$this->SetFont('Arial', 'B', 6); $this->Cell(20, 5, utf8_decode('Clasificación: '), 0, 0, 'L'); 
		$this->SetFillColor(255, 255, 255);
		$this->SetFont('Arial', '', 6); $this->Cell(20, 5, $field_cc['NomClasificacion'], 0, 1, 'L');
		$this->SetFillColor(245, 245, 245);
		$this->SetFont('Arial', 'B', 6); $this->Cell(20, 5, utf8_decode('Empleado: '), 0, 0, 'L'); 
		$this->SetFillColor(255, 255, 255);
		$this->SetFont('Arial', '', 6); $this->Cell(20, 5, $field_cc['NomBeneficiario'], 0, 1, 'L');
		//	imprimo titulos
		if ($Head1) {
			$this->SetFillColor(255,255,255);
			$this->SetDrawColor(0,0,0);
			$this->SetFont('Arial','B',6);
			$this->SetWidths(array(5,20,40,112,40,20,12,12,12,12,15,15,8,12));
			$this->SetAligns(array('C','C','L','L','L','C','R','R','R','R','C','C','C','R'));
			$this->Row(array('#',
							 'Fecha',
							 'Concepto',
							 utf8_decode('Descripción'),
							 'Proveedor',
							 'Documento',
							 'Monto Afecto',
							 'Monto No Afecto',
							 'Monto Impuesto',
							 'Monto Total',
							 'Partida',
							 'Cuenta',
							 'C.C',
							 'Monto Distrib.'));
		}
	}
	//	Pie de página.
	function Footer() {}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada.
$pdf = new PDF('L', 'mm', 'Legal');
$pdf->AliasNbPages();
$pdf->SetMargins(10, 5, 10);
$pdf->SetAutoPageBreak(5, 1);
$Head1 = true;
$pdf->AddPage();
//---------------------------------------------------
$pdf->SetTextColor(0, 0, 0);
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', '', 6);
//	imprimo cuerpo
$sql = "SELECT
			ccd.Secuencia,
			ccd.Descripcion,
			ccd.CodTipoDocumento,
			ccd.NroDocumento,
			ccd.MontoAfecto,
			ccd.MontoNoAfecto,
			ccd.MontoImpuesto,
			ccd.MontoPagado,
			ccd.Fecha,
			ccdi.Linea,
			ccdi.CodPartida,
			ccdi.CodCuenta,
			ccdi.CodCentroCosto,
			ccdi.Monto AS MontoDistribuido,
			cg.Descripcion AS NomConceptoGasto,
			p.NomCompleto AS NomPersona
		FROM
			ap_cajachicadetalle ccd
			INNER JOIN ap_cajachicadistribucion ccdi ON (ccd.FlagCajaChica = ccdi.FlagCajaChica AND
														 ccd.Periodo = ccdi.Periodo AND
														 ccd.NroCajaChica = ccdi.NroCajaChica AND
														 ccd.Secuencia = ccdi.Secuencia)
			INNER JOIN ap_conceptogastos cg ON (ccdi.CodConceptoGasto = cg.CodConceptoGasto)
			INNER JOIN mastpersonas p ON (ccd.CodProveedor = p.CodPersona)
		WHERE
			ccd.FlagCajaChica = '".$flagcajachica."' AND
			ccd.Periodo = '".$periodo."' AND
			ccd.NroCajaChica = '".$nrocajachica."'
		ORDER BY Fecha, Secuencia, Linea";
$query_detalle = mysql_query($sql) or die($sql.mysql_error());
$i = 0;
$total_afecto = 0;
$total_noafecto = 0;
$total_impuesto = 0;
$total_pagado = 0;
$total_distribuido = 0;
while ($field_detalle = mysql_fetch_array($query_detalle)) {$i++;
	$total_afecto += $field_detalle['MontoAfecto'];
	$total_noafecto += $field_detalle['MontoNoAfecto'];
	$total_impuesto += $field_detalle['MontoImpuesto'];
	$total_pagado += $field_detalle['MontoPagado'];
	$total_distribuido += $field_detalle['MontoDistribuido'];
	$pdf->Row(array($i,
					formatFechaDMA($field_detalle['Fecha']),
					utf8_decode($field_detalle['NomConceptoGasto']),
					utf8_decode($field_detalle['Descripcion']),
					utf8_decode($field_detalle['NomPersona']),
					$field_detalle['CodTipoDocumento'].'-'.$field_detalle['NroDocumento'],
					number_format($field_detalle['MontoAfecto'], 2, ',', '.'),
					number_format($field_detalle['MontoNoAfecto'], 2, ',', '.'),
					number_format($field_detalle['MontoImpuesto'], 2, ',', '.'),
					number_format($field_detalle['MontoPagado'], 2, ',', '.'),
					$field_detalle['CodPartida'],
					$field_detalle['CodCuenta'],
					$field_detalle['CodCentroCosto'],
					number_format($field_detalle['MontoDistribuido'], 2, ',', '.')));
}
//---------------------------------------------------
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetFillColor(0, 0, 0);
$y = $pdf->GetY();
$pdf->Rect(10, $y, 195, 0.1, "FD");
$pdf->SetY($y+2);
$pdf->SetDrawColor(255, 255, 255);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 6);
$pdf->Row(array('',
				'',
				'',
				'',
				'',
				'',
				number_format($total_afecto, 2, ',', '.'),
				number_format($total_noafecto, 2, ',', '.'),
				number_format($total_impuesto, 2, ',', '.'),
				number_format(($total_afecto+$total_noafecto+$total_impuesto), 2, ',', '.'),
				'',
				'',
				'',
				number_format($total_distribuido, 2, ',', '.')));
//---------------------------------------------------
$Head1 = false;
$pdf->AddPage();
//	imprimo distribucion
$pdf->SetFillColor(255,255,255);
$pdf->SetDrawColor(0,0,0);
$pdf->SetFont('Arial','B',8);
$pdf->SetWidths(array(25,270,40));
$pdf->SetAligns(array('C','L','R'));
$pdf->Row(array('Partida',
				utf8_decode('Descripción'),
				'Monto'
));

$sql = "(SELECT
			ccd.CodPartida,
			pc.denominacion AS Partida,
			SUM(ccd.Monto) AS Monto
		FROM
			ap_cajachicadistribucion ccd
			INNER JOIN pv_partida pc ON (ccd.CodPartida = pc.cod_partida)
		WHERE
			ccd.FlagCajaChica = '".$flagcajachica."' AND
			ccd.Periodo = '".$periodo."' AND
			ccd.NroCajaChica = '".$nrocajachica."'
		GROUP BY CodPartida)
		UNION
		(SELECT
			cod_partida AS CodPartida,
			denominacion AS Partida,
			'".$field_cc['MontoImpuesto']."' AS Monto
		 FROM pv_partida
		 WHERE cod_partida = '".$_PARAMETRO['IVADEFAULT']."')
		ORDER BY CodPartida";
$query_distribucion = mysql_query($sql) or die ($sql.mysql_error());
$total_partida = 0;
while($fd = mysql_fetch_array($query_distribucion)) {
	$total_partida += $fd['Monto'];
	$pdf->SetFillColor(255,255,255);
	$pdf->SetDrawColor(0,0,0);
	$pdf->SetFont('Arial','',8);
	$pdf->SetWidths(array(25,270,40));
	$pdf->SetAligns(array('C','L','R'));
	$pdf->Row(array($fd['CodPartida'],
					utf8_decode($fd['Partida']),
					number_format($fd['Monto'],2,',','.')
	));
}
$pdf->SetFont('Arial','B',8);
$pdf->Cell(335, 5, number_format($total_partida,2,',','.'), 0, 1, 'R');
//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
