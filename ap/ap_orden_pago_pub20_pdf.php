<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$_HOJA = "ORDEN DE PAGO";
$h = 4;
$y = 30;
//---------------------------------------------------
list($Anio, $CodOrganismo, $NroOrden) = split("[_]", $registro);
//	obtengo la informacion general de la orden de pago
$sql = "SELECT
			op.*,
			o.MontoAfecto,
			o.MontoNoAfecto,
			(o.MontoAfecto + o.MontoNoAfecto) AS MontoBruto,
			o.MontoImpuesto,
			o.MontoImpuestoOtros,
			o.MontoObligacion,
			o.MontoPagoParcial,
			o.MontoAdelanto,
			o.FechaPreparacion,
			o.IngresadoPor,
			mp.DocFiscal,
			b.Banco,
			cb.CtaBanco
		FROM
			ap_ordenpago op
			INNER JOIN ap_obligaciones o ON (op.CodProveedor = o.CodProveedor AND
											 op.CodTipoDocumento = o.CodTipoDocumento AND
											 op.NroDocumento = o.NroDocumento)
			INNER JOIN mastpersonas mp ON (op.CodProveedorPagar = mp.CodPersona)
			LEFT JOIN ap_ctabancaria cb ON (op.NroCuenta = cb.NroCuenta)
			LEFT JOIN mastbancos b ON (cb.CodBanco = b.CodBanco)
		WHERE
			op.Anio = '".$Anio."' AND
			op.CodOrganismo = '".$CodOrganismo."' AND
			op.NroOrden = '".$NroOrden."'";
$query_orden = mysql_query($sql) or die($sql.mysql_error());
if (mysql_num_rows($query_orden) != 0) $field_orden = mysql_fetch_array($query_orden);
$field_orden['MontoTotal'] = round($field_orden['MontoTotal'],2);
//---------------------------------------------------
//	obtengo las retenciones
$sql = "(SELECT
			oi.CodImpuesto AS Codigo,
			oi.FactorPorcentaje,
			oi.MontoImpuesto,
			oi.Linea,
			oi.FlagProvision,
			i.Descripcion
		 FROM
			ap_ordenpago op
			INNER JOIN ap_obligacionesimpuesto oi ON (op.CodProveedor = oi.CodProveedor AND
													  op.CodTipoDocumento = oi.CodTipoDocumento AND
													  op.NroDocumento = oi.NroDocumento)
			INNER JOIN mastimpuestos i ON (oi.CodImpuesto = i.CodImpuesto)
		 WHERE
			op.Anio = '".$Anio."' AND
			op.CodOrganismo = '".$CodOrganismo."' AND
			op.NroOrden = '".$NroOrden."')
		UNION
		(SELECT
			oi.CodConcepto AS Codigo,
			oi.FactorPorcentaje,
			oi.MontoImpuesto,
			oi.Linea,
			oi.FlagProvision,
			c.Descripcion
		 FROM
			ap_ordenpago op
			INNER JOIN ap_obligacionesimpuesto oi ON (op.CodProveedor = oi.CodProveedor AND
													  op.CodTipoDocumento = oi.CodTipoDocumento AND
													  op.NroDocumento = oi.NroDocumento)
			INNER JOIN pr_concepto c ON (oi.CodConcepto = c.CodConcepto)
		 WHERE
			op.Anio = '".$Anio."' AND
			op.CodOrganismo = '".$CodOrganismo."' AND
			op.NroOrden = '".$NroOrden."')
		ORDER BY Linea";
$query_retencion = mysql_query($sql) or die($sql.mysql_error());
$rows_retencion = mysql_num_rows($query_retencion);
//---------------------------------------------------
//	obtengo la distribucion presupuestaria
$sql = "SELECT
			do.cod_partida,
			do.Monto,
				do.CodFuente,
				pv.CategoriaProg,
				CONCAT(ss.CodSector, pr.CodPrograma, a.CodActividad) AS CatProg,
			p.denominacion
		FROM
			ap_ordenpagodistribucion do
			INNER JOIN pv_partida p ON (do.cod_partida = p.cod_partida)
				LEFT JOIN pv_presupuesto pv ON (pv.CodOrganismo = do.CodOrganismo AND pv.CodPresupuesto = do.CodPresupuesto)
				LEFT JOIN pv_categoriaprog cp ON (cp.CategoriaProg = pv.CategoriaProg)
				LEFT JOIN pv_actividades a ON (a.IdActividad = cp.IdActividad)
				LEFT JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
				LEFT JOIN pv_subprogramas spr On (spr.IdSubPrograma = py.IdSubPrograma)
				LEFT JOIN pv_programas pr ON (pr.IdPrograma = spr.IdPrograma)
				LEFT JOIN pv_subsector ss On (ss.IdSubSector = pr.IdSubSector)
		WHERE
			do.Anio = '".$Anio."' AND
			do.CodOrganismo = '".$CodOrganismo."' AND
			do.NroOrden = '".$NroOrden."'
		ORDER BY cod_partida";
