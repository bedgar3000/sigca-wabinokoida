<?php
include("../lib/fphp.php");
include("lib/fphp.php");
##	
$sql = "SELECT * FROM lg_guiaremision WHERE CodGuia = '$CodGuia'";
$field = getRecord($sql);
?>
<form name="frmdespacho" id="frmdespacho" action="lg_guiaremision_lista.php" method="POST" enctype="multipart/form-data" autocomplete="off">
	<input type="hidden" name="CodGuia" id="CodGuia" value="<?=$field['CodGuia']?>">
	<table style="width:100%;" class="tblForm">
		<tr>
			<td class="tagForm">Fecha de Entrega a Transportista:</td>
			<td>
				<input type="text" name="FechaInicioTraslado" id="FechaInicioTraslado" value="<?=formatFechaDMA($field['FechaInicioTraslado'])?>" maxlength="10" style="width:80px;" class="datepicker" onkeyup="setFechaDMA(this);" />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Fecha Recepción Final:</td>
			<td>
				<input type="text" name="FechaProgramadaEntrega" id="FechaProgramadaEntrega" value="<?=formatFechaDMA($field['FechaProgramadaEntrega'])?>" maxlength="10" style="width:80px;" class="datepicker" onkeyup="setFechaDMA(this);" />
			</td>
		</tr>
	</table>
</form>

<script type="text/javascript">
</script>