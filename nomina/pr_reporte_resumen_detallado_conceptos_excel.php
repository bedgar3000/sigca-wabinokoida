<?php
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=".$_POST['Archivo'].".xls");
header("Pragma: no-cache");
header("Expires: 0");
//---------------------------------------------------
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$filtro = '';
$filtro1 = '';
$filtro2 = '';
$filtro3 = '';
if ($fFechaGeneracionD != '') $filtro .= " AND (ptne.FechaGeneracion >= '".formatFechaAMD($fFechaGeneracionD)."')";
if ($fFechaGeneracionH != '') $filtro .= " AND (ptne.FechaGeneracion <= '".formatFechaAMD($fFechaGeneracionH)."')";
if ($fCodPersona != "") {
	$filtro1 = " AND ptne.CodPersona = '".$fCodPersona."'";
}
//---------------------------------------------------
##	conceptos (asignaciones)
$sql = "SELECT
			tnec.CodConcepto,
			c.Descripcion,
			c.Abreviatura
		FROM
			pr_tiponominaempleadoconcepto tnec
			INNER JOIN pr_tiponominaempleado ptne ON (ptne.CodTipoNom = tnec.CodTipoNom AND 
													  ptne.Periodo = tnec.Periodo AND 
													  ptne.CodPersona = tnec.CodPersona AND 
													  ptne.CodOrganismo = tnec.CodOrganismo AND 
													  ptne.CodTipoProceso = tnec.CodTipoProceso)
			INNER JOIN pr_concepto c ON (tnec.CodConcepto = c.CodConcepto AND c.Tipo = 'I')
		WHERE
			tnec.CodOrganismo = '".$fCodOrganismo."' AND
			tnec.CodTipoNom = '".$fCodTipoNom."' AND
			tnec.Periodo = '".$fPeriodo."' AND
			tnec.CodTipoProceso = '".$fCodTipoProceso."' $filtro1 $filtro
		GROUP BY CodConcepto
		ORDER BY CodConcepto";
$field_asignaciones = getRecords($sql);
##	conceptos (deducciones)
$sql = "SELECT
			tnec.CodConcepto,
			c.Descripcion,
			c.Abreviatura
		FROM
			pr_tiponominaempleadoconcepto tnec
			INNER JOIN pr_tiponominaempleado ptne ON (ptne.CodTipoNom = tnec.CodTipoNom AND 
													  ptne.Periodo = tnec.Periodo AND 
													  ptne.CodPersona = tnec.CodPersona AND 
													  ptne.CodOrganismo = tnec.CodOrganismo AND 
													  ptne.CodTipoProceso = tnec.CodTipoProceso)
			INNER JOIN pr_concepto c ON (tnec.CodConcepto = c.CodConcepto AND c.Tipo = 'D')
		WHERE
			tnec.CodOrganismo = '".$fCodOrganismo."' AND
			tnec.CodTipoNom = '".$fCodTipoNom."' AND
			tnec.Periodo = '".$fPeriodo."' AND
			tnec.CodTipoProceso = '".$fCodTipoProceso."' $filtro1 $filtro
		GROUP BY CodConcepto
		ORDER BY CodConcepto";