$query_presupuesto = mysql_query($sql) or die($sql.mysql_error());
$rows_presupuesto = mysql_num_rows($query_presupuesto);
//---------------------------------------------------
//	obtengo la distribucion financiera
$sql = "SELECT
			vd.CodCuenta,
			vd.MontoVoucher AS Monto,
			pc.Descripcion,
			pc.TipoSaldo
		FROM
			ap_obligaciones o
			INNER JOIN ac_voucherdet vd ON (vd.CodOrganismo = o.CodOrganismo AND
											vd.Periodo = o.VoucherPeriodoPub20 AND
											vd.Voucher = o.VoucherPub20)
			INNER JOIN ac_mastplancuenta20 pc ON (pc.CodCuenta = vd.CodCuenta)
		WHERE
			o.CodProveedor = '".$field_orden['CodProveedor']."' AND
			o.CodTipoDocumento = '".$field_orden['CodTipoDocumento']."' AND
			o.NroDocumento = '".$field_orden['NroDocumento']."' AND
			vd.CodContabilidad = 'F'
		ORDER BY Monto, CodCuenta";
$query_cuenta = mysql_query($sql) or die($sql.mysql_error());
$rows_cuenta = mysql_num_rows($query_cuenta);
//---------------------------------------------------
//	obtengo la distribucion financiera
$sql = "SELECT
			opc.CodCuenta,
			opc.Monto,
			pc.Descripcion,
			pc.TipoSaldo
		FROM
			ap_ordenpagocontabilidad opc
			INNER JOIN ac_mastplancuenta20 pc ON (pc.CodCuenta = opc.CodCuenta)
		WHERE
			opc.Anio = '".$Anio."' AND
			opc.CodOrganismo = '".$CodOrganismo."' AND
			opc.NroOrden = '".$NroOrden."' AND
			opc.CodContabilidad = 'F'
		ORDER BY Monto, CodCuenta";
$query_cuenta2 = mysql_query($sql) or die($sql.mysql_error());
$rows_cuenta2 = mysql_num_rows($query_cuenta2);
//---------------------------------------------------
//	obtengo las firmas
list($_PREPARADO['Nombre'], $_PREPARADO['Cargo'], $_PREPARADO['Nivel']) = getFirma($field_orden['IngresadoPor']);
list($_REVISADO['Nombre'], $_REVISADO['Cargo'], $_REVISADO['Nivel']) = getFirma($field_orden['RevisadoPor']);
list($_APROBADO['Nombre'], $_APROBADO['Cargo'], $_APROBADO['Nivel']) = getFirma($field_orden['RevisadoPor']);
list($_PAGUESE['Nombre'], $_PAGUESE['Cargo'], $_PAGUESE['Nivel']) = getFirma($field_orden['AprobadoPor']);
$_PREPARADO['Fecha'] = $field_orden['FechaPreparacion'];
$_REVISADO['Fecha'] = $field_orden['FechaRevisado'];
$_APROBADO['Fecha'] = $field_orden['FechaOrdenPago'];
$_PAGUESE['Fecha'] = $field_orden['FechaOrdenPago'];
//---------------------------------------------------

