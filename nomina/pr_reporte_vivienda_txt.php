<?php
header('Content-Type: text/html; charset=iso-8859-1');
header("Content-Disposition: attachment; filename=F03212000056881555796.txt");
header("Pragma: no-cache");
header("Expires: 0");
//---------------------------------------------------
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$LF = 0x0A;
$CR = 0x0D;
$nl = sprintf("%c%c",$CR,$LF);
$fPeriodo = $fPeriodoAnio.'-'.$fPeriodoMes;
//---------------------------------------------------
$sql = "SELECT 
			mp.Ndocumento, 
			mp.NomCompleto,
			mp.Apellido1,
			mp.Apellido2,
			mp.Nombres,
			mp.Nacionalidad,
			mp.Ndocumento,
			ptne.SueldoIntegral,
			ptne.TotalIngresos,
			ptnec.Monto AS MontoRetencion,
			(SELECT Monto
			 FROM pr_tiponominaempleadoconcepto ptnec2
			 WHERE
			 	ptnec2.CodOrganismo = ptnec.CodOrganismo AND
				ptnec2.CodTipoNom = ptnec.CodTipoNom AND 
				ptnec2.Periodo = ptnec.Periodo AND 
				ptnec2.CodTipoproceso = ptnec.CodTipoProceso AND 
				ptnec2.CodPersona = ptnec.CodPersona AND
				ptnec2.CodConcepto = '0041') AS MontoAporte
		FROM
			mastpersonas mp
			INNER JOIN pr_tiponominaempleado ptne ON (mp.CodPersona = ptne.CodPersona)
			INNER JOIN pr_tiponominaempleadoconcepto ptnec ON (ptne.CodOrganismo = ptnec.CodOrganismo AND
															   ptne.CodTipoNom = ptnec.CodTipoNom AND 
															   ptne.Periodo = ptnec.Periodo AND 
															   ptne.CodTipoproceso = ptnec.CodTipoProceso AND 
															   ptne.CodPersona = ptnec.CodPersona AND
															   ptnec.CodConcepto = '0029')
			INNER JOIN rh_sueldos s ON (s.Periodo = ptne.Periodo AND
										s.CodPersona = ptne.CodPersona)
		WHERE
			ptne.CodOrganismo = '".$fCodOrganismo."' AND
			ptne.CodTipoNom = '".$fCodTipoNom."' AND
			ptne.Periodo = '".$fPeriodo."' AND
			ptne.CodTipoProceso = '".$fCodTipoProceso."'
		ORDER BY LENGTH(mp.Ndocumento), mp.Ndocumento";
$field = getRecords($sql);
foreach($field as $f) {
	$Nombres = explode(' ', $f['Nombres']);
	$Apellido1 = (($f['Apellido1']!='')?$f['Apellido1']:$f['Apellido2']);
	$Monto = $f['MontoRetencion'] + $f['MontoAporte'];
	$Monto = str_replace('.', '', $Monto);
	$Texto .= $f['Nacionalidad'].",".$f['Ndocumento'].",".substr($Apellido1,0,25).","." ".",".$Nombres[0].","." ".",".$Monto.","."000".$nl;
}
echo $Texto;
?>