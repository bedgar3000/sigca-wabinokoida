<?php
if ($opcion == "nuevo") {
	$field['Estado'] = "A";
	##
	$titulo = "Nuevo Registro";
	$accion = "nuevo";
	$label_submit = "Guardar";
	$disabled_nuevo = "disabled";
	$disabled_obligacion = "disabled";
	$visibility_obligacion = "visibility:hidden;";
	$clkCancelar = "document.getElementById('frmentrada').submit();";
	$focus = "Descripcion";
}
elseif ($opcion == "modificar" || $opcion == "ver") {
	//	consulto datos generales
	$sql = "SELECT
				c.*,
				p.NomCompleto AS NomPersona
			FROM
				pr_concepto c
				LEFT JOIN mastpersonas p ON (p.CodPersona = c.CodPersona)
			WHERE c.CodConcepto = '".$sel_registros."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query)) $field = mysql_fetch_array($query);
	
	if ($opcion == "modificar") {
		$titulo = "Modificar Registro";
		$accion = "modificar";
		$disabled_modificar = "disabled";
		$display_ver = "display:none;";
		if ($field['FlagObligacion'] != "S") { $visibility_obligacion = "visibility:hidden;"; $disabled_obligacion = "disabled"; }
		$label_submit = "Modificar";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
		$focus = "Descripcion";
	}
	
	elseif ($opcion == "ver") {
		$titulo = "Ver Registro";
		$disabled_nuevo = "disabled";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$disabled_obligacion = "disabled";
		$display_ver = "display:none;";
		$display_submit = "display:none;";
		$visibility_obligacion = "visibility:hidden;";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
	}
}
//	------------------------------------
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<table width="800" align="center" cellpadding="0" cellspacing="0">
	<tr>
    	<td>
            <div class="header" style="width:800px;">
            <ul id="tab">
            <!-- CSS Tabs -->
            <li id="li1" onclick="currentTab('tab', this);" class="current">
            	<a href="#" onclick="mostrarTab('tab', 1, 3);">Informaci&oacute;n General</a>
            </li>
            <li id="li2" onclick="currentTab('tab', this);">
            	<a href="#" onclick="mostrarTab('tab', 2, 3);">Formula</a>
            </li>
            <li id="li3" onclick="currentTab('tab', this);">
            	<a href="#" onclick="mostrarTab('tab', 3, 3);">Informaci&oacute;n Contable</a>
            </li>
            </ul>
            </div>
        </td>
    </tr>
</table>

