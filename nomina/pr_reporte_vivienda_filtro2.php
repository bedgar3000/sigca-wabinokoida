<?php
$fCodOrganismo = $_SESSION["ORGANISMO_ACTUAL"];
$fCodTipoNom = $_SESSION["NOMINA_ACTUAL"];
$fPeriodo = "$AnioActual-$MesActual";
$fPeriodoAnio = $AnioActual;
$fPeriodoMes = $MesActual;
//	------------------------------------
$_titulo = "Ley del R&eacute;gimen Prestacional de Vivienda y H&aacute;bitat";
$_width = 900;
?>
<div class="ui-layout-north">
	<div style="padding:5px;">
        <table width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td class="titulo"><?=$_titulo?></td>
                <td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
            </tr>
        </table><hr width="100%" color="#333333" />
        <form name="frmentrada" id="frmentrada" action="pr_reporte_vivienda_pdf.php" method="post" autocomplete="off" target="pdf" onsubmit="return validar();">
        <input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
        <input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
        
        <!--FILTRO-->
        <div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
        <table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
            <tr>
                <td align="right" width="100">Organismo:</td>
                <td>
                    <input type="checkbox" checked onclick="this.checked=!this.checked" />
                    <select name="fCodOrganismo" id="fCodOrganismo" style="width:275px;">
                        <?=getOrganismos($fCodOrganismo, 3)?>
                    </select>
                </td>
                <td align="right" width="150">N&oacute;mina:</td>
                <td>
                    <input type="checkbox" checked onclick="chkFiltro(this.checked, 'fCodTipoNom');" />
                    <select name="fCodTipoNom" id="fCodTipoNom" style="width:250px;">
                        <option value="">&nbsp;</option>
                        <?=loadSelect("tiponomina", "CodTipoNom", "Nomina", $fCodTipoNom, 0)?>
                    </select>
                </td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td align="right">Periodo:</td>
                <td>
                    <input type="checkbox" checked onclick="this.checked=!this.checked" />
                    <select name="fPeriodoAnio" id="fPeriodoAnio" style="width:55px;" onChange="loadSelect($('#fPeriodoMes'), 'tabla=loadControlPeriodosMes&CodOrganismo='+$('#fCodOrganismo').val()+'&CodTipoNom='+$('#fCodTipoNom').val()+'&Anio='+this.value, 1);">
                        <option value="">&nbsp;</option>
                        <?=loadControlPeriodosAnio($fCodOrganismo, '', $fPeriodoAnio)?>
                    </select> -
                    <select name="fPeriodoMes" id="fPeriodoMes" style="width:45px;">
                        <option value="">&nbsp;</option>
                        <?=loadControlPeriodosMes($fCodOrganismo, '', $fPeriodoAnio, $fPeriodoMes)?>
                    </select>
                </td>
                <td align="right">Proceso:</td>
                <td>
                    <input type="checkbox" checked onclick="chkFiltro(this.checked, 'fCodTipoProceso');" />
                    <select name="fCodTipoProceso" id="fCodTipoProceso" style="width:250px;">
                        <option value="">&nbsp;</option>
                        <?=loadSelect("pr_tipoproceso", "CodTipoProceso", "Descripcion", $fCodTipoProceso, 0)?>
                    </select>
                </td>
                <td>&nbsp;</td>
            </tr>
			<tr><td colspan="5"><hr /></td></tr>
            <tr>
                <td align="right">Archivo: </td>
                <td>
                	<input type="radio" name="rArchivo" id="PDF" value="S" onChange="$('#Archivo').val('').prop('disabled', this.checked); $('#frmentrada').attr('action', 'pr_reporte_vivienda_pdf.php');" checked /> PDF
                    &nbsp; &nbsp; &nbsp; &nbsp; 
                	<input type="radio" name="rArchivo" id="XLS" value="S" onChange="$('#Archivo').val('').prop('disabled', !this.checked); $('#frmentrada').attr('action', 'pr_reporte_vivienda_excel.php');" /> EXCEL
                    &nbsp; &nbsp; &nbsp; &nbsp; 
                    <input type="radio" name="rArchivo" id="TXT" value="S" onChange="$('#Archivo').val('').prop('disabled', !this.checked); $('#frmentrada').attr('action', 'pr_reporte_vivienda_txt2.php');" /> TXT
                    <input type="text" name="Archivo" id="Archivo" style="width:130px;" disabled="disabled" />
                </td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
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
function validar() {
	if ($('#XLS').prop('checked') && $('#Archivo').val() == '') cajaModal('Debe ingresar el nombre del Archivo','error');
	else document.getElementById('frmentrada').submit();
	return false;
}
</script>