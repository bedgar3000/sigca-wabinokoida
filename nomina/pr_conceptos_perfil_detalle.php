<?php
$clkCancelar = "document.getElementById('frmentrada').submit();";
//	------------------------------------
//	consulto los conceptos
$sql = "SELECT *
		FROM pr_conceptoperfil
		WHERE CodPerfilConcepto = '".$sel_registros."'";
$query_perfil = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
if (mysql_num_rows($query_perfil)) $field_perfil = mysql_fetch_array($query_perfil);
//	------------------------------------
//	tipo de proceso by default
list($CodTipoProceso, $NomTipoProceso) = getPrimeroDefault("pr_tipoproceso", "CodTipoProceso", "Descripcion", "Descripcion");
//	------------------------------------
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo">Mantenimiento del Perfil</td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=pr_conceptos_perfil_lista" method="POST" enctype="multipart/form-data" onsubmit="return conceptos_perfil_detalle(this, 'conceptos');" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />

<div class="divBorder" style="width:1050px;">
<table width="1050" class="tblFiltro">
	<tr>
    	<td colspan="2" class="divFormCaption">Datos del Concepto</td>
    </tr>
	<tr>
		<td align="right" width="125">Perfil:</td>
		<td>
        	<input type="text" id="CodPerfilConcepto" style="width:40px;" class="codigo" value="<?=$field_perfil['CodPerfilConcepto']?>" disabled />
		</td>
	</tr>
	<tr>
		<td align="right">Descripci&oacute;n:</td>
		<td>
        	<input type="text" id="Descripcion" style="width:300px;" class="codigo" value="<?=$field_perfil['Descripcion']?>" disabled />
		</td>
	</tr>
</table>
</div>
<center>
<input type="submit" value="Guardar" style="width:75px;" />
<input type="button" value="Cancelar" style="width:75px;" onclick="<?=$clkCancelar?>" />
</center>
</form>
<br />

<center>
<form name="frm_conceptos" id="frm_conceptos">
<input type="hidden" id="sel_conceptos" />
<table width="1050" class="tblBotones">
    <thead>
        <th class="divFormCaption" colspan="3">Detalle de Conceptos</th>
    </thead>
    <tbody>
    <tr>
    	<td width="60">Proceso: </td>
    	<td width="300">
        	<select id="CodTipoProceso" style="width:100%;" onChange="$('.procesos').css('display', 'none'); $('#'+this.value).css('display', 'block');">
                <?=loadSelect("pr_tipoproceso", "CodTipoProceso", "Descripcion", $CodTipoProceso, 0)?>
            </select>
        </td>
        <td align="right" class="gallery clearfix">
            <a id="acod_partida" href="../lib/listas/listado_clasificador_presupuestario.php?filtrar=default&cod=cod_partida&ventana=conceptos_perfil_partida_sel&seldetalle=sel_conceptos&iframe=true&width=100%&height=425" rel="prettyPhoto[iframe1]" style="display:none;"></a>
            <input type="button" style="width:80px;" value="Partida" onclick="validarAbrirLista('sel_conceptos', 'acod_partida');" <?=$disabled_ver?> />
            
            <a id="aCuentaDebe" href="../lib/listas/listado_plan_cuentas.php?filtrar=default&cod=CuentaDebe&ventana=selListadoLista&seldetalle=sel_conceptos&iframe=true&width=100%&height=425" rel="prettyPhoto[iframe2]" style="display:none;"></a>
            <input type="button" style="width:80px;" value="Debe" onclick="validarAbrirLista('sel_conceptos', 'aCuentaDebe');" <?=$disabled_ver?> />
            
            <a id="aCuentaDebePub20" href="../lib/listas/listado_plan_cuentas_pub20.php?filtrar=default&cod=CuentaDebePub20&ventana=selListadoLista&seldetalle=sel_conceptos&iframe=true&width=100%&height=425" rel="prettyPhoto[iframe3]" style="display:none;"></a>
            <input type="button" style="width:80px;" value="Debe Pub.20" onclick="validarAbrirLista('sel_conceptos', 'aCuentaDebePub20');" <?=$disabled_ver?> />
            
            <a id="aCuentaHaber" href="../lib/listas/listado_plan_cuentas.php?filtrar=default&cod=CuentaHaber&ventana=selListadoLista&seldetalle=sel_conceptos&iframe=true&width=100%&height=425" rel="prettyPhoto[iframe4]" style="display:none;"></a>
            <input type="button" style="width:80px;" value="Haber" onclick="validarAbrirLista('sel_conceptos', 'aCuentaHaber');" <?=$disabled_ver?> />
            
            <a id="aCuentaHaberPub20" href="../lib/listas/listado_plan_cuentas_pub20.php?filtrar=default&cod=CuentaHaberPub20&ventana=selListadoLista&seldetalle=sel_conceptos&iframe=true&width=100%&height=425" rel="prettyPhoto[iframe5]" style="display:none;"></a>
            <input type="button" style="width:80px;" value="Haber Pub.20" onclick="validarAbrirLista('sel_conceptos', 'aCuentaHaberPub20');" <?=$disabled_ver?> />

            <a id="aSelCategoriaProg" href="../lib/listas/gehen.php?anz=lista_pv_categoriaprog&filtrar=default&campo1=CategoriaProg&ventana=selListadoListaParent&seldetalle=sel_conceptos&iframe=true&width=100%&height=400" rel="prettyPhoto[iframe7]" style="display:none;"></a>
            <input type="button" style="width:90px;" id="btSelCategoriaProg" value="Cat. Prog." onclick="validarAbrirLista('sel_conceptos', 'aSelCategoriaProg');" <?=$disabled_ver?> <?=$disabled_restringido?> />
        </td>
    </tr>
    </tbody>
