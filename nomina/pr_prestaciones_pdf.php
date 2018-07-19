<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$LF = 0x0A;
$CR = 0x0D;
$nl = sprintf("%c%c",$CR,$LF);
//---------------------------------------------------
list($CodPersona, $Secuencia) = split("[_]", $sel_registros);
$CodTipoProceso = "PRS";
$CodConcepto = "0110";
//	consulto datos
$sql = "SELECT
			le.*,
			p.NomCompleto,
			p.Ndocumento,
			e.CodOrganismo,
			e.CodTipoNom,
			e.FechaFinNomina,
			e.Fegreso,
			pt.DescripCargo,
			o.Organismo,
			o.RepresentLegal,
			mc.MotivoCese
		FROM
			pr_liquidacionempleado le
			INNER JOIN mastpersonas p ON (p.CodPersona = le.CodPersona)
			INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
			INNER JOIN rh_puestos pt ON (pt.CodCargo = le.CodCargo)
			INNER JOIN mastorganismos o ON (o.CodOrganismo = le.CodOrganismo)
			LEFT JOIN rh_motivocese mc ON (mc.CodMotivoCes = le.CodMotivoCes)
		WHERE
			le.CodPersona = '".$CodPersona."' AND
			le.Secuencia = '".$Secuencia."'";
$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
##
$Ciudad = getVar2("mastciudades", "Ciudad", array("CodCiudad"), array($_PARAMETRO['CIUDADDEFAULT']));
$ProcesadoPor = getVar2("pr_procesoperiodo", "ProcesadoPor", array("CodOrganismo","CodTipoNom","Periodo","CodTipoProceso"), array($field['CodOrganismo'],$field['CodTipoNom'],$field['Periodo'],$field['CodTipoProceso']));
$AprobadoPor = getVar2("pr_procesoperiodo", "AprobadoPor", array("CodOrganismo","CodTipoNom","Periodo","CodTipoProceso"), array($field['CodOrganismo'],$field['CodTipoNom'],$field['Periodo'],$field['CodTipoProceso']));
list($Firma1Nombre, $Firma1Cargo, $Firma1Nivel) = getFirma($ProcesadoPor);
list($Firma2Nombre, $Firma2Cargo, $Firma2Nivel) = getFirma($AprobadoPor);
list($Firma3Nombre, $Firma3Cargo, $Firma3Nivel) = getFirma($field['CodPersona']);
##
$sql = "SELECT
			Sueldo,
			SueldoNormal,
			SueldoIntegral,
			ROUND((Sueldo/30),2) AS SueldoDiario,
			ROUND((SueldoNormal/30),2) AS SueldoNormalDiario,
			ROUND((SueldoIntegral/30),2) AS SueldoIntegralDiario,
			ROUND((SueldoIntegralParcial/30),2) AS SueldoIntegralParcialDiario
		FROM rh_sueldos
		WHERE
			CodPersona = '".$CodPersona."' AND
			Periodo < '".substr($field['Fegreso'], 0, 7)."'
		ORDER BY Periodo DESC
		LIMIT 0, 1";
$query_sn = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
if (mysql_num_rows($query_sn) != 0) $field_sn = mysql_fetch_array($query_sn);
//---------------------------------------------------

