<?php
$fCodOrganismo = $_SESSION["ORGANISMO_ACTUAL"];
$fCodTipoNom = $_SESSION["NOMINA_ACTUAL"];
$fPeriodo = "$AnioActual-$MesActual";
//	------------------------------------
$_titulo = "Resumen Detallado de Conceptos";
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
        <form name="frmentrada" id="frmentrada" action="pr_reporte_resumen_detallado_conceptos_pdf.php" method="post" autocomplete="off" target="pdf" onsubmit="return validar();">
        <input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
        <input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
        
        <!--FILTRO-->
        <div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
        <table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
            <tr>
                <td align="right" width="100">Organismo:</td>
                <td>
                    <input type="checkbox" checked onclick="this.checked=!this.checked" />
                    <select name="fCodOrganismo" id="fCodOrganismo" style="width:275px;" onChange="loadSelect($('#fCodTipoNom'), 'tabla=loadControlNominas&CodOrganismo='+this.value, 1, ['fPeriodoAnio','fPeriodoMes','fCodTipoProceso']);">
                        <?=getOrganismos($fCodOrganismo, 3)?>
                    </select>
                </td>
                <td align="right" width="150">N&oacute;mina:</td>
                <td>
                    <input type="checkbox" checked onclick="this.checked=!this.checked" />
                    <select name="fCodTipoNom" id="fCodTipoNom" style="width:250px;" onChange="loadSelect($('#fPeriodoAnio'), 'tabla=loadControlPeriodosAnio&CodOrganismo='+$('#fCodOrganismo').val()+'&CodTipoNom='+this.value, 1, ['fPeriodoMes','fCodTipoProceso']);">
                        <?=loadControlNominas($fCodOrganismo, $fCodTipoNom)?>
                    </select>
                </td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td align="right">Periodo:</td>
                <td>
                    <input type="checkbox" checked onclick="this.checked=!this.checked" />
                    <select name="fPeriodoAnio" id="fPeriodoAnio" style="width:55px;" onChange="loadSelect($('#fPeriodoMes'), 'tabla=loadControlPeriodosMes&CodOrganismo='+$('#fCodOrganismo').val()+'&CodTipoNom='+$('#fCodTipoNom').val()+'&Anio='+this.value, 1, ['fCodTipoProceso']);">
                        <option value="">&nbsp;</option>
                        <?=loadControlPeriodosAnio($fCodOrganismo, $fCodTipoNom, $fPeriodoAnio)?>
                    </select> -
                    <select name="fPeriodoMes" id="fPeriodoMes" style="width:45px;" onChange="loadSelect($('#fCodTipoProceso'), 'tabla=loadControlProcesos&CodOrganismo='+$('#fCodOrganismo').val()+'&CodTipoNom='+$('#fCodTipoNom').val()+'&Periodo='+$('#fPeriodoAnio').val()+'-'+this.value, 1);">
                        <option value="">&nbsp;</option>
                        <?=loadControlPeriodosMes($fCodOrganismo, $fCodTipoNom, $fPeriodoAnio, $fPeriodoMes)?>
                    </select>
                </td>
                <td align="right">Proceso:</td>
                <td>
                    <input type="checkbox" checked onclick="this.checked=!this.checked" />
                    <select name="fCodTipoProceso" id="fCodTipoProceso" style="width:250px;">
                        <?=loadControlProcesos($fCodOrganismo, $fCodTipoNom, $fPeriodoAnio.'-'.$fPeriodoMes, $fCodTipoProceso)?>
                    </select>
                </td>
                <td>&nbsp;</td>
            </tr>
			<tr><td colspan="5"><hr /></td></tr>
            <tr>
                <td align="right">Empleado:</td>
                <td class="gallery clearfix">
                    <input type="checkbox" onclick="ckLista(this.checked, ['fCodPersona','fNomPersona','fCodEmpleado'], ['aCodPersona']);" />
                    <input type="hidden" name="fCodPersona" id="fCodPersona" />
                    <input type="text" name="fCodEmpleado" id="fCodEmpleado" style="width:40px;" readonly />
                    <input type="text" name="fNomPersona" id="fNomPersona" style="width:220px;" readonly />
                    <a href="../lib/listas/gehen.php?anz=lista_empleados&filtrar=default&campo1=fCodPersona&campo2=fNomPersona&campo3=fCodEmpleado&ventana=selLista&iframe=true&width=950&height=430" rel="prettyPhoto[iframe1]" id="aCodPersona" style="visibility:hidden;">
                        <img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
                    </a>
                </td>
                <td align="right">Fecha Generaci&oacute;n: </td>
                <td>
                    <input type="checkbox" onclick="chkCampos2(this.checked, ['fFechaGeneracionD','fFechaGeneracionH']);" />
                    <input type="text" name="fFechaGeneracionD" id="fFechaGeneracionD" maxlength="10" style="width:60px;" class="datepicker" disabled="disabled" /> -
                    <input type="text" name="fFechaGeneracionH" id="fFechaGeneracionH" maxlength="10" style="width:60px;" class="datepicker" disabled="disabled" />
                </td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td align="right">Archivo: </td>
                <td>
                	<input type="radio" name="rArchivo" id="PDF" value="S" onChange="$('#Archivo').val('').prop('disabled', this.checked); $('#frmentrada').attr('action', 'pr_reporte_resumen_detallado_conceptos_pdf.php');" checked /> PDF
                    &nbsp; &nbsp; &nbsp; &nbsp; 
                	<input type="radio" name="rArchivo" id="XLS" value="S" onChange="$('#Archivo').val('').prop('disabled', !this.checked); $('#frmentrada').attr('action', 'pr_reporte_resumen_detallado_conceptos_excel.php');" /> EXCEL
                    <input type="text" name="Archivo" id="Archivo" style="width:155px;" disabled="disabled" />
                </td>
                <td>&nbsp;</td>
                <td>
                    <input type="checkbox" name="FlagAsignaciones" id="FlagAsignaciones" value="I" checked /> Asignaciones &nbsp; &nbsp; &nbsp;
                    <input type="checkbox" name="FlagDeducciones" id="FlagDeducciones" value="D" checked /> Deducciones
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
function validar() {
	if ($('#PDF').prop('checked')) $('#frmentrada').attr('action', 'pr_reporte_resumen_detallado_conceptos_pdf.php');
	else if ($('#XLS').prop('checked')) $('#frmentrada').attr('action', 'pr_reporte_resumen_detallado_conceptos_excel.php');
	if ($('#XLS').prop('checked') && $('#Archivo').val() == '') cajaModal('Debe ingresar el nombre del Archivo','error');
	else document.getElementById('frmentrada').submit();
	return false;
}
</script>