<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".".sql", "w+");
##############################################################################/
if ($modulo == "ajax") {
	if ($accion == "adelanto_detalle") {
		$i = 0;
		$sql = "SELECT
					doa.*,
					do.NroDocumento,
					p.NomCompleto AS Cajero,
					dc.MontoPagado
				FROM co_documentoadelanto doa
				INNER JOIN co_documento do ON do.CodDocumento = doa.CodDocumento
				INNER JOIN co_documentocobranza dc ON dc.CodDocumento  = do.CodDocumento
				INNER JOIN co_cobranza co ON co.CodCobranza = dc.CodCobranza
				LEFT JOIN mastpersonas p oN p.CodPersona = co.CodPersonaCajero
				WHERE doa.CodDocumento = '$CodDocumento'";
		$field_detalle = getRecords($sql);
		foreach($field_detalle as $f) {
			?>
			<tr class="trListaBody">
				<th><?=$nro_detalle?></th>
				<td align="center"><?=$f['NroDocumento']?></td>
				<td><?=htmlentities($f['Cajero'])?></td>
				<td align="right"><?=number_format($f['MontoPagado'],2,',','.')?></td>
				<td align="center"><?=$f['CodTipoDocRel']?><?=$f['NroDocRel']?></td>
			</tr>
			<?php
		}
	}
}
?>