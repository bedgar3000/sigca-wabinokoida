<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$_PAGINA = "PRIMERA";
$_ENCABEZADO = true;
//---------------------------------------------------
//	consulto
$sql = "SELECT
			r.*,
			me1.CodEmpleado AS CodPreparado,
			mp1.NomCompleto AS NomPreparado,
			me2.CodEmpleado AS CodRevisado,
			mp2.NomCompleto AS NomRevisado,
			me3.CodEmpleado AS CodAprobado,
			mp3.NomCompleto AS NomAprobado,
			me4.CodEmpleado AS CodConformada,
			mp4.NomCompleto AS NomConformada,
			lc.TipoRequerimiento,
			cc.Descripcion AS NomCentroCosto,
			o.Organismo,
			d.Dependencia,
			c.Descripcion AS NomClasificacion,
			d.CodPersona AS DependenciaResponsable,
			ue.CodPersona AS CategoriaResponsable,
			ue.CodUnidadEjec,
			cp.CategoriaProg
		FROM
			lg_requerimientos r
			INNER JOIN mastpersonas mp1 ON (r.PreparadaPor = mp1.CodPersona)
			INNER JOIN mastempleado me1 ON (mp1.CodPersona = me1.CodPersona)
			INNER JOIN lg_clasificacion lc ON (r.Clasificacion = lc.Clasificacion)
			INNER JOIN ac_mastcentrocosto cc ON (r.CodCentroCosto = cc.CodCentroCosto)
			INNER JOIN mastorganismos o ON (r.CodOrganismo = o.CodOrganismo)
			INNER JOIN mastdependencias d ON (r.CodDependencia = d.CodDependencia)
			INNER JOIN lg_clasificacion c ON (r.Clasificacion = c.Clasificacion)
			LEFT JOIN mastpersonas mp2 ON (r.RevisadaPor = mp2.CodPersona)
			LEFT JOIN mastempleado me2 ON (mp2.CodPersona = me2.CodPersona)
			LEFT JOIN mastpersonas mp3 ON (r.AprobadaPor = mp3.CodPersona)
			LEFT JOIN mastempleado me3 ON (mp3.CodPersona = me3.CodPersona)
			LEFT JOIN mastpersonas mp4 ON (r.ConformadaPor = mp4.CodPersona)
			LEFT JOIN mastempleado me4 ON (mp4.CodPersona = me4.CodPersona)
			LEFT JOIN pv_presupuesto pv ON (pv.CodOrganismo = r.CodOrganismo AND pv.CodPresupuesto = r.CodPresupuesto)
			LEFT JOIN pv_categoriaprog cp On (cp.CategoriaProg = pv.CategoriaProg)
			LEFT JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
		WHERE r.CodRequerimiento = '".$registro."'";
$query = mysql_query($sql) or die ($sql.mysql_error());
if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
//---------------------------------------------------

