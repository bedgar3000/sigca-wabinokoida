<?php
if ($_PARAMETRO['CONTONCO'] != "S") { $li1 = "display:none;"; }
if ($_PARAMETRO['CONTPUB20'] != "S") { $li2 = "display:none;"; }
if ($origen == "pago-anulacion") {
	list($NroProceso, $Secuencia) = split("[_]", $registro);
	$sql = "SELECT
				CodOrganismo,
				VoucherAnulacion,
				PeriodoAnulacion,
				VoucherAnulPub20,
				PeriodoAnulPub20
			FROM ap_pagos
			WHERE
				NroProceso = '".$NroProceso."' AND
				Secuencia = '".$Secuencia."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	$registrot = $field['CodOrganismo']."_".$field['PeriodoAnulacion']."_".$field['VoucherAnulacion']."_T";
	$registrof = $field['CodOrganismo']."_".$field['PeriodoAnulPub20']."_".$field['VoucherAnulPub20']."_F";
	if ($field['VoucherAnulacion'] == "") { $li1 = "display:none;"; }
	if ($field['VoucherAnulPub20'] == "") { $li2 = "display:none;"; }
}
elseif ($origen == "pago-ver") {
	list($NroProceso, $Secuencia) = split("[_]", $registro);
	$sql = "SELECT
				CodOrganismo,
				VoucherPago,
				VoucherPeriodo,
				VoucherPagoPub20,
				PeriodoPagoPub20
			FROM ap_pagos
			WHERE
				NroProceso = '".$NroProceso."' AND
				Secuencia = '".$Secuencia."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	$registrot = $field['CodOrganismo']."_".$field['VoucherPeriodo']."_".$field['VoucherPago']."_T";
	$registrof = $field['CodOrganismo']."_".$field['PeriodoPagoPub20']."_".$field['VoucherPagoPub20']."_F";
	if ($field['VoucherPago'] == "") { $li1 = "display:none;"; }
	if ($field['VoucherPagoPub20'] == "") { $li2 = "display:none;"; }
}
elseif ($origen == "orden-anulacion") {
	list($Anio, $CodOrganismo, $NroOrden) = split("[_]", $registro);
	$sql = "SELECT
				op.CodOrganismo,
				op.VoucherPagoAnulacion,
				op.PeriodoPagoAnulacion,
				o.VoucherAnulacion,
				o.PeriodoAnulacion
			FROM
				ap_ordenpago op
				INNER JOIN ap_obligaciones o ON (o.CodProveedor = op.CodProveedor AND
												 o.CodTipoDocumento = op.CodTipoDocumento AND
												 o.NroDocumento = op.NroDocumento)
			WHERE
				op.Anio = '".$Anio."' AND
				op.CodOrganismo = '".$CodOrganismo."' AND
				op.NroOrden = '".$NroOrden."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	$registrot = $field['CodOrganismo']."_".$field['PeriodoAnulacion']."_".$field['VoucherAnulacion']."_T";
	$registrof = $field['CodOrganismo']."_".$field['PeriodoPagoAnulacion']."_".$field['VoucherPagoAnulacion']."_F";
	if ($field['VoucherAnulacion'] == "") { $li1 = "display:none;"; }
	if ($field['VoucherPagoAnulacion'] == "") { $li2 = "display:none;"; }
}
elseif ($origen == "obligacion-anulacion") {
	list($CodProveedor, $CodTipoDocumento, $NroDocumento) = split("[_]", $registro);
	$sql = "SELECT
				CodOrganismo,
				VoucherAnulPub20,
				PeriodoAnulPub20,
				Estado
			FROM ap_obligaciones
			WHERE
				CodProveedor = '".$CodProveedor."' AND
				CodTipoDocumento = '".$CodTipoDocumento."' AND
				NroDocumento = '".$NroDocumento."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	$registrot = $field['CodOrganismo']."_".$field['PeriodoAnulPub20']."_".$field['VoucherAnulPub20']."_F";
	if ($field['VoucherAnulPub20'] == "" || $field['Estado'] == "AN") { $li1 = "display:none;"; }
	$li2 = "display:none;";
}
elseif ($origen == "orden-adelanto-total") {
	list($CodOrganismo, $Periodo, $Voucher, $CodContabilidad) = explode("_", $registro);
	
	if ($_PARAMETRO['CONTONCO'] == "S") { $registrot = $registro; }
	if ($_PARAMETRO['CONTPUB20'] == "S") { $registrof = $registro; }
}
if ($li1 == "display:none;") $_registro = $registrof; else $_registro = $registrot;
?>
<form name="frmentrada" id="frmentrada" method="post" target="iReporte">
<table width="1000" align="center" cellpadding="0" cellspacing="0">
    <tr>
        <td>
            <div class="header">
            <ul id="tab">
            <!-- CSS Tabs -->
			<?php if ($_PARAMETRO['CONTONCO'] == "S") : ?>
				<li id="li1" onclick="current($(this));" class="current" style=" <?=$li1?>">
					<a href="#" onclick="cargarPagina(document.getElementById('frmentrada'), 'gehen.php?anz=ap_voucher_form&registro=<?=$registrot?>&accion=<?=$accion?>');">Voucher Contable</a>
				</li>
			<?php endif; ?>
			<?php if ($_PARAMETRO['CONTPUB20'] == "S") : ?>
				<li id="li2" onclick="current($(this));" style=" <?=$li2?>">
					<a href="#" onclick="cargarPagina(document.getElementById('frmentrada'), 'gehen.php?anz=ap_voucher_form&registro=<?=$registrof?>&accion=<?=$accion?>');">Voucher Contable (Pub. 20)</a>
				</li>
			<?php endif; ?>
            </ul>
            </div>
        </td>
    </tr>
</table>
</form>


<?php
//	muestro vouchers
if ($li1 == "display:none;" && $li2 == "display:none;") {
	?>
    <script type="text/javascript">
	$(document).ready(function() {
		parent.$.prettyPhoto.close();
    });
    </script>
    <?php
} else {
	?>
    <center>
    <iframe name="iReporte" id="iReporte" style="border-left:solid 1px #CDCDCD; border-right:solid 1px #CDCDCD; border-bottom:solid 1px #CDCDCD; border-top:0; width:1000px; height:500px;" src="gehen.php?anz=ap_voucher_form&registro=<?=$_registro?>&accion=<?=$accion?>"></iframe>
    </center>
    <?php
}
?>