<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$LF = 0x0A;
$CR = 0x0D;
$nl = sprintf("%c%c",$CR,$LF);
//---------------------------------------------------
list($CodOrganismo, $CodViatico) = explode("_", $sel_registros);
##	consulto datos generales
$sql = "SELECT
			v.*,
			p1.NomCompleto AS NomEmpleado,
			p1.Ndocumento,
			p1.Sexo,
			e1.CodEmpleado,
			p2.NomCompleto AS NomPreparadoPor,
			p3.NomCompleto AS NomRevisadoPor,
			p4.NomCompleto AS NomGeneradoPor
		FROM
			ap_viaticos v
			INNER JOIN mastpersonas p1 ON (p1.CodPersona = v.CodPersona)
			LEFT JOIN mastempleado e1 ON (e1.CodPersona = v.CodPersona)
			LEFT JOIN mastpersonas p2 ON (p2.CodPersona = v.PreparadoPor)
			LEFT JOIN mastpersonas p3 ON (p3.CodPersona = v.RevisadoPor)
			LEFT JOIN mastpersonas p4 ON (p4.CodPersona = v.GeneradoPor)
		WHERE
			v.CodOrganismo = '".$CodOrganismo."' AND
			v.CodViatico = '".$CodViatico."'";
$field = getRecord($sql);
##	consulto conceptos
$sql = "SELECT *
		FROM ap_viaticosdetalle
		WHERE
			CodOrganismo = '".$CodOrganismo."' AND
			CodViatico = '".$CodViatico."'
		ORDER BY Articulo, Numeral";
$field_conceptos = getRecords($sql);
//---------------------------------------------------

class PDF extends FPDF {
	//	Cabecera de página.
	function Header() {
		global $_PARAMETRO;
		global $Ahora;
		global $field;
		global $_POST;
		extract($_POST);
		##
		$Logo = getValorCampo("mastorganismos", "CodOrganismo", "Logo", $_SESSION['ORGANISMO_ACTUAL']);
		$NomOrganismo = getValorCampo("mastorganismos", "CodOrganismo", "Organismo", $_SESSION['ORGANISMO_ACTUAL']);
		$NomDependencia = getValorCampo("mastdependencias", "CodDependencia", "Dependencia", $_PARAMETRO["DEPLOGCXP"]);
		##
		$this->SetTextColor(0, 0, 0);
		$this->SetFillColor(255, 255, 255);
		$this->SetDrawColor(0, 0, 0);
		$this->Image($_PARAMETRO["PATHLOGO"].$Logo, 3, 3, 10, 10);
		$this->SetFont('Arial', '', 8);
		$this->SetXY(12, 3); $this->Cell(100, 5, $NomOrganismo, 0, 1, 'L');
		$this->SetXY(12, 8); $this->Cell(100, 5, $NomDependencia, 0, 0, 'L');
		$this->SetFont('Arial', 'B', 9);
		$this->Ln(10);
		##
		$this->Cell(272, 5, utf8_decode('HOJA DE CALCULOS PARA VIATICOS N° '.$field['CodInterno'].'-'.$field['Anio']), 1, 1, 'L');
		##	resolucion
		$this->SetFont('Arial', '', 9);
		$this->MultiCell(272, 5, utf8_decode($field['DescripcionGral']), 1, 'J');
		##	datos empleado
		$this->SetWidths(array(80,25,32,90,45));
		$this->SetAligns(array('L','C','C','L','C'));
		$this->SetFont('Arial', 'B', 9);
		$this->Row(array('FUNCIONARIO',utf8_decode('CÉDULA'),'FECHA DEL VIAJE','CARGO',utf8_decode('FECHA DE ELABORACIÓN')));
		//list($_FUNCIONARIO['Nombre'], $_FUNCIONARIO['Cargo']) = getFirma($field['CodPersona']);
		$field['DescripCargo'] = str_replace("(A)", "", $field['DescripCargo']);
		if ($field['Sexo'] == "M") {
		} else {
			$field['DescripCargo'] = str_replace("JEFE", "JEFA", $field['DescripCargo']);
			$field['DescripCargo'] = str_replace("DIRECTOR", "DIRECTORA", $field['DescripCargo']);
			$field['DescripCargo'] = str_replace("CONTRALOR", "CONTRALORA", $field['DescripCargo']);
			$field['DescripCargo'] = trim($field['DescripCargo']);
		}
		$this->SetFont('Arial', '', 9);
		$this->Row(array(utf8_decode($field['NomEmpleado']),
						 number_format($field['Ndocumento'], 0, '', '.'),
						 formatFechaDMA($field['Fecha']),
						 //utf8_decode($_FUNCIONARIO['Cargo']),
						 utf8_decode($field['DescripCargo'].(($field['FlagCargoEncargado']=='S')?' (E)':'')),
						 formatFechaDMA($field['FechaPreparado'])
		));
		##	MOTIVO
		$this->MultiCell(272, 5, 'MOTIVO: '.utf8_decode($field['Motivo']), 1, 'J');
		##	
		$y = $this->GetY();
		$this->SetFont('Arial', 'B', 9);
		$this->SetXY(3, $y); $this->MultiCell(105, 10, utf8_decode('CONCEPTO DE VIÁTICO'), 1, 'L');
		$this->SetXY(108, $y); $this->MultiCell(92, 5, utf8_decode('MONTO UNITARIO'), 1, 'C');
		$this->SetXY(108, $y+5); $this->MultiCell(30.5, 5, utf8_decode('Unid. Viático'), 1, 'C');
		$this->SetXY(138.5, $y+5); $this->MultiCell(30.5, 5, utf8_decode('Unid. Tributaria'), 1, 'C');
		$this->SetXY(169, $y+5); $this->MultiCell(31, 5, utf8_decode('Monto Viático'), 1, 'C');
		$this->SetXY(200, $y); $this->MultiCell(30, 5, utf8_decode('DIAS DE COMISIÓN'), 1, 'C');
		$this->SetXY(230, $y); $this->MultiCell(45, 10, utf8_decode('MONTO TOTAL'), 1, 'R');
	}
	
