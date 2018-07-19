<?php
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$fEstado = "PR";
	$sql = "SELECT MAX(Ejercicio) FROM ob_planobras";
	$Ejercicio = getVar3($sql);
	$fEjercicio = ($Ejercicio?$AnioActual:$AnioActual);
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodPlanObra";
}
//	------------------------------------
if ($lista == "listar") {
	$_titulo = "Formulaci&oacute;n Presupuestaria";
	$_btNuevo = "";
	$_btModificar = "";
	$_btAprobar = "display:none;";
	$_btGenerar = "display:none;";
}
elseif ($lista == "aprobar") {
	$fEstado = "PR";
	##	
	$_titulo = "Formulaci&oacute;n Presupuestaria / Aprobar";
	$_btNuevo = "display:none;";
	$_btModificar = "display:none;";
	$_btAprobar = "";
	$_btGenerar = "display:none;";
}
elseif ($lista == "generar") {
	$fEstado = "AP";
	##	
	$_titulo = "Formulaci&oacute;n Presupuestaria / Generar";
	$_btNuevo = "display:none;";
	$_btModificar = "display:none;";
	$_btAprobar = "display:none;";
	$_btGenerar = "";
}
//	------------------------------------
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (po.CategoriaProg LIKE '%".$fBuscar."%' OR
					  po.Ejercicio LIKE '%".$fBuscar."%' OR
					  po.Denominacion LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (ppo.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (d.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCategoriaProg != "") { $cCategoriaProg = "checked"; $filtro.=" AND (po.CategoriaProg = '".$fCategoriaProg."')"; } else $dCategoriaProg = "visibility:hidden;";
if ($fFechaInicio != "") { 
	$cFecha = "checked"; 
	if ($fFechaInicio != "") $filtro.=" AND ('".formatFechaAMD($fFechaInicio)."' >= po.FechaInicio AND '".formatFechaAMD($fFechaInicio)."' <= po.FechaFin)";
	if ($fFechaFin != "") $filtro.=" AND ('".formatFechaAMD($fFechaFin)."' >= po.FechaInicio AND '".formatFechaAMD($fFechaFin)."' <= po.FechaFin)";
} else $dFecha = "disabled";
if ($fEjercicio != "") { $cEjercicio = "checked"; $filtro.=" AND (po.Ejercicio = '".$fEjercicio."')"; } else $dEjercicio = "disabled";
if ($fTipoObra != "") { $cTipoObra = "checked"; $filtro.=" AND (po.TipoObra = '".$fTipoObra."')"; } else $dTipoObra = "disabled";
if ($fSituacion != "") { $cSituacion = "checked"; $filtro.=" AND (po.Situacion = '".$fSituacion."')"; } else $dSituacion = "disabled";
if ($fCodResponsable != "") { $cCodResponsable = "checked"; $filtro.=" AND (po.CodResponsable = '".$fCodResponsable."')"; } else $dCodResponsable = "visibility:hidden;";
//	------------------------------------
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=pv_presupuestoobra_lista" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />

<!--FILTRO-->
<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
	<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
		<tr>
			<td align="right">Organismo: </td>
			<td>
	            <input type="checkbox" <?=$cCodOrganismo?> onclick="this.checked=!this.checked;" />
	            <select name="fCodOrganismo" id="fCodOrganismo" style="width:242px;" <?=$dCodOrganismo?> onchange="$('#aCategoriaProg').attr('href','../lib/listas/gehen.php?anz=lista_pv_categoriaprog&filtrar=default&ventana=pv_metas&campo1=fCategoriaProg&FlagOrganismo=S&fCodOrganismo='+$(this).val()+'&iframe=true&width=100%&height=100%');">
	                <?=loadSelect2('mastorganismos','CodOrganismo','Organismo',$fCodOrganismo)?>
	            </select>
			</td>
			<td align="right">Estado: </td>
			<td>
				<?php
				if ($lista == "listar") {
					?>
		            <input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
		            <select name="fEstado" id="fEstado" style="width:110px;" <?=$dEstado?>>
		                <option value="">&nbsp;</option>
		                <?=loadSelectValores("presupuesto-obras-estado", $fEstado, 0)?>
		            </select>
					<?php
				} else {
					?>
		            <input type="checkbox" <?=$cEstado?> onclick="this.checked=!this.checked;" />
		            <select name="fEstado" id="fEstado" style="width:110px;" <?=$dEstado?>>
		                <?=loadSelectValores("presupuesto-obras-estado", $fEstado, 1)?>
		            </select>
					<?php
				}
				?>
			</td>
			<td align="right">Buscar: </td>
			<td>
				<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
				<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:242px;" <?=$dBuscar?> />
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Categoria Prog.:</td>
			<td class="gallery clearfix">
				<input type="checkbox" <?=$cCategoriaProg?> onclick="ckLista(this.checked, ['fCategoriaProg'], ['aCategoriaProg']);" />
				<input type="text" name="fCategoriaProg" id="fCategoriaProg" value="<?=$fCategoriaProg?>" style="width:133px;" readonly="readonly" />
				<a href="../lib/listas/gehen.php?anz=lista_pv_categoriaprog&filtrar=default&ventana=pv_metas&campo1=fCategoriaProg&FlagOrganismo=S&fCodOrganismo=<?=$fCodOrganismo?>&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" id="aCategoriaProg" style=" <?=$dCategoriaProg?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
			<td align="right">Tipo de Obra: </td>
			<td>
	            <input type="checkbox" <?=$cTipoObra?> onclick="chkFiltro(this.checked, 'fTipoObra');" />
	            <select name="fTipoObra" id="fTipoObra" style="width:110px;" <?=$dTipoObra?>>
	                <option value="">&nbsp;</option>
	                <?=loadSelectValores('plan-obras-tipo',$fTipoObra)?>
	            </select>
			</td>
			<td align="right">Responsable:</td>
			<td class="gallery clearfix">
				<input type="checkbox" <?=$cCodResponsable?> onclick="ckLista(this.checked, ['fCodResponsable','fCodEmpleado','fNomPersona'], ['aCodResponsable']);" />
	            <input type="hidden" name="fCodResponsable" id="fCodResponsable" value="<?=$fCodResponsable?>" />
				<input type="text" name="fCodEmpleado" id="fCodEmpleado" value="<?=$fCodEmpleado?>" style="width:50px;" readonly />
				<input type="text" name="fNomPersona" id="fNomPersona" value="<?=$fNomPersona?>" style="width:189px;" readonly />
				<a href="../lib/listas/gehen.php?anz=lista_empleados&filtrar=default&campo1=fCodResponsable&campo2=fNomPersona&campo3=fCodEmpleado&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe2]" id="aCodResponsable" style=" <?=$dCodResponsable?>">
					<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
				</a>
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Fecha: </td>
			<td>
				<input type="checkbox" <?=$cFecha?> onclick="chkCampos2(this.checked, ['fFechaInicio','fFechaFin']);" />
				<input type="text" name="fFechaInicio" id="fFechaInicio" value="<?=$fFechaInicio?>" style="width:65px;" maxlength="10" class="datepicker" <?=$dFecha?> />
				<input type="text" name="fFechaFin" id="fFechaFin" value="<?=$fFechaFin?>" style="width:65px;" maxlength="10" class="datepicker" <?=$dFecha?> />
			</td>
			<td align="right">Situaci&oacute;n: </td>
			<td>
	            <input type="checkbox" <?=$cSituacion?> onclick="chkFiltro(this.checked, 'fSituacion');" />
	            <select name="fSituacion" id="fSituacion" style="width:110px;" <?=$dSituacion?>>
	                <option value="">&nbsp;</option>
	                <?=loadSelectGeneral('plan-obras-situacion',$fSituacion)?>
	            </select>
			</td>
			<td align="right">Ejercicio: </td>
			<td>
				<input type="checkbox" <?=$cEjercicio?> onclick="chkCampos(this.checked, 'fEjercicio');" />
				<input type="text" name="fEjercicio" id="fEjercicio" value="<?=$fEjercicio?>" style="width:50px;" maxlength="4" <?=$dEjercicio?> />
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
            <input type="button" value="Nuevo" style="width:75px; <?=$_btNuevo?>" class="insert" onclick="cargarPagina(this.form, 'gehen.php?anz=pv_presupuestoobra_form&opcion=nuevo&action=pv_presupuestoobra_lista');" />
            <input type="button" value="Modificar" style="width:75px; <?=$_btModificar?>" class="update" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'pv_presupuestoobra_ajax.php', 'modulo=validar&accion=modificar', 'gehen.php?anz=pv_presupuestoobra_form&opcion=modificar&action=pv_presupuestoobra_lista', 'SELF', '');" />
            <input type="button" value="Aprobar" style="width:75px; <?=$_btAprobar?>" class="update" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'pv_presupuestoobra_ajax.php', 'modulo=validar&accion=aprobar', 'gehen.php?anz=pv_presupuestoobra_form&opcion=aprobar&action=pv_presupuestoobra_lista', 'SELF', '');" />
            <input type="button" value="Generar" style="width:75px; <?=$_btGenerar?>" class="update" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'pv_presupuestoobra_ajax.php', 'modulo=validar&accion=generar', 'gehen.php?anz=pv_presupuestoobra_form&opcion=generar&action=pv_presupuestoobra_lista', 'SELF', '');" />
            <input type="button" value="Anular" style="width:75px;" class="update" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'pv_presupuestoobra_ajax.php', 'modulo=validar&accion=anular', 'gehen.php?anz=pv_presupuestoobra_form&opcion=anular&action=pv_presupuestoobra_lista', 'SELF', '');" />
            <input type="button" value="Ver" style="width:75px;" class="ver" onclick="cargarOpcion2(this.form, 'gehen.php?anz=pv_presupuestoobra_form&opcion=ver&action=pv_presupuestoobra_lista', 'SELF', '', $('#sel_registros').val());" />
        </td>
    </tr>
