<?php
//	------------------------------------
if ($filtrar == "default") {
	$fEstado = 'A';
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodImpuesto";
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (i.CodImpuesto LIKE '%$fBuscar%'
					  OR i.Descripcion LIKE '%$fBuscar%'
					  OR rf.Descripcion LIKE '%$fBuscar%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (i.Estado = '$fEstado')"; } else $dEstado = "disabled";
if ($fCodRegimenFiscal != "") { $cCodRegimenFiscal = "checked"; $filtro.=" AND (i.CodRegimenFiscal = '$fCodRegimenFiscal')"; } else $dCodRegimenFiscal = "disabled";
if ($fFlagImponible != "") { $cFlagImponible = "checked"; $filtro.=" AND (i.FlagImponible = '$fFlagImponible')"; } else $dFlagImponible = "disabled";
if ($fFlagProvision != "") { $cFlagProvision = "checked"; $filtro.=" AND (i.FlagProvision = '$fFlagProvision')"; } else $dFlagProvision = "disabled";
if ($fTipoComprobante != "") { $cTipoComprobante = "checked"; $filtro.=" AND (i.TipoComprobante = '$fTipoComprobante')"; } else $dTipoComprobante = "disabled";
//	------------------------------------
$_titulo = "Impuestos";
$_width = 700;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=ap_impuestos_lista" method="post" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />

	<!--FILTRO-->
	<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
		<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
			<tr>
				<td align="right">Régimen Fiscal: </td>
				<td>
		            <input type="checkbox" <?=$cCodRegimenFiscal?> onclick="chkFiltro(this.checked, 'fCodRegimenFiscal');" />
		            <select name="fCodRegimenFiscal" id="fCodRegimenFiscal" style="width:175px;" <?=$dCodRegimenFiscal?>>
		                <option value="">&nbsp;</option>
		                <?=loadSelect2('ap_regimenfiscal', 'CodRegimenFiscal', 'Descripcion', $fCodRegimenFiscal, 0)?>
		            </select>
				</td>
				<td align="right">Imponible:</td>
				<td>
		            <input type="checkbox" <?=$cFlagImponible?> onclick="chkFiltro(this.checked, 'fFlagImponible');" />
		            <select name="fFlagImponible" id="fFlagImponible" style="width:175px;" <?=$dFlagImponible?>>
		                <option value="">&nbsp;</option>
		                <?=loadSelectValores("IMPUESTO-IMPONIBLE", $fFlagImponible)?>
		            </select>
				</td>
				<td align="right">Buscar:</td>
				<td>
					<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
					<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:175px;" <?=$dBuscar?> />
				</td>
		        <td>&nbsp;</td>
			</tr>
			<tr>
				<td align="right">Tipo de Comprobante:</td>
				<td>
		            <input type="checkbox" <?=$cTipoComprobante?> onclick="chkFiltro(this.checked, 'fTipoComprobante');" />
		            <select name="fTipoComprobante" id="fTipoComprobante" style="width:175px;" <?=$dTipoComprobante?>>
		                <option value="">&nbsp;</option>
		                <?=loadSelectValores("IMPUESTO-COMPROBANTE", $fTipoComprobante)?>
		            </select>
				</td>
				<td align="right">Provisión En:</td>
				<td>
		            <input type="checkbox" <?=$cFlagProvision?> onclick="chkFiltro(this.checked, 'fFlagProvision');" />
		            <select name="fFlagProvision" id="fFlagProvision" style="width:175px;" <?=$dFlagProvision?>>
		                <option value="">&nbsp;</option>
		                <?=loadSelectValores("IMPUESTO-PROVISION", $fFlagProvision)?>
		            </select>
				</td>
				<td align="right">Estado: </td>
				<td>
		            <input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
		            <select name="fEstado" id="fEstado" style="width:175px;" <?=$dEstado?>>
		                <option value="">&nbsp;</option>
		                <?=loadSelectGeneral("ESTADO", $fEstado)?>
		            </select>
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
	        <td align="right">
	            <input type="button" value="Nuevo" style="width:75px;" class="insert" onclick="cargarPagina(this.form, 'gehen.php?anz=ap_impuestos_form&opcion=nuevo');" />
	            <input type="button" value="Modificar" style="width:75px;" class="update" onclick="cargarOpcion2(this.form, 'gehen.php?anz=ap_impuestos_form&opcion=modificar', 'SELF', '', $('#sel_registros').val());" />
	            <input type="button" value="Eliminar" style="width:75px;" class="delete" onclick="opcionRegistro3(this.form, $('#sel_registros').val(), 'formulario', 'eliminar', 'ap_impuestos_ajax.php');" />
	            <input type="button" value="Ver" style="width:75px;" class="ver" onclick="cargarOpcion2(this.form, 'gehen.php?anz=ap_impuestos_form&opcion=ver', 'SELF', '', $('#sel_registros').val());" />
	        </td>
	    </tr>
	</table>

	<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
		<table class="tblLista" style="width:100%; min-width:<?=$_width?>px;">
			<thead>
			    <tr>
			        <th width="60" onclick="order('CodImpuesto')">Código</th>
			        <th style="min-width: 250px;" align="left" onclick="order('Descripcion')">Descripción</th>
			        <th width="60" onclick="order('TipoComprobante')">Tipo</th>
			        <th width="125" align="left" onclick="order('RegimenFiscal')">Régimen Fiscal</th>
			        <th width="75" align="right" onclick="order('FactorPorcentaje')">%</th>
			        <th width="150" onclick="order('FlagProvision')">Provisión En</th>
			        <th width="100" onclick="order('FlagImponible')">Imponible</th>
			        <th width="60" onclick="order('Signo')">Signo</th>
			        <th width="75" onclick="order('Estado')">Estado</th>
			    </tr>
		    </thead>
		    
		    <tbody id="lista_registros">
			<?php
			//	consulto todos
			$sql = "SELECT *
					FROM mastimpuestos i
					INNER JOIN ap_regimenfiscal rf ON rf.CodRegimenFiscal = i.CodRegimenFiscal
					WHERE 1 $filtro";
			$rows_total = getNumRows3($sql);
			//	consulto lista
			$sql = "SELECT
						i.*,
						rf.Descripcion AS RegimenFiscal
					FROM mastimpuestos i
					INNER JOIN ap_regimenfiscal rf ON rf.CodRegimenFiscal = i.CodRegimenFiscal
					WHERE 1 $filtro
					ORDER BY $fOrderBy
					LIMIT ".intval($limit).", ".intval($maxlimit);
			$field = getRecords($sql);
			$rows_lista = count($field);
			foreach($field as $f) {
				$id = $f['CodImpuesto'];
				?>
				<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
					<td align="center"><?=$f['CodImpuesto']?></td>
					<td><?=htmlentities($f['Descripcion'])?></td>
            		<td align="center"><?=printValores("IMPUESTO-COMPROBANTE", $f['TipoComprobante'])?></td>
					<td><?=htmlentities($f['RegimenFiscal'])?></td>
					<td align="right"><strong><?=number_format($f['FactorPorcentaje'],2,',','.')?></strong></td>
		            <td><?=printValores("IMPUESTO-PROVISION", $f['FlagProvision'])?></td>
		            <td><?=printValores("IMPUESTO-IMPONIBLE", $f['FlagImponible'])?></td>
            		<td align="center"><?=printSigno($f['Signo'])?></td>
					<td align="center"><?=printValoresGeneral('ESTADO',$f['Estado'])?></td>
				</tr>
				<?php
			}
			?>
		    </tbody>
		</table>
	</div>

	<table style="width:100%; min-width:<?=$_width?>px;">
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