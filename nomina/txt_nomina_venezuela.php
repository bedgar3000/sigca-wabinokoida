<?php
session_start();
header('Content-Type: text/html; charset=iso-8859-1');
set_time_limit(-1);
include("fphp_nomina.php");
connect();
$texto="";
$archivo=fopen($nombre_archivo.".txt", "w+");
//---------------
$LF = 0x0A;
$CR = 0x0D;
$nl = sprintf("%c%c",$CR,$LF);
$fecha=date("d/m/y");
//---------------

//---------------
$filtro = '';
if (trim($fCodTipoNom) != '') $filtro = " AND ptne.CodTipoNom = '$fCodTipoNom'";
$sql = "SELECT
			mp.Ndocumento,
			CONCAT(mp.Apellido1, ' ', mp.Apellido2, ' ', mp.Nombres) AS Busqueda,
			mp.Apellido1,
			mp.Apellido2, 
			mp.Nombres,
			mp.Nacionalidad,
			ptne.TotalNeto,
			bp.Ncuenta,
			bp.TipoCuenta,
			rbp.CodBeneficiario,
			rbp.NroDocumento,
			rbp.NombreCompleto
		FROM
			pr_tiponominaempleado ptne
			INNER JOIN mastpersonas mp ON (ptne.CodPersona = mp.CodPersona)
			INNER JOIN bancopersona bp ON (ptne.CodPersona = bp.CodPersona AND bp.FlagPrincipal = 'S')
			LEFT JOIN rh_beneficiariopension rbp ON (mp.CodPersona = rbp.CodPersona)
		WHERE
			ptne.CodTipoProceso = '".$codproceso."' AND
			ptne.Periodo = '".$periodo."' AND
			ptne.CodOrganismo = '".$organismo."' AND
			ptne.TotalNeto > 0 $filtro
		ORDER BY length(mp.Ndocumento), mp.Ndocumento";
$query = mysql_query($sql) or die ($sql.mysql_error());
while ($field = mysql_fetch_array($query)) {
	if ($field['CodBeneficiario'] != "") {
		$nombre = $field['NombreCompleto'];
		$cedula = $field['NroDocumento'];
	} else {
		$nombre = '';
		if (trim($field['Apellido1'])) $nombre .= $field['Apellido1'];
		if (trim($field['Apellido2'])) $nombre .= ' '.$field['Apellido2'];
		if (trim($field['Nombres'])) $nombre .= ' '.$field['Nombres'];
		$cedula = $field['Ndocumento'];
	}

	$sum += $field['TotalNeto'];
 	//--
	//if ($field['TipoCuenta'] == "CO") $tipo_cuenta = "0"; else $tipo_cuenta = "1";
 	//--
	$nrocuenta = (string) str_repeat("0", 20-mb_strlen($field['Ncuenta'])).$field['Ncuenta'];
	//--
	list($int, $dec)=explode('.', $field['TotalNeto']); $field_monto = $int.$dec;
	$monto = (string) str_repeat("0", 10-mb_strlen($field_monto)).$field_monto;
	//--
	//$relleno_1 = "0770";
	//--
	$nombre = (string) $nombre.str_repeat(" ", 40-mb_strlen($nombre,'UTF8'));
	//--
	$cedula = (string) str_repeat("0", 10-mb_strlen($cedula)).$cedula;
	//--
	//$relleno_2 = "003291  ";
	//--
	//$texto.=$tipo_cuenta.$nrocuenta.$monto.$relleno_1.$nombre.$cedula.$relleno_2."$nl";
	if ($texto) $texto .= $nl;
	$texto.=$field['Nacionalidad'].$cedula.$nombre.$nrocuenta.$monto;
}

fwrite($archivo, $texto);
fclose($archivo);

?>