//---------------------------------------------------
function hoja_retencion($pdf, $query_retencion, $ln, $imprimir) {
	global $h;
	global $y;
	$Retenciones = 0;
	$Amortizaciones = 0;
	$pdf->SetXY(10, $y);
	$pdf->SetFont('Arial', 'B', 9);
	$pdf->Cell(195, $h, 'RETENCIONES', 1, 0, 'C');
	$y+=$h;
	$pdf->SetXY(10, $y);
	$pdf->SetFont('Arial', 'B', 9);
	$pdf->Cell(15, $h, 'Cod.', 1, 0, 'C');
	$pdf->Cell(105, $h, utf8_decode('Denominación'), 1, 0, 'L');
	$pdf->Cell(30, $h, 'Partida', 1, 0, 'C');
	$pdf->Cell(15, $h, 'Tasa(%)', 1, 0, 'R');
	$pdf->Cell(30, $h, 'Monto', 1, 0, 'R');
	$y+=$h;
	$pdf->Rect(10, $y, 195, $h*$ln, "D");
	if ($imprimir) {
		while($field_retencion = mysql_fetch_array($query_retencion)) {
			$pdf->SetXY(10, $y);
			$pdf->SetFont('Arial', '', 9);
			$pdf->Cell(15, $h, $field_retencion['Codigo'], 0, 0, 'C');
			$pdf->Cell(105, $h, strtoupper(substr(utf8_decode($field_retencion['Descripcion']), 0, 60)), 0, 0, 'L');
			$pdf->Cell(30, $h, $field_retencion['PartidaPresupuestal'], 0, 0, 'C');
			$pdf->Cell(15, $h, number_format($field_retencion['FactorPorcentaje'], 2, ',', '.'), 0, 0, 'R');
			$pdf->Cell(30, $h, number_format($field_retencion['MontoImpuesto'], 2, ',', '.'), 0, 0, 'R');
			$y+=$h;
			$TotalMontoImpuesto += $field_retencion['MontoImpuesto'];
			if ($field_retencion['FlagProvision'] == 'A') $Amortizaciones += $field_retencion['MontoImpuesto'];
			else $Retenciones += $field_retencion['MontoImpuesto'];
		}
		$pdf->SetXY(10, $y+1);
		$pdf->SetFont('Arial', 'B', 9);
		$pdf->Cell(195, $h, number_format($TotalMontoImpuesto, 2, ',', '.'), 0, 0, 'R');
		$y+=$h;
	} else {
		$y+=($h*2);
		$pdf->SetXY(10, $y);
		$pdf->SetFont('Arial', 'B', 9);
		$pdf->Cell(195, $h, 'VER RELACION ANEXA', 0, 0, 'C');
	}
	return [$Retenciones, $Amortizaciones];
}

function hoja_presupuesto($pdf, $query_presupuesto, $ln, $imprimir) {
	global $h;
	global $y;	
	$pdf->SetXY(10, $y);
	$pdf->SetFont('Arial', 'B', 9);
	$pdf->Cell(195, $h, 'CONTABILIDAD PRESUPUESTARIA', 1, 0, 'C');
	$y+=$h;	
	$pdf->SetXY(10, $y);
	$pdf->SetFont('Arial', 'B', 9);
	$pdf->Cell(20, $h, 'Cat. Prog.', 1, 0, 'C');
	$pdf->Cell(10, $h, 'F.F.', 1, 0, 'C');
	$pdf->Cell(20, $h, 'Partida', 1, 0, 'C');
	$pdf->Cell(115, $h, utf8_decode('Denominación'), 1, 0, 'L');
	$pdf->Cell(30, $h, 'Monto', 1, 0, 'R');
	$y+=$h;
	$pdf->Rect(10, $y, 195, $h*$ln, "D");		
	if ($imprimir) {
		while($field_presupuesto = mysql_fetch_array($query_presupuesto)) {
			$total_monto += $field_presupuesto['Monto'];
			$pdf->SetXY(10, $y);
			$pdf->SetFont('Arial', '', 9);
			$pdf->Cell(20, $h, $field_presupuesto['CatProg'], 0, 0, 'C');
			$pdf->Cell(10, $h, $field_presupuesto['CodFuente'], 0, 0, 'C');
			$pdf->Cell(20, $h, $field_presupuesto['cod_partida'], 0, 0, 'C');
			$pdf->Cell(115, $h, strtoupper(substr(utf8_decode($field_presupuesto['denominacion']), 0, 60)), 0, 0, 'L');
			$pdf->Cell(30, $h, number_format($field_presupuesto['Monto'], 2, ',', '.'), 0, 0, 'R');
			$y+=$h;
		}
		$pdf->SetXY(10, $y+1);
		$pdf->SetFont('Arial', 'B', 9);
		$pdf->Cell(30, $h, '', 0, 0, 'C');
		$pdf->Cell(135, $h, '', 0, 0, 'L');
		$pdf->Cell(30, $h, number_format($total_monto, 2, ',', '.'), 0, 0, 'R');
		$y+=$h;
	} else {
		$y+=($h*3);
		$pdf->SetXY(10, $y);
		$pdf->SetFont('Arial', 'B', 9);
		$pdf->Cell(195, $h, 'VER RELACION ANEXA', 0, 0, 'C');
	}
}

