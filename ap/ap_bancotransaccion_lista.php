<?php
//	------------------------------------
if ($filtrar == "default") 
{
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "NroTransaccion,Secuencia";
}
//	------------------------------------
if ($lista == "listar") 
{
	$_titulo = "Transacciones Bancarias";
	$_btNuevo = "";
	$_btModificar = "";
	$_btActualizar = "display:none;";
	##	
	if ($filtrar == "default")
	{
		$fFechaTransaccionD = "01-$MesActual-$AnioActual";
		$fFechaTransaccionH = formatFechaDMA($FechaActual);	
	}
}
elseif ($lista == "actualizar") 
{
	$_titulo = "Transacciones Bancarias / Actualizar";
	$_btNuevo = "display:none;";
	$_btModificar = "display:none;";
	$_btActualizar = "";
	##	
	if ($filtrar == "default")
	{
		$fEstado = "PR";
	}
}
//	------------------------------------
if ($fBuscar != "") 
{
	$cBuscar = "checked";
	$filtro .= " AND (bt.NroTransaccion LIKE '%".$fBuscar."%' OR 
					  bt.Secuencia LIKE '%".$fBuscar."%' OR 
					  btt.Descripcion LIKE '%".$fBuscar."%' OR 
					  bt.NroCuenta LIKE '%".$fBuscar."%' OR 
					  bt.PeriodoContable LIKE '%".$fBuscar."%' OR 
					  bt.CodigoReferenciaInterno LIKE '%".$fBuscar."%' OR 
					  bt.CodigoReferenciaBanco LIKE '%".$fBuscar."%' OR 
					  bt.NroPago LIKE '%".$fBuscar."%' OR 
					  bt.Comentarios LIKE '%".$fBuscar."%' OR ";
    if ($_PARAMETRO['CONTPUB20'] == 'S')
    {
		$filtro .= "CONCAT(bt.VoucherPeriodoPub20,'-',bt.VoucherPub20) LIKE '%".$fBuscar."%')";
    }
    else
    {
		$filtro .= "CONCAT(bt.VoucherPeriodo,'-',bt.Voucher) LIKE '%".$fBuscar."%')";
    }
} else $dBuscar = "disabled";
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (bt.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCodTipoTransaccion != "") { $cCodTipoTransaccion = "checked"; $filtro.=" AND (bt.CodTipoTransaccion = '".$fCodTipoTransaccion."')"; } else $dCodTipoTransaccion = "disabled";
if ($fCodTipoDocumento != "") { $cCodTipoDocumento = "checked"; $filtro.=" AND (bt.CodTipoDocumento = '".$fCodTipoDocumento."')"; } else $dCodTipoDocumento = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (bt.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fCodBanco != "") { $cCodBanco = "checked"; $filtro.=" AND (bt.CodBanco = '".$fCodBanco."')"; } else $dCodBanco = "disabled";
if ($fNroCuenta != "") { $cNroCuenta = "checked"; $filtro.=" AND (bt.NroCuenta = '".$fNroCuenta."')"; } else $dNroCuenta = "disabled";
if ($fFechaTransaccionD != "" || $fFechaTransaccionH != "") { 
	$cFechaTransaccion = "checked";
	if ($fFechaTransaccionD != "") $filtro.=" AND (bt.FechaTransaccion >= '".formatFechaAMD($fFechaTransaccionD)."')"; 
	if ($fFechaTransaccionH != "") $filtro.=" AND (bt.FechaTransaccion <= '".formatFechaAMD($fFechaTransaccionH)."')"; 
} else $dFechaTransaccion = "disabled";
if ($fFlagAutomatico != "") { $cFlagAutomatico = "checked"; } else $filtro.=" AND (bt.FlagAutomatico = 'N')";
//	------------------------------------
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=ap_bancotransaccion_lista" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />

