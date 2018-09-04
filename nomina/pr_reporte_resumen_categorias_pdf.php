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
		global $FlagTotal;
		global $field_concepto;
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
		$this->Cell(205, 5, strtoupper(utf8_decode('RESUMEN POR CATEGORIAS PROGRAMATICAS - '.$field_proceso['Nomina'])), 0, 1, 'L');
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
		if ($FlagTotal) {
			$this->SetDrawColor(0,0,0); 
			$this->Line(5, $this->GetY()-1, 210, $this->GetY()-1);
			$this->Line(5, $this->GetY(), 210, $this->GetY());
			$this->SetFont('Arial', 'B', 8);
			$this->Cell(205, 8, utf8_decode('TOTAL GENERAL'), 0, 1, 'L');
			$this->Line(5, $this->GetY(), 210, $this->GetY());
			$this->Line(5, $this->GetY()+1, 210, $this->GetY()+1);
		} else {
			$this->SetDrawColor(0,0,0); 
			$this->Line(5, $this->GetY()-1, 210, $this->GetY()-1);
			$this->Line(5, $this->GetY(), 210, $this->GetY());
			$this->SetFont('Arial', 'B', 8);
			$this->Cell(205, 8, utf8_decode(mb_strtoupper(substr($field_concepto['CategoriaProg'],0,2).substr($field_concepto['CategoriaProg'],4,2).substr($field_concepto['CategoriaProg'],10,2).' - '.$field_concepto['Actividad'],'UTF8')), 0, 1, 'L');
			$this->Line(5, $this->GetY(), 210, $this->GetY());
			$this->Line(5, $this->GetY()+1, 210, $this->GetY()+1);

		}
		$this->Ln(2);
		##
		$this->SetTextColor(0,0,0);
		$this->SetDrawColor(255,255,255);
		$this->SetFillColor(255,255,255);
		$this->SetFont('Arial', 'B', 8);
		$this->SetHeights(array(3,3,3));
		$this->SetWidths(array(155,25,25));
		$this->SetAligns(array('L','R','C'));
		$this->Row(array(utf8_decode('CONCEPTO'),
						 utf8_decode('MONTO'),
						 utf8_decode('PARTIDA')
						 ));
		$this->Ln(1);
		##	
		$this->SetDrawColor(0,0,0); $this->Line(5, $this->GetY(), 210, $this->GetY());
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
//---------------------------------------------------
//	TOTAL GENERAL
$Grupo1 = '';
$Grupo2 = '';
$FlagTotal = false;
##	
$sql = "(SELECT
			pc.CodConcepto,
			pc.Descripcion,
			SUM(ptnec.Monto) AS Monto,
			cpd.cod_partida,
			'1' AS Orden,
			e.CategoriaProg,
			ue.Denominacion AS UnidadEjecutora,
			a.Denominacion AS Actividad
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
			INNER JOIN mastempleado e ON (e.CodPersona = ptne.CodPersona)
			LEFT JOIN pv_categoriaprog cpr ON (cpr.CategoriaProg = e.CategoriaProg)
			LEFT JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cpr.CodUnidadEjec)
			LEFT JOIN pv_actividades a ON (a.IdACtividad = cpr.IdACtividad)
		 WHERE
			pc.Tipo = 'I' AND
			ptnec.CodTipoNom = '".$fCodTipoNom."' AND 
			ptnec.Periodo = '".$fPeriodo."' AND 
			ptnec.CodTipoProceso = '".$fCodTipoProceso."' $filtro2 $filtro
		 GROUP BY
		 	e.CategoriaProg,
			ptnec.CodTipoNom,
			ptnec.Periodo,
			ptnec.CodTipoProceso,
			ptnec.CodConcepto)
		UNION
		(SELECT
			pc.CodConcepto,
			pc.Descripcion,
			SUM(ptnec.Monto) AS Monto,
			cpd.cod_partida,
			'2' AS Orden,
			e.CategoriaProg,
			ue.Denominacion AS UnidadEjecutora,
			a.Denominacion AS Actividad
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
			INNER JOIN mastempleado e ON (e.CodPersona = ptne.CodPersona)
			LEFT JOIN pv_categoriaprog cpr ON (cpr.CategoriaProg = e.CategoriaProg)
			LEFT JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cpr.CodUnidadEjec)
			LEFT JOIN pv_actividades a ON (a.IdACtividad = cpr.IdACtividad)
		 WHERE
			pc.Tipo = 'D' AND
			ptnec.CodTipoNom = '".$fCodTipoNom."' AND 
			ptnec.Periodo = '".$fPeriodo."' AND 
			ptnec.CodTipoProceso = '".$fCodTipoProceso."' $filtro2 $filtro
		 GROUP BY
		 	e.CategoriaProg,
			ptnec.CodTipoNom,
			ptnec.Periodo,
			ptnec.CodTipoProceso,
			ptnec.CodConcepto)
		UNION
		(SELECT
			pc.CodConcepto,
			pc.Descripcion,
			SUM(ptnec.Monto) AS MontoAde,
			cpd.cod_partida,
			'3' AS Orden,
			e.CategoriaProg,
			ue.Denominacion AS UnidadEjecutora,
			a.Denominacion AS Actividad
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
			INNER JOIN mastempleado e ON (e.CodPersona = ptne.CodPersona)
			LEFT JOIN pv_categoriaprog cpr ON (cpr.CategoriaProg = e.CategoriaProg)
			LEFT JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cpr.CodUnidadEjec)
			LEFT JOIN pv_actividades a ON (a.IdACtividad = cpr.IdACtividad)
		 WHERE
			pc.Tipo = 'A' AND
			ptnec.CodTipoNom = '".$fCodTipoNom."' AND 
			ptnec.Periodo = '".$fPeriodo."' AND 
			ptnec.CodTipoProceso = '".$fCodTipoProceso."' $filtro2 $filtro
		 GROUP BY
		 	e.CategoriaProg,
			ptnec.CodTipoNom,
			ptnec.Periodo,
			ptnec.CodTipoProceso,
			ptnec.CodConcepto)
		ORDER BY CategoriaProg, Orden, CodConcepto";
