<?php
header('Content-Type: text/html; charset=iso-8859-1');
header("Content-Disposition: attachment; filename=BONO".$_POST['fCodTipoNom'].".txt");
header("Pragma: no-cache");
header("Expires: 0");
//---------------------------------------------------
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$LF = 0x0A;
$CR = 0x0D;
$nl = sprintf("%c%c",$CR,$LF);
$Fecha = $DiaActual.'/'.$MesActual.'/'.$AnioActual;
$fPeriodo = $fPeriodoAnio.'-'.$fPeriodoMes;
//---------------------------------------------------
$filtro = "";
if ($fCodOrganismo != "") $filtro .= " AND (bad.CodOrganismo = '".$fCodOrganismo."')";
if ($fCodDependencia != "") $filtro .= " AND (e.CodDependencia = '".$fCodDependencia."')";
if ($fCodCentroCosto != "") $filtro .= " AND (e.CodCentroCosto = '".$fCodCentroCosto."')";
if ($fCodTipoNom != "") $filtro .= " AND (ba.CodTipoNom = '".$fCodTipoNom."')";
if ($fCodPerfil != "") $filtro .= " AND (e.CodPerfil = '".$fCodPerfil."')";
if ($fEdoReg != "") $filtro .= " AND (p.Estado = '".$fEdoReg."')";
if ($fSitTra != "") $filtro .= " AND (e.Estado = '".$fSitTra."')";
if ($fBuscar != "") $filtro .= " AND (e.CodEmpleado LIKE '%".$fBuscar."%' OR
									  p.NomCompleto LIKE '%".$fBuscar."%' OR
									  p.Ndocumento LIKE '%".$fBuscar."%')";
if ($fCodCargo != "") {
	$inner_cargo = "INNER JOIN rh_cargoreporta cr ON (cr.CodCargo = e.CodCargo AND
													  cr.CargoReporta = '".$fCodCargo."')";
}
//---------------------------------------------------
list($Anio, $CodOrganismo, $CodBonoAlim) = split("[_]", $fMes);
//---------------------------------------------------
$sql = "SELECT
			bad.*,
			p.NomCompleto,
			p.Ndocumento,
			p.Nacionalidad,
			p.Apellido1,
			p.Apellido2, 
			p.Nombres,
			e.CodEmpleado,
			pt.DescripCargo,
			bp.Ncuenta,
			bp.TipoCuenta
		FROM
			rh_bonoalimentaciondet bad
			INNER JOIN rh_bonoalimentacion ba ON (ba.Anio = bad.Anio AND
												  ba.CodOrganismo = bad.CodOrganismo AND
												  ba.CodBonoAlim = bad.CodBonoAlim)
			INNER JOIN mastpersonas p ON (p.CodPersona = bad.CodPersona)
			INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
			INNER JOIN rh_puestos pt ON (pt.CodCargo = e.CodCargo)
			INNER JOIN bancopersona bp ON (bad.CodPersona = bp.CodPersona AND bp.FlagPrincipal = 'S')
			$inner_cargo
		WHERE
			bad.Anio = '".$Anio."' AND
			bad.CodOrganismo = '".$CodOrganismo."' AND
			bad.CodBonoAlim = '".$CodBonoAlim."' $filtro
		GROUP BY CodPersona
		ORDER BY LENGTH(Ndocumento), Ndocumento";
$field = getRecords($sql);
foreach($field as $f) {
	$cedula = (string) str_repeat("0", 10-mb_strlen($f['Ndocumento'])).$f['Ndocumento'];
	##	
	$nombre = '';
	if (trim($f['Apellido1'])) $nombre .= $f['Apellido1'];
	if (trim($f['Apellido2'])) $nombre .= ' '.$f['Apellido2'];
	if (trim($f['Nombres'])) $nombre .= ' '.$f['Nombres'];
	$nombre = (string) $nombre.str_repeat(" ", 40-mb_strlen($nombre,'UTF8'));
	##	
	list($int, $dec) = explode('.', $f['TotalPagar']); $TotalPagar = $int.$dec;
	$monto = (string) str_repeat("0", 10-mb_strlen($TotalPagar)).$TotalPagar;
	##	
	$Texto .= $f['Nacionalidad'].$cedula.$nombre.$f['Ncuenta'].$monto.$nl;
}
echo $Texto;
?>