class PDF extends FPDF {
	//	Cabecera de página.
	function Header() {
		global $_PARAMETRO;
		global $Ahora;
		global $field;
		global $field_sn;
		global $_POST;
		global $_GET;
		global $SubTitulo;
		extract($_POST);
		extract($_GET);
		##
		$Logo = getValorCampo("mastorganismos", "CodOrganismo", "Logo", $field['CodOrganismo']);
		$NomOrganismo = getValorCampo("mastorganismos", "CodOrganismo", "Organismo", $field['CodOrganismo']);
		$NomDependencia = getValorCampo("mastdependencias", "CodDependencia", "Dependencia", $_PARAMETRO["DEPRHPR"]);
		##
		$this->SetFillColor(255, 255, 255);
		$this->SetDrawColor(0, 0, 0);
		$this->Image($_PARAMETRO["PATHLOGO"].$Logo, 10, 5, 10, 10);
		$this->SetFont('Arial', '', 8);
		$this->SetXY(20, 5); $this->Cell(100, 5, $NomOrganismo, 0, 1, 'L');
		$this->SetXY(20, 10); $this->Cell(100, 5, $NomDependencia, 0, 0, 'L');
		$this->SetFont('Arial', '', 8);
		$this->SetXY(180, 5); $this->Cell(10, 5, utf8_decode('Fecha: '), 0, 0, 'L');
		$this->Cell(30, 5, formatFechaDMA(substr($Ahora, 0, 10)), 0, 1, 'L');
		$this->SetXY(180, 10); $this->Cell(10, 5, utf8_decode('Página: '), 0, 0, 'L');
		$this->Cell(30, 5, $this->PageNo().' de {nb}', 0, 1, 'L');
		$this->SetFont('Arial', 'B', 10);
		$this->SetXY(10, 20); 
		if (!$SubTitulo) $this->Cell(200, 5, utf8_decode('LIQUIDACIÓN DE PRESTACIONES SOCIALES'), 0, 1, 'C', 0);
		else $this->Cell(200, 5, utf8_decode('INTERESES DE MORA'), 0, 1, 'C', 0);
		$this->Ln(5);
		##
		$this->SetFillColor(240, 240, 240);
		$this->SetFont('Arial', 'B', 8);
		$this->Cell(38, 5, utf8_decode('NOMBRES Y APELLIDOS:'), 0, 0, 'L', 1);
		$this->SetFont('Arial', '', 8);
		$this->Cell(38, 5, utf8_decode($field['NomCompleto']), 0, 1, 'L');
		$this->Ln(1);
		$this->SetFont('Arial', 'B', 8);
		$this->Cell(38, 5, utf8_decode('CARGO:'), 0, 0, 'L', 1);
		$this->SetFont('Arial', '', 8);
		$this->Cell(38, 5, utf8_decode($field['DescripCargo']), 0, 1, 'L');
		$this->Ln(1);
		$this->SetFont('Arial', 'B', 8);
		$this->Cell(38, 5, utf8_decode('FECHA DE INGRESO:'), 0, 0, 'L', 1);
		$this->SetFont('Arial', '', 8);
		$this->Cell(38, 5, formatFechaDMA($field['Fingreso']), 0, 1, 'L');
		$this->Ln(1);
		$this->SetFont('Arial', 'B', 8);
		$this->Cell(38, 5, utf8_decode('FECHA DE CESE:'), 0, 0, 'L', 1);
		$this->SetFont('Arial', '', 8);
		$this->Cell(38, 5, formatFechaDMA($field['FechaFinNomina']), 0, 1, 'L');
		$this->Ln(1);
		$this->SetFont('Arial', 'B', 8);
		$this->Cell(38, 5, utf8_decode('TIEMPO DE SERVICIO:'), 0, 0, 'L', 1);
		$this->SetFont('Arial', '', 8);
		list($Anios, $Meses, $Dias) = getEdad(formatFechaDMA($field['Fingreso']), formatFechaDMA($field['Fegreso']));
		$this->Cell(38, 5, utf8_decode("$Anios años; $Meses meses; $Dias dias"), 0, 1, 'L');
		$this->Ln(1);
		$this->SetFont('Arial', 'B', 8);
		$this->Cell(38, 5, utf8_decode('MOTIVO:'), 0, 0, 'L', 1);
		$this->SetFont('Arial', '', 8);
		$this->Cell(38, 5, utf8_decode($field['MotivoCese']), 0, 1, 'L');
		$this->Ln(1);
		$this->SetFont('Arial', 'B', 8);
		$this->Cell(38, 5, utf8_decode('SUELDO NORMAL:'), 0, 0, 'L', 1);
		$this->SetFont('Arial', '', 8);
		$this->Cell(38, 5, number_format($field_sn['SueldoNormal'], 2, ',', '.'), 0, 1, 'L');
		$this->Ln(6);
	}
	
