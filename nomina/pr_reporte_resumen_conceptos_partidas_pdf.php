<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$fPeriodo = $fPeriodoAnio.'-'.$fPeriodoMes;
$filtro = '';
$filtro1 = '';
$filtro2 = '';
$filtro3 = '';
if ($fFechaGeneracionD != '') $filtro .= " AND (ptne.FechaGeneracion >= '".formatFechaAMD($fFechaGeneracionD)."')";
if ($fFechaGeneracionH != '') $filtro .= " AND (ptne.FechaGeneracion <= '".formatFechaAMD($fFechaGeneracionH)."')";
if ($fCodPersona != "") {
	$filtro1 = " AND tnec2.CodPersona = '".$fCodPersona."'";
	$filtro2 = " AND ptnec.CodPersona = '".$fCodPersona."'";
	$filtro3 = " AND CodPersona = '".$fCodPersona."'";
}
//---------------------------------------------------
##	proceso
$sql = "SELECT 
			pp.*,
			tn.Nomina,
			tp.Descripcion AS TipoProceso
		FROM 
			pr_procesoperiodo pp 
			INNER JOIN tiponomina tn ON (tn.CodTipoNom = pp.CodTipoNom)
			INNER JOIN pr_tipoproceso tp ON (tp.CodTipoProceso = pp.CodTipoProceso)
		WHERE 
			pp.CodOrganismo = '".$fCodOrganismo."' AND 
			pp.CodTipoNom = '".$fCodTipoNom."' AND 
			pp.Periodo = '".$fPeriodo."' AND 
			pp.CodTipoProceso = '".$fCodTipoProceso."'";
$field_proceso = getRecord($sql);
//---------------------------------------------------

class PDF extends FPDF {
	//	Cabecera de página.
	function Header() {
		global $_PARAMETRO;
		global $field_proceso;
		global $fPeriodo;
		global $FechaActual;
		global $_POST;
		extract($_POST);
		##
		$NomOrganismo = getValorCampo("mastorganismos", "CodOrganismo", "Organismo", $fCodOrganismo);
		$NomDependencia = getValorCampo("mastdependencias", "CodDependencia", "Dependencia", $_PARAMETRO["DEPRHPR"]);
		##
		$this->SetFillColor(255, 255, 255);
		$this->SetDrawColor(0, 0, 0);
		$this->SetFont('Arial', 'B', 8);
		$this->Cell(205, 5, strtoupper(utf8_decode($NomOrganismo)), 0, 1, 'L');
		$this->Cell(205, 5, strtoupper(utf8_decode($NomDependencia)), 0, 1, 'L');
		$this->Cell(205, 5, strtoupper(utf8_decode('RESUMEN DE CONCEPTOS POR PARTIDAS - '.$field_proceso['Nomina'])), 0, 1, 'L');
		$this->Cell(205, 5, strtoupper(utf8_decode('MES DE '.getNombreMes($fPeriodo).' '.substr($fPeriodo,0,4).' '.$field_proceso['TipoProceso'])), 0, 1, 'L');
		$this->Cell(205, 5, 'DEL '.formatFechaDMA($field_proceso['FechaDesde']).' AL '.formatFechaDMA($field_proceso['FechaHasta']), 0, 1, 'L');
		##	-------------------
		$this->SetFont('Arial', '', 8);
		$this->SetXY(175, 5); $this->Cell(10, 5, utf8_decode('Fecha: '), 0, 0, 'L');
		$this->Cell(30, 5, formatFechaDMA($FechaActual), 0, 1, 'L');
		$this->SetXY(175, 10); $this->Cell(10, 5, utf8_decode('Página: '), 0, 0, 'L');
		$this->Cell(30, 5, $this->PageNo().' de {nb}', 0, 1, 'L');
		##	-------------------
		$this->SetY(35); 
		$this->SetTextColor(0,0,0);
		$this->SetDrawColor(255,255,255);
		$this->SetFillColor(255,255,255);
		##
		$this->SetFont('Arial', 'B', 8);
		$this->SetHeights(array(3,3,3));

		if ($field_proceso['CodTipoProceso'] == 'FIN') {
			$this->SetWidths(array(105,25,25,25,25));
			$this->SetAligns(array('L','R','R','R','C'));
			$this->Row(array(utf8_decode('CONCEPTO'),
							 utf8_decode('ADELANTO'),
							 utf8_decode('FIN DE MES'),
							 utf8_decode('NETO'),
							 utf8_decode('PARTIDA')
							 ));
		} else {
			$this->SetWidths(array(155,25,25));
			$this->SetAligns(array('L','R','C'));
			$this->Row(array(utf8_decode('CONCEPTO'),
							 utf8_decode('NETO'),
							 utf8_decode('PARTIDA')
							 ));
		}
		$this->Ln(1);
		##	
		$this->SetDrawColor(0,0,0);
		$this->Line(5, $this->GetY(), 210, $this->GetY());
		$this->Ln(2);
	}
	
