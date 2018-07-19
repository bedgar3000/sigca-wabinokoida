<?php
list($CodOrganismo, $CodProveedor, $CodTipoDocumento, $NroDocumento, $CodVoucher) = split("[_]", $registro);
$sql = "SELECT FlagGenerarPago
		FROM ap_obligaciones
		WHERE 
			CodOrganismo = '".$CodOrganismo."' AND
			CodProveedor = '".$CodProveedor."' AND
			CodTipoDocumento = '".$CodTipoDocumento."' AND
			NroDocumento = '".$NroDocumento."'";
$FlagGenerarPago = getVar3($sql);
?>
<form name="frmentrada" id="frmentrada" method="post" target="iReporte">
<input type="hidden" name="registro" id="registro" value="<?=$registro?>" />
<table width="1000" align="center" cellpadding="0" cellspacing="0">
    <tr>
        <td>
            <div class="header">
            <ul id="tab">
            <!-- CSS Tabs -->
            <?php
			if ($_PARAMETRO['CONTONCO'] == "S") {
				$anz = "ap_generar_vouchers_provision_voucher";
				?>
				<li id="li1" onclick="current($(this));" class="current">
					<a href="#" onclick="cargarPagina(document.getElementById('frmentrada'), 'gehen.php?anz=ap_generar_vouchers_provision_voucher');">Voucher Contable</a>
				</li>
				<?php
			} else { $anz = "ap_generar_vouchers_ordenacion"; $current = "current"; }
						
			if ($_PARAMETRO['CONTPUB20'] == "S" && $FlagGenerarPago == "S") {
				?>
                <li id="li2" onclick="current($(this));" class="<?=$current?>">
                    <a href="#" onclick="cargarPagina(document.getElementById('frmentrada'), 'gehen.php?anz=ap_generar_vouchers_ordenacion');">Voucher Contable (Pub. 20)</a>
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
<iframe name="iReporte" id="iReporte" style="border-left:solid 1px #CDCDCD; border-right:solid 1px #CDCDCD; border-bottom:solid 1px #CDCDCD; border-top:0; width:1000px; height:500px;" src="gehen.php?anz=<?=$anz?>&registro=<?=$registro?>"></iframe>
</center>