function hoja_cuenta($pdf, $query_cuenta, $ln, $imprimir) {
	global $h;
	global $y;	
	$pdf->SetXY(10, $y);
	$pdf->SetFont('Arial', 'B', 9);
	$pdf->Cell(195, $h, 'CONTABILIDAD FINANCIERA', 1, 0, 'C');
	$y+=$h;	
	$pdf->SetXY(10, $y);
	$pdf->SetXY(10, $y);
	$pdf->SetFont('Arial', 'B', 9);
	$pdf->Cell(25, $h, 'Cuenta', 1, 0, 'C');
	$pdf->Cell(110, $h, utf8_decode('Denominación'), 1, 0, 'L');
	$pdf->Cell(30, $h, 'Debe', 1, 0, 'R');
	$pdf->Cell(30, $h, 'Haber', 1, 0, 'R');
	$y+=$h;
	$pdf->Rect(10, $y, 195, $h*$ln, "D");		
	if ($imprimir) {
		while($field_cuenta = mysql_fetch_array($query_cuenta)) {
			if ($field_cuenta['Monto'] >= 0) { $debe = $field_cuenta['Monto']; $haber = 0.00; }
			else  { $haber = $field_cuenta['Monto']; $debe = 0.00; }
			$total_debe += $debe;
			$total_haber += $haber;
			$pdf->SetXY(10, $y);
			$pdf->SetFont('Arial', '', 9);
			$pdf->Cell(20, $h, $field_cuenta['CodCuenta'], 0, 0, 'C');
			$pdf->Cell(115, $h, strtoupper(substr(utf8_decode($field_cuenta['Descripcion']), 0, 57)), 0, 0, 'L');
			$pdf->Cell(30, $h, number_format($debe, 2, ',', '.'), 0, 0, 'R');
			$pdf->Cell(30, $h, number_format($haber, 2, ',', '.'), 0, 0, 'R');
			$y+=$h;
		}
		$pdf->SetXY(10, $y+1);
		$pdf->SetFont('Arial', 'B', 9);
		$pdf->Cell(30, $h, '', 0, 0, 'C');
		$pdf->Cell(105, $h, '', 0, 0, 'L');
		$pdf->Cell(30, $h, number_format($total_debe, 2, ',', '.'), 0, 0, 'R');
		$pdf->Cell(30, $h, number_format($total_haber, 2, ',', '.'), 0, 0, 'R');
		$y+=$h;
	} else {
		$y+=($h*3);
		$pdf->SetXY(10, $y);
		$pdf->SetFont('Arial', 'B', 9);
		$pdf->Cell(195, $h, 'VER RELACION ANEXA', 0, 0, 'C');
	}
}

