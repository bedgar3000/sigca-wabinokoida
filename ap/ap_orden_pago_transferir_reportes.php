<?php
list($NroProceso, $Secuencia, $CodTipoPago) = split("[_]", $registro);
//---------------------------------------------------
//	verifico si muestro tab de retenciones
//	consulto iva
$sql = "SELECT r.*
		FROM ap_retenciones r
		WHERE
			r.PagoNroProceso = '".$NroProceso."' AND
			r.PagoSecuencia = '".$Secuencia."' AND
			r.TipoComprobante = 'IVA'";
$query_iva = mysql_query($sql) or die ($sql.mysql_error());
//---------------------------------------------------
//	consulto islr
$sql = "SELECT r.*
		FROM ap_retenciones r
		WHERE
			r.PagoNroProceso = '".$NroProceso."' AND
			r.PagoSecuencia = '".$Secuencia."' AND
			r.TipoComprobante = 'ISLR'";
$query_islr = mysql_query($sql) or die ($sql.mysql_error());
//---------------------------------------------------
//	consulto 1X1000
$sql = "SELECT r.*
		FROM ap_retenciones r
		WHERE
			r.PagoNroProceso = '".$NroProceso."' AND
			r.PagoSecuencia = '".$Secuencia."' AND
			r.TipoComprobante = '1X1000'";
$query_mil = mysql_query($sql) or die ($sql.mysql_error());
//---------------------------------------------------
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo">Reportes Generados del Pago</td>
		<td align="right"><a class="cerrar" href="javascript:window.close();">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" method="post" target="iReporte">
<input type="hidden" name="registro" id="registro" value="<?=$registro?>" />
<table width="1000" align="center" cellpadding="0" cellspacing="0">
    <tr>
        <td>
            <div class="header">
            <ul id="tab">
            <!-- CSS Tabs -->
            <li id="li1" onclick="currentTab('tab', this);">
			<?php
            if ($CodTipoPago == "02") {
				if ($_PARAMETRO['CONTORDENDIS'] == "T") $tab1 = "ap_orden_pago_transferir_sustento_cheques_pdf";
				elseif ($_PARAMETRO['CONTORDENDIS'] == "F") $tab1 = "ap_orden_pago_transferir_sustento_chequespub20_pdf";
			}
            else {
				if ($_PARAMETRO['CONTORDENDIS'] == "T") $tab1 = "ap_orden_pago_transferir_sustento_nocheques_pdf";
				elseif ($_PARAMETRO['CONTORDENDIS'] == "F") $tab1 = "ap_orden_pago_transferir_sustento_nochequespub20_pdf";
			}
            ?>
            <a href="#" onclick="cargarPagina(document.getElementById('frmentrada'), '<?=$tab1?>.php');">Sustento</a>
            </li>
            <?php
			if ($CodTipoPago == "02") {
				?>
                <li id="li2" onclick="currentTab('tab', this);">
                    <a href="#" onclick="cargarPagina(document.getElementById('frmentrada'), 'ap_orden_pago_transferir_cheque_pdf.php');">Cheque</a>
                </li>
                <?php
			}
			/*if (mysql_num_rows($query_iva) != 0 || mysql_num_rows($query_islr) != 0 || mysql_num_rows($query_mil) != 0) {
				?>
                <li id="li3" onclick="currentTab('tab', this);">
                    <a href="#" onclick="cargarPagina(document.getElementById('frmentrada'), 'ap_orden_pago_transferir_retenciones_pdf.php');">Retenciones</a>
                </li>
                <?php
			}*/
			if (mysql_num_rows($query_iva) != 0) {
				?>
                <li id="li4" onclick="currentTab('tab', this);">
                    <a href="#" onclick="cargarPagina(document.getElementById('frmentrada'), 'ap_orden_pago_transferir_retenciones_iva_pdf.php');">I.V.A</a>
                </li>
                <?php
			}
			if (mysql_num_rows($query_islr) != 0) {
				?>
                <li id="li4" onclick="currentTab('tab', this);">
                    <a href="#" onclick="cargarPagina(document.getElementById('frmentrada'), 'ap_orden_pago_transferir_retenciones_islr_pdf.php');">I.S.L.R</a>
                </li>
                <?php
			}
			if (mysql_num_rows($query_mil) != 0) {
				?>
                <li id="li4" onclick="currentTab('tab', this);">
                    <a href="#" onclick="cargarPagina(document.getElementById('frmentrada'), 'ap_orden_pago_transferir_retenciones_1xmil_pdf.php');">1 X 1000</a>
                </li>
                <?php
			}
			?>
            </ul>
            </div>
        </td>
    </tr>
</table>
</form>

<center>
<iframe name="iReporte" id="iReporte" style="border-left:solid 1px #CDCDCD; border-right:solid 1px #CDCDCD; border-bottom:solid 1px #CDCDCD; border-top:0; width:1000px; height:600px;" src="<?=$tab1?>.php?registro=<?=$registro?>"></iframe>
</center>