//---------------------------------------------------
class PDF extends FPDF {
	//	Cabecera de página.
	function Header() {
		global $_PARAMETRO;
		global $field;
		global $_PAGINA;
		global $_ENCABEZADO;
		##
		$this->Image($_PARAMETRO["PATHLOGO"].'logo-alcaldia.jpg', 5, 5, 10, 10);		
		$this->SetFont('Arial', '', 8);
		$this->SetXY(15, 5); $this->Cell(100, 5, utf8_decode($field['Organismo']), 0, 1, 'L');
		$this->SetXY(15, 10); $this->Cell(100, 5, utf8_decode(getValorCampo("mastdependencias", "CodDependencia", "Dependencia", $_PARAMETRO["DEPLOGCXP"])), 0, 0, 'L');
		$this->Ln(10);
		##
		$this->SetFillColor(250, 250, 250);
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(200, 5, utf8_decode('Requerimiento Nº '.$field['CodInterno']), 0, 0, 'C');
		$this->Ln(10);
		##
		if ($_PAGINA == "PRIMERA") {
			$_PAGINA = "OTRA";
			$this->SetFont('Arial', '', 8);
			$this->Cell(25, 5, 'Dependencia: ', 0, 0, 'L', 1);
			$this->Cell(175, 5, utf8_decode($field['Dependencia']), 0, 0, 'L');
			$this->Ln(6);	
			$this->Cell(25, 5, 'Centro de Costos: ', 0, 0, 'L', 1);
			$this->Cell(120, 5, utf8_decode($field['NomCentroCosto']), 0, 0, 'L');
			$this->Cell(25, 5, utf8_decode('Clasificación: '), 0, 0, 'L', 1);
			$this->Cell(75, 5, utf8_decode($field['NomClasificacion']), 0, 0, 'L');
			$this->Ln(6);
			$this->Cell(25, 5, 'Dirigido A: ', 0, 0, 'L', 1);
			$this->Cell(120, 5, utf8_decode(printValores("DIRIGIDO", $field['TipoClasificacion'])), 0, 0, 'L');
			$this->Cell(25, 5, 'Prioridad: ', 0, 0, 'L', 1);
			$this->Cell(75, 5, utf8_decode(printValores("PRIORIDAD", $field['Prioridad'])), 0, 0, 'L');
			$this->Ln(6);
			$this->Cell(25, 5, 'Fecha Requerida: ', 0, 0, 'L', 1);
			$this->Cell(120, 5, formatFechaDMA($field['FechaRequerida']), 0, 0, 'L');
			$this->Cell(25, 5, 'Estado: ', 0, 0, 'L', 1);
			$this->SetFont('Arial', 'B', 8);
			$this->Cell(75, 5, utf8_decode(printValores("ESTADO-REQUERIMIENTO", $field['Estado'])), 0, 0, 'L');
			$this->SetFont('Arial', '', 8);
			$this->Ln(6);
			$this->Cell(25, 5, 'Comentarios: ', 0, 0, 'L', 1);
			$this->MultiCell(175, 4, utf8_decode($field['Comentarios']), 0, 'J');
			$this->Ln(5);
		}
		##
		if ($_ENCABEZADO) {
			$this->SetDrawColor(0, 0, 0); $this->SetFillColor(255, 255, 255); $this->SetTextColor(0, 0, 0);
			$this->SetFont('Arial', 'B', 8);
			$this->SetWidths(array(10, 20, 20, 120, 15, 15));
			$this->SetAligns(array('R', 'C', 'C', 'L', 'C', 'R'));
			$this->Row(array('#', 'CODIGO', 'ITEM', 'DESCRIPCION', 'UNI.', 'CANT.'));
			$this->Ln(1);
		}
	}
	
