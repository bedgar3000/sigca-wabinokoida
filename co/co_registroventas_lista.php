<?php
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$fPeriodo = $PeriodoActual;
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodRegistro";
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (rv.Periodo LIKE '%$fBuscar%'
					  OR rv.CodTipoDocumento LIKE '%$fBuscar%'
					  OR rv.NroDocumento LIKE '%$fBuscar%'
					  OR rv.NombreCliente LIKE '%$fBuscar%'
					  OR rv.Voucher LIKE '%$fBuscar%'
					  OR rv.Comentarios LIKE '%$fBuscar%')";
} else $dBuscar = "disabled";
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (rv.CodOrganismo = '$fCodOrganismo')"; } else $dCodOrganismo = "disabled";
if ($fCodTipoDocumento != "") { $cCodTipoDocumento = "checked"; $filtro.=" AND (rv.CodTipoDocumento = '$fCodTipoDocumento')"; } else $dCodTipoDocumento = "disabled";
if ($fSistemaFuente != "") { $cSistemaFuente = "checked"; $filtro.=" AND (rv.SistemaFuente = '$fSistemaFuente')"; } else $dSistemaFuente = "disabled";
if ($fCodPersonaCliente != "") { $cCodPersonaCliente = "checked"; $filtro.=" AND (rv.CodPersonaCliente = '$fCodPersonaCliente')"; } else $dCodPersonaCliente = "visibility:hidden;";
if ($fPeriodo != "") { $cPeriodo = "checked"; $filtro.=" AND (rv.Periodo = '$fPeriodo')"; } else $dPeriodo = "disabled";
//	------------------------------------
$_titulo = "Registro de Ventas";
$_width = 800;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=co_registroventas_lista" method="post" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />

	<!--FILTRO-->
	<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
		<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
			<tr>
				<td align="right">Organismo:</td>
				<td>
					<input type="checkbox" <?=$cCodOrganismo?> onclick="this.checked=!this.checked;" />
					<select name="fCodOrganismo" id="fCodOrganismo" style="width:225px;" <?=$dCodOrganismo?>>
						<?=getOrganismos($fCodOrganismo, 3);?>
					</select>
				</td>
				<td align="right">Tipo Doc.:</td>
				<td>
					<input type="checkbox" <?=$cCodTipoDocumento?> onclick="chkFiltro(this.checked, 'fCodTipoDocumento');" />
					<select name="fCodTipoDocumento" id="fCodTipoDocumento" style="width:150px;" <?=$dCodTipoDocumento?>>
						<option value="">&nbsp;</option>
						<?=loadSelect2('co_tipodocumento','CodTipoDocumento','Descripcion',$fCodTipoDocumento)?>
					</select>
				</td>
				<td align="right">Sistema Fuente:</td>
				<td>
					<input type="checkbox" <?=$cSistemaFuente?> onclick="chkFiltro(this.checked, 'fSistemaFuente');" />
					<select name="fSistemaFuente" id="fSistemaFuente" style="width:150px;" <?=$dSistemaFuente?>>
						<option value="">&nbsp;</option>
						<?=loadSelectValores("registro-ventas-sistema-fuente", $fSistemaFuente)?>
					</select>
				</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td align="right">Cliente:</td>
				<td class="gallery clearfix">
					<input type="checkbox" <?=$cCodPersonaCliente?> onclick="ckLista(this.checked, ['fCodPersonaCliente','fNombreCliente','fDocFiscalCliente'], ['aCodPersonaCliente']);" />
					<input type="hidden" name="fDocFiscalCliente" id="fDocFiscalCliente" value="<?=$fDocFiscalCliente?>">
					<input type="hidden" name="fCodPersonaCliente" id="fCodPersonaCliente" value="<?=$fCodPersonaCliente?>" />
					<input type="text" name="fNombreCliente" id="fNombreCliente" value="<?=htmlentities($fNombreCliente)?>" style="width:225px;" readonly />
					<a href="../lib/listas/gehen.php?anz=lista_personas&campo1=fCodPersonaCliente&campo2=fNombreCliente&campo3=fDocFiscalCliente&ventana=&filtrar=default&FlagClasePersona=S&fEsCliente=S&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" style=" <?=$dCodPersonaCliente?>" id="aCodPersonaCliente">
		            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
		            </a>
				</td>
				<td align="right">Buscar:</td>
				<td>
					<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
					<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:150px;" <?=$dBuscar?> />
				</td>
				<td align="right">Periodo:</td>
				<td>
					<input type="checkbox" <?=$cPeriodo?> onclick="chkCampos(this.checked, 'fPeriodo');" />
					<input type="text" name="fPeriodo" id="fPeriodo" value="<?=$fPeriodo?>" style="width:75px;" <?=$dPeriodo?> />
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
	            <input type="button" value="Nuevo" style="width:75px;" class="insert F1" onclick="cargarPagina(this.form, 'gehen.php?anz=co_registroventas_form&opcion=nuevo');" />
	            <input type="button" value="Modificar" style="width:75px;" class="update" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'co_registroventas_ajax.php', 'modulo=validar&accion=modificar', 'gehen.php?anz=co_registroventas_form&opcion=modificar', 'SELF', '');" />
	            <input type="button" value="Eliminar" style="width:75px;" class="delete" onclick="opcionRegistro3(this.form, $('#sel_registros').val(), 'formulario', 'eliminar', 'co_registroventas_ajax.php');" />
	            <input type="button" value="Ver" style="width:75px;" class="ver" onclick="cargarOpcion2(this.form, 'gehen.php?anz=co_registroventas_form&opcion=ver', 'SELF', '', $('#sel_registros').val());" />
	        </td>
	    </tr>
	</table>

	<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
		<table class="tblLista" style="width:100%; min-width:1400px;">
			<thead>
			    <tr>
			        <th width="50" onclick="order('Periodo')">Periodo</th>
			        <th width="75" onclick="order('SistemaFuente')">Sist. Fuente</th>
			        <th width="30" onclick="order('CodTipoDocumento')">Tipo Doc.</th>
			        <th width="75" onclick="order('NroDocumento')">Nro. Documento</th>
			        <th width="75" onclick="order('FechaDocumento')">Fec. Documento</th>
			        <th style="min-width: 300px;" align="left" onclick="order('NombreCliente')">Razón Social</th>
			        <th width="50" onclick="order('MonedaDocumento')">Moneda</th>
			        <th width="125" align="right" onclick="order('MontoTotal')">Monto Total</th>
			        <th width="125" onclick="order('Voucher')">Voucher</th>
			        <th style="min-width: 300px;" align="left" onclick="order('Comentarios')">Comentarios</th>
			    </tr>
		    </thead>
		    
		    <tbody id="lista_registros">
			<?php
			//	consulto todos
			$sql = "SELECT *
					FROM co_registroventas rv
					WHERE 1 $filtro";
			$rows_total = getNumRows3($sql);
			//	consulto lista
			$sql = "SELECT rv.*
					FROM co_registroventas rv
					WHERE 1 $filtro
					ORDER BY $fOrderBy
					LIMIT ".intval($limit).", ".intval($maxlimit);
			$field = getRecords($sql);
			$rows_lista = count($field);
			foreach($field as $f) {
				$id = $f['CodRegistro'];
				?>
				<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
					<td align="center"><?=$f['Periodo']?></td>
					<td align="center"><?=printValores('registro-ventas-sistema-fuente',$f['SistemaFuente'])?></td>
					<td align="center"><?=$f['CodTipoDocumento']?></td>
					<td align="center"><?=$f['NroDocumento']?></td>
					<td align="center"><?=$f['FechaDocumento']?></td>
					<td><?=htmlentities($f['NombreCliente'])?></td>
					<td align="center"><?=printValoresGeneral('monedas',$f['MonedaDocumento'])?></td>
					<td align="right"><?=number_format($f['MontoTotal'],2,',','.')?></td>
					<td align="center"><?=$f['Voucher']?></td>
					<td><?=htmlentities($f['Comentarios'])?></td>
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