	//	Pie de página.
	function Footer() {}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada.
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(5, 5, 5);
$pdf->SetAutoPageBreak(5, 5);
$pdf->AddPage();
//---------------------------------------------------
##	asignaciones
$TotalAsignaciones = 0;
$MontoAdeA = 0;
$MontoFinA = 0;
if ($fCodTipoProceso == 'FIN') {
	$sql = "(SELECT
				pc.CodConcepto,
				pc.Descripcion,
				SUM(ptnec.Monto) AS MontoFin,
				(SELECT SUM(tnec2.Monto)
				 FROM
					pr_tiponominaempleadoconcepto tnec2
					INNER JOIN pr_tiponominaempleadoconcepto tnec3 ON (tnec2.CodTipoNom = tnec3.CodTipoNom AND
																	   tnec2.Periodo = tnec3.Periodo AND
																	   tnec2.CodPersona = tnec3.CodPersona AND
																	   tnec2.CodOrganismo = tnec3.CodOrganismo AND
																	   tnec2.CodConcepto = tnec3.CodConcepto AND
																	   tnec3.CodTipoProceso = 'FIN')
				 WHERE
					tnec2.CodConcepto = ptnec.CodConcepto AND
					tnec2.CodTipoNom = '".$fCodTipoNom."' AND 
					tnec2.Periodo = '".$fPeriodo."' AND 
					tnec2.CodTipoProceso = 'ADE' $filtro1
				 GROUP BY
					tnec2.CodTipoNom,
					tnec2.Periodo,
					tnec2.CodTipoProceso) AS MontoAde,
				cpd.cod_partida,
				'1' AS Orden
			 FROM
				pr_concepto pc
				INNER JOIN pr_tiponominaempleadoconcepto ptnec ON (pc.CodConcepto = ptnec.CodConcepto)
				INNER JOIN pr_tiponominaempleado ptne ON (ptne.CodTipoNom = ptnec.CodTipoNom AND 
														  ptne.Periodo = ptnec.Periodo AND 
														  ptne.CodPersona = ptnec.CodPersona AND 
														  ptne.CodOrganismo = ptnec.CodOrganismo AND 
														  ptne.CodTipoProceso = ptnec.CodTipoProceso)
				LEFT JOIN tiponomina tn ON (ptnec.CodTipoNom = tn.CodTipoNom)
				LEFT JOIN pr_conceptoperfil cp ON (tn.CodPerfilConcepto = cp.CodPerfilConcepto)
				LEFT JOIN pr_conceptoperfildetalle cpd ON (cp.CodPerfilConcepto = cpd.CodPerfilConcepto AND
														   ptnec.CodTipoProceso = cpd.CodTipoProceso AND
														   pc.CodConcepto = cpd.CodConcepto)
			 WHERE
				pc.Tipo = 'I' AND
				ptnec.CodTipoNom = '".$fCodTipoNom."' AND 
				ptnec.Periodo = '".$fPeriodo."' AND 
				ptnec.CodTipoProceso = '".$fCodTipoProceso."' $filtro2 $filtro
			 GROUP BY
				ptnec.CodTipoNom,
				ptnec.Periodo,
				ptnec.CodTipoProceso,
				ptnec.CodConcepto)
			UNION
			(SELECT
				pc.CodConcepto,
				pc.Descripcion,
				'' AS MontoFin,
				SUM(ptnec.Monto) AS MontoAde,
				cpd.cod_partida,
				'2' AS Orden
			 FROM
				pr_concepto pc
				INNER JOIN pr_tiponominaempleadoconcepto ptnec ON (pc.CodConcepto = ptnec.CodConcepto)
				INNER JOIN pr_tiponominaempleado ptne ON (ptne.CodTipoNom = ptnec.CodTipoNom AND 
														  ptne.Periodo = ptnec.Periodo AND 
														  ptne.CodPersona = ptnec.CodPersona AND 
														  ptne.CodOrganismo = ptnec.CodOrganismo AND 
														  ptne.CodTipoProceso = ptnec.CodTipoProceso)
				LEFT JOIN tiponomina tn ON (ptnec.CodTipoNom = tn.CodTipoNom)
				LEFT JOIN pr_conceptoperfil cp ON (tn.CodPerfilConcepto = cp.CodPerfilConcepto)
				LEFT JOIN pr_conceptoperfildetalle cpd ON (cp.CodPerfilConcepto = cpd.CodPerfilConcepto AND
														   ptnec.CodTipoProceso = cpd.CodTipoProceso AND
														   pc.CodConcepto = cpd.CodConcepto)
			 WHERE
				pc.Tipo = 'I' AND
				ptnec.CodTipoNom = '".$fCodTipoNom."' AND 
				ptnec.Periodo = '".$fPeriodo."' AND 
				ptnec.CodTipoProceso = 'ADE' AND
				ptnec.CodConcepto NOT IN (SELECT CodConcepto FROM pr_tiponominaempleadoconcepto WHERE CodTipoNom = '".$fCodTipoNom."' AND Periodo = '".$fPeriodo."' AND CodTipoProceso = 'FIN') $filtro2 $filtro
			 GROUP BY
				ptnec.CodTipoNom,
				ptnec.Periodo,
				ptnec.CodTipoProceso,
				ptnec.CodConcepto)";
} else {
	$sql = "SELECT
				pc.CodConcepto,
				pc.Descripcion,
				SUM(ptnec.Monto) AS MontoAde,
				'0.00' AS MontoFin,
				cpd.cod_partida
			FROM
				pr_concepto pc
				INNER JOIN pr_tiponominaempleadoconcepto ptnec ON (pc.CodConcepto = ptnec.CodConcepto)
				INNER JOIN pr_tiponominaempleado ptne ON (ptne.CodTipoNom = ptnec.CodTipoNom AND 
														  ptne.Periodo = ptnec.Periodo AND 
														  ptne.CodPersona = ptnec.CodPersona AND 
														  ptne.CodOrganismo = ptnec.CodOrganismo AND 
														  ptne.CodTipoProceso = ptnec.CodTipoProceso)
				LEFT JOIN tiponomina tn ON (ptnec.CodTipoNom = tn.CodTipoNom)
				LEFT JOIN pr_conceptoperfil cp ON (tn.CodPerfilConcepto = cp.CodPerfilConcepto)
				LEFT JOIN pr_conceptoperfildetalle cpd ON (cp.CodPerfilConcepto = cpd.CodPerfilConcepto AND
														   ptnec.CodTipoProceso = cpd.CodTipoProceso AND
														   pc.CodConcepto = cpd.CodConcepto)
			WHERE
				pc.Tipo = 'I' AND
				ptnec.CodTipoNom = '".$fCodTipoNom."' AND 
				ptnec.Periodo = '".$fPeriodo."' AND 
				ptnec.CodTipoProceso = '".$fCodTipoProceso."' $filtro2 $filtro
			GROUP BY
				ptnec.CodTipoNom,
				ptnec.Periodo,
				ptnec.CodTipoProceso,
				ptnec.CodConcepto";
}
$field_asignaciones = getRecords($sql);
foreach($field_asignaciones as $f) {
	$TotalMes = (($fCodTipoProceso == 'FIN')?($f['MontoFin'] - $f['MontoAde']):$f['MontoAde']);
	$TotalAsignaciones += $TotalMes;
	$MontoAdeA += $f['MontoAde'];
	$MontoFinA += $f['MontoFin'];
	##	
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetDrawColor(255, 255, 255);
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 8);
	if ($fCodTipoProceso == 'FIN') {
		$pdf->Row(array(utf8_decode($f['Descripcion']),
						number_format($f['MontoAde'],2,',','.'),
						number_format($f['MontoFin'],2,',','.'),
						number_format($TotalMes,2,',','.'),
						$f['cod_partida']
						));
	} else {
		$pdf->Row(array(utf8_decode($f['Descripcion']),
						number_format($TotalMes,2,',','.'),
						$f['cod_partida']
						));
	}
	$pdf->Ln(2);
}
$pdf->SetTextColor(0, 0, 0);
$pdf->SetDrawColor(255, 255, 255);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 8);
if ($fCodTipoProceso == 'FIN') {
	$pdf->Row(array('TOTAL ASIGNACIONES',
					number_format($MontoAdeA,2,',','.'),
					number_format($MontoFinA,2,',','.'),
					number_format($TotalAsignaciones,2,',','.'),
					''
					));
} else {
	$pdf->Row(array('TOTAL ASIGNACIONES',
					number_format($TotalAsignaciones,2,',','.'),
					''
					));
}
$pdf->Ln(5);
##	deducciones
$TotalDeducciones = 0;
$MontoAdeF = 0;
$MontoFinF = 0;
if ($fCodTipoProceso == 'FIN') {
	$sql = "(SELECT
				pc.CodConcepto,
				pc.Descripcion,
				SUM(ptnec.Monto) AS MontoFin,
				(SELECT SUM(tnec2.Monto)
				 FROM
					pr_tiponominaempleadoconcepto tnec2
					INNER JOIN pr_tiponominaempleadoconcepto tnec3 ON (tnec2.CodTipoNom = tnec3.CodTipoNom AND
																	   tnec2.Periodo = tnec3.Periodo AND
																	   tnec2.CodPersona = tnec3.CodPersona AND
																	   tnec2.CodOrganismo = tnec3.CodOrganismo AND
																	   tnec2.CodConcepto = tnec3.CodConcepto AND
																	   tnec3.CodTipoProceso = 'FIN')
				 WHERE
					tnec2.CodConcepto = ptnec.CodConcepto AND
					tnec2.CodTipoNom = '".$fCodTipoNom."' AND 
					tnec2.Periodo = '".$fPeriodo."' AND 
					tnec2.CodTipoProceso = 'ADE' $filtro1
				 GROUP BY
					tnec2.CodTipoNom,
					tnec2.Periodo,
					tnec2.CodTipoProceso) AS MontoAde,
				cpd.cod_partida,
				'1' AS Orden
			 FROM
				pr_concepto pc
				INNER JOIN pr_tiponominaempleadoconcepto ptnec ON (pc.CodConcepto = ptnec.CodConcepto)
				INNER JOIN pr_tiponominaempleado ptne ON (ptne.CodTipoNom = ptnec.CodTipoNom AND 
														  ptne.Periodo = ptnec.Periodo AND 
														  ptne.CodPersona = ptnec.CodPersona AND 
														  ptne.CodOrganismo = ptnec.CodOrganismo AND 
														  ptne.CodTipoProceso = ptnec.CodTipoProceso)
				LEFT JOIN tiponomina tn ON (ptnec.CodTipoNom = tn.CodTipoNom)
				LEFT JOIN pr_conceptoperfil cp ON (tn.CodPerfilConcepto = cp.CodPerfilConcepto)
				LEFT JOIN pr_conceptoperfildetalle cpd ON (cp.CodPerfilConcepto = cpd.CodPerfilConcepto AND
														   ptnec.CodTipoProceso = cpd.CodTipoProceso AND
														   pc.CodConcepto = cpd.CodConcepto)
			 WHERE
				pc.Tipo = 'D' AND
				ptnec.CodTipoNom = '".$fCodTipoNom."' AND 
				ptnec.Periodo = '".$fPeriodo."' AND 
				ptnec.CodTipoProceso = '".$fCodTipoProceso."' $filtro2 $filtro
			 GROUP BY
				ptnec.CodTipoNom,
				ptnec.Periodo,
				ptnec.CodTipoProceso,
				ptnec.CodConcepto)
			UNION
			(SELECT
				pc.CodConcepto,
				pc.Descripcion,
				'' AS MontoFin,
				SUM(ptnec.Monto) AS MontoAde,
				cpd.cod_partida,
				'2' AS Orden
			 FROM
				pr_concepto pc
				INNER JOIN pr_tiponominaempleadoconcepto ptnec ON (pc.CodConcepto = ptnec.CodConcepto)
				INNER JOIN pr_tiponominaempleado ptne ON (ptne.CodTipoNom = ptnec.CodTipoNom AND 
														  ptne.Periodo = ptnec.Periodo AND 
														  ptne.CodPersona = ptnec.CodPersona AND 
														  ptne.CodOrganismo = ptnec.CodOrganismo AND 
														  ptne.CodTipoProceso = ptnec.CodTipoProceso)
				LEFT JOIN tiponomina tn ON (ptnec.CodTipoNom = tn.CodTipoNom)
				LEFT JOIN pr_conceptoperfil cp ON (tn.CodPerfilConcepto = cp.CodPerfilConcepto)
				LEFT JOIN pr_conceptoperfildetalle cpd ON (cp.CodPerfilConcepto = cpd.CodPerfilConcepto AND
														   ptnec.CodTipoProceso = cpd.CodTipoProceso AND
														   pc.CodConcepto = cpd.CodConcepto)
			 WHERE
				pc.Tipo = 'D' AND
				ptnec.CodTipoNom = '".$fCodTipoNom."' AND 
				ptnec.Periodo = '".$fPeriodo."' AND 
				ptnec.CodTipoProceso = 'ADE' AND
				ptnec.CodConcepto NOT IN (SELECT CodConcepto FROM pr_tiponominaempleadoconcepto WHERE CodTipoNom = '".$fCodTipoNom."' AND Periodo = '".$fPeriodo."' AND CodTipoProceso = 'FIN')  $filtro2 $filtro
			 GROUP BY
				ptnec.CodTipoNom,
				ptnec.Periodo,
				ptnec.CodTipoProceso,
				ptnec.CodConcepto)";
} else {
	$sql = "SELECT
				pc.CodConcepto,
				pc.Descripcion,
				SUM(ptnec.Monto) AS MontoAde,
				cpd.cod_partida
			FROM
				pr_concepto pc
				INNER JOIN pr_tiponominaempleadoconcepto ptnec ON (pc.CodConcepto = ptnec.CodConcepto)
				INNER JOIN pr_tiponominaempleado ptne ON (ptne.CodTipoNom = ptnec.CodTipoNom AND 
														  ptne.Periodo = ptnec.Periodo AND 
														  ptne.CodPersona = ptnec.CodPersona AND 
														  ptne.CodOrganismo = ptnec.CodOrganismo AND 
														  ptne.CodTipoProceso = ptnec.CodTipoProceso)
				LEFT JOIN tiponomina tn ON (ptnec.CodTipoNom = tn.CodTipoNom)
				LEFT JOIN pr_conceptoperfil cp ON (tn.CodPerfilConcepto = cp.CodPerfilConcepto)
				LEFT JOIN pr_conceptoperfildetalle cpd ON (cp.CodPerfilConcepto = cpd.CodPerfilConcepto AND
														   ptnec.CodTipoProceso = cpd.CodTipoProceso AND
														   pc.CodConcepto = cpd.CodConcepto)
			WHERE
				pc.Tipo = 'D' AND
				ptnec.CodTipoNom = '".$fCodTipoNom."' AND 
				ptnec.Periodo = '".$fPeriodo."' AND 
				ptnec.CodTipoProceso = '".$fCodTipoProceso."' $filtro2 $filtro
			GROUP BY
				ptnec.CodTipoNom,
				ptnec.Periodo,
				ptnec.CodTipoProceso,
				ptnec.CodConcepto";
}
$field_deducciones = getRecords($sql);
foreach($field_deducciones as $f) {
	if ($f['CodConcepto'] != "0017") $TotalMes = (($fCodTipoProceso == 'FIN')?$f['MontoFin']:$f['MontoAde']); else $TotalMes = 0;
	$TotalDeducciones += $TotalMes;
	$MontoAdeF += $f['MontoAde'];
	$MontoFinF += $f['MontoFin'];
	##	
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetDrawColor(255, 255, 255);
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 8);
	if ($fCodTipoProceso == 'FIN') {
		$pdf->Row(array(utf8_decode($f['Descripcion']),
						number_format($f['MontoAde'],2,',','.'),
						number_format($f['MontoFin'],2,',','.'),
						number_format($TotalMes,2,',','.'),
						$f['cod_partida']
						));
	} else {
		$pdf->Row(array(utf8_decode($f['Descripcion']),
						number_format($TotalMes,2,',','.'),
						$f['cod_partida']
						));
	}
	$pdf->Ln(2);
}
$pdf->SetTextColor(0, 0, 0);
$pdf->SetDrawColor(255, 255, 255);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 8);
if ($fCodTipoProceso == 'FIN') {
	$pdf->Row(array('TOTAL DEDUCCIONES',
					number_format($MontoAdeF,2,',','.'),
					number_format($MontoFinF,2,',','.'),
					number_format($TotalDeducciones,2,',','.'),
					''
					));
} else {
	$pdf->Row(array('TOTAL DEDUCCIONES',
					number_format($TotalDeducciones,2,',','.'),
					''
					));
}
$pdf->Ln(5);
##	neto
$pdf->SetDrawColor(0,0,0);
$pdf->Line(5, $pdf->GetY(), 210, $pdf->GetY());
$pdf->Ln(1);
$TotalAdelanto = $MontoAdeA - $MontoAdeF;
$TotalFin = $MontoFinA - $MontoFinF;
$TotalNeto = $TotalAsignaciones - $TotalDeducciones;
##	
$pdf->SetTextColor(0, 0, 0);
$pdf->SetDrawColor(255, 255, 255);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 8);
if ($fCodTipoProceso == 'FIN') {
	$pdf->Row(array('TOTAL NETO',
					number_format($TotalAdelanto,2,',','.'),
					number_format($TotalFin,2,',','.'),
					number_format($TotalNeto,2,',','.'),
					''
					));
} else {
	$pdf->Row(array('TOTAL NETO',
					number_format($TotalNeto,2,',','.'),
					''
					));
}
$pdf->Ln(1);
##	
$pdf->SetDrawColor(0,0,0);
$pdf->Line(5, $pdf->GetY(), 210, $pdf->GetY());
$pdf->Ln(5);
##	aportes
$TotalAportes = 0;
$MontoAdeP = 0;
$MontoFinP = 0;
if ($fCodTipoProceso == 'FIN') {
	$sql = "(SELECT
				pc.CodConcepto,
				pc.Descripcion,
				SUM(ptnec.Monto) AS MontoFin,
				(SELECT SUM(Monto)
					FROM pr_tiponominaempleadoconcepto
						WHERE
							CodConcepto = pc.CodConcepto AND
							Tipo = 'A' AND
							CodTipoNom = '".$fCodTipoNom."' AND 
							Periodo = '".$fPeriodo."' AND 
							CodTipoProceso = 'ADE'
						GROUP BY
							CodTipoNom,
							Periodo,
							CodTipoProceso) AS MontoAde,
				cpd.cod_partida
			FROM
				pr_concepto pc
				INNER JOIN pr_tiponominaempleadoconcepto ptnec ON (pc.CodConcepto = ptnec.CodConcepto)
				INNER JOIN pr_tiponominaempleado ptne ON (ptne.CodTipoNom = ptnec.CodTipoNom AND 
														  ptne.Periodo = ptnec.Periodo AND 
														  ptne.CodPersona = ptnec.CodPersona AND 
														  ptne.CodOrganismo = ptnec.CodOrganismo AND 
														  ptne.CodTipoProceso = ptnec.CodTipoProceso)
				LEFT JOIN tiponomina tn ON (ptnec.CodTipoNom = tn.CodTipoNom)
				LEFT JOIN pr_conceptoperfil cp ON (tn.CodPerfilConcepto = cp.CodPerfilConcepto)
				LEFT JOIN pr_conceptoperfildetalle cpd ON (cp.CodPerfilConcepto = cpd.CodPerfilConcepto AND
														   ptnec.CodTipoProceso = cpd.CodTipoProceso AND
														   pc.CodConcepto = cpd.CodConcepto)
			WHERE
				pc.Tipo = 'A' AND
				ptnec.CodTipoNom = '".$fCodTipoNom."' AND 
				ptnec.Periodo = '".$fPeriodo."' AND 
				ptnec.CodTipoProceso = '".$fCodTipoProceso."' $filtro2 $filtro
			GROUP BY
				ptnec.CodTipoNom,
				ptnec.Periodo,
				ptnec.CodTipoProceso,
				ptnec.CodConcepto)
			UNION
			(SELECT
				pc.CodConcepto,
				pc.Descripcion,
				'' AS MontoFin,
				SUM(ptnec.Monto) AS MontoAde,
				cpd.cod_partida
			FROM
				pr_concepto pc
				INNER JOIN pr_tiponominaempleadoconcepto ptnec ON (pc.CodConcepto = ptnec.CodConcepto)
				INNER JOIN pr_tiponominaempleado ptne ON (ptne.CodTipoNom = ptnec.CodTipoNom AND 
														  ptne.Periodo = ptnec.Periodo AND 
														  ptne.CodPersona = ptnec.CodPersona AND 
														  ptne.CodOrganismo = ptnec.CodOrganismo AND 
														  ptne.CodTipoProceso = ptnec.CodTipoProceso)
				LEFT JOIN tiponomina tn ON (ptnec.CodTipoNom = tn.CodTipoNom)
				LEFT JOIN pr_conceptoperfil cp ON (tn.CodPerfilConcepto = cp.CodPerfilConcepto)
				LEFT JOIN pr_conceptoperfildetalle cpd ON (cp.CodPerfilConcepto = cpd.CodPerfilConcepto AND
														   ptnec.CodTipoProceso = cpd.CodTipoProceso AND
														   pc.CodConcepto = cpd.CodConcepto)
			WHERE
				pc.Tipo = 'A' AND
				ptnec.CodTipoNom = '".$fCodTipoNom."' AND 
				ptnec.Periodo = '".$fPeriodo."' AND 
				ptnec.CodTipoProceso = 'ADE' AND
				ptnec.CodConcepto NOT IN (SELECT CodConcepto FROM pr_tiponominaempleadoconcepto WHERE CodTipoNom = '".$fCodTipoNom."' AND Periodo = '".$fPeriodo."' AND CodTipoProceso = 'FIN') $filtro2 $filtro
			GROUP BY
				ptnec.CodTipoNom,
				ptnec.Periodo,
				ptnec.CodTipoProceso,
				ptnec.CodConcepto)";
} else {
	$sql = "SELECT
				pc.CodConcepto,
				pc.Descripcion,
				SUM(ptnec.Monto) AS MontoAde,
				cpd.cod_partida
			FROM
				pr_concepto pc
				INNER JOIN pr_tiponominaempleadoconcepto ptnec ON (pc.CodConcepto = ptnec.CodConcepto)
				INNER JOIN pr_tiponominaempleado ptne ON (ptne.CodTipoNom = ptnec.CodTipoNom AND 
														  ptne.Periodo = ptnec.Periodo AND 
														  ptne.CodPersona = ptnec.CodPersona AND 
														  ptne.CodOrganismo = ptnec.CodOrganismo AND 
														  ptne.CodTipoProceso = ptnec.CodTipoProceso)
				LEFT JOIN tiponomina tn ON (ptnec.CodTipoNom = tn.CodTipoNom)
				LEFT JOIN pr_conceptoperfil cp ON (tn.CodPerfilConcepto = cp.CodPerfilConcepto)
				LEFT JOIN pr_conceptoperfildetalle cpd ON (cp.CodPerfilConcepto = cpd.CodPerfilConcepto AND
														   ptnec.CodTipoProceso = cpd.CodTipoProceso AND
														   pc.CodConcepto = cpd.CodConcepto)
			WHERE
				pc.Tipo = 'A' AND
				ptnec.CodTipoNom = '".$fCodTipoNom."' AND 
				ptnec.Periodo = '".$fPeriodo."' AND 
				ptnec.CodTipoProceso = '".$fCodTipoProceso."' $filtro2 $filtro
			GROUP BY
				ptnec.CodTipoNom,
				ptnec.Periodo,
				ptnec.CodTipoProceso,
				ptnec.CodConcepto";
}
$field_aportes = getRecords($sql);
foreach($field_aportes as $f) {
	$TotalMes = (($fCodTipoProceso == 'FIN')?$f['MontoFin']:$f['MontoAde']);
	$TotalAportes += $TotalMes;
	$MontoAdeP += $f['MontoAde'];
	$MontoFinP += $f['MontoFin'];
	##	
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetDrawColor(255, 255, 255);
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 8);
	if ($fCodTipoProceso == 'FIN') {
		$pdf->Row(array(utf8_decode($f['Descripcion']),
						number_format($f['MontoAde'],2,',','.'),
						number_format($f['MontoFin'],2,',','.'),
						number_format($TotalMes,2,',','.'),
						$f['cod_partida']
						));
	} else {
		$pdf->Row(array(utf8_decode($f['Descripcion']),
						number_format($TotalMes,2,',','.'),
						$f['cod_partida']
						));
	}
	$pdf->Ln(2);
}
$pdf->SetTextColor(0, 0, 0);
$pdf->SetDrawColor(255, 255, 255);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 8);
if ($fCodTipoProceso == 'FIN') {
	$pdf->Row(array('TOTAL APORTES',
					number_format($MontoAdeP,2,',','.'),
					number_format($MontoFinP,2,',','.'),
					number_format($TotalAportes,2,',','.'),
					''
					));
} else {
	$pdf->Row(array('TOTAL APORTES',
					number_format($TotalAportes,2,',','.'),
					''
					));
}
##	
$pdf->Ln(30);
$pdf->SetDrawColor(0,0,0);
$pdf->Line(6, $pdf->GetY(), 61, $pdf->GetY());
$pdf->Line(108, $pdf->GetY(), 163, $pdf->GetY());
list($ElaboradoNombre, $ElaboradoCargo, $ElaboradoNivel) = getFirma($field_proceso['ProcesadoPor']);
list($ConformadoNombre, $ConformadoCargo, $ConformadoNivel) = getFirmaxDependencia($_PARAMETRO["DEPRHPR"]);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(102, 5, utf8_decode('ELABORADO POR:'), 0, 0, 'L');
$pdf->Cell(103, 5, utf8_decode('CONFORMADO POR:'), 0, 1, 'L');
$pdf->Cell(102, 5, utf8_decode($ElaboradoNombre), 0, 0, 'L');
$pdf->Cell(103, 5, utf8_decode($ConformadoNombre), 0, 1, 'L');
$pdf->Cell(102, 5, utf8_decode($ElaboradoCargo), 0, 0, 'L');
$pdf->Cell(103, 5, utf8_decode($ConformadoCargo), 0, 1, 'L');
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