<!--FILTRO-->
<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
	<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
		<tr>
			<td align="right">Organismo:</td>
			<td>
				<input type="checkbox" <?=$cCodOrganismo?> onclick="this.checked=!this.checked;" />
				<select name="fCodOrganismo" id="fCodOrganismo" style="width:250px;" <?=$dCodOrganismo?>>
					<?=getOrganismos($fCodOrganismo, 3);?>
				</select>
			</td>
			<td align="right">Fecha: </td>
			<td>
			<input type="checkbox" <?=$cFechaTransaccion?> onclick="chkCampos(this.checked, 'fFechaTransaccionD', 'fFechaTransaccionH');" />
				<input type="text" name="fFechaTransaccionD" id="fFechaTransaccionD" value="<?=$fFechaTransaccionD?>" style="width:75px;" class="datepicker" <?=$dFechaTransaccion?> />
				<input type="text" name="fFechaTransaccionH" id="fFechaTransaccionH" value="<?=$fFechaTransaccionH?>" style="width:75px;" class="datepicker" <?=$dFechaTransaccion?> />
			</td>
			<td align="right">Banco:</td>
			<td>
				<input type="checkbox" <?=$cCodBanco?> onclick="chkCampos(this.checked, 'fCodBanco', 'fNroCuenta');" />
				<select name="fCodBanco" id="fCodBanco" style="width:200px;" onchange="getOptionsSelect(this.value, 'cuentas_bancarias', 'fNroCuenta', 1);" <?=$dCodBanco?>>
	            	<option value="">&nbsp;</option>
	                <?=loadSelect2("mastbancos","CodBanco","Banco",$fCodBanco)?>
				</select>
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Tipo de Transacci&oacute;n:</td>
			<td>
				<input type="checkbox" <?=$cCodTipoTransaccion?> onclick="chkCampos(this.checked, 'fCodTipoTransaccion');" />
				<select name="fCodTipoTransaccion" id="fCodTipoTransaccion" style="width:250px;" <?=$dCodTipoTransaccion?>>
					<option value="">&nbsp;</option>
					<?=loadSelect2('ap_bancotipotransaccion','CodTipoTransaccion','Descripcion',$fCodTipoTransaccion,10)?>
				</select>
			</td>
			<td align="right">Buscar: </td>
			<td>
				<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
				<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:153px;" <?=$dBuscar?> />
			</td>
			<td align="right">Cta. Bancaria:</td>
			<td>
				<input type="checkbox" style="visibility:hidden;" />
				<select name="fNroCuenta" id="fNroCuenta" style="width:200px;" <?=$dCodBanco?>>
	            	<option value="">&nbsp;</option>
	                <?=loadSelect2("ap_ctabancaria","NroCuenta","NroCuenta",$fNroCuenta,0,['CodBanco'],[$fCodBanco])?>
				</select>
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Tipo de Documento:</td>
			<td>
				<input type="checkbox" <?=$cCodTipoDocumento?> onclick="chkCampos(this.checked, 'fCodTipoDocumento');" />
				<select name="fCodTipoDocumento" id="fCodTipoDocumento" style="width:250px;" <?=$dCodTipoDocumento?>>
					<option value="">&nbsp;</option>
					<?=loadSelect2('ap_tipodocumento','CodTipoDocumento','Descripcion',$fCodTipoDocumento,10)?>
				</select>
			</td>
			<td align="right">Estado: </td>
			<td>
				<?php
				if ($lista == "listar") {
					?>
		            <input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
		            <select name="fEstado" id="fEstado" style="width:153px;" <?=$dEstado?>>
		                <option value="">&nbsp;</option>
		                <?=loadSelectValores("ESTADO-BANCARIO",$fEstado)?>
		            </select>
					<?php
				} else {
					?>
		            <input type="checkbox" <?=$cEstado?> onclick="this.checked=!this.checked;" />
		            <select name="fEstado" id="fEstado" style="width:153px;" <?=$dEstado?>>
		                <?=loadSelectValores("ESTADO-BANCARIO",$fEstado)?>
		            </select>
					<?php
				}
				?>
			</td>
			<td>&nbsp;</td>
			<td>
				<input type="checkbox" name="fFlagAutomatico" id="fFlagAutomatico" value="S" <?=$cFlagAutomatico?> /> Autom&aacute;tico del Sistema
			</td>
	        <td align="right"><input type="submit" value="Buscar"></td>
		</tr>
	</table>
</div>
<div class="sep"></div>

