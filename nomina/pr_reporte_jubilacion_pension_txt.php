<?php
header('Content-Type: text/html; charset=iso-8859-1');
header("Content-Disposition: attachment; filename=".$_POST['Archivo'].".txt");
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
$sql = "SELECT 
			mp.Ndocumento, 
			mp.NomCompleto,
			mp.Nacionalidad,
			mp.Sexo,
			mp.Fnacimiento,
			mp.Nombres,
			mp.Apellido1,
			s.SueldoIntegral,
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
				ptnec2.CodConcepto = '0042') AS MontoAporte
		FROM
			mastpersonas mp
			INNER JOIN pr_tiponominaempleado ptne ON (mp.CodPersona = ptne.CodPersona)
			INNER JOIN pr_tiponominaempleadoconcepto ptnec ON (ptne.CodOrganismo = ptnec.CodOrganismo AND
															   ptne.CodTipoNom = ptnec.CodTipoNom AND 
															   ptne.Periodo = ptnec.Periodo AND 
															   ptne.CodTipoproceso = ptnec.CodTipoProceso AND 
															   ptne.CodPersona = ptnec.CodPersona AND
															   ptnec.CodConcepto = '0030')
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
	$Nombre = $f['Nombres']." ".$f['Apellido1'];
	$Date = $DiaActual.$MesActual.$AnioActual;
	$Texto .= $f['Ndocumento']."|".$Date."|".$Nombre."|"."10041901"."|".number_format($f['TotalIngresos'],2,',','')."|".number_format($f['MontoRetencion'],2,',','')."|".number_format($f['MontoAporte'],2,',','').$nl;
}
echo $Texto;
?>