function hoja_cuenta2($pdf, $query_cuenta, $ln, $imprimir) {
	global $h;
	global $y;	
	$pdf->SetXY(10, $y);
	$pdf->SetFont('Arial', 'B', 9);
	$pdf->Cell(195, $h, 'CONTABILIDAD FINANCIERA (ORDEN DE PAGO)', 1, 0, 'C');
	$y+=$h;	
	$pdf->SetXY(10, $y);
	$pdf->SetXY(10, $y);
	$pdf->SetFont('Arial', 'B', 9);
	$pdf->Cell(25, $h, 'Cuenta', 1, 0, 'C');
	$pdf->Cell(110, $h, utf8_decode('Denominación'), 1, 0, 'L');
	$pdf->Cell(30, $h, 'Debe', 1, 0, 'R');
	$pdf->Cell(30, $h, 'Haber', 1, 0, 'R');
	$y+=$h;
	$pdf->Rect(10, $y, 195, $h*$ln, "D");		
	if ($imprimir) {
		while($field_cuenta = mysql_fetch_array($query_cuenta)) {
			if ($field_cuenta['Monto'] >= 0) { $debe = $field_cuenta['Monto']; $haber = 0.00; }
			else  { $haber = $field_cuenta['Monto']; $debe = 0.00; }
			
			$total_debe += $debe;
			$total_haber += $haber;
			
			$pdf->SetXY(10, $y);
			$pdf->SetFont('Arial', '', 9);
			$pdf->Cell(20, $h, $field_cuenta['CodCuenta'], 0, 0, 'C');
			$pdf->Cell(115, $h, strtoupper(substr(utf8_decode($field_cuenta['Descripcion']), 0, 57)), 0, 0, 'L');
			$pdf->Cell(30, $h, number_format($debe, 2, ',', '.'), 0, 0, 'R');
			$pdf->Cell(30, $h, number_format($haber, 2, ',', '.'), 0, 0, 'R');
			$y+=$h;
		}
		$pdf->SetXY(10, $y+1);
		$pdf->SetFont('Arial', 'B', 9);
		$pdf->Cell(30, $h, '', 0, 0, 'C');
		$pdf->Cell(105, $h, '', 0, 0, 'L');
		$pdf->Cell(30, $h, number_format($total_debe, 2, ',', '.'), 0, 0, 'R');
		$pdf->Cell(30, $h, number_format($total_haber, 2, ',', '.'), 0, 0, 'R');
		$y+=$h;
	} else {
		$y+=($h*3);
		$pdf->SetXY(10, $y);
		$pdf->SetFont('Arial', 'B', 9);
		$pdf->Cell(195, $h, 'VER RELACION ANEXA', 0, 0, 'C');
	}
}

class PDF extends FPDF {
	//	Cabecera de página.
	function Header() {
		global $_PARAMETRO;
		global $field_orden;
		global $_HOJA;
		##
		$Logo = getValorCampo("mastorganismos", "CodOrganismo", "Logo", $_SESSION['ORGANISMO_ACTUAL']);
		$NomOrganismo = getValorCampo("mastorganismos", "CodOrganismo", "Organismo", $_SESSION['ORGANISMO_ACTUAL']);
		$NomDependencia = getValorCampo("mastdependencias", "CodDependencia", "Dependencia", $_SESSION['DEPLOGCXP']);
		##	
		$this->Image($_PARAMETRO["PATHLOGO"].'logo-alcaldia.jpg', 10, 5, 10, 10);		
		$this->SetFont('Arial', '', 10);
		$this->SetXY(20, 5); $this->Cell(100, 5, utf8_decode($NomOrganismo), 0, 1, 'L');
		$this->SetXY(20, 10); $this->Cell(100, 5, utf8_decode($NomDependencia), 0, 0, 'L');
		$this->SetFont('Arial', 'B', 10);
		$this->SetXY(165, 5); $this->Cell(20, 5, utf8_decode('Nro. Orden: '), 0, 0, 'L'); 
		$this->Cell(30, 5, $field_orden['NroOrden'], 0, 1, 'L');		
		$this->SetFont('Arial', '', 10);
		$this->SetXY(165, 10); $this->Cell(20, 5, utf8_decode('Fecha: '), 0, 0, 'L');
		$this->Cell(30, 5, formatFechaDMA($field_orden['FechaOrdenPago']), 0, 1, 'L');
		$this->SetXY(165, 15); $this->Cell(20, 5, utf8_decode('Página: '), 0, 0, 'L'); 
		$this->Cell(30, 5, $this->PageNo().' de {nb}', 0, 1, 'L');
		$this->SetFont('Arial', 'B', 14);		
		$this->SetXY(10, 20); $this->Cell(195, 5, utf8_decode($_HOJA), 0, 1, 'C', 0);
	}
	