<!--REGISTROS-->
<input type="hidden" name="sel_registros" id="sel_registros" />
<table class="tblBotones" style="width:100%; min-width:<?=$_width?>px;">
    <tr>
        <td><div id="rows"></div></td>
        <td align="right" class="gallery clearfix">
        	<a id="aVoucher" href="pagina.php?iframe=true" rel="prettyPhoto[iframe1]" style="display:none;"></a>

            <input type="button" value="Nuevo" style="width:75px; <?=$_btNuevo?>" class="insert" onclick="cargarPagina(this.form, 'gehen.php?anz=ap_bancotransaccion_form&opcion=nuevo&action=ap_bancotransaccion_lista');" />
            <input type="button" value="Modificar" style="width:75px; <?=$_btModificar?>" class="update" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'ap_bancotransaccion_ajax.php', 'modulo=validar&accion=modificar', 'gehen.php?anz=ap_bancotransaccion_form&opcion=modificar&action=ap_bancotransaccion_lista', 'SELF', '');" />
            <input type="button" value="Actualizar" style="width:75px; <?=$_btActualizar?>" class="update" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'ap_bancotransaccion_ajax.php', 'modulo=validar&accion=actualizar', 'gehen.php?anz=ap_bancotransaccion_form&opcion=actualizar&action=ap_bancotransaccion_lista', 'SELF', '');" />
            <input type="button" value="Desactualizar" style="width:90px; <?=$_btActualizar?>" class="update" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'ap_bancotransaccion_ajax.php', 'modulo=validar&accion=desactualizar', 'gehen.php?anz=ap_bancotransaccion_form&opcion=desactualizar&action=ap_bancotransaccion_lista', 'SELF', '');" />
            <input type="button" value="Generar Voucher" style="width:100px; <?=$_btActualizar?>" class="update" onclick="cargarOpcionValidar4(this.form, $('#sel_registros').val(), 'ap_bancotransaccion_ajax.php', 'modulo=validar&accion=generar-voucher', 'gehen.php?anz=ap_voucher&opcion=ap_bancotransaccion', 'BLANK', 'aVoucher');" />
            <input type="button" value="Ver" style="width:75px;" class="ver" onclick="cargarOpcion2(this.form, 'gehen.php?anz=ap_bancotransaccion_form&opcion=ver&action=ap_bancotransaccion_lista', 'SELF', '', $('#sel_registros').val());" /> |
            <input type="button" value="Eliminar" style="width:75px;" class="delete" onclick="opcionRegistro3(this.form, $('#sel_registros').val(), 'formulario', 'eliminar', 'ap_bancotransaccion_ajax.php');" />
            <input type="button" value="Anular" style="width:75px;" class="update" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'ap_bancotransaccion_ajax.php', 'modulo=validar&accion=anular', 'gehen.php?anz=ap_bancotransaccion_form&opcion=anular&action=ap_bancotransaccion_lista', 'SELF', '');" />
        </td>
    </tr>
</table>