	//	Pie de página.
	function Footer() {}
}
//---------------------------------------------------

//---------------------------------------------------
$SubTitulo = FALSE;
//	Creación del objeto de la clase heredada.
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(5, 5);
$pdf->AddPage();
//---------------------------------------------------
//	ingresos
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(130, 5, 'INGRESOS', 0, 0, 'L');
$pdf->Cell(20, 5, 'CANT.', 0, 0, 'R');
$pdf->Cell(20, 5, 'SUELDO.', 0, 0, 'R');
$pdf->Cell(30, 5, 'TOTAL', 0, 1, 'R');
$y = $pdf->GetY();
$pdf->SetDrawColor(0, 0, 0);
$pdf->Line(10, $y, 210, $y);
$pdf->Ln(3);
##	consulto
$sql = "SELECT
			tnec.CodConcepto,
			tnec.Monto,
			tnec.Cantidad,
			c.Descripcion
		FROM
			pr_tiponominaempleadoconcepto tnec
			INNER JOIN pr_liquidacionempleado le ON (le.CodTipoNom = tnec.CodTipoNom AND
													 le.Periodo = tnec.Periodo AND
													 le.CodPersona = tnec.CodPersona AND
													 le.CodOrganismo = tnec.CodOrganismo AND
													 le.CodTipoProceso = tnec.CodTipoProceso)
			INNER JOIN pr_concepto c ON (c.CodConcepto = tnec.CodConcepto)
		WHERE
			le.CodPersona = '".$CodPersona."' AND
			le.Secuencia = '".$Secuencia."' AND
			c.Tipo = 'I'
		ORDER BY CodConcepto";
$query_conceptos = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
while ($field_conceptos = mysql_fetch_array($query_conceptos)) {
	if ($field_conceptos['Cantidad'] > 0 && $field_conceptos['CodConcepto'] != "0106" && $field_conceptos['CodConcepto'] != "0109") $Cantidad = number_format($field_conceptos['Cantidad'], 2, ',', '.'); else $Cantidad = "";
	if ($field_conceptos['CodConcepto'] == "0100" || $field_conceptos['CodConcepto'] == "0101" || $field_conceptos['CodConcepto'] == "0102" || $field_conceptos['CodConcepto'] == "0103") $MontoDiario = number_format($field_sn['SueldoNormalDiario'], 2, ',', '.');
	elseif ($field_conceptos['CodConcepto'] == "0104" || $field_conceptos['CodConcepto'] == "0105") $MontoDiario = number_format($field_sn['SueldoIntegralParcialDiario'], 2, ',', '.');
	else $MontoDiario = "";
	##	
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetDrawColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 8);
	$pdf->SetWidths(array(130, 20, 20, 30));
	$pdf->SetAligns(array('L', 'R', 'R', 'R'));
	$pdf->Row(array(utf8_decode($field_conceptos['Descripcion']),
					$Cantidad,
					$MontoDiario,
					number_format($field_conceptos['Monto'], 2, ',', '.')));
	$pdf->Ln(1);
}
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(160, 5, 'TOTAL INGRESOS: ', 0, 0, 'R');
$pdf->Cell(10, 5); $pdf->Cell(30, 5, number_format($field['TotalIngresos'], 2, ',', '.'), 0, 1, 'R', 1);
$pdf->Ln(5);
//	descuentos
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(50, 5, 'DESCUENTOS', 0, 1, 'L');
$y = $pdf->GetY();
$pdf->SetDrawColor(0, 0, 0);
$pdf->Line(10, $y, 210, $y);
$pdf->Ln(3);
##	consulto
$sql = "(SELECT
			tnec.CodConcepto,
			tnec.Monto,
			tnec.Cantidad,
			c.Descripcion
		 FROM
			pr_tiponominaempleadoconcepto tnec
			INNER JOIN pr_liquidacionempleado le ON (le.CodTipoNom = tnec.CodTipoNom AND
													 le.Periodo = tnec.Periodo AND
													 le.CodPersona = tnec.CodPersona AND
													 le.CodOrganismo = tnec.CodOrganismo AND
													 le.CodTipoProceso = tnec.CodTipoProceso)
			INNER JOIN pr_concepto c ON (c.CodConcepto = tnec.CodConcepto)
		 WHERE
			le.CodPersona = '".$CodPersona."' AND
			le.Secuencia = '".$Secuencia."' AND
			c.Tipo = 'D')
		/*UNION
		(SELECT
			tnec.CodConcepto,
			tnec.Monto,
			tnec.Cantidad,
			CONCAT(c.Descripcion, ' (', tnec.Periodo, ')') AS descripcion
		 FROM
			pr_tiponominaempleadoconcepto tnec
			INNER JOIN pr_tiponominaempleado tne ON (tne.CodTipoNom = tnec.CodTipoNom AND
													 tne.Periodo = tnec.Periodo AND
													 tne.CodPersona = tnec.CodPersona AND
													 tne.CodOrganismo = tnec.CodOrganismo AND
													 tne.CodTipoProceso = tnec.CodTipoProceso)
			INNER JOIN pr_concepto c ON (c.CodConcepto = tnec.CodConcepto)
		 WHERE
		 	tne.CodPersona = '".$CodPersona."' AND
		 	tne.CodTipoProceso = 'APR' AND
			tne.EstadoPago = 'PA')*/
		ORDER BY CodConcepto";
