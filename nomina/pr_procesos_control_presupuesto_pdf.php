<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$filtro = '';
if ($fCodOrganismo) $filtro .= " AND (tnec.CodOrganismo = '$fCodOrganismo')";
if ($fCodTipoNom) $filtro .= " AND (tnec.CodTipoNom = '$fCodTipoNom')";
if ($fPeriodo) $filtro .= " AND (tnec.Periodo = '$fPeriodo')";
if ($fCodTipoProceso) $filtro .= " AND (tnec.CodTipoProceso = '$fCodTipoProceso')";
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
			pp.CodOrganismo = '$fCodOrganismo' AND 
			pp.CodTipoNom = '$fCodTipoNom' AND 
			pp.Periodo = '$fPeriodo' AND 
			pp.CodTipoProceso = '$fCodTipoProceso'";
$field_proceso = getRecord($sql);
//---------------------------------------------------

class PDF extends FPDF {
	//	Cabecera de página.
	function Header() {
		global $_PARAMETRO;
		global $field_proceso;
		global $f;
		global $FechaActual;
		global $_POST;
		extract($_POST);
		##	
		$Logo = getValorCampo("mastorganismos", "CodOrganismo", "Logo", $field_proceso['CodOrganismo']);
		$NomOrganismo = getValorCampo("mastorganismos", "CodOrganismo", "Organismo", $field_proceso['CodOrganismo']);
		$NomDependencia = getValorCampo("mastdependencias", "CodDependencia", "Dependencia", $_PARAMETRO["DEPRHPR"]);
		##
		$this->SetFillColor(255, 255, 255);
		$this->SetDrawColor(0, 0, 0);
		$this->SetFont('Arial', 'B', 8);
		$this->Image($_PARAMETRO["PATHLOGO"].$Logo, 10, 7, 18, 15);
		$this->SetXY(30,10); $this->Cell(205, 5, strtoupper(utf8_decode($NomOrganismo)), 0, 1, 'L');
		$this->SetXY(30,15); $this->Cell(205, 5, strtoupper(utf8_decode($NomDependencia)), 0, 1, 'L');
		##	-------------------
		$this->SetFont('Arial', '', 7);
		$this->SetXY(175,10); $this->Cell(10, 5, utf8_decode('Fecha: '), 0, 0, 'L');
		$this->Cell(30, 5, formatFechaDMA($FechaActual), 0, 1, 'L');
		$this->SetXY(175,15); $this->Cell(10, 5, utf8_decode('Página: '), 0, 0, 'L');
		$this->Cell(30, 5, $this->PageNo().' de {nb}', 0, 1, 'L');
		##	-------------------
		$this->SetY(25); 
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(200, 5, utf8_decode('DISTRIBUCIÓN PRESUPUESTARIA'), 0, 1, 'C');
		$this->SetY(35); 
		$this->SetTextColor(0,0,0);
		$this->SetDrawColor(0,0,0);
		$this->SetFillColor(235,235,235);
		$this->SetFont('Arial', 'B', 8);
		$this->SetWidths(array(15,15,25,95,25,25));
		$this->SetAligns(array('C','C','C','L','R','R'));
		$this->Row(array(utf8_decode('CAT. PROG'),
						 utf8_decode('F.F.'),
						 utf8_decode('PARTIDA'),
						 utf8_decode('DENOMINACIÓN'),
						 utf8_decode('MONTO'),
						 utf8_decode('DISPONIBLE')
						 ));
		$this->Ln(1);
	}
	
	//	Pie de página.
	function Footer() {}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada.
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(25, 1);
//---------------------------------------------------
$Grupo1 = '';
##	
$sql = "SELECT
			(CASE WHEN cpd.FlagCategoriaProg = 'S' THEN cpd.CategoriaProg ELSE e.CategoriaProg END) AS CategoriaProg,
			CONCAT(ss.CodSector, pr.CodPrograma, a.CodActividad) AS CatProg,
			c.CodConcepto,
			c.Descripcion AS Concepto,
			c.Tipo,
			cpd.cod_partida,
			pt.denominacion,
			a.Denominacion AS Actividad,
			ue.Denominacion AS UnidadEjecutora,
			SUM(tnec.Monto) AS Monto,
			ppd.CodFuente,
			COALESCE(ppd.MontoAjustado,0) AS MontoAjustado,
			COALESCE(ppd.MontoCompromiso,0) AS MontoCompromiso,
			(COALESCE(ppd.MontoAjustado,0) - COALESCE(ppd.MontoCompromiso,0)) AS MontoDisponible
		FROM
			pr_tiponominaempleadoconcepto tnec
			INNER JOIN pr_concepto c ON (c.CodConcepto = tnec.CodConcepto)
			INNER JOIN mastempleado e ON (e.CodPersona = tnec.CodPersona)
			INNER JOIN tiponomina tn ON (tn.CodTipoNom = tnec.CodTipoNom)
			INNER JOIN pr_conceptoperfil cp ON (cp.CodPerfilConcepto = tn.CodPerfilConcepto)
			INNER JOIN pr_conceptoperfildetalle cpd ON (cpd.CodPerfilConcepto = cp.CodPerfilConcepto
														AND cpd.CodTipoProceso = tnec.CodTipoProceso
														AND cpd.CodConcepto = tnec.CodConcepto)
			INNER JOIN pv_partida pt ON (pt.cod_partida = cpd.cod_partida)
			INNER JOIN pv_presupuesto pp ON (pp.CodOrganismo = tnec.CodOrganismo
											 AND ((pp.CategoriaProg = cpd.CategoriaProg AND cpd.FlagCategoriaProg = 'S')
												   OR (pp.CategoriaProg = e.CategoriaProg AND cpd.FlagCategoriaProg <> 'S'))
											 AND pp.Ejercicio = SUBSTRING(tnec.Periodo, 1, 4))
			INNER JOIN pv_presupuestodet ppd ON (ppd.CodOrganismo = pp.CodOrganismo
												 AND ppd.CodPresupuesto = pp.CodPresupuesto
												 AND ppd.cod_partida = cpd.cod_partida
												 AND ppd.CodFuente = '$_PARAMETRO[FFMETASDEF]')
			INNER JOIN pv_categoriaprog cpg ON (cpg.CategoriaProg = pp.CategoriaProg)
			INNER JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cpg.CodUnidadEjec)
			INNER JOIN pv_actividades a ON (a.IdActividad = cpg.IdActividad)
			INNER JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
			INNER JOIN pv_subprogramas spr ON (spr.IdSubPrograma = py.IdSubPrograma)
			INNER JOIN pv_programas pr ON (pr.IdPrograma = spr.IdPrograma)
			INNER JOIN pv_subsector ss ON (ss.IdSubSector = pr.IdSubSector)
		WHERE 1 $filtro
		GROUP BY CategoriaProg, cod_partida
		ORDER BY CategoriaProg, cod_partida";
$field = getRecords($sql);
foreach ($field as $f) {
	if ($Grupo1 != $f['CategoriaProg']) {
		if (!$Grupo1) $pdf->AddPage();
		$Grupo1 = $f['CategoriaProg'];
	}
	$pdf->Ln(2);
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetDrawColor(255, 255, 255);
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 8);
	$pdf->Row(array(utf8_decode($f['CatProg']),
					utf8_decode($f['CodFuente']),
					utf8_decode($f['cod_partida']),
					utf8_decode($f['denominacion']),
					number_format($f['Monto'],2,',','.'),
					number_format($f['MontoDisponible'],2,',','.')
					));
}
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
