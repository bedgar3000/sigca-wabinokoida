<?php
define('FPDF_FONTPATH','font/');
require('mc_table_eventos.php');
include("../lib/fphp.php");
//---------------------------------------------------
$filtro=strtr($filtro, "*", "'");
$filtro=strtr($filtro, ";", "%");
$filtro_evento=strtr($filtro_evento, "*", "'");
$filtro_evento=strtr($filtro_evento, ";", "%");
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada
$pdf=new PDF_MC_Table();
$pdf->AliasNbPages();
$pdf->Open();
$pdf->SetMargins(5, 5, 5);
$pdf->SetAutoPageBreak(1, 30);

list($dd, $md, $ad)=SPLIT( '[/.-]', $fingresod); $mesd = (int) $md;
list($dh, $mh, $ah)=SPLIT( '[/.-]', $fingresoh); $mesh = (int) $mh;

if ($md == $mh) $periodo = "Del ".$dd." Al ".$dh." De ".getNombreMes("$ad-$md");
else $periodo = "Del ".$dd." de ".getNombreMes("$ad-$md")." Al ".$dh." De ".getNombreMes("$ah-$mh");
$pdf->SetPeriodo($periodo);


$pdf->AddPage('P', 'Letter');
//	Cuerpo
if ($fcargo == "") {
	$sql = "SELECT
				mp.Apellido1,
				mp.Apellido2,
				mp.Nombres,
				mp.Ndocumento,
				me.CodPersona,
				me.CodEmpleado,
				en.CodDependencia,
				me.CodCarnetProv,
				me.CodPerfil,
				md.Dependencia,
				rp.DescripCargo
			FROM
				mastempleado me
				INNER JOIN mastpersonas mp ON (me.CodPersona = mp.CodPersona)
				INNER JOIN rh_empleadonivelacion en ON (me.CodPersona = en.CodPersona)
				INNER JOIN mastdependencias md ON (en.CodDependencia = md.CodDependencia)
				INNER JOIN rh_puestos rp ON (en.CodCargo = rp.CodCargo)
			WHERE 
				en.Secuencia = (SELECT MAX(Secuencia) FROM rh_empleadonivelacion WHERE CodPersona = me.CodPersona)
				$filtro
			ORDER BY CodDependencia, CodEmpleado";
} else {
	$sql = "SELECT 
				mp.Apellido1,
				mp.Apellido2,
				mp.Nombres,
				mp.Ndocumento, 
				me.CodEmpleado, 
				en.CodDependencia, 
				me.CodCarnetProv,
				me.CodPerfil,
				md.Dependencia, 
				rp.DescripCargo
			FROM 
				mastempleado me 
				INNER JOIN mastpersonas mp ON (me.CodPersona = mp.CodPersona) 
				INNER JOIN rh_empleadonivelacion en ON (me.CodPersona = en.CodPersona)
			  	INNER JOIN mastdependencias md ON (en.CodDependencia = md.CodDependencia)
				INNER JOIN rh_puestos rp ON (en.CodCargo = rp.CodCargo)
				INNER JOIN rh_cargoreporta rcc ON (rcc.CodCargo = en.CodCargo AND rcc.CargoReporta = '".$fcargo."') 
			WHERE 
				en.Secuencia = (SELECT MAX(Secuencia) FROM rh_empleadonivelacion WHERE CodPersona = me.CodPersona)
				$filtro
			ORDER BY CodDependencia, CodEmpleado";
}
$query_empleado = mysql_query($sql) or die ($sql.mysql_error());
while ($field_empleado = mysql_fetch_array($query_empleado)) {
	if ($field_empleado['Apellido1'] != "") $Apellido = $field_empleado['Apellido1']; else $Apellido = $field_empleado['Apellido2'];
	$codempleado = $field_empleado['CodEmpleado'];
	$sql = "SELECT ce.*
			FROM rh_controlasistencia ce
			WHERE ce.CodPersona = '".$codempleado."' AND ce.Estado = 'P' $filtro_evento 
			ORDER BY ce.FechaFormat, ce.HoraFormat, ce.Event_Puerta";
	$query_eventos = mysql_query($sql) or die ($sql.mysql_error());
	if (mysql_num_rows($query_eventos) != 0) {
		if ($dependencia != $field_empleado['CodDependencia']) {
			$dependencia = $field_empleado['CodDependencia'];
			$pdf->SetFont('Arial', 'B', 10);
			$pdf->Cell(190, 6, utf8_decode($field_empleado['Dependencia']), 0, 1, 'L');	
		}
		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell(5, 6);
		$pdf->Cell(18, 6, $field_empleado['CodEmpleado'], 0, 0, 'L');
		$pdf->Cell(18, 6, number_format($field_empleado['Ndocumento'], 0, '', '.'), 0, 0, 'R');	
		$pdf->Cell(80, 6, utf8_decode(substr($field_empleado['Nombres'].', '.$Apellido, 0, 50)), 0, 0, 'L');	
		$pdf->Cell(75, 6, utf8_decode(substr($field_empleado['DescripCargo'], 0, 50)), 0, 1, 'L');
		while ($field_eventos = mysql_fetch_array($query_eventos)) {
			if ($hora != $field_eventos['Hora']) {
				$pdf->SetFont('Arial', '', 10);
				$pdf->Cell(10, 6);
				$pdf->Cell(30, 6, $field_eventos['Fecha'], 0, 0, 'L');
				$pdf->Cell(30, 6, $field_eventos['Hora'], 0, 0, 'L');
				$pdf->Cell(70, 6, $field_eventos['Event_Puerta'], 0, 0, 'C');
				$pdf->Cell(50, 6, '_________________________', 0, 1, 'C');
			}
			$hora = $field_eventos['Hora'];
		}
		$pdf->Ln(7);
	}
}
//---------------------------------------------------
$pdf->Output();
?>