$field_deducciones = getRecords($sql);
##	
$q = '';
$w = 110 + 10;
$Widths[] = 17;		$Aligns[] = 'R';	$Rows[] = utf8_decode('CEDULA');
$Widths[] = 80;		$Aligns[] = 'L';	$Rows[] = utf8_decode('NOMBRES Y APELLIDOS');
$Widths[] = 13;		$Aligns[] = 'C';	$Rows[] = utf8_decode('CARGO');
if ($FlagAsignaciones == 'I') {
	foreach($field_asignaciones as $f) {
		$Widths[] = 20;
		$Aligns[] = 'R';
		$Rows[] = utf8_decode($f['Abreviatura']);
		$Descripcion[] = utf8_decode($f['Descripcion']);
		$CodConcepto[] = $f['CodConcepto'];
		$w += 20;
		$q .= ", (SELECT Monto 
					FROM pr_tiponominaempleadoconcepto 
					WHERE
						CodOrganismo = '".$fCodOrganismo."' AND
						CodTipoNom = '".$fCodTipoNom."' AND
						Periodo = '".$fPeriodo."' AND
						CodTipoProceso = '".$fCodTipoProceso."' AND
						CodConcepto = '".$f['CodConcepto']."' AND
						CodPersona = p.CodPersona
					) AS '".$f['CodConcepto']."'";
	}
	$Widths[] = 20;		$Aligns[] = 'R';	$Rows[] = utf8_decode('T.ASIG.');	$CodConcepto[] = 'T.ASIG.';	$Descripcion[] = 'TOTAL ASIGNACIONES';
	$w += 20;
}
if ($FlagDeducciones == 'D') {
	foreach($field_deducciones as $f) {
		$Widths[] = 20;
		$Aligns[] = 'R';
		$Rows[] = utf8_decode($f['Abreviatura']);
		$Descripcion[] = utf8_decode($f['Descripcion']);
		$CodConcepto[] = $f['CodConcepto'];
		$w += 20;
		$q .= ", (SELECT Monto 
					FROM pr_tiponominaempleadoconcepto 
					WHERE
						CodOrganismo = '".$fCodOrganismo."' AND
						CodTipoNom = '".$fCodTipoNom."' AND
						Periodo = '".$fPeriodo."' AND
						CodTipoProceso = '".$fCodTipoProceso."' AND
						CodConcepto = '".$f['CodConcepto']."' AND
						CodPersona = p.CodPersona
					) AS '".$f['CodConcepto']."'";
	}
	$Widths[] = 20;		$Aligns[] = 'R';	$Rows[] = utf8_decode('T.DEDUC.');	$CodConcepto[] = 'T.DEDUC.';	$Descripcion[] = 'TOTAL DEDUCCIONES';
	$Widths[] = 20;		$Aligns[] = 'R';	$Rows[] = utf8_decode('TOTAL');		$CodConcepto[] = 'TOTAL';		$Descripcion[] = 'MONTO TOTAL';
	$w += 40;
}
//---------------------------------------------------
?>
<table border="1">
	<thead>
    	<tr>
        	<?php
            for($i=0; $i<count($Rows); $i++) {
				?><th><?=$Rows[$i]?></th><?php
			}
			?>
        </tr>
    </thead>
    <tbody>
		<?php
        $sql = "SELECT
                      ptne.CodPersona,
                      ptne.TotalIngresos,
                      ptne.TotalEgresos,
                      ptne.TotalNeto,
                      p.Ndocumento,
                      p.NomCompleto,
                      c.CodDesc AS CodCargo
                      $q
                FROM
                      pr_tiponominaempleado ptne
                      INNER JOIN mastpersonas p ON (ptne.CodPersona = p.CodPersona)
                      INNER JOIN mastempleado e ON (p.CodPersona = e.CodPersona)
                      INNER JOIN rh_puestos c ON (e.CodCargo = c.CodCargo)
                WHERE
                      ptne.CodTipoNom = '".$fCodTipoNom."' AND
                      ptne.Periodo = '".$fPeriodo."' AND
                      ptne.CodOrganismo = '".$fCodOrganismo."' AND
                      ptne.CodTipoProceso = '".$fCodTipoProceso."' $filtro2 $filtro
                ORDER BY length(p.Ndocumento), Ndocumento";
        $field = getRecords($sql);
        foreach($field as $f) {
            ?>
            <tr>
                <td align="right"><?=number_format($f['Ndocumento'],0,'','.')?></td>
                <td><?=utf8_decode($f['NomCompleto'])?></td>
                <td align="center"><?=$f['CodCargo']?></td>
            <?php
            ##	conceptos
            $i = 0;
            for($wa=3; $wa<count($Widths); $wa++) {
                $index = $CodConcepto[$i];
                if ($CodConcepto[$i] == 'T.ASIG.') $Monto = $f['TotalIngresos'];
                elseif ($CodConcepto[$i] == 'T.DEDUC.') $Monto = $f['TotalEgresos'];
                elseif ($CodConcepto[$i] == 'TOTAL') $Monto = $f['TotalNeto'];
                else $Monto = $f[$index];
                $TotalConcepto[$index] += $Monto;
                ?><td align="right"><?=number_format($Monto,2,',','.')?></td><?php
                ++$i;
            }
            ++$j;
			?>
	        </tr>
            <?php
        }
        ?>
    </tbody>
</table>