</table>

<?php
//	tipos de proceso
$sql = "SELECT * FROM pr_tipoproceso WHERE Estado = 'A' ORDER BY Descripcion";
$query_procesos = mysql_query($sql) or die ($sql.mysql_error());
while ($field_procesos = mysql_fetch_array($query_procesos)) {
	if ($CodTipoProceso != $field_procesos['CodTipoProceso']) $display = "display:none;";
	?>
    <div class="procesos" style="overflow:scroll; width:1050px; height:400px; <?=$display?>" id="<?=$field_procesos['CodTipoProceso']?>">
    <table width="100%" class="tblLista">
        <thead>
        <tr>
            <th align="left" rowspan="2">Concepto</th>
            <th width="70" rowspan="2">Partida</th>
            <th colspan="2">Debe</th>
            <th width="20" rowspan="2">C.C</th>
            <th colspan="2">Haber</th>
            <th width="20" rowspan="2">C.C</th>
            <th colspan="2" rowspan="2">Cat. Prog.</th>
        </tr>
        <tr>
          <th width="90">Cuenta</th>
          <th width="90">Pub. 20</th>
          <th width="90">Cuenta</th>
          <th width="90">Pub. 20</th>
        </tr>
        </thead>
        
        <tbody id="lista_conceptos">
		<?php	
        //	consulto conceptos
        $sql = "(SELECT
                    c.CodConcepto,
                    c.Descripcion,
                    c.Tipo,
					c.FlagRetencion,
                    '1' AS Orden,
                    cp.CodTipoProceso,
                    cpd.cod_partida,
                    cpd.CuentaDebe,
                    cpd.CuentaHaber,
                    cpd.CuentaDebePub20,
                    cpd.CuentaHaberPub20,
                    cpd.FlagCategoriaProg,
                    cpd.CategoriaProg
                FROM
                    pr_concepto c
                    INNER JOIN pr_conceptoproceso cp ON (cp.CodConcepto = c.CodConcepto AND cp.CodTipoProceso = '".$field_procesos['CodTipoProceso']."')
                    INNER JOIN pr_conceptotiponomina ctn ON (c.CodConcepto = ctn.CodConcepto AND
                                                             ctn.CodTipoNom IN (SELECT CodTipoNom
                                                                                FROM tiponomina
                                                                                WHERE CodPerfilConcepto = '".$field_perfil['CodPerfilConcepto']."'))
                    LEFT JOIN pr_conceptoperfildetalle cpd ON (c.CodConcepto = cpd.CodConcepto AND
															   cpd.CodTipoProceso = '".$field_procesos['CodTipoProceso']."' AND
															   cpd.CodPerfilConcepto = '".$field_perfil['CodPerfilConcepto']."')
                WHERE c.Tipo = 'I' AND c.Estado = 'A')
                UNION
                (SELECT
                    c.CodConcepto,
                    c.Descripcion,
                    c.Tipo,
					c.FlagRetencion,
                    '2' AS Orden,
                    cp.CodTipoProceso,
                    cpd.cod_partida,
                    cpd.CuentaDebe,
                    cpd.CuentaHaber,
                    cpd.CuentaDebePub20,
                    cpd.CuentaHaberPub20,
                    cpd.FlagCategoriaProg,
                    cpd.CategoriaProg
                FROM
                    pr_concepto c
                    INNER JOIN pr_conceptoproceso cp ON (cp.CodConcepto = c.CodConcepto AND cp.CodTipoProceso = '".$field_procesos['CodTipoProceso']."')
                    INNER JOIN pr_conceptotiponomina ctn ON (c.CodConcepto = ctn.CodConcepto AND
                                                             ctn.CodTipoNom IN (SELECT CodTipoNom
                                                                                FROM tiponomina
                                                                                WHERE CodPerfilConcepto = '".$field_perfil['CodPerfilConcepto']."'))
                    LEFT JOIN pr_conceptoperfildetalle cpd ON (c.CodConcepto = cpd.CodConcepto AND
															   cpd.CodTipoProceso = '".$field_procesos['CodTipoProceso']."' AND
															   cpd.CodPerfilConcepto = '".$field_perfil['CodPerfilConcepto']."')
                WHERE c.Tipo = 'D' AND c.Estado = 'A')
                UNION
                (SELECT
                    c.CodConcepto,
                    c.Descripcion,
                    c.Tipo,
					c.FlagRetencion,
                    '3' AS Orden,
                    cp.CodTipoProceso,
                    cpd.cod_partida,
                    cpd.CuentaDebe,
                    cpd.CuentaHaber,
                    cpd.CuentaDebePub20,
                    cpd.CuentaHaberPub20,
                    cpd.FlagCategoriaProg,
                    cpd.CategoriaProg
                FROM
                    pr_concepto c
                    INNER JOIN pr_conceptoproceso cp ON (cp.CodConcepto = c.CodConcepto AND cp.CodTipoProceso = '".$field_procesos['CodTipoProceso']."')
                    INNER JOIN pr_conceptotiponomina ctn ON (c.CodConcepto = ctn.CodConcepto AND
                                                             ctn.CodTipoNom IN (SELECT CodTipoNom
                                                                                FROM tiponomina
                                                                                WHERE CodPerfilConcepto = '".$field_perfil['CodPerfilConcepto']."'))
                    LEFT JOIN pr_conceptoperfildetalle cpd ON (c.CodConcepto = cpd.CodConcepto AND
															   cpd.CodTipoProceso = '".$field_procesos['CodTipoProceso']."' AND
															   cpd.CodPerfilConcepto = '".$field_perfil['CodPerfilConcepto']."')
                WHERE c.Tipo = 'A' AND c.Estado = 'A')
                UNION
                (SELECT
                    c.CodConcepto,
                    c.Descripcion,
                    c.Tipo,
					c.FlagRetencion,
                    '4' AS Orden,
                    cp.CodTipoProceso,
                    cpd.cod_partida,
                    cpd.CuentaDebe,
                    cpd.CuentaHaber,
                    cpd.CuentaDebePub20,
                    cpd.CuentaHaberPub20,
                    cpd.FlagCategoriaProg,
                    cpd.CategoriaProg
                FROM
                    pr_concepto c
                    INNER JOIN pr_conceptoproceso cp ON (cp.CodConcepto = c.CodConcepto AND cp.CodTipoProceso = '".$field_procesos['CodTipoProceso']."')
                    INNER JOIN pr_conceptotiponomina ctn ON (c.CodConcepto = ctn.CodConcepto AND
                                                             ctn.CodTipoNom IN (SELECT CodTipoNom
                                                                                FROM tiponomina
                                                                                WHERE CodPerfilConcepto = '".$field_perfil['CodPerfilConcepto']."'))
                    LEFT JOIN pr_conceptoperfildetalle cpd ON (c.CodConcepto = cpd.CodConcepto AND
															   cpd.CodTipoProceso = '".$field_procesos['CodTipoProceso']."' AND
															   cpd.CodPerfilConcepto = '".$field_perfil['CodPerfilConcepto']."')
                WHERE c.Tipo = 'P' AND c.Estado = 'A')
                UNION
                (SELECT
                    c.CodConcepto,
                    c.Descripcion,
                    c.Tipo,
					c.FlagRetencion,
                    '5' AS Orden,
                    cp.CodTipoProceso,
                    cpd.cod_partida,
                    cpd.CuentaDebe,
                    cpd.CuentaHaber,
                    cpd.CuentaDebePub20,
                    cpd.CuentaHaberPub20,
                    cpd.FlagCategoriaProg,
                    cpd.CategoriaProg
                FROM
                    pr_concepto c
                    INNER JOIN pr_conceptoproceso cp ON (cp.CodConcepto = c.CodConcepto AND cp.CodTipoProceso = '".$field_procesos['CodTipoProceso']."')
                    INNER JOIN pr_conceptotiponomina ctn ON (c.CodConcepto = ctn.CodConcepto AND
                                                             ctn.CodTipoNom IN (SELECT CodTipoNom
                                                                                FROM tiponomina
                                                                                WHERE CodPerfilConcepto = '".$field_perfil['CodPerfilConcepto']."'))
                    LEFT JOIN pr_conceptoperfildetalle cpd ON (c.CodConcepto = cpd.CodConcepto AND
															   cpd.CodTipoProceso = '".$field_procesos['CodTipoProceso']."' AND
															   cpd.CodPerfilConcepto = '".$field_perfil['CodPerfilConcepto']."')
                WHERE c.Tipo = 'T' AND c.Estado = 'A')
                ORDER BY Orden, CodConcepto";
        $query_conceptos = mysql_query($sql) or die ($sql.mysql_error());
        while ($field_conceptos = mysql_fetch_array($query_conceptos)) {
			$id = "$field_conceptos[CodTipoProceso]$field_conceptos[CodConcepto]";
			if ($field_conceptos['FlagRetencion'] == "S") $style_retencion = "color:red;"; else $style_retencion = "";
            if ($Grupo != $field_conceptos['Tipo']) {
                $Grupo = $field_conceptos['Tipo'];
                ?>
                <tr class="trListaBody2">
                    <td colspan="10"><?=printValores("CONCEPTO-TIPO", $field_conceptos['Tipo'])?></td>
                </tr>
                <?php
            }
            ?>
            <tr class="trListaBody" onclick="clk($(this), 'conceptos', 'conceptos_<?=$id?>');">
                <td>
                    <input type="hidden" name="CodTipoProceso" value="<?=$field_procesos['CodTipoProceso']?>" />
                    <input type="hidden" name="CodConcepto" value="<?=$field_conceptos['CodConcepto']?>" />
                    <input type="text" class="cell2" style=" <?=$style_retencion?>" value="<?=htmlentities($field_conceptos['Descripcion'])?>" readonly />
                </td>
                <td>
                    <input type="text" name="cod_partida" id="cod_partida_<?=$id?>" class="cell" style="text-align:center;" value="<?=$field_conceptos['cod_partida']?>" />
                </td>
                <td>
                    <input type="text" name="CuentaDebe" id="CuentaDebe_<?=$id?>" class="cell" style="text-align:center;" value="<?=$field_conceptos['CuentaDebe']?>" />
                </td>
                <td>
                    <input type="text" name="CuentaDebePub20" id="CuentaDebePub20_<?=$id?>" class="cell" style="text-align:center;" value="<?=$field_conceptos['CuentaDebePub20']?>" />
                </td>
                <td align="center">
                    <input type="checkbox" name="FlagDebeCC" <?=chkFlag($field_conceptos['FlagDebeCC'])?> />
                </td>
                <td>
                    <input type="text" name="CuentaHaber" id="CuentaHaber_<?=$id?>" class="cell" style="text-align:center;" value="<?=$field_conceptos['CuentaHaber']?>" />
                </td>
                <td>
                    <input type="text" name="CuentaHaberPub20" id="CuentaHaberPub20_<?=$id?>" class="cell" style="text-align:center;" value="<?=$field_conceptos['CuentaHaberPub20']?>" />
                </td>
                <td align="center">
                    <input type="checkbox" name="FlagHaberCC" <?=chkFlag($field_conceptos['FlagHaberCC'])?> />
                </td>
                <td align="center" width="25">
                    <input type="checkbox" name="FlagCategoriaProg" <?=chkFlag($field_conceptos['FlagCategoriaProg'])?> onchange="$('#CategoriaProg_<?=$id?>').val('');" />
                </td>
                <td width="100">
                    <input type="text" name="CategoriaProg" id="CategoriaProg_<?=$id?>" class="cell" style="text-align:center;" value="<?=$field_conceptos['CategoriaProg']?>" readonly />
                </td>
            </tr>
            <?php
        }
		?>
        </tbody>
    </table>
    </div>
    <?php
}
?>
<input type="hidden" id="nro_conceptos" value="<?=$nro_conceptos?>" />
<input type="hidden" id="can_conceptos" value="<?=$nro_conceptos?>" />
</form>
</center>