$field = getRecords($sql);
foreach($field as $f) {
	$field_concepto = $f;
	if ($Grupo1 != $f['CategoriaProg']) {
		if ($Grupo1 != '') {
			$pdf->SetTextColor(0, 0, 0);
			$pdf->SetDrawColor(255, 255, 255);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetFont('Arial', 'B', 8);
			if ($Orden == '1') {
				$pdf->Row(array('TOTAL',
								number_format($Asignaciones,2,',','.'),
								''
								));
			}
			elseif ($Orden == '2') {
				$pdf->Row(array('TOTAL',
								number_format($Deducciones,2,',','.'),
								''
								));
			}
			elseif ($Orden == '3') {
				$pdf->Row(array('TOTAL',
								number_format($Aportes,2,',','.'),
								''
								));
			}
			##	
			$pdf->Ln(2);
			$pdf->SetDrawColor(0,0,0);
			$pdf->Line(5, $pdf->GetY(), 210, $pdf->GetY());
			$pdf->Ln(2);
			##	
			$pdf->SetDrawColor(255, 255, 255);
			$pdf->Row(array('TOTAL ASIGNACIONES',
							number_format($Asignaciones,2,',','.'),
							''
							));
			$pdf->Ln(2);
			$pdf->Row(array('TOTAL DEDUCCIONES',
							number_format($Deducciones,2,',','.'),
							''
							));
			$pdf->Ln(2);
			$pdf->Row(array('NETO A PAGAR',
							number_format(($Asignaciones - $Deducciones),2,',','.'),
							''
							));
			##	
			$pdf->Ln(2);
			$pdf->SetDrawColor(0,0,0);
			$pdf->Line(5, $pdf->GetY(), 210, $pdf->GetY());
			$pdf->Ln(2);
		}
		$pdf->AddPage();
		$pdf->Ln(2);
		##	
		$Grupo1 = $f['CategoriaProg'];
		$Asignaciones = 0;
		$Deducciones = 0;
		$Aportes = 0;
	}
	if ($Grupo2 != $f['Orden']) {
		if ($f['Orden'] == '1') {
			$Asignaciones = 0;
			##	
			$pdf->SetFont('Arial', 'BU', 8);
			$pdf->Cell(205, 5, strtoupper(utf8_decode('ASIGNACIONES')), 0, 1, 'C');
		}
		elseif ($f['Orden'] == '2') {
			$Deducciones = 0;
			##	TOTAL ASIGNACIONES
			$pdf->SetTextColor(0, 0, 0);
			$pdf->SetDrawColor(255, 255, 255);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetFont('Arial', 'B', 8);
			$pdf->Row(array('TOTAL',
							number_format($Asignaciones,2,',','.'),
							''
							));
			##	
			$pdf->Ln(5);
			$pdf->SetFont('Arial', 'BU', 8);
			$pdf->Cell(205, 5, strtoupper(utf8_decode('DEDUCCIONES')), 0, 1, 'C');
		}
		elseif ($f['Orden'] == '3') {
			$Aportes = 0;
			##	TOTAL DEDUCCIONES
			$pdf->SetTextColor(0, 0, 0);
			$pdf->SetDrawColor(255, 255, 255);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetFont('Arial', 'B', 8);
			$pdf->Row(array('TOTAL',
							number_format($Deducciones,2,',','.'),
							''
							));
			##	
			$pdf->Ln(5);
			$pdf->SetFont('Arial', 'BU', 8);
			$pdf->Cell(205, 5, strtoupper(utf8_decode('APORTES')), 0, 1, 'C');
		}
		$Grupo2 = $f['Orden'];
	}
	##	
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetDrawColor(255, 255, 255);
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 8);
	$pdf->Row(array(utf8_decode($f['Descripcion']),
					number_format($f['Monto'],2,',','.'),
					$f['cod_partida']
					));
	$pdf->Ln(2);
	##	
	if ($f['Orden'] == '1') {
		$Asignaciones += $f['Monto'];
	}
	elseif ($f['Orden'] == '2') {
		$Deducciones += $f['Monto'];
	}
	elseif ($f['Orden'] == '3') {
		$Aportes += $f['Monto'];
	}
	$Orden = $f['Orden'];
}
##	
$pdf->SetFont('Arial', 'B', 8);
$pdf->Ln(2);
$pdf->SetDrawColor(0,0,0);
$pdf->Line(5, $pdf->GetY(), 210, $pdf->GetY());
$pdf->Ln(2);
##	
$pdf->SetDrawColor(255, 255, 255);
$pdf->Row(array('TOTAL ASIGNACIONES',
				number_format($Asignaciones,2,',','.'),
				''
				));
