<?php
include("../lib/fphp.php");
include("lib/fphp.php");
##	
$sql = "SELECT
			c.*,
			p.CodPersona,
			p.NomCompleto,
			p.Ndocumento,
			e.CodEmpleado,
			e.CodOrganismo,
			o.Organismo,
			tc.TipoContrato,
			fc.RutaPlant
		FROM
			rh_contratos c
			INNER JOIN mastpersonas p ON (p.CodPersona = c.CodPersona)
			INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
			INNER JOIN mastorganismos o On (o.CodOrganismo = e.CodOrganismo)
			LEFT JOIN rh_formatocontrato fc ON (fc.CodFormato = c.CodFormato)
			LEFT JOIN rh_tipocontrato tc ON (tc.TipoContrato = fc.TipoContrato)
		WHERE c.CodContrato = '$CodContrato'";
$field = getRecord($sql);
##	
$archivo = fopen($_PARAMETRO['PATHSIA'].$_PARAMETRO["PATHFORM"].$field["RutaPlant"], "r");
if ($archivo) {
	##	
	while (!feof($archivo)) $texto .= fgets($archivo, 255);
	##	
	//$texto = ereg_replace("_EMPLEADO_NOMBRE_", "{\\b ".$field['NomCompleto']." }", $texto);
	$search = array(
		"EMPLEADO_NOMBRE",
		"EMPLEADO_CEDULA",
		"ORGANISMO_NOMBRE",
		"FECHA_DESDE",
		"FECHA_HASTA"
	);
	$replace = array(
		utf8_decode($field['NomCompleto']),
		number_format($field['Ndocumento'],0,'','.'),
		utf8_decode($field['Organismo']),
		formatFechaDMA($field['FechaDesde']),
		formatFechaDMA($field['FechaHasta'])
	);
	$cuerpo = str_replace($search, $replace, $texto);
	##	
	header('Content-type: application/msword');
	header('Content-Disposition: inline; filename='.$field['RutaPlant']);
	$output = "{\\rtf1";
	$output .= $cuerpo;
	$output .= "}";
	echo $output;
}
?>