<div id="tab1" style="display:block;">
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=pr_conceptos_lista" method="POST" enctype="multipart/form-data" onsubmit="return form(this, '<?=$accion?>');" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
<input type="hidden" name="fTipo" id="fTipo" value="<?=$fTipo?>" />
<table width="800" class="tblForm">
	<tr>
    	<td colspan="4" class="divFormCaption">Datos Generales</td>
    </tr>
	<tr>
		<td class="tagForm" width="100">C&oacute;digo:</td>
		<td>
            <input type="text" id="CodConcepto" style="width:50px;" class="codigo" value="<?=$field['CodConcepto']?>" disabled />
		</td>
		<td class="tagForm" width="100">* Tipo:</td>
		<td>
			<select id="Tipo" style="width:100px;" <?=$disabled_ver?>>
				<?=loadSelectValores("CONCEPTO-TIPO", $field['Tipo'], 0)?>
			</select>
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Descripci&oacute;n:</td>
		<td>
            <input type="text" id="Descripcion" style="width:275px;" maxlength="100" value="<?=htmlentities($field['Descripcion'])?>" <?=$disabled_ver?> />
		</td>
		<td class="tagForm">Abreviatura:</td>
		<td>
            <input type="text" id="Abreviatura" style="width:95px;" maxlength="10" value="<?=htmlentities($field['Abreviatura'])?>" <?=$disabled_ver?> />
		</td>
    </tr>
    <tr>
		<td class="tagForm">* Impresi&oacute;n:</td>
		<td>
            <input type="text" id="TextoImpresion" style="width:275px;" maxlength="50" value="<?=htmlentities($field['TextoImpresion'])?>" <?=$disabled_ver?> />
		</td>
		<td class="tagForm">Orden en Boleta:</td>
		<td>
            <input type="text" id="PlanillaOrden" style="width:50px;" maxlength="2" value="<?=htmlentities($field['PlanillaOrden'])?>" <?=$disabled_ver?> />
		</td>
    </tr>
	<tr>
    	<td colspan="4" class="divFormCaption">Caracter&iacute;sticas</td>
    </tr>
    <tr>
		<td class="tagForm">&nbsp;</td>
		<td>
			<input type="checkbox" id="FlagAutomatico" <?=chkOpt($field['FlagAutomatico'], "S")?> <?=$disabled_ver?> /> Asignaci&oacute;n Autom&aacute;tica
		</td>
		<td class="tagForm">&nbsp;</td>
		<td>
			<input type="checkbox" id="FlagBono" <?=chkOpt($field['FlagBono'], "S")?> <?=$disabled_ver?> /> Bonificaci&oacute;n
		</td>
	</tr>
    <tr>
		<td class="tagForm">&nbsp;</td>
		<td>
			<input type="checkbox" id="FlagRetencion" <?=chkOpt($field['FlagRetencion'], "S")?> <?=$disabled_ver?> /> Retenci&oacute;n
		</td>
		<td class="tagForm">&nbsp;</td>
		<td>
			<input type="checkbox" id="FlagBonoRemuneracion" <?=chkOpt($field['FlagBonoRemuneracion'], "S")?> <?=$disabled_ver?> /> Bonificaci&oacute;n (Remunerada)
		</td>
	</tr>
    <tr>
		<td class="tagForm">&nbsp;</td>
		<td>
			<input type="checkbox" id="FlagRelacionIngreso" <?=chkOpt($field['FlagRelacionIngreso'], "S")?> <?=$disabled_ver?> /> Mostrar en relaci&oacute;n de ingresos
		</td>
		<td class="tagForm">&nbsp;</td>
		<td>
			<input type="checkbox" id="FlagJubilacion" <?=chkOpt($field['FlagJubilacion'], "S")?> <?=$disabled_ver?> /> Jubilados / Pensionados
		</td>
	</tr>
    <tr>
		<td class="tagForm">&nbsp;</td>
		<td>
			<input type="checkbox" id="FlagDiferencia" <?=chkOpt($field['FlagDiferencia'], "S")?> <?=$disabled_ver?> /> Diferencia de Ingresos
		</td>
        <td class="tagForm">&nbsp;</td>
        <td>
            <input type="checkbox" id="FlagBonoAlimentacion" <?=chkOpt($field['FlagBonoAlimentacion'], "S")?> <?=$disabled_ver?> /> Bono de Alimentaci&oacute;n
        </td>
	</tr>
    <tr>
		<td class="tagForm">Estado:</td>
		<td colspan="3">
			<input type="radio" name="Estado" id="Activo" value="A" <?=chkOpt($field['Estado'], "A")?> <?=$disabled_nuevo?> /> Activo
            &nbsp;&nbsp;
            <input type="radio" name="Estado" id="Inactivo" value="I" <?=chkOpt($field['Estado'], "I");?> <?=$disabled_nuevo?> /> Inactivo
		</td>
	</tr>
    <tr>
		<td class="tagForm">&Uacute;ltima Modif.:</td>
		<td colspan="3">
			<input type="text" size="30" value="<?=$field['UltimoUsuario']?>" disabled="disabled" />
			<input type="text" size="25" value="<?=$field['UltimaFecha']?>" disabled="disabled" />
		</td>
	</tr>