	//	Pie de página.
	function Footer() {}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada.
$pdf = new PDF('L', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(3, 3, 3);
$pdf->SetAutoPageBreak(1, 3);
$pdf->AddPage();
//---------------------------------------------------
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetDrawColor(0, 0, 0);
##	conceptos
foreach($field_conceptos as $f) {
	if ($Grupo != $f['Articulo'].$f['Numeral']) {
		$Grupo = $f['Articulo'].$f['Numeral'];
		$pdf->SetFont('Arial', 'B', 9);
		$pdf->SetWidths(array(272));
		$pdf->SetAligns(array('L'));
		$pdf->Row(array(utf8_decode('Articulo '.$f['Articulo'].' Numeral '.$f['Numeral'])));
	}
	$pdf->SetFont('Arial', '', 9);
	$pdf->SetWidths(array(105,30.5,30.5,31,30,45));
	$pdf->SetAligns(array('L','R','R','R','C','R'));
	$pdf->Row(array(utf8_decode($f['Descripcion']),
					number_format($f['ValorUT'], 2, ',', '.'),
					number_format($f['UnidadTributaria'], 2, ',', '.'),
					number_format($f['MontoViatico'], 2, ',', '.'),
					number_format($f['CantidadDias'], 2, ',', '.'),
					number_format($f['MontoTotal'], 2, ',', '.')
	));
}
$pdf->SetFillColor(225, 225, 225);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(227, 5, 'TOTAL', 1, 0, 'C', 1);
$pdf->Cell(45, 5, number_format($field['Monto'], 2, ',', '.'), 1, 1, 'R', 1);
##	firmas
list($_PREPARADO['Nombre'], $_PREPARADO['Cargo'], $_PREPARADO['Nivel']) = getFirma($field['PreparadoPor']);
list($_AUTORIZADO['Nombre'], $_AUTORIZADO['Cargo'], $_AUTORIZADO['Nivel']) = getFirma(getPersonaUnidadEjecutora($_PARAMETRO["CATMAX"]));
$_REVISADO = getUnidadEjecutora($_PARAMETRO["CATPV"]);
$_CONFORMADO = getUnidadEjecutora($_PARAMETRO["CATADM"]);

$Preparado = " ELABORADO POR: $nl $nl $nl $_PREPARADO[Nivel] $_PREPARADO[Nombre] $nl $_PREPARADO[Cargo]";
$Autorizado = " AUTORIZADO POR: $nl $nl $nl $_AUTORIZADO[Nivel] $_AUTORIZADO[Nombre] $nl $_AUTORIZADO[Cargo]";
$Revisado = " REVISADO POR: $nl $nl $nl $_REVISADO";
$Conformado = " CONFORMADO POR: $nl $nl $nl $_CONFORMADO";
##
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', '', 9);
$pdf->SetWidths(array(68,68,68,68));
$pdf->SetAligns(array('C','C','C','C'));
$pdf->SetHeights(array(5, 4.5, 30));
$pdf->Row(array(utf8_decode($Preparado),
				utf8_decode($Autorizado),
				utf8_decode($Revisado),
				utf8_decode($Conformado)
));
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