$query_conceptos = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
while ($field_conceptos = mysql_fetch_array($query_conceptos)) {
	if ($field_conceptos['Cantidad'] > 0) $Cantidad = number_format($field_conceptos['Cantidad'], 2, ',', '.'); else $Cantidad = "";
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetDrawColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 8);
	$pdf->SetWidths(array(130, 25, 15, 30));
	$pdf->SetAligns(array('L', 'R', 'L', 'R'));
	$pdf->Row(array(utf8_decode($field_conceptos['Descripcion']),
					$Cantidad,
					'',
					number_format($field_conceptos['Monto'], 2, ',', '.')));
	$pdf->Ln(1);
}
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(160, 5, 'TOTAL DESCUENTOS: ', 0, 0, 'R');
$pdf->Cell(10, 5); $pdf->Cell(30, 5, number_format(($field['TotalEgresos']+$field['TotalDescuento']), 2, ',', '.'), 0, 1, 'R', 1);
$pdf->Ln(5);
//	neto
$pdf->SetFillColor(220, 220, 220);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(160, 5, 'TOTAL NETO: ', 0, 0, 'R');
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(10, 5); $pdf->Cell(30, 5, number_format($field['TotalNeto'], 2, ',', '.'), 0, 1, 'R', 1);
$pdf->Ln(1);
//	intereses
$pdf->SetFillColor(220, 220, 220);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(160, 5, 'MONTO INTERESES: ', 0, 0, 'R');
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(10, 5); $pdf->Cell(30, 5, number_format($field['MontoIntereses'], 2, ',', '.'), 0, 1, 'R', 1);
$pdf->Ln(1);
//	intereses
$pdf->SetFillColor(220, 220, 220);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(160, 5, 'TOTAL PRESTACIONES: ', 0, 0, 'R');
$pdf->Cell(10, 5); $pdf->Cell(30, 5, number_format($field['TotalPrestaciones'], 2, ',', '.'), 0, 1, 'R', 1);
$pdf->Ln(5);
//	firma
$TotalIngresosLetras = strtoupper(convertir_a_letras($field['TotalPrestaciones'], "moneda"));
$TotalIngresos = number_format($field['TotalPrestaciones'], 2, ',', '.');
$Titulo = strtoupper("$Ciudad, $DiaActual de ".getNombreMes("$AnioActual-$MesActual")." del $AnioActual.");
##
$Texto = utf8_decode("RECIBO DE <strong>$field[Organismo]</strong> LA CANTIDAD DE <strong>Bs. $TotalIngresos ($TotalIngresosLetras)</strong>, CORRESPONDIENTES A MI LIQUIDACIÓN DE PRESTACIONES SOCIALES EN LA CUAL DOY POR CANCELADA TODA OBLIGACIÓN DE <strong>$field[Organismo]</strong> POR LOS DERECHOS QUE ME CONCEDE LA LOTTT en los articulos 141, 142 y 128.");
$pdf->SetFont('Arial', '', 8);
$pdf->WriteHTML($Texto);
$pdf->Ln(10);
##
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(160, 5, utf8_decode($Titulo), 0, 0, 'L');
$pdf->Ln(25);
##
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(90, 5, utf8_decode($Firma1Nivel.' '.$Firma1Nombre), 0, 0, 'C');
$pdf->Cell(20, 5);
$pdf->Cell(90, 5, utf8_decode($Firma2Nivel.' '.$Firma2Nombre), 0, 0, 'C');
$pdf->Ln(5);
$pdf->Cell(90, 5, utf8_decode($Firma1Cargo), 0, 0, 'C');
$pdf->Cell(20, 5);
$pdf->Cell(90, 5, utf8_decode($Firma2Cargo), 0, 0, 'C');
$pdf->Ln(20);
$pdf->Cell(55, 5);
$pdf->Cell(90, 5, utf8_decode($Firma3Nivel.' '.$Firma3Nombre), 0, 0, 'C');
$pdf->Ln(5);
$pdf->Cell(55, 5);
$pdf->Cell(90, 5, number_format($field['Ndocumento'], 0, '', '.'), 0, 0, 'C');
//	INTERESES
$SubTitulo = TRUE;
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(20, 5);
$pdf->Cell(25, 5, 'PERIODO', 0, 0, 'C');
$pdf->Cell(45, 5, 'MONTO BASE', 0, 0, 'R');
$pdf->Cell(45, 5, 'TASA (%)', 0, 0, 'R');
$pdf->Cell(45, 5, 'MONTO', 0, 1, 'R');
$y = $pdf->GetY();
$pdf->SetDrawColor(0, 0, 0);
$pdf->Line(30, $y, 200, $y);
$pdf->Ln(3);
##	consulto
$sql = "SELECT *
		FROM pr_liquidacionempleadointereses
		WHERE
			CodPersona = '".$CodPersona."' AND 
			Secuencia = '".$Secuencia."'
		ORDER BY Periodo DESC";
$field_periodos = getRecords($sql);
foreach($field_periodos as $f) {
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetDrawColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 8);
	$pdf->SetWidths(array(25,45,45,45));
	$pdf->SetAligns(array('C','R','R','R'));
	$pdf->Cell(20, 5);
	$pdf->Row(array($f['Periodo'],
					number_format($f['MontoBase'], 2, ',', '.'),
					number_format($f['Porcentaje'], 2, ',', '.'),
					number_format($f['Monto'], 2, ',', '.')));
	$pdf->Ln(1);
}
$MontoTotal = $field['MontoIntereses'] + $field['TotalNeto'];
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(120, 5, 'TOTAL INTERESES: ', 0, 0, 'R');
$pdf->Cell(10, 5); $pdf->Cell(50, 5, number_format($field['MontoIntereses'], 2, ',', '.'), 0, 1, 'R', 1);
$pdf->Ln(2);
$pdf->Cell(120, 5, 'PRESTACIONES: ', 0, 0, 'R');
$pdf->Cell(10, 5); $pdf->Cell(50, 5, number_format($field['TotalNeto'], 2, ',', '.'), 0, 1, 'R', 1);
$pdf->Ln(2);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(120, 5, 'MONTO TOTAL: ', 0, 0, 'R');
$pdf->Cell(10, 5); $pdf->Cell(50, 5, number_format($MontoTotal, 2, ',', '.'), 0, 1, 'R', 1);
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  