$pdf->Ln(2);
$pdf->Row(array('TOTAL DEDUCCIONES',
				number_format($Deducciones,2,',','.'),
				''
				));
$pdf->Ln(2);
$pdf->Row(array('NETO A PAGAR',
				number_format(($Asignaciones - $Deducciones),2,',','.'),
				''
				));
##	
$pdf->Ln(2);
$pdf->SetDrawColor(0,0,0);
$pdf->Line(5, $pdf->GetY(), 210, $pdf->GetY());
$pdf->Ln(2);
//---------------------------------------------------
//	TOTAL GENERAL
$Grupo = '';
$FlagTotal = true;
##	
$pdf->AddPage();
$sql = "(SELECT
			pc.CodConcepto,
			pc.Descripcion,
			SUM(ptnec.Monto) AS Monto,
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
			SUM(ptnec.Monto) AS Monto,
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
			SUM(ptnec.Monto) AS MontoAde,
			cpd.cod_partida,
			'3' AS Orden
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
		ORDER BY Orden, CodConcepto";
$field = getRecords($sql);
foreach($field as $f) {
	if ($Grupo != $f['Orden']) {
		$Grupo = $f['Orden'];
		if ($f['Orden'] == '1') {
			$Asignaciones = 0;
			##	
			$pdf->SetFont('Arial', 'BU', 8);
			$pdf->Cell(205, 5, strtoupper(utf8_decode('ASIGNACIONES')), 0, 1, 'C');
		}
		elseif ($f['Orden'] == '2') {
			$Deducciones = 0;
			##	TOTAL ASIGNACIONES
			$pdf->SetTextColor(0, 0, 0);
			$pdf->SetDrawColor(255, 255, 255);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetFont('Arial', 'B', 8);
			$pdf->Row(array('TOTAL',
							number_format($Asignaciones,2,',','.'),
							''
							));
			##	
			$pdf->Ln(5);
			$pdf->SetFont('Arial', 'BU', 8);
			$pdf->Cell(205, 5, strtoupper(utf8_decode('DEDUCCIONES')), 0, 1, 'C');
		}
		elseif ($f['Orden'] == '3') {
			$Aportes = 0;
			##	TOTAL DEDUCCIONES
			$pdf->SetTextColor(0, 0, 0);
			$pdf->SetDrawColor(255, 255, 255);
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetFont('Arial', 'B', 8);
			$pdf->Row(array('TOTAL',
							number_format($Deducciones,2,',','.'),
							''
							));
			##	
			$pdf->Ln(5);
			$pdf->SetFont('Arial', 'BU', 8);
			$pdf->Cell(205, 5, strtoupper(utf8_decode('APORTES')), 0, 1, 'C');
		}
	}
	##	
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetDrawColor(255, 255, 255);
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 8);
	$pdf->Row(array(utf8_decode($f['Descripcion']),
					number_format($f['Monto'],2,',','.'),
					$f['cod_partida']
					));
	$pdf->Ln(2);
	##	
	if ($f['Orden'] == '1') {
		$Asignaciones += $f['Monto'];
	}
	elseif ($f['Orden'] == '2') {
		$Deducciones += $f['Monto'];
	}
	elseif ($f['Orden'] == '3') {
		$Aportes += $f['Monto'];
	}
}
$pdf->SetTextColor(0, 0, 0);
$pdf->SetDrawColor(255, 255, 255);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 8);
if ($f['Orden'] == '1') {
	$pdf->Row(array('TOTAL',
					number_format($Asignaciones,2,',','.'),
					''
					));
}
elseif ($f['Orden'] == '2') {
	$pdf->Row(array('TOTAL',
					number_format($Deducciones,2,',','.'),
					''
					));
}
elseif ($f['Orden'] == '3') {
	$pdf->Row(array('TOTAL',
					number_format($Aportes,2,',','.'),
					''
					));
}
##	
$pdf->Ln(2);
$pdf->SetDrawColor(0,0,0);
$pdf->Line(5, $pdf->GetY(), 210, $pdf->GetY());
$pdf->Ln(2);
##	
$pdf->SetDrawColor(255, 255, 255);
$pdf->Row(array('TOTAL ASIGNACIONES',
				number_format($Asignaciones,2,',','.'),
				''
				));
$pdf->Ln(2);
$pdf->Row(array('TOTAL DEDUCCIONES',
				number_format($Deducciones,2,',','.'),
				''
				));
$pdf->Ln(2);
$pdf->Row(array('NETO A PAGAR',
				number_format(($Asignaciones - $Deducciones),2,',','.'),
				''
				));
##	
$pdf->Ln(2);
$pdf->SetDrawColor(0,0,0);
$pdf->Line(5, $pdf->GetY(), 210, $pdf->GetY());
$pdf->Ln(2);
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
