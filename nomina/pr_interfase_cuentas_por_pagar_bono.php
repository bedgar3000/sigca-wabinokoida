<?php
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["ORGANISMO_ACTUAL"];
	$fCodTipoNom = $_SESSION["NOMINA_ACTUAL"];
	$fPeriodo = $PeriodoActual;
    $fAnio = $AnioActual;
    $fMes = $MesActual;
}
//	------------------------------------
$i=0;
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo">Interfase Cuentas x Pagar (Bono de Alimentaci&oacute;n)</td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=pr_interfase_cuentas_por_pagar_bono" method="post" autocomplete="off">

<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="id_tab" id="id_tab" value="<?=$id_tab?>" />
<input type="hidden" name="sel_registros" id="sel_registros" />

<div class="divBorder" style="width:100%; min-width:<?=$_width?>px; margin:auto;">
    <table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
    	<tr>
    		<td align="right" width="100">Organismo:</td>
    		<td>
    			<input type="checkbox" checked="checked" onclick="this.checked=!this.checked" />
    			<select name="fCodOrganismo" id="fCodOrganismo" style="width:300px;" onChange="loadSelect($('#fCodTipoNom'), 'tabla=loadBonoNomina&CodOrganismo='+this.value, 1, destinos=['fAnio', 'fMes', 'fCodBonoAlim']);">
    				<?=getOrganismos($fCodOrganismo, 3)?>
    			</select>
    		</td>
    		<td align="right" width="100">N&oacute;mina:</td>
    		<td>
    			<input type="checkbox" checked="checked" onclick="this.checked=!this.checked" />
    			<select name="fCodTipoNom" id="fCodTipoNom" style="width:300px;" onChange="loadSelect($('#fAnio'), 'tabla=loadBonoAnio&CodOrganismo='+$('#fCodOrganismo').val()+'&CodTipoNom='+this.value, 1, destinos=['fMes', 'fCodBonoAlim']);">
    				<?=loadBonoNomina($fCodOrganismo, $fCodTipoNom)?>
    			</select>
    		</td>
            <td>&nbsp;</td>
    	</tr>
    	<tr>
    		<td align="right">Periodo:</td>
    		<td>
    			<input type="checkbox" checked="checked" onclick="this.checked=!this.checked" />
                <select name="fAnio" id="fAnio" style="width:55px;" onChange="loadSelect($('#fMes'), 'tabla=loadBonoMes&CodOrganismo='+$('#fCodOrganismo').val()+'&CodTipoNom='+$('#fCodTipoNom').val()+'&Anio='+this.value, 1, destinos=['fCodBonoAlim']);">
                    <option value="">&nbsp;</option>
                    <?=loadBonoAnio($fCodOrganismo, $fCodTipoNom, $fAnio)?>
                </select> -
                <select name="fMes" id="fMes" style="width:42px;" onChange="loadSelect($('#fCodBonoAlim'), 'tabla=loadBonoProceso&CodOrganismo='+$('#fCodOrganismo').val()+'&CodTipoNom='+$('#fCodTipoNom').val()+'&Anio='+$('#fAnio').val()+'&Mes='+this.value, 1);">
                    <option value="">&nbsp;</option>
                    <?=loadBonoMes($fCodOrganismo, $fCodTipoNom, $fAnio, $fMes)?>
                </select>
    		</td>
    		<td align="right">Proceso:</td>
    		<td>
    			<input type="checkbox" checked="checked" onclick="this.checked=!this.checked" />
    			<select name="fCodBonoAlim" id="fCodBonoAlim" style="width:300px;">
                	<option value="">&nbsp;</option>
                	<?=loadBonoProceso($fCodOrganismo, $fCodTipoNom, $fAnio, $fMes, $fCodBonoAlim)?>
    			</select>
    		</td>
            <td width="25"><input type="submit" value="Buscar"></td>
    	</tr>
    </table>
</div>
<div class="sep"></div>

<table class="tblBotones" style="width:100%; min-width:<?=$_width?>px; margin:auto;">
    <tr>
        <td>
            <input type="button" value="Calcular Obligaciones" style="width:130px;" onclick="calcular();" />
            <input type="button" value="Consolidar Obligaciones" style="width:130px;" onclick="consolidar();" />
            <input type="button" value="Verificar Presupuesto" style="width:130px;" onclick="verificar();" />
        </td>
        <td align="right">
            <input type="button" value="Generar Obligaciones" style="width:130px;" onclick="generar();" />
        </td>
    </tr>
</table>

