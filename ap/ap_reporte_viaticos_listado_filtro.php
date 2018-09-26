<?php
$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
//	------------------------------------
$_titulo = "Listado de Vi&aacute;ticos";
$_width = 860;
?>
<div class="ui-layout-north">
	<div style="padding:5px;">
        <table width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td class="titulo"><?=$_titulo?></td>
                <td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
            </tr>
        </table><hr width="100%" color="#333333" />
        <form name="frmentrada" id="frmentrada" action="ap_reporte_viaticos_listado_pdf.php" method="post" autocomplete="off" target="pdf">
        <input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
        <input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
        
        <!--FILTRO-->
        <div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
        <table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
            <tr>
                <td align="right">Organismo: </td>
                <td>
                    <input type="checkbox" checked onclick="chkFiltro(this.checked, 'fCodOrganismo');" />
                    <select name="fCodOrganismo" id="fCodOrganismo" style="width:300px;" onChange="getOptionsSelect(this.value, 'dependencia_filtro', 'fCodDependencia', true);">
                        <option value="">&nbsp;</option>
                        <?=getOrganismos($fCodOrganismo, 3)?>
                    </select>
                </td>
                <td align="right">Estado: </td>
                <td>
                    <input type="checkbox" onclick="chkFiltro(this.checked, 'fEstado');" />
                    <select name="fEstado" id="fEstado" style="width:145px;" disabled>
                        <option value="">&nbsp;</option>
                        <?=loadSelectValores("ESTADO-VIATICOS", $fEstado, 0)?>
                    </select>
                </td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td align="right">Dependencia:</td>
                <td>
                    <input type="checkbox" onclick="chkFiltro(this.checked, 'fCodDependencia');" />
                    <select name="fCodDependencia" id="fCodDependencia" style="width:300px;" disabled>
                        <option value="">&nbsp;</option>
                        <?=getDependencias($fCodDependencia, $fCodOrganismo, 3)?>
                    </select>
                </td>
                <td align="right">Fecha Preparaci&oacute;n:</td>
                <td>
                    <input type="checkbox" checked onclick="chkCampos2(this.checked, ['fFechaPreparadoD','fFechaPreparadoH']);" />
                    <input type="text" name="fFechaPreparadoD" id="fFechaPreparadoD" value="<?="01-$MesActual-$AnioActual"?>" style="width:65px;" class="datepicker" maxlength="10" />
                    <input type="text" name="fFechaPreparadoH" id="fFechaPreparadoH" value="<?=formatFechaDMA($FechaActual)?>" style="width:65px;" class="datepicker" maxlength="10" />
                </td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td align="right">Buscar:</td>
                <td>
                    <input type="checkbox" onclick="chkCampos(this.checked, 'fBuscar');" />
                    <input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:295px;" disabled />
                </td>
                <td align="right">Periodo:</td>
                <td>
                    <input type="checkbox" onclick="chkCampos(this.checked, 'fPeriodo');" />
                    <input type="text" name="fPeriodo" id="fPeriodo" value="<?=$fPeriodo?>" style="width:65px;" maxlength="7" disabled />
                </td>
                <td align="right"><input type="submit" value="Buscar"></td>
            </tr>
        </table>
        </div>
        <div class="sep"></div>
        </form>
    </div>
</div>

<iframe class="ui-layout-center" id="pdf" name="pdf"></iframe>

<script type="text/javascript" src="../js/jquery.layout.js" charset="utf-8"></script>
<script type="text/javascript" language="javascript">
$(document).ready(function() {
	$('body').layout({ applyDemoStyles: true });
});
</script>