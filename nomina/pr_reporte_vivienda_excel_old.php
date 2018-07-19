<?php
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=".$_POST['Archivo'].".xls");
header("Pragma: no-cache");
header("Expires: 0");
//---------------------------------------------------
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$fPeriodo = $fPeriodoAnio.'-'.$fPeriodoMes;
$filtro1 = '';
$filtro2 = '';
if ($fCodTipoNom) { $filtro1 .= " AND pp.CodTipoNom = '".$fCodTipoNom."'"; $filtro2 .= " AND ptne.CodTipoNom = '".$fCodTipoNom."'"; }
if ($fCodTipoProceso) { $filtro1 .= " AND pp.CodTipoProceso = '".$fCodTipoProceso."'"; $filtro2 .= " AND ptne.CodTipoProceso = '".$fCodTipoProceso."'"; }
?>
<table border="1">
	<thead>
    	<tr>
            <th>NUMERO DE RIF</th>
            <th>CODIGO DE AGENCIA</th>
            <th>NACIONALIDAD</th>
            <th>CEDULA</th>
            <th>FECHA APORTE</th>
            <th>APELLIDOS Y NOMBRES</th>
            <th>APORTE EMPLEADO</th>
            <th>APORTE EMPRESA</th>
            <th>F. DE NACIMIENTO</th>
            <th>SEXO</th>
            <th>APARTADO POSTAL</th>
            <th>CODIGO DE LA EMPRESA</th>
            <th>ESTATUS</th>
        </tr>
    </thead>
    <tbody>
		<?php
		$sql = "SELECT 
					mp.Ndocumento, 
					mp.NomCompleto,
					mp.Nacionalidad,
					mp.Sexo,
					mp.Fnacimiento,
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
					ptne.Periodo = '".$fPeriodo."' $filtro2
				ORDER BY LENGTH(mp.Ndocumento), mp.Ndocumento";
        $field = getRecords($sql);
        foreach($field as $f) {
			list($aNac, $mNac, $dNac) = explode('-', $f['Fnacimiento']);
            ?>
            <tr>
                <td>G200005688</td>
                <td>20</td>
                <td><?=$f['Nacionalidad']?></td>
                <td><?=$f['Ndocumento']?></td>
                <td><?=date("d/m/Y")?></td>
                <td><?=utf8_decode($f['NomCompleto'])?></td>
                <td align="right"><?=number_format($f['MontoRetencion'],2,',','.')?></td>
                <td align="right"><?=number_format($f['MontoAporte'],2,',','.')?></td>
                <td><?=$dNac."/".$mNac."/".$aNac?></td>
                <td><?=$f['Sexo']?></td>
                <td>6401</td>
                <td></td>
                <td>1</td>
	        </tr>
            <?php
        }
        ?>
    </tbody>
</table>