</table>

<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px; margin:auto;">
	<table class="tblLista" style="width:100%; min-width:1200px;">
		<thead>
		    <tr>
		        <th width="60" onclick="order('Ejercicio')">Ejercicio</th>
		        <th width="125" onclick="order('CategoriaProg')">Cat. Prog.</th>
		        <th align="left" onclick="order('Denominacion')">Denominaci&oacute;n</th>
		        <th width="65" onclick="order('FechaInicio')">Inicio</th>
		        <th width="65" onclick="order('FechaFin')">T&eacute;rmino</th>
		        <th width="100" onclick="order('Situacion')">Situaci&oacute;n</th>
		        <th width="100" onclick="order('Estado')">Estado</th>
		    </tr>
	    </thead>
	    
	    <tbody id="lista_registros">
		<?php
		//	consulto todos
		$sql = "SELECT ppo.*
				FROM
					pv_presupuestoobra ppo
					INNER JOIN ob_planobras po ON (po.CodPlanObra = ppo.CodPlanObra)
					INNER JOIN mastdependencias d ON (d.CodDependencia = po.CodDependencia)
				WHERE 1 $filtro";
		$rows_total = getNumRows3($sql);
		//	consulto lista
		$sql = "SELECT
					ppo.*,
					po.Ejercicio,
					po.CategoriaProg,
					po.FechaInicio,
					po.FechaFin,
					po.Situacion
				FROM
					pv_presupuestoobra ppo
					INNER JOIN ob_planobras po ON (po.CodPlanObra = ppo.CodPlanObra)
					INNER JOIN mastdependencias d ON (d.CodDependencia = po.CodDependencia)
				WHERE 1 $filtro
				ORDER BY $fOrderBy
				LIMIT ".intval($limit).", ".intval($maxlimit);
		$field = getRecords($sql);
		$rows_lista = count($field);
		foreach($field as $f) {
			$id = $f['CodOrganismo'].'_'.$f['CodPresupuesto'];
			?>
			<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
				<td align="center"><?=$f['Ejercicio']?></td>
				<td align="center"><?=$f['CategoriaProg']?></td>
				<td><?=htmlentities($f['Denominacion'])?></td>
				<td align="center"><?=formatFechaDMA($f['FechaInicio'])?></td>
				<td align="center"><?=formatFechaDMA($f['FechaFin'])?></td>
				<td align="center"><?=printValoresGeneral('plan-obras-situacion',$f['Situacion'])?></td>
				<td align="center"><?=printValores('presupuesto-obras-estado',$f['Estado'])?></td>
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