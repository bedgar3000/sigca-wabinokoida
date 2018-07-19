<?php
if (!$ventana) $ventana = "selLista";
//	------------------------------------
if ($filtrar == "default") {
    $fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
    $fCodDependencia = $_SESSION["DEPENDENCIA_ACTUAL"];
    $maxlimit = $_SESSION["MAXLIMIT"];
    $fOrderBy = "CodPoa";
}
if ($fBuscar != "") {
    $cBuscar = "checked";
    $filtro .= " AND (poa.CodPoa LIKE '%".$fBuscar."%' OR
					  r.Anio LIKE '%".$fBuscar."%' OR
					  o.Organismo LIKE '%".$fBuscar."%' OR
					  d.Dependencia LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (poa.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (r.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCodDependencia != "") { $cCodDependencia = "checked"; $filtro.=" AND (r.CodDependencia = '".$fCodDependencia."')"; } else $dCodDependencia = "disabled";
if ($fCodRecurso != "") { $cCodRecurso = "checked"; $filtro.=" AND (poa.CodRecurso = '".$fCodRecurso."')"; } else $dCodRecurso = "visibility:hidden;";
//	------------------------------------
$_titulo = "Planificaci&oacute;n Oparativa";
$_width = 700;
?>
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lista_po_poa" method="post" autocomplete="off">
	<input type="hidden" name="registro" id="registro" />
	<input type="hidden" name="campo1" id="campo1" value="<?=$campo1?>" />
	<input type="hidden" name="campo2" id="campo2" value="<?=$campo2?>" />
	<input type="hidden" name="campo3" id="campo3" value="<?=$campo3?>" />
	<input type="hidden" name="campo4" id="campo4" value="<?=$campo4?>" />
	<input type="hidden" name="campo5" id="campo5" value="<?=$campo5?>" />
	<input type="hidden" name="campo6" id="campo6" value="<?=$campo6?>" />
	<input type="hidden" name="campo7" id="campo7" value="<?=$campo7?>" />
	<input type="hidden" name="campo8" id="campo8" value="<?=$campo8?>" />
	<input type="hidden" name="campo9" id="campo9" value="<?=$campo9?>" />
	<input type="hidden" name="campo10" id="campo10" value="<?=$campo10?>" />
	<input type="hidden" name="ventana" id="ventana" value="<?=$ventana?>" />
	<input type="hidden" name="detalle" id="detalle" value="<?=$detalle?>" />
	<input type="hidden" name="modulo" id="modulo" value="<?=$modulo?>" />
	<input type="hidden" name="accion" id="accion" value="<?=$accion?>" />
	<input type="hidden" name="url" id="url" value="<?=$url?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="FlagDependencia" id="FlagDependencia" value="<?=$FlagDependencia?>" />

    <!--FILTRO-->
    <div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
        <table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
            <tr>
                <td align="right" width="100">Organismo: </td>
                <td>
                    <input type="checkbox" checked onclick="this.checked=!this.checked;" />
                    <select name="fCodOrganismo" id="fCodOrganismo" style="width:275px;" onChange="loadSelect($('#fCodDependencia'), 'tabla=dependencia_filtro&opcion='+$('#fCodOrganismo').val(), 1);">
                        <?=getOrganismos($fCodOrganismo, 3)?>
                    </select>
                </td>
                <td align="right" width="100">Buscar:</td>
                <td>
                    <input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
                    <input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:145px;" <?=$dBuscar?> />
                </td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td align="right">Dependencia:</td>
                <td>
                	<?php
                	if ($FlagDependencia == 'S') {
                		?>
	                    <input type="checkbox" <?=$cCodDependencia?> onclick="this.checked=!this.checked;" />
	                    <select name="fCodDependencia" id="fCodDependencia" style="width:275px;" <?=$dCodDependencia?>>
	                        <?=loadSelect2('mastdependencias','CodDependencia','Dependencia',$fCodDependencia,1,['CodOrganismo'],[$fCodOrganismo])?>
	                    </select>
                		<?php
                	} else  {
                		?>
	                    <input type="checkbox" <?=$cCodDependencia?> onclick="chkCampos(this.checked, 'fCodDependencia');" />
	                    <select name="fCodDependencia" id="fCodDependencia" style="width:275px;" <?=$dCodDependencia?>>
	                        <option value="">&nbsp;</option>
	                        <?=getDependencias($fCodDependencia, $fCodOrganismo, 3)?>
	                    </select>
                		<?php
                	}
                	?>
                </td>
                <td align="right" width="100">Estado: </td>
                <td>
                    <input type="checkbox" <?=$cEstado?> onclick="this.checked=!this.checked;" />
                    <select name="fEstado" id="fEstado" style="width:150px;" <?=$dEstado?>>
                        <?=loadSelectGeneral("poa-estado", $fEstado, 0)?>
                    </select>
                </td>
                <td align="right"><input type="submit" value="Buscar"></td>
            </tr>
        </table>
    </div>
    <div class="sep"></div>

	<!--REGISTROS-->
	<input type="hidden" name="sel_registros" id="sel_registros" />

	<div style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px; margin:auto;">
		<table class="tblLista" style="width:100%; min-width:1300px;">
			<thead>
			    <tr>
	                <th width="100" onclick="order('CodPoa')">Planificaci&oacute;n</th>
	                <th width="75" onclick="order('Anio')">A&ntilde;o</th>
	                <th align="left" onclick="order('Dependencia')">Dependencia</th>
	                <th align="left" onclick="order('Organismo')">Organismo</th>
	                <th width="100" onclick="order('Estado')">Estado</th>
			    </tr>
		    </thead>
		    
		    <tbody id="lista_registros">
		        <?php
	            //	consulto todos
	            $sql = "SELECT poa.*
	                    FROM po_poa poa
	                    INNER JOIN po_recursos r ON (poa.CodRecurso = r.CodRecurso)
	                    INNER JOIN mastdependencias d ON (d.CodDependencia = r.CodDependencia)
	                    INNER JOIN mastorganismos o ON (o.CodOrganismo = r.CodOrganismo)
	                    WHERE 1 $filtro";
	            $rows_total = getNumRows3($sql);
	            //	consulto lista
	            $sql = "SELECT
	                        poa.*,
	                        r.CodOrganismo,
	                        r.CodDependencia,
	                        r.Anio,
	                        o.Organismo,
	                        d.Dependencia
	                    FROM po_poa poa
	                    INNER JOIN po_recursos r ON (poa.CodRecurso = r.CodRecurso)
	                    INNER JOIN mastdependencias d ON (d.CodDependencia = r.CodDependencia)
	                    INNER JOIN mastorganismos o ON (o.CodOrganismo = r.CodOrganismo)
	                    WHERE 1 $filtro
	                    ORDER BY $fOrderBy
	                    LIMIT ".intval($limit).", ".intval($maxlimit);
	            $field = getRecords($sql);
	            $rows_lista = count($field);
		        foreach($field as $f) {
		            $id = $f['CodPoa'];
		            ?>
		            <tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CodPoa']?>'], ['<?=$campo1?>']);">
	                        <td align="center"><?=$f['CodPoa']?></td>
	                        <td align="center"><?=$f['Anio']?></td>
	                        <td><?=htmlentities($f['Dependencia'])?></td>
	                        <td><?=htmlentities($f['Organismo'])?></td>
	                        <td align="center"><?=printValoresGeneral('poa-estado',$f['Estado'])?></td>
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