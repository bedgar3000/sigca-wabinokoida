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
    #Nacionalidad (1)
    $nacionalidad = $field['Nacionalidad'];

    #Cedula (9 / 2-10)
    if ($field['CodBeneficiario'] != "") {
        $cedula = mb_str_pad($field['NroDocumento'], 9, "0", STR_PAD_LEFT);
	} else {
        $cedula = mb_str_pad($field['Ndocumento'], 9, "0", STR_PAD_LEFT);
    }
    
    #Nombre (40 / 11-50)
	if ($field['CodBeneficiario'] != "") {
        $nombre = mb_str_pad($field['NombreCompleto'], 40, " ", STR_PAD_RIGHT);
	} else {
		$nombre = '';
		if (trim($field['Apellido1'])) $nombre .= $field['Apellido1'];
		if (trim($field['Apellido2'])) $nombre .= ' '.$field['Apellido2'];
		if (trim($field['Nombres'])) $nombre .= ' '.$field['Nombres'];
        $nombre = mb_str_pad($nombre, 40, " ", STR_PAD_RIGHT);
    }
    
    #Cuenta Bancaria (20 51-70)
    $cuenta = $field['Ncuenta'];

    #Monto
    list($int, $dec) = explode('.', number_format($field['TotalNeto'],2)); 
    $string = $int . $dec;
	$monto = mb_str_pad($string, 10, "0", STR_PAD_LEFT);
	
	$texto .= $nacionalidad . $cedula . $nombre . $cuenta . $monto . $nl;
}

/**
 * mb_str_pad
 *
 * @param string $input
 * @param int $pad_length
 * @param string $pad_string
 * @param int $pad_type
 * @return string
 * @author Kari "Haprog" Sderholm
 */
function mb_str_pad( $input, $pad_length, $pad_string = ' ', $pad_type = STR_PAD_RIGHT)
{
    $diff = strlen( $input ) - mb_strlen( $input );
    return str_pad( $input, $pad_length + $diff, $pad_string, $pad_type );
}

fwrite($archivo, $texto);
fclose($archivo);           
?>