</table>
<center>
<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" />
<input type="button" value="Cancelar" style="width:75px;" onclick="<?=$clkCancelar?>" />
</center>
</form>
<br />
<table align="center" width="800" cellpadding="0" cellspacing="0">
<tr>
    <td>
        <form name="frm_nominas" id="frm_nominas">
        <input type="hidden" id="sel_nominas" />
        <table width="400" class="tblBotones">
            <thead>
                <th class="divFormCaption">Tipos de N&oacute;mina</th>
            </thead>
            <tbody>
            <tr>
                <td align="right" class="gallery clearfix">
                    <a id="a_nominas" href="../lib/listas/listado_tipo_nomina.php?filtrar=default&ventana=conceptos_nominas_insertar&detalle=nominas&iframe=true&width=475&height=410" rel="prettyPhoto[iframe1]" style="display:none;"></a>
                    <input type="button" class="btLista" value="Insertar" <?=$disabled_nominas?> onclick="$('#a_nominas').click();" />
                    <input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'nominas');" <?=$disabled_nominas?> />
                </td>
            </tr>
            </tbody>
        </table>
        <div style="overflow:scroll; width:400px; height:200px;">
        <table width="100%" class="tblLista">
            <thead>
            <tr>
                <th scope="col" width="15">&nbsp;</th>
                <th scope="col" align="left">Tipo de N&oacute;mina</th>
            </tr>
            </thead>
            
            <tbody id="lista_nominas">
            <?php
            $sql = "SELECT
						ctn.CodTipoNom,
						tn.Nomina
                    FROM
						pr_conceptotiponomina ctn
						INNER JOIN tiponomina tn ON (tn.CodTipoNom = ctn.CodTipoNom)
                    WHERE ctn.CodConcepto = '".$field['CodConcepto']."'
                    ORDER BY CodTipoNom";
            $query_nominas = mysql_query($sql) or die ($sql.mysql_error());
            while ($field_nominas = mysql_fetch_array($query_nominas)) {	$nro_nominas++;
                ?>
                <tr class="trListaBody" onclick="clk($(this), 'nominas', '<?=$field_nominas['CodTipoNom']?>');" id="nominas_<?=$field_nominas['CodTipoNom']?>">
                    <th>
                        <?=$nro_nominas?>
                    </th>
                    <td>
                        <input type="hidden" name="CodTipoNom" value="<?=$field_nominas['CodTipoNom']?>" />
                        <?=htmlentities($field_nominas['Nomina'])?>
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        </div>
        <input type="hidden" id="nro_nominas" value="<?=$nro_nominas?>" />
        <input type="hidden" id="can_nominas" value="<?=$nro_nominas?>" />
        </form>
    </td>
    <td>
    	<form name="frm_procesos" id="frm_procesos">
        <input type="hidden" id="sel_procesos" />
        <table width="400" class="tblBotones">
            <thead>
                <th class="divFormCaption">Tipos de Proceso</th>
            </thead>
            <tbody>
            <tr>
                <td align="right" class="gallery clearfix">
                    <a id="a_procesos" href="../lib/listas/listado_tipo_proceso.php?filtrar=default&ventana=conceptos_procesos_insertar&detalle=procesos&iframe=true&width=500&height=410" rel="prettyPhoto[iframe2]" style="display:none;"></a>
                    <input type="button" class="btLista" value="Insertar" <?=$disabled_procesos?> onclick="$('#a_procesos').click();" />
                    <input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'procesos');" <?=$disabled_procesos?> />
                </td>
            </tr>
            </tbody>
        </table>
        <div style="overflow:scroll; width:400px; height:200px;">
        <table width="100%" class="tblLista">
            <thead>
            <tr>
                <th scope="col" width="15">&nbsp;</th>
                <th scope="col" align="left">Tipo de Proceso</th>
            </tr>
            </thead>
            
            <tbody id="lista_procesos">
            <?php
            $sql = "SELECT
						cp.CodTipoProceso,
						tp.Descripcion AS NomTipoProceso
                    FROM
						pr_conceptoproceso cp
						INNER JOIN pr_tipoproceso tp ON (tp.CodTipoProceso = cp.CodTipoProceso)
                    WHERE cp.CodConcepto = '".$field['CodConcepto']."'
                    ORDER BY CodTipoProceso";
            $query_procesos = mysql_query($sql) or die ($sql.mysql_error());
            while ($field_procesos = mysql_fetch_array($query_procesos)) {	$nro_procesos++;
                ?>
                <tr class="trListaBody" onclick="clk($(this), 'procesos', '<?=$field_procesos['CodTipoProceso']?>');" id="procesos_<?=$field_procesos['CodTipoProceso']?>">
                    <th>
                        <?=$nro_procesos?>
                    </th>
                    <td>
                        <input type="hidden" name="CodTipoProceso" value="<?=$field_procesos['CodTipoProceso']?>" />
                        <?=htmlentities($field_procesos['NomTipoProceso'])?>
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        </div>
        <input type="hidden" id="nro_procesos" value="<?=$nro_procesos?>" />
        <input type="hidden" id="can_procesos" value="<?=$nro_procesos?>" />
        </form>
    </td>
</tr>
</table>

</div>

<div id="tab2" style="display:none;">
<table width="800" class="tblForm">
<tr>
    <td colspan="2" class="divFormCaption">Formula</td>
</tr>
<tr>
    <td valign="top" style="padding-top:2px;">
        <textarea id="Formula" style="width:565px; height:205px; background-color:#FFF;"><?=htmlentities($field['Formula'])?></textarea>
    </td>
    <td align="center" valign="top">
        <table cellpadding="0" cellspacing="0">
        <tr>
            <td width="25">
            	<input type="button" style="width:100%;" value="A" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td width="25">
            	<input type="button" style="width:100%;" value="B" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td width="25">
            	<input type="button" style="width:100%;" value="C" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td width="25">
            	<input type="button" style="width:100%;" value="D" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td width="25">
            	<input type="button" style="width:100%;" value="E" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td width="25">
            	<input type="button" style="width:100%;" value="F" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td width="25">
            	<input type="button" style="width:100%;" value="G" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td width="25">
            	<input type="button" style="width:100%;" value="H" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td width="25">
            	<input type="button" style="width:100%;" value="I" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
        </tr>
        <tr>
            <td>
            	<input type="button" style="width:100%;" value="J" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<input type="button" style="width:100%;" value="K" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<input type="button" style="width:100%;" value="L" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<input type="button" style="width:100%;" value="M" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<input type="button" style="width:100%;" value="N" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<input type="button" style="width:100%;" value="O" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<input type="button" style="width:100%;" value="P" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<input type="button" style="width:100%;" value="Q" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<input type="button" style="width:100%;" value="R" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
        </tr>
        <tr>
            <td>
            	<input type="button" style="width:100%;" value="S" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<input type="button" style="width:100%;" value="T" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<input type="button" style="width:100%;" value="U" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<input type="button" style="width:100%;" value="V" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<input type="button" style="width:100%;" value="W" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<input type="button" style="width:100%;" value="X" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<input type="button" style="width:100%;" value="Y" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<input type="button" style="width:100%;" value="Z" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<input type="button" style="width:100%;" value="$" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
        </tr>
        <tr>
            <td>
            	<input type="button" style="width:100%;" value="7" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<input type="button" style="width:100%;" value="8" onclick="$('#Formula').insertAtCaret($(this).val());" />
			</td>
            <td>
            	<input type="button" style="width:100%;" value="9" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<input type="button" style="width:100%;" value="+" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<input type="button" style="width:100%;" value="(" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<input type="button" style="width:100%;" value=")" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<input type="button" style="width:100%;" value="==" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<input type="button" style="width:100%;" value="_" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<input type="button" style="width:100%;" value="&&" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
        </tr>
        <tr>
            <td>
            	<input type="button" style="width:100%;" value="4" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<input type="button" style="width:100%;" value="5" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<input type="button" style="width:100%;" value="6" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<input type="button" style="width:100%;" value="-" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<input type="button" style="width:100%;" value="{" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<input type="button" style="width:100%;" value="}" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<input type="button" style="width:100%;" value="!=" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<input type="button" style="width:100%;" value='"' onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<input type="button" style="width:100%;" value="IF" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
        </tr>
        <tr>
            <td>
            	<input type="button" style="width:100%;" value="3" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<input type="button" style="width:100%;" value="2" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<input type="button" style="width:100%;" value="1" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<input type="button" style="width:100%;" value="/" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<input type="button" style="width:100%;" value="<" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<input type="button" style="width:100%;" value=">" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<input type="button" style="width:100%;" value="!" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td colspan="2">
            	<input type="button" style="width:100%;" value="ELSE" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
        </tr>
        <tr>
            <td>
            	<input type="button" style="width:100%;" value="=" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<input type="button" style="width:100%;" value="0" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<input type="button" style="width:100%;" value="." onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<input type="button" style="width:100%;" value="*" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<input type="button" style="width:100%;" value="<=" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<input type="button" style="width:100%;" value=">=" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<input type="button" style="width:100%;" value="%" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td colspan="2">
            	<button style="width:100%;" onclick="setFormula($(this).val(), 'enter');"><img src="../imagenes/enter.png" width="16" height="16" /></button>
            </td>
        </tr>
        <tr>
            <td>
            	<input type="button" style="width:100%;" value="||" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<input type="button" style="width:100%;" value="," onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td colspan="4">&nbsp;
            	
            </td>
            <td>
            	<input type="button" style="width:100%;" value=";" onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
            <td>
            	<button style="width:100%;" onclick="$('.span-selected').remove(); $('#Formula :last').addClass('span-selected');"><img src="../imagenes/backspace.png" width="16" height="18" /></button>
            </td>
            <td>
            	<input type="button" style="width:100%;" value=" " onclick="$('#Formula').insertAtCaret($(this).val());" />
            </td>
        </tr>
        <tr>
            <td colspan="3">
            	<input type="button" style="width:100%" value="MONTO" onclick="$('#Formula').insertAtCaret('$_'+$(this).val());" />
            </td>
            <td colspan="3">
            	<input type="button" style="width:100%" value="CANTIDAD" onclick="$('#Formula').insertAtCaret('$_'+$(this).val());" />
            </td>
            <td colspan="3">
            	<input type="button" style="width:100%" value="LIMPIAR" onclick="$('#Formula').val('');" />
            </td>
        </tr>
        </table>
    </td>
</tr>
</table>

<table align="center">
<tr>
	<td valign="top">
    	<div class="divFormCaption">Variables</div>
    	<div style="overflow:scroll; width:220px; height:150px;">
        <table width="100%" class="tblLista">
            <tbody>
            <?php
            $sql = "SELECT *
					FROM pr_variables
					WHERE Estado = 'A'
					ORDER BY Variable";
            $query_variables = mysql_query($sql) or die ($sql.mysql_error());
            while ($field_variables = mysql_fetch_array($query_variables)) {	$nro_variables++;
                ?>
                <tr class="trListaBody" onclick="$('#Formula').insertAtCaret('$_<?=$field_variables['Variable']?>');">
                    <td title="<?=htmlentities($field_variables['Descripcion'])?>">
                        <?=$field_variables['Variable']?>
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        </div>
        
    	<div class="divFormCaption">Par&aacute;metros</div>
    	<div style="overflow:scroll; width:220px; height:150px;">
        <table width="100%" class="tblLista">
            <tbody>
            <?php
            $sql = "SELECT
						ParametroClave,
						DescripcionParam
					FROM mastparametros
					WHERE Estado = 'A'
					ORDER BY ParametroClave";
            $query_parametros = mysql_query($sql) or die ($sql.mysql_error());
            while ($field_parametros = mysql_fetch_array($query_parametros)) {	$nro_parametros++;
                ?>
                <tr class="trListaBody" onclick="$('#Formula').insertAtCaret('$P_<?=$field_parametros['ParametroClave']?>');">
                    <td title="<?=htmlentities($field_parametros['DescripcionParam'])?>">
                        <?=$field_parametros['ParametroClave']?>
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        </div>
    </td>
    <td valign="top">
    	<div class="divFormCaption">Funciones</div>
    	<div style="overflow:scroll; width:285px; height:310px;">
        <table width="100%" class="tblLista">
            <tbody>
            <?php
            $sql = "SELECT *
					FROM pr_funciones
					WHERE Estado = 'A'
					ORDER BY Funcion";
            $query_funciones = mysql_query($sql) or die ($sql.mysql_error());
            while ($field_funciones = mysql_fetch_array($query_funciones)) {	$nro_funciones++;
                ?>
                <tr class="trListaBody" onclick="$('#Formula').insertAtCaret('<?=$field_funciones['Funcion']?>()');">
                    <td title="<?=htmlentities($field_funciones['Descripcion'])?>">
                        <?=$field_funciones['Funcion']?>
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        </div>
    </td>
    <td valign="top">
    	<div class="divFormCaption">Conceptos</div>
    	<div style="overflow:scroll; width:285px; height:310px;">
        <table width="500" class="tblLista">
            <tbody>
            <?php
            $sql = "SELECT *
					FROM pr_concepto
					WHERE Estado = 'A'
					ORDER BY Tipo, CodConcepto";
            $query_conceptos = mysql_query($sql) or die ($sql.mysql_error());
            while ($field_conceptos = mysql_fetch_array($query_conceptos)) {	$nro_conceptos++;
				if ($Grupo != $field_conceptos['Tipo']) {
					$Grupo = $field_conceptos['Tipo'];
					?>
					<tr class="trListaBody2">
						<td colspan="2">
							<?=htmlentities(printValores("CONCEPTO-TIPO", $field_conceptos['Tipo']))?>
						</td>
					</tr>
					<?php
				}
                ?>
                <tr class="trListaBody" onclick="$('#Formula').insertAtCaret('$C_<?=$field_conceptos['CodConcepto']?>');">
                	<th><?=$field_conceptos['CodConcepto']?></th>
                    <td>
                        <?=htmlentities($field_conceptos['Descripcion'])?>
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        </div>
    </td>
</tr>
</table>
</div>

<div id="tab3" style="display:none;">
<center>
<table width="800" class="tblBotones">
    <thead>
        <th class="divFormCaption">Informaci&oacute;n Contable</th>
    </thead>
</table>
<div style="overflow:scroll; width:800px; height:300px;">
<table width="1100" class="tblLista">
    <thead>
    <tr>
        <th width="150" align="left" rowspan="2" scope="col">Perfil</th>
        <th rowspan="2" align="left" scope="col">Tipo de Proceso</th>
        <th width="75" rowspan="2" scope="col">Partida</th>
        <th colspan="2" scope="col">Debe</th>
        <th width="15" rowspan="2" scope="col">C.C</th>
        <th colspan="2" scope="col">Haber</th>
        <th scope="col" width="15" rowspan="2">C.C</th>
    </tr>
    <tr>
      <th scope="col" width="100">Cuenta</th>
      <th scope="col" width="100">Pub. 20</th>
      <th scope="col" width="100">Cuenta</th>
      <th scope="col" width="100">Pub. 20</th>
    </tr>
    </thead>
    
    <tbody>
    <?php
    $sql = "SELECT
				cpd.*,
				cp.Descripcion AS NomPerfilConcepto,
				tp.Descripcion AS NomTipoProceso
			FROM
				pr_conceptoperfildetalle cpd
				INNER JOIN pr_conceptoperfil cp ON (cpd.CodPerfilConcepto = cp.CodPerfilConcepto)
				INNER JOIN pr_tipoproceso tp ON (cpd.CodTipoProceso = tp.CodTipoProceso)
			WHERE cpd.CodConcepto = '".$field['CodConcepto']."'";
    $query_perfil = mysql_query($sql) or die ($sql.mysql_error());
    while ($field_perfil = mysql_fetch_array($query_perfil)) {
        ?>
        <tr class="trListaBody">
            <td>
                <?=htmlentities($field_perfil['NomPerfilConcepto'])?>
            </td>
            <td>
                <?=htmlentities($field_perfil['NomTipoProceso'])?>
            </td>
            <td align="center">
                <?=$field_perfil['cod_partida']?>
            </td>
            <td align="center">
                <?=$field_perfil['CuentaDebe']?>
            </td>
            <td align="center">
                <?=$field_perfil['CuentaDebePub20']?>
            </td>
            <td align="center">
                <?=printFlag($field_perfil['FlagDebeCC'])?>
            </td>
            <td align="center">
                <?=$field_perfil['CuentaHaber']?>
            </td>
            <td align="center">
                <?=$field_perfil['CuentaHaberPub20']?>
            </td>
            <td align="center">
                <?=printFlag($field_perfil['FlagHaberCC'])?>
            </td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>
</div>

<table width="800" class="tblForm">
	<tr>
    	<td width="125">&nbsp;</td>
		<td>
            <input type="checkbox" id="FlagObligacion" <?=chkOpt($field['FlagObligacion'], "S")?> onclick="flagObligacion(this.checked);" <?=$disabled_ver?> /> Se genera obligaci&oacute;n para este proveedor
		</td>
	</tr>
	<tr>
    	<td class="tagForm">Proveedor: </td>
		<td class="gallery clearfix">
			<input type="text" id="CodPersona" style="width:45px;" value="<?=$field['CodPersona']?>" disabled="disabled" />
			<input type="text" id="NomPersona" style="width:250px;" value="<?=htmlentities($field['NomPersona'])?>" disabled="disabled" />
            <a id="aCodPersona" href="../lib/listas/listado_personas.php?filtrar=default&cod=CodPersona&nom=NomPersona&iframe=true&width=820&height=400" rel="prettyPhoto[iframe3]" style=" <?=$visibility_obligacion?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
		</td>
	</tr>
	<tr>
    	<td class="tagForm">Tipo de Documento: </td>
		<td>
        	<select id="CodTipoDocumento" style="width:310px;" <?=$disabled_obligacion?>>
            	<option value="">&nbsp;</option>
				<?=loadSelect2("ap_tipodocumento", "CodTipoDocumento", "Descripcion", $field['CodTipoDocumento'], 0)?>
			</select>
		</td>
	</tr>
</table>
</center>
</div>

<br />
<div style="width:800px" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript" language="javascript">
//	valido y envio formulario
function form(form, accion) {
	$(".div-progressbar").css("display", "block");
	//	valido
	var error = "";
	if ($("#Descripcion").val().trim() == "" || $("#TextoImpresion").val().trim() == "" || $("#Tipo").val() == "") error = "Debe llenar los campos obligatorios";
	if (isNaN(setNumero($("#PlanillaOrden").val()))) error = "Orden de Boleta incorrecto";
	//	tipos de nomina
	var detalles_nominas = "";
	var frm_nominas = document.getElementById("frm_nominas");
	for(var i=0; n=frm_nominas.elements[i]; i++) {
		if (n.name == "CodTipoNom") detalles_nominas += n.value + ";char:tr;";
	}
	var len = detalles_nominas.length; len-=9;
	detalles_nominas = detalles_nominas.substr(0, len);
	//	tipos de proceso
	var detalles_procesos = "";
	var frm_procesos = document.getElementById("frm_procesos");
	for(var i=0; n=frm_procesos.elements[i]; i++) {
		if (n.name == "CodTipoProceso") detalles_procesos += n.value + ";char:tr;";
	}
	var len = detalles_procesos.length; len-=9;
	detalles_procesos = detalles_procesos.substr(0, len);
	//	valido errores
	if (error != "") { cajaModal(error, "error", 400); }
	else {
		//	formulario
		var post = getForm(form);
		if ($("#FlagObligacion").prop("checked")) var FlagObligacion = "S"; else var FlagObligacion = "N";
		var CodPersona = $("#CodPersona").val();
		//var FormulaEditor = changeUrl($("#Formula").html());
		var Formula = changeUrl($("#Formula").val().trim());
		if ($('#Formula').val().trim() != "") var FlagFormula = "S"; else var FlagFormula = "N";
		//	ajax
		$.ajax({
			type: "POST",
			url: "pr_conceptos_ajax.php",
			data: "modulo=conceptos&accion="+accion+"&"+post+"&FlagObligacion="+FlagObligacion+"&CodPersona="+CodPersona+"&Formula="+Formula+"&detalles_nominas="+detalles_nominas+"&detalles_procesos="+detalles_procesos+"&FlagFormula="+FlagFormula+"&CodTipoDocumento="+$('#CodTipoDocumento').val(),
			async: false,
			success: function(resp) {
				if (resp.trim() != "") cajaModal(resp, "error", 400);
				else form.submit();
			}
		});
	}
	return false;
}
//	
function setFormula(valor, tipo, descripcion) {
	$(".last").removeClass("last");
	if (tipo == "numero") { 
		var span = "<span class='span-selected last' onclick='selSpan($(this));' style='color:#F60;'>"+valor+"</span>";
	}
	else if (tipo == "char") { 
		var span = "<span class='span-selected last' onclick='selSpan($(this));' style='color:#000;'>"+valor+"</span>";
	}
	else if (tipo == "signo") {
		var span = "<span class='span-selected last' onclick='selSpan($(this));' style='color:#000;'>"+valor+"</span>";
	}
	else if (tipo == "signo2") {
		var span = "<span class='span-selected last' onclick='selSpan($(this));' style='color:#000;'>"+valor+"</span>";
	}
	else if (tipo == "salto") {
		var span = "<span onclick='selSpan($(this));' style='color:#000;'>"+valor+"</span> <br class='span-selected last' />";
	}
	else if (tipo == "var") {
		var span = "<span class='span-selected last' onclick='selSpan($(this));' style='color:#333;'>"+valor+"</span>";
	}
	else if (tipo == "var-total") {
		var span = "<span class='span-selected last' onclick='selSpan($(this));' style='color:#333; font-weight:bold;'>"+valor+"</span>";
	}
	else if (tipo == "espacio") {
		var span = "<span class='span-selected last' onclick='selSpan($(this));'> </span>";
	}
	else if (tipo == "enter") {
		var span = "<br class='span-selected last' />";
	}
	else if (tipo == "variable") {
		var span = "<span class='span-selected last' onclick='selSpan($(this));' style='color:#090;' title='"+descripcion+"'>$_"+valor+"</span>";
	}
	else if (tipo == "parametro") {
		var span = "<span class='span-selected last' onclick='selSpan($(this));' style='color:#330;' title='"+descripcion+"'>$_"+valor+"</span>";
	}
	else if (tipo == "concepto") {
		var span = "<span class='span-selected last' onclick='selSpan($(this));' style='color:#900;' title='"+descripcion+"'>$_"+valor+"</span>";
	}
	else if (tipo == "funcion") {
		var span = "<span class='span-selected last' onclick='selSpan($(this));' style='color:#06F;' title='"+descripcion+"'>"+valor+"()</span>";
	}
	if ($("#Formula").text().trim() == "") $("#Formula").append(span);	
	else $(span).insertAfter(".span-selected");
	$(".span-selected").removeClass("span-selected").addClass("span");
	$(".last").addClass("span-selected");
}
//	activo/desactivo datos de obligacion
function flagObligacion(boo) {
	$('#CodPersona').val('');
	$('#NomPersona').val('');
	$('#CodTipoDocumento').val('').prop('disabled', !boo);
	if (boo) $('#aCodPersona').css('visibility', 'visible');
	else $('#aCodPersona').css('visibility', 'hidden');
}
</script>