	//	Pie de página.
	function Footer() {
		global $field;
		global $_PAGINA;
		global $_PARAMETRO;
		##
		$this->SetDrawColor(0, 0, 0);
		$this->SetFillColor(0, 0, 0);
		if ($_PAGINA == "ULTIMA") {
			##	
			list($_SOLICITADO['Nombre'], $_SOLICITADO['Cargo'], $_SOLICITADO['Nivel']) = getFirma($field['CategoriaResponsable']);
			$SolicitadoPor = getUnidadEjecutora($field["CategoriaProg"]);
			$RevisadoPor = getUnidadEjecutora($_PARAMETRO["CATPV"]);
			$ConformadoPor = getUnidadEjecutora($_PARAMETRO["CATADM"]);
			$AprobadoPor = getUnidadEjecutora($_PARAMETRO["CATCOMPRAS"]);
			##	
			$this->SetY(225);
			$y=$this->GetY();
			$this->Rect(17, $y-2, 65, 0.1, "DF");
			$this->SetXY(17, 225); $this->Cell(20, 4, 'Solicitado Por:', 0, 0, 'R'); $this->MultiCell(60, 4, utf8_decode($_SOLICITADO['Nombre']), 0, 'L');
			$this->SetXY(17, 229); $this->Cell(20, 4, '', 0, 0, 'R'); $this->MultiCell(70, 4, utf8_decode($SolicitadoPor), 0, 'L');
			$y=$this->GetY();
			$this->SetXY(17, $y); $this->Cell(20, 4, 'Fecha:', 0, 0, 'R'); $this->Cell(80, 4, '', 0, 1, 'L');
			##
			$this->SetY(225);
			$y=$this->GetY();
			$this->Rect(130, $y-2, 65, 0.1, "DF");
			$this->SetXY(130, 225); $this->Cell(20, 4, 'Revisado Por:', 0, 0, 'R'); $this->MultiCell(55, 4, utf8_decode($RevisadoPor), 0, 'L');
			$y=$this->GetY();
			$this->SetXY(130, $y); $this->Cell(20, 4, 'Fecha:', 0, 0, 'R'); $this->Cell(80, 4, '', 0, 1, 'L');
			##
			$this->SetY(250);
			$y=$this->GetY();
			$this->Rect(17, $y-2, 65, 0.1, "DF");	
			$this->SetXY(17, 250); $this->Cell(20, 4, 'Conformado Por:', 0, 0, 'R'); $this->MultiCell(60, 4, utf8_decode($ConformadoPor), 0, 'L');
			$y=$this->GetY();
			$this->SetXY(17, $y); $this->Cell(20, 4, 'Fecha:', 0, 0, 'R'); $this->Cell(80, 4, '', 0, 1, 'L');
			##
			$this->SetY(250);
			$y=$this->GetY();
			$this->Rect(130, $y-2, 65, 0.1, "DF");
			$this->SetXY(130, 250); $this->Cell(20, 4, 'Aprobado Por:', 0, 0, 'R'); $this->MultiCell(55, 4, utf8_decode($AprobadoPor), 0, 'L');
			$y=$this->GetY();
			$this->SetXY(130, $y); $this->Cell(20, 4, 'Fecha:', 0, 0, 'R'); $this->Cell(80, 4, '', 0, 1, 'L');
		}
	}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada.
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(10, 10, 5);
$pdf->SetAutoPageBreak(5, 75);
$pdf->AddPage();
//---------------------------------------------------
$pdf->SetTextColor(0, 0, 0);
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetFillColor(255, 255, 255);
//	Detalles
$sql = "SELECT 
			rd.*,
			i.CodInterno
		FROM 
			lg_requerimientosdet rd
			LEFT JOIN lg_itemmast i ON (rd.CodItem = i.CodItem)
		WHERE rd.CodRequerimiento = '".$registro."'
		ORDER BY Secuencia";
$query_det = mysql_query($sql) or die ($sql.mysql_error()); $i=0;
while ($field_det = mysql_fetch_array($query_det)) { $i++;
	if ($field_det['CodItem'] != "") $codigo = $field_det['CodItem']; else $codigo = $field_det['CommoditySub'];
	$pdf->Ln(2);
	$pdf->SetDrawColor(255, 255, 255); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont('Arial', '', 8);
	$pdf->SetWidths(array(10, 20, 20, 120, 15, 15));
	$pdf->SetAligns(array('R', 'C', 'C', 'L', 'C', 'R'));
	$pdf->Row(array($i,
					$field_det['CodInterno'],
					$codigo,
					utf8_decode($field_det['Descripcion']),
					$field_det['CodUnidad'],
					$field_det['CantidadPedida']));
}
$_ENCABEZADO = false;
##
$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(0, 0, 0); $pdf->SetTextColor(0, 0, 0);
$y=$pdf->GetY();
$pdf->Rect(10, $y, 200, 0.1, "DF");
$pdf->Ln(2);
$pdf->SetFont('Arial', 'B', 7);
$pdf->Cell(200, 5, utf8_decode('Total de Items: '.$i), 0, 0, 'L');
##
if ($field['Clasificacion'] != "STO") {
	//	distribucion contable
	$pdf->Ln(20);
	$pdf->SetFillColor(250, 250, 250);
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->Cell(200, 5, utf8_decode('Distribución Contable'), 0, 0, 'C');
	$pdf->Ln(5);
	$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->SetWidths(array(20, 165, 15));
	$pdf->SetAligns(array('C', 'L', 'R'));
	$pdf->Row(array('CUENTA', 'DESCRIPCION', '%'));
	$pdf->Ln(1);
	##
	if ($_PARAMETRO['CONTORDENDIS'] == "T") {
		$sql = "SELECT
					rd.CodCuenta,
					c.Descripcion,
					(SELECT COUNT(*)
					 FROM lg_requerimientosdet
					 WHERE
						CodCuenta = rd.CodCuenta AND
						CodRequerimiento = rd.CodRequerimiento) AS Numero,
					(SELECT COUNT(*)
					 FROM lg_requerimientosdet
					 WHERE CodRequerimiento = rd.CodRequerimiento) AS Total
				FROM
					lg_requerimientosdet rd
					INNER JOIN ac_mastplancuenta c ON (rd.CodCuenta = c.CodCuenta)
				WHERE rd.CodRequerimiento = '".$registro."'
				GROUP BY CodCuenta";
	}
	elseif ($_PARAMETRO['CONTORDENDIS'] == "F") {
		$sql = "SELECT
					rd.CodCuentaPub20 AS CodCuenta,
					c.Descripcion,
					(SELECT COUNT(*)
					 FROM lg_requerimientosdet
					 WHERE
						CodCuentaPub20 = rd.CodCuentaPub20 AND
						CodRequerimiento = rd.CodRequerimiento) AS Numero,
					(SELECT COUNT(*)
					 FROM lg_requerimientosdet
					 WHERE CodRequerimiento = rd.CodRequerimiento) AS Total
				FROM
					lg_requerimientosdet rd
					INNER JOIN ac_mastplancuenta20 c ON (rd.CodCuentaPub20 = c.CodCuenta)
				WHERE rd.CodRequerimiento = '".$registro."'
				GROUP BY CodCuentaPub20";
	}
	$query_cuentas = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while ($field_cuentas = mysql_fetch_array($query_cuentas)) {
		$Porcentaje = $field_cuentas['Numero'] * 100 / $field_cuentas['Total'];
		$pdf->SetDrawColor(255, 255, 255); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
		$pdf->SetFont('Arial', '', 8);
		$pdf->Row(array($field_cuentas['CodCuenta'],
						utf8_decode($field_cuentas['Descripcion']),
						number_format($Porcentaje, 2, ',', '.')));
	}
	##
	//	distribucion presupuestaria
	$pdf->Ln(10);
	$pdf->SetFillColor(250, 250, 250);
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->Cell(200, 5, utf8_decode('Distribución Presupuestaria'), 0, 0, 'C');
	$pdf->Ln(5);
	$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->SetWidths(array(20, 10, 20, 135, 15));
	$pdf->SetAligns(array('C', 'C', 'C', 'L', 'R'));
	$pdf->Row(array('CAT. PROG.', 'F.F.', 'PARTIDA', 'DESCRIPCION', '%'));
	$pdf->Ln(1);
	##
	$sql = "SELECT
				rd.cod_partida,
				c.denominacion,
				rd.CodFuente,
				pv.CategoriaProg,
				CONCAT(ss.CodSector, pr.CodPrograma, a.CodActividad) AS CatProg,
				(SELECT COUNT(*)
				 FROM lg_requerimientosdet
				 WHERE
					cod_partida = rd.cod_partida AND
					CodRequerimiento = rd.CodRequerimiento) AS Numero,
				(SELECT COUNT(*)
				 FROM lg_requerimientosdet
				 WHERE CodRequerimiento = rd.CodRequerimiento) AS Total
			FROM
				lg_requerimientosdet rd
				INNER JOIN pv_partida c ON (rd.cod_partida = c.cod_partida)
				LEFT JOIN pv_presupuesto pv ON (pv.CodOrganismo = rd.CodOrganismo AND pv.CodPresupuesto = rd.CodPresupuesto)
				LEFT JOIN pv_categoriaprog cp ON (cp.CategoriaProg = pv.CategoriaProg)
				LEFT JOIN pv_actividades a ON (a.IdActividad = cp.IdActividad)
				LEFT JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
				LEFT JOIN pv_subprogramas spr On (spr.IdSubPrograma = py.IdSubPrograma)
				LEFT JOIN pv_programas pr ON (pr.IdPrograma = spr.IdPrograma)
				LEFT JOIN pv_subsector ss On (ss.IdSubSector = pr.IdSubSector)
			WHERE rd.CodRequerimiento = '".$registro."'
			GROUP BY rd.CodOrganismo, rd.CodFuente, cod_partida";
	$query_partidas = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while ($field_partidas = mysql_fetch_array($query_partidas)) {
		$Porcentaje = $field_partidas['Numero'] * 100 / $field_partidas['Total'];
		$pdf->SetDrawColor(255, 255, 255); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
		$pdf->SetFont('Arial', '', 8);
		$pdf->Row(array($field_partidas['CatProg'],
						$field_partidas['CodFuente'],
						$field_partidas['cod_partida'],
						utf8_decode($field_partidas['denominacion']),
						number_format($Porcentaje, 2, ',', '.')));
	}
}
##
$_PAGINA = "ULTIMA";
//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>