<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px; margin:auto;">
	<table class="tblLista" style="width:100%; min-width:2800px;">
		<thead>
		    <tr>
		        <th width="60" onclick="order('NroTransaccion')">N&uacute;mero</th>
		        <th width="25" onclick="order('Secuencia')">#</th>
		        <th width="90" onclick="order('Estado')">Estado</th>
		        <th width="75" onclick="order('FechaTransaccion')">Fecha</th>
		        <th width="300" onclick="order('NomTipoTransaccion')">Transacci&oacute;n</th>
		        <th width="25" onclick="order('TipoTransaccion')">I/E</th>
		        <th width="125" align="right" onclick="order('Monto')">Monto</th>
		        <th width="140" onclick="order('NroCuenta')">Cta. Bancaria</th>
		        <th width="30" onclick="order('FlagPresupuesto')">Afe. Pre.</th>
		        <th width="30" onclick="order('FlagAutomatico')">Auto</th>
		        <th width="60" onclick="order('PeriodoContable')">Periodo</th>
		        <?php
		        if ($_PARAMETRO['CONTPUB20'] == 'S')
		        {
		        	?><th width="115" onclick="order('VoucherPeriodoPub20,VoucherPub20')">Voucher (Pub.20)</th><?php
		        }
		        else
		        {
		        	?><th width="115" onclick="order('VoucherPeriodo,Voucher')">Voucher</th><?php	
		        }
		        ?>
		        <th width="150" onclick="order('CodigoReferenciaInterno')">Nro. Documento</th>
		        <th width="150" onclick="order('CodigoReferenciaBanco')">Doc. Referencia Banco</th>
		        <th width="150" onclick="order('NroPago')">Cheque</th>
		        <th align="left" onclick="order('Comentarios')">Comentarios</th>
		    </tr>
	    </thead>
	    
	    <tbody id="lista_registros">
		<?php
		//	consulto todos
		$sql = "SELECT
					bt.*,
					btt.Descripcion AS NomTipoTransaccion
				FROM
					ap_bancotransaccion bt
					INNER JOIN ap_bancotipotransaccion btt ON btt.CodTipoTransaccion = bt.CodTipoTransaccion
				WHERE 1 $filtro";
		$rows_total = getNumRows3($sql);
		//	consulto lista
		$sql = "SELECT
					bt.*,
					btt.Descripcion AS NomTipoTransaccion
				FROM
					ap_bancotransaccion bt
					INNER JOIN ap_bancotipotransaccion btt ON btt.CodTipoTransaccion = bt.CodTipoTransaccion
				WHERE 1 $filtro
				ORDER BY $fOrderBy
				LIMIT ".intval($limit).", ".intval($maxlimit);
		$field = getRecords($sql);
		$rows_lista = count($field);
		foreach($field as $f) {
			$id = $f['NroTransaccion'].'_'.$f['Secuencia'];
			?>
			<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
				<td align="center"><?=$f['NroTransaccion']?></td>
				<td align="center"><?=$f['Secuencia']?></td>
				<td align="center"><?=printValores("ESTADO-BANCARIO", $f['Estado'])?></td>
				<td align="center"><?=formatFechaDMA($f['FechaTransaccion'])?></td>
				<td><?=htmlentities($f['NomTipoTransaccion'])?></td>
				<td align="center"><?=$f['TipoTransaccion']?></td>
				<td align="right"><strong><?=number_format($f['Monto'], 2, ',', '.')?></strong></td>
				<td align="center"><?=$f['NroCuenta']?></td>
				<td align="center"><?=printFlag($f['FlagPresupuesto'])?></td>
				<td align="center"><?=printFlag($f['FlagAutomatico'])?></td>
				<td align="center"><?=$f['PeriodoContable']?></td>
		        <?php
		        if ($_PARAMETRO['CONTPUB20'] == 'S')
		        {
		        	?><td align="center"><?=$f['VoucherPeriodoPub20']?>-<?=$f['VoucherPub20']?></td><?php
		        }
		        else
		        {
		        	?><td align="center"><?=$f['VoucherPeriodo']?>-<?=$f['Voucher']?></td><?php
		        }
		        ?>
				<td align="center"><?=$f['CodigoReferenciaInterno']?></td>
				<td align="center"><?=$f['CodigoReferenciaBanco']?></td>
				<td align="center"><?=$f['NroPago']?></td>
				<td><?=htmlentities($f['Comentarios'])?></td>
			</tr>
			<?php
		}
		?>
	    </tbody>
	</table>
</div>

<table style="width:100%; min-width:<?=$_width?>px; margin:auto;">
	<tr>
    	<td>
        	Mostrar: 
            <select name="maxlimit" style="width:50px;" onchange="this.form.submit();">
                <?=loadSelectGeneral("MAXLIMIT", $maxlimit, 0)?>
            </select>
        </td>
        <td align="right">
        	<?=paginacion(intval($rows_total), intval($rows_lista), intval($maxlimit), intval($limit));?>
        </td>
    </tr>
</table>
</form>

<?php
if ($GenerarVoucher == 'S')
{
	?>
	<script type="text/javascript" language="javascript">
		$(document).ready(function() {
			var url = "gehen.php?anz=ap_voucher&opcion=ap_bancotransaccion&sel_registros=<?=$NroTransaccion?>&iframe=true&width=100%&height=100%";
			$("#aVoucher").attr("href", url);
			document.getElementById("aVoucher").click();
		});
	</script>
	<?php
}
?>