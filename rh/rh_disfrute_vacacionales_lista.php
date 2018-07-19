<?php
$_titulo = "Tabla de Disfrutes Vacacionales";
$_width = 600;
$_sufijo = "disfrute_vacacionales";
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=rh_<?=$_sufijo?>_lista" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="sel_registros" id="sel_registros" />

<center>
<table width="<?=$_width?>" class="tblBotones">
    <tr>
        <td><div id="rows"></div></td>
        <td align="right">
            <input type="button" id="btNuevo" value="Nuevo" style="width:75px;" onclick="cargarPagina(this.form, 'gehen.php?anz=rh_<?=$_sufijo?>_form&opcion=nuevo');" />
            
            <input type="button" id="btModificar" value="Modificar" style="width:75px;" onclick="cargarOpcion2(this.form, 'gehen.php?anz=rh_<?=$_sufijo?>_form&opcion=modificar', 'SELF', '', $('#sel_registros').val());" />
            
            <input type="button" id="btEliminar" value="Eliminar" style="width:75px;" onclick="opcionRegistro2(this.form, this.form.sel_registros.value, '<?=$_sufijo?>', 'eliminar');" />
            
            <input type="button" id="btVer" value="Ver" style="width:75px;" onclick="cargarOpcion2(this.form, 'gehen.php?anz=rh_<?=$_sufijo?>_form&opcion=ver', 'SELF', '', $('#sel_registros').val());" />
        </td>
    </tr>
</table>
<div class="scroll" style="overflow:scroll; width:<?=$_width?>px; height:350px;">
<table width="100%" class="tblLista">
    <thead>
    <tr>
        <th width="15"></th>
        <th>N&oacute;mina</th>
        <th width="75">A&ntilde;os</th>
        <th width="75">Dias de Disfrutes</th>
        <th width="75">Dias Adicionales</th>
        <th width="100">Total Dias a Disfrutar</th>
    </tr>
    </thead>
    
    <tbody id="lista_registros">
    <?php
    //	consulto lista
    $sql = "SELECT
				vt.*,
				(vt.DiasDisfrutes + vt.DiasAdicionales) AS DiasTotal,
				tn.Nomina
            FROM
				rh_vacaciontabla vt
				INNER JOIN tiponomina tn ON (tn.CodTipoNom = vt.CodTipoNom)
            WHERE 1 $filtro
            ORDER BY Nomina, NroAnio";
    $query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
    $rows_lista = mysql_num_rows($query);	$i=0;
    while ($field = mysql_fetch_array($query)) {
        $id = $field['CodTipoNom'].'_'.$field['NroAnio'];
        ?>
        <tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
            <th><?=++$i?></th>
            <td align="center"><strong><?=htmlentities($field['Nomina'])?></strong></td>
            <td align="center"><?=$field['NroAnio']?></td>
            <td align="center"><?=$field['DiasDisfrutes']?></td>
            <td align="center"><?=$field['DiasAdicionales']?></td>
            <td align="center"><strong><?=$field['DiasTotal']?></strong></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>
</div>
</center>
</form>