	//	Pie de página.
	function Footer() {
		global $_HOJA;
		global $_PREPARADO;
		global $_REVISADO;
		global $_APROBADO;
		global $_PAGUESE;
		global $_PARAMETRO;
		##
		if ($_HOJA == "ORDEN DE PAGO") {
			$y = 237;
			$this->SetXY(10, $y);
			$this->SetDrawColor(0, 0, 0);
			$this->SetFillColor(255, 255, 255);
			$this->SetFont('Arial', 'B', 8);
			
			$this->Rect(10, $y, 195, 35, "DF");
			$this->Rect(10, $y+4, 195, 0.1, "DF");
			$this->Rect(60, $y, 0.1, 35, "DF");
			$this->Rect(106, $y, 0.1, 35, "DF");
			$this->Rect(156, $y, 0.1, 35, "DF");
			
			$this->SetXY(10, 237); $this->Cell(50, 5, 'PREPARADO POR', 0, 0, 'L');
			$this->SetXY(60, 237); $this->Cell(50, 5, 'REVISADO POR', 0, 0, 'L');
			$this->SetXY(106, 237); $this->Cell(50, 5, 'APROBADO POR', 0, 0, 'L');
			$this->SetXY(156, 237); $this->Cell(50, 5, 'PAGUESE', 0, 0, 'L');

			$this->SetFont('Arial', 'B', 6);
			$this->SetXY(10, 241); $this->MultiCell(50, 5, utf8_decode(substr($_PREPARADO['Nivel'].' '.$_PREPARADO['Nombre'], 0, 35)), 0, 'L');
			$this->SetXY(60, 241); $this->MultiCell(50, 5, utf8_decode(substr($_REVISADO['Nivel'].' '.$_REVISADO['Nombre'], 0, 35)), 0, 'L');
			$this->SetXY(106, 241); $this->MultiCell(50, 5, utf8_decode(substr($_APROBADO['Nivel'].' '.$_APROBADO['Nombre'], 0, 35)), 0, 'L');
			$this->SetXY(156, 241); $this->MultiCell(50, 5, utf8_decode(substr($_PAGUESE['Nivel'].' '.$_PAGUESE['Nombre'], 0, 35)), 0, 'L');

			$this->SetXY(10, 249); $this->MultiCell(45, 3, utf8_decode($_PREPARADO['Cargo']), 0, 'C');
			$this->SetXY(60, 249); $this->MultiCell(45, 3, utf8_decode($_REVISADO['Cargo']), 0, 'C'); 
			$this->SetXY(106, 249); $this->MultiCell(45, 3, utf8_decode($_APROBADO['Cargo']), 0, 'C'); 
			$this->SetXY(156, 249); $this->MultiCell(45, 3, utf8_decode($_PAGUESE['Cargo']), 0, 'C'); 

			$this->SetFont('Arial', 'B', 6);
			$this->SetXY(10, 267); $this->Cell(50, 5, 'Fecha: '.formatFechaDMA($_PREPARADO['Fecha']), 0, 0, 'L'); 
			$this->SetXY(60, 267); $this->Cell(50, 5, 'Fecha: '.formatFechaDMA($_REVISADO['Fecha']), 0, 0, 'L'); 
			$this->SetXY(106, 267); $this->Cell(50, 5, 'Fecha: '.formatFechaDMA($_APROBADO['Fecha']), 0, 0, 'L');
			$this->SetXY(156, 267); $this->Cell(50, 5, 'Fecha: '.formatFechaDMA($_PAGUESE['Fecha']), 0, 0, 'L'); 

		}
	}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada.
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(10, 5, 10);
$pdf->SetAutoPageBreak(5, 1);
$pdf->AddPage();
//---------------------------------------------------
$int = (int) $field_orden['MontoTotal'];
$dec = strstr($field_orden['MontoTotal'], '.');
$int_letras = strtoupper(convertir_a_letras($int, "entero"));
$monto_letras = "$int_letras CON $dec/100";
//---------------------------------------------------
$pdf->SetTextColor(0, 0, 0);
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetFillColor(255, 255, 255);

//	imprimo la primera parte
$pdf->SetXY(10, $y);
$pdf->Rect(10, $y, 165, $h, "D");
$pdf->Rect(175, $y, 30, $h, "D");
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(165, $h, 'BENEFICIARIO', 0, 0, 'L');
$pdf->Cell(30, $h, 'CEDULA/R.I.F.', 0, 0, 'C');
$y+=$h;

$pdf->SetXY(10, $y);
$pdf->Rect(10, $y, 165, $h, "D");
$pdf->Rect(175, $y, 30, $h, "D");
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(165, $h, utf8_decode($field_orden['NomProveedorPagar']), 0, 0, 'L');
$pdf->Cell(30, $h, $field_orden['DocFiscal'], 0, 0, 'C');
$y+=$h;

$pdf->SetXY(10, $y);
$pdf->Rect(10, $y, 195, $h*2, "D");
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(40, $h, 'POR LA CANTIDAD DE:', 0, 0, 'L');
$pdf->SetFont('Arial', '', 9);
$pdf->MultiCell(155, $h, utf8_decode($monto_letras), 0, 'L');
$y+=$h*2;

$pdf->SetXY(10, $y);
$pdf->Rect(10, $y, 195, $h*5, "D");
$pdf->SetFont('Arial', '', 9);
$pdf->MultiCell(195, $h, 'POR CONCEPTO DE: '.utf8_decode($field_orden['Concepto']), 0, 'J');
$y+=$h*5;

$pdf->SetXY(10, $y);
$pdf->Rect(10, $y, 110, $h, "D");
$pdf->Rect(120, $y, 85, $h, "D");
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(15, $h, 'BANCO:', 0, 0, 'L');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(95, $h, utf8_decode($field_orden['Banco']), 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(25, $h, ' NRO. CUENTA:', 0, 0, 'C');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(63, $h, $field_orden['CtaBanco'], 0, 0, 'L');
$y+=$h;

$pdf->SetXY(10, $y);
$pdf->Rect(10, $y, 195, $h+3, "D");
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(150, 8, 'MONTO BRUTO (Bs.): ', 0, 0, 'R');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(45, 8, number_format($field_orden['MontoBruto'], 2, ',', '.'), 0, 0, 'R');
$y+=$h+3;

//	imprimo retenciones
if ($rows_retencion > 4) {
	list($Retenciones, $Amortizaciones) = hoja_retencion($pdf, $query_retencion, 10, false);
	$y+=($h*5);
} else {
	list($Retenciones, $Amortizaciones) = hoja_retencion($pdf, $query_retencion, 10, true);
	$y+=($h*(5-$rows_retencion));
}

$pdf->SetXY(10, $y);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(165, $h, 'MONTO RETENCIONES (Bs.): ', 0, 0, 'R');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, $h, number_format($Retenciones, 2, ',', '.'), 0, 0, 'R');
$y+=$h;
$pdf->SetXY(10, $y);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(165, $h, 'AMORTIZACION ANTICIPO (Bs.): ', 0, 0, 'R');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, $h, number_format($Amortizaciones, 2, ',', '.'), 0, 0, 'R');
$y+=$h;
$pdf->SetXY(10, $y);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(165, $h, 'MONTO NETO (Bs.): ', 0, 0, 'R');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, $h, number_format($field_orden['MontoObligacion'], 2, ',', '.'), 0, 0, 'R');
$y+=$h;