<div class="scroll" style="overflow:scroll; height:350px; width:100%; min-width:<?=$_width?>px; margin:auto;">
    <table class="tblLista" style="width:100%; min-width:1150px;">
        <thead>
            <tr>
                <th width="25">&nbsp;</th>
                <th width="50">Proveedor</th>
                <th align="left">Nombre del Proveedor</th>
                <th width="25">Con.</th>
                <th width="25">Ver.</th>
                <th width="25">Trf.</th>
                <th width="90">Total Obligaci&oacute;n</th>
                <th width="15">Doc.</th>
                <th width="135">Nro. Documento</th>
                <th width="75">Fecha Registro</th>
                <th width="75">Nro. Registro</th>
            </tr>
        </thead>
        
        <tbody id="lista_bancos">
        <?php
        $i = 0;
        //  consulto lista
        $sql = "SELECT
                    o.*,
                    p.NomCompleto AS NomProveedor,
                    p.Ndocumento,
                    td.CodTipoDocumento,
                    td.Descripcion AS NomTipoDocumento
                FROM
                    pr_obligacionesbono o
                    INNER JOIN mastpersonas p ON (o.CodProveedor = p.CodPersona)
                    INNER JOIN ap_tipodocumento td ON (o.CodTipoDocumento = td.CodTipoDocumento)
                    INNER JOIN rh_bonoalimentacion ba ON (ba.Anio = o.Anio AND ba.CodOrganismo = o.CodOrganismo AND ba.CodBonoAlim = o.CodBonoAlim)
                WHERE
                    o.CodOrganismo = '$fCodOrganismo'
                    AND ba.CodTipoNom = '$fCodTipoNom'
                    AND ba.Periodo = '$fAnio-$fMes'
                    AND ba.CodBonoAlim = '$fCodBonoAlim'
                ORDER BY LENGTH(Ndocumento), Ndocumento";
        $field = getRecords($sql);
        foreach ($field as $f) {
            $id = $f['CodObligacionBono'];
            ?>
            <tr class="trListaBody" onclick="clkMulti($(this), 'CodObligacionBono<?=$id?>');">
                <th><?=++$i?></th>
                <td align="center">
                    <input type="checkbox" name="CodObligacionBono[]" id="CodObligacionBono<?=$id?>" value="<?=$f['CodObligacionBono']?>" style="display:none;" />
                    <?=$f['CodProveedor']?>
                </td>
                <td><?=htmlentities($f['NomProveedor'])?></td>
                <td align="center"><?=printFlag($f['FlagConsolidado'])?></td>
                <td align="center"><?=printFlag($f['FlagVerificado'])?></td>
                <td align="center"><?=printFlag($f['FlagTransferido'])?></td>
                <td align="right"><strong><?=number_format($f['MontoObligacion'], 2, ',', '.')?></strong></td>
                <td align="center"><?=$f['CodTipoDocumento']?></td>
                <td><?=$f['NroControl']?></td>
                <td align="center"><?=formatFechaDMA($f['FechaRegistro'])?></td>
                <td align="center"><?=$f['CodObligacionBono']?></td>
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
            <a class="link" href="#" onclick="selTodos2('bancos','CodObligacionBono');">Todos</a> |
            <a class="link" href="#" onclick="selNinguno2('bancos','CodObligacionBono');">Ninguno</a>
        </td>
    </tr>
</table>

<span class="gallery clearfix">
    <a id="a_check" href="pagina.php?iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" style="display:none;"></a>
</span>

</form>

<script type="text/javascript" language="javascript">
    function calcular() {
    	bloqueo(true);
        //  ajax
        $.ajax({
            type: "POST",
            url: "pr_interfase_cuentas_por_pagar_bono_ajax.php",
            data: "modulo=formulario&accion=calcular&"+$('form').serialize(),
            async: false,
            success: function(resp) {
                if (resp.trim() != "") cajaModal(resp, "error", 450);
                else {
                    var funct = "document.getElementById('frmentrada').submit();";
                    cajaModal("Se calcularon las obligaciones exitosamente", "exito", 400, funct);
                }
            }
        });
    }
    function consolidar() {
        bloqueo(true);
        //  ajax
        $.ajax({
            type: "POST",
            url: "pr_interfase_cuentas_por_pagar_bono_ajax.php",
            data: "modulo=formulario&accion=consolidar&"+$('form').serialize(),
            async: false,
            success: function(resp) {
                if (resp.trim() != "") cajaModal(resp, "error", 450);
                else {
                    var funct = "document.getElementById('frmentrada').submit();";
                    cajaModal("Se consolidaron las obligaciones exitosamente", "exito", 400, funct);
                }
            }
        });
    }
    function verificar() {
        if ($("input[name='CodObligacionBono[]']:checked").length != 1) cajaModal('Debe seleccionar una obligaci&oacute;n','error');
        else {
            //  ajax
            $.ajax({
                type: "POST",
                url: "pr_interfase_cuentas_por_pagar_bono_ajax.php",
                data: "modulo=validar&accion=verificar&"+$('form').serialize(),
                async: false,
                success: function(resp) {
                    if (resp.trim() != "") cajaModal(resp, "error", 450);
                    else {
                        var href = "gehen.php?anz=pr_interfase_cuentas_por_pagar_bono_verificar&"+$('form').serialize()+"&iframe=true&width=100%&height=100%";
                        $('#a_check').attr('href', href);
                        $('#a_check').click();
                    }
                }
            });
        }
    }
    function generar() {
        if ($("input[name='CodObligacionBono[]']:checked").length != 1) cajaModal('Debe seleccionar una obligaci&oacute;n','error');
        else {
            //  ajax
            $.ajax({
                type: "POST",
                url: "pr_interfase_cuentas_por_pagar_bono_ajax.php",
                data: "modulo=validar&accion=generar&"+$('form').serialize(),
                async: false,
                success: function(resp) {
                    if (resp.trim() != "") cajaModal(resp, "error", 450);
                    else cargarPagina(document.getElementById('frmentrada'), "../ap/gehen.php?anz=ap_obligacion_form&opcion=interfase-bono-nuevo&origen=pr_interfase_cuentas_por_pagar_bono&"+$('form').serialize());
                }
            });
        }
    }
</script>