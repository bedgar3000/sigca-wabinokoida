<?php
session_start();
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion.sql", "w+");
//	fwrite($__archivo, $sql.";\n\n");
///////////////////////////////////////////////////////////////////////////////
//	RETENCIONES JUDICIALES (NUEVO, MODIFICAR, ELIMINAR)
///////////////////////////////////////////////////////////////////////////////
if ($modulo == "retencion_judicial") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		//	inserto
		$CodRetencion = codigo("rh_retencionjudicial", "CodRetencion", 6, array('CodOrganismo'), array($CodOrganismo));
		$sql = "INSERT INTO rh_retencionjudicial
				SET
					CodOrganismo = '".$CodOrganismo."',
					CodRetencion = '".$CodRetencion."',
					CodPersona = '".$CodPersona."',
					Expediente = '".$Expediente."',
					FechaResolucion = '".formatFechaAMD($FechaResolucion)."',
					TipoRetencion = '".$TipoRetencion."',
					Juzgado = '".$Juzgado."',
					Demandante = '".$Demandante."',
					CodTipoPago = '".$CodTipoPago."',
					Observaciones = '".changeUrl($Observaciones)."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		//	conceptos
		for($i=0; $i<count($CodConcepto); $i++) {
			$sql = "INSERT INTO rh_retencionjudicialconceptos
					SET
						CodOrganismo = '".$CodOrganismo."',
						CodRetencion = '".$CodRetencion."',
						CodConcepto = '".$CodConcepto[$i]."',
						TipoDescuento = '".$TipoDescuento[$i]."',
						Descuento = '".setNumero($Descuento[$i])."',
						TipoSueldo = '".$TipoSueldo[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	//	modificar
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "UPDATE rh_retencionjudicial
				SET
					Expediente = '".$Expediente."',
					FechaResolucion = '".formatFechaAMD($FechaResolucion)."',
					TipoRetencion = '".$TipoRetencion."',
					Juzgado = '".$Juzgado."',
					Demandante = '".$Demandante."',
					CodTipoPago = '".$CodTipoPago."',
					Observaciones = '".changeUrl($Observaciones)."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodRetencion = '".$CodRetencion."'";
		execute($sql);
		//	conceptos
		$sql = "DELETE FROM rh_retencionjudicialconceptos
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodRetencion = '".$CodRetencion."'";
		execute($sql);
		for($i=0; $i<count($CodConcepto); $i++) {
			$sql = "INSERT INTO rh_retencionjudicialconceptos
					SET
						CodOrganismo = '".$CodOrganismo."',
						CodRetencion = '".$CodRetencion."',
						CodConcepto = '".$CodConcepto[$i]."',
						TipoDescuento = '".$TipoDescuento[$i]."',
						Descuento = '".setNumero($Descuento[$i])."',
						TipoSueldo = '".$TipoSueldo[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		//	-----------------
		mysql_query("COMMIT");
	}
}
elseif ($modulo == "ajax") {
	//	cargos a quien reporta
	if ($accion == "conceptos") {
		$sql = "SELECT
					CodConcepto,
					Descripcion AS NomConcepto
				FROM pr_concepto
				WHERE CodConcepto = '".$CodConcepto."'";
		$field_conceptos = getRecords($sql);
		foreach ($field_conceptos as $f) {
			$id = $f['CodConcepto'];
			?>
            <tr class="trListaBody" onclick="clk($(this), 'conceptos', 'conceptos_<?=$id?>');" id="conceptos_<?=$id?>">
                <th>
					<?=++$nro_detalles?>
                    <input type="hidden" name="CodConcepto[]" value="<?=$f['CodConcepto']?>" />
                </th>
                <td align="center">
                	<?=$f['CodConcepto']?>
                </td>
                <td>
                	<?=htmlentities($f['NomConcepto'])?>
                </td>
                <td>
                    <select name="TipoDescuento[]" class="cell" onchange="setTipoDescuento(this.value, '<?=$id?>');">
                    	<option value="">&nbsp;</option>
                        <?=loadSelectValores('tipo-descuento')?>
                    </select>
                </td>
                <td>
                    <input type="text" name="Descuento[]" value="0,00" style="text-align:right;" class="cell currency" />
                </td>
                <td>
                    <select name="TipoSueldo[]" id="dTipoSueldo_<?=$id?>" class="cell" disabled>
                    	<option value="">&nbsp;</option>
                        <?=loadSelectValores('tipo-sueldo')?>
                    </select>
                </td>
            </tr>
            <?php
		}
	}
}
?>