//	imprimo distribucion presupuestaria
if ($rows_presupuesto > 5) {
	hoja_presupuesto($pdf, $query_presupuesto, 8, false);
	$y+=($h*5);
} else {
	hoja_presupuesto($pdf, $query_presupuesto, 8, true);
	$y+=($h*(7-$rows_presupuesto));
}

//	imprimo contabilidad financiera
if ($rows_cuenta > 5) {
	hoja_cuenta($pdf, $query_cuenta, 8, false);
	$y+=($h*5);
} else {
	hoja_cuenta($pdf, $query_cuenta, 8, true);
	$y+=($h*(7-$rows_cuenta));
}

//	imprimo contabilidad financiera
if ($rows_cuenta2 > 5) {
	hoja_cuenta2($pdf, $query_cuenta2, 8, false);
	$y+=($h*5);
} else {
	hoja_cuenta2($pdf, $query_cuenta2, 8, true);
	$y+=($h*(7-$rows_cuenta2));
}

//	imprimo las hojas anexas
$y = 30;
if ($rows_retencion > 4) {
	$pdf->AddPage();
	$_HOJA = "RELACIÓN ANEXA";
	hoja_retencion($pdf, $query_retencion, 57, true);
}

$y = 30;
if ($rows_presupuesto > 5) {
	$pdf->AddPage();
	$_HOJA = "RELACIÓN ANEXA";
	hoja_presupuesto($pdf, $query_presupuesto, 57, true);
}

$y = 30;
if ($rows_cuenta > 5) {
	$pdf->AddPage();
	$_HOJA = "RELACIÓN ANEXA";
	hoja_cuenta($pdf, $query_cuenta, 57, true);
}

$y = 30;
if ($rows_cuenta2 > 5) {
	$pdf->AddPage();
	$_HOJA = "RELACIÓN ANEXA";
	hoja_cuenta2($pdf, $query_cuenta2, 57, true);
}
//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
