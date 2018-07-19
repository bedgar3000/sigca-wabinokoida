<?php
$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
$fPeriodoD = "01-01-" . $AnioActual;
$fPeriodoH = getDiasMes($PeriodoActual) . "-" . $MesActual . "-" . $AnioActual;
//	------------------------------------
$_titulo = "Resumen Estad&iacute;stico de Partidas por Actividades";
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
        <form name="frmentrada" id="frmentrada" action="pv_reporte_resumen_actividades_pdf.php" method="post" autocomplete="off" target="pdf" onsubmit="return validar();">
            <input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
            <input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
            
            <!--FILTRO-->
            <div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
                <table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
                    <tr>
                        <td align="right">Organismo:</td>
                        <td>
                            <input type="checkbox" checked onclick="this.checked=!this.checked;" />
                            <select name="fCodOrganismo" id="fCodOrganismo" style="width:225px;" onChange="loadSelect($('#fCodUnidadEjec'), 'tabla=pv_unidadejecutora&CodOrganismo='+$(this).val(), 1); loadSelect($('#fCodDependencia'), 'tabla=dependencia_filtro&opcion='+$(this).val(), 1);">
                                <?=getOrganismos($fCodOrganismo, 3);?>
                            </select>
                        </td>
                        <td align="right">Sub-Programa:</td>
                        <td>
                            <input type="checkbox" onclick="chkCampos(this.checked, 'fIdSubPrograma');" onChange="loadSelect($('#fIdProyecto'), 'tabla=pv_proyectos&IdSubPrograma='+$('#fIdSubPrograma').val(), 1, ['fIdActividad']);" />
                            <select name="fIdSubPrograma" id="fIdSubPrograma" disabled style="width:225px;" onChange="loadSelect($('#fIdProyecto'), 'tabla=pv_proyectos&IdSubPrograma='+$('#fIdSubPrograma').val(), 1, ['fIdActividad']);">
                                <option value="">&nbsp;</option>
                                <?=loadSelect2('pv_subprogramas','IdSubPrograma','Denominacion','',0,['IdPrograma'],[''],'CodSubPrograma')?>
                            </select>
                        </td>
                        <td align="right">Fecha:</td>
                        <td>
                            <input type="checkbox" checked onclick="this.checked=!this.checked;" />
                            <input type="text" name="fPeriodoD" id="fPeriodoD" value="<?=$fPeriodoD?>" class="datepicker" style="width:60px;" maxlength="10" />
                            <input type="text" name="fPeriodoH" id="fPeriodoH" value="<?=$fPeriodoH?>" class="datepicker" style="width:60px;" maxlength="10" />
                        </td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="right">Sector:</td>
                        <td>
                            <input type="checkbox" onclick="chkCampos(this.checked, 'fIdSubSector');" onChange="loadSelect($('#fIdPrograma'), 'tabla=pv_programas&IdSubSector='+$('#fIdSubSector').val(), 1, ['fIdSubPrograma','fIdProyecto','fIdActividad']);" />
                            <select name="fIdSubSector" id="fIdSubSector" style="width:225px;" disabled onChange="loadSelect($('#fIdPrograma'), 'tabla=pv_programas&IdSubSector='+$('#fIdSubSector').val(), 1, ['fIdSubPrograma','fIdProyecto','fIdActividad']);">
                                <option value="">&nbsp;</option>
                                <?=loadSelect2('pv_subsector','IdSubSector','Denominacion','',0,NULL,NULL,'CodClaSectorial')?>
                            </select>
                        </td>
                        <td align="right">Proyecto:</td>
                        <td>
                            <input type="checkbox" onclick="chkCampos(this.checked, 'fIdProyecto');" onChange="loadSelect($('#fIdActividad'), 'tabla=pv_actividades&IdProyecto='+$('#fIdProyecto').val(), 1);" />
                            <select name="fIdProyecto" id="fIdProyecto" style="width:225px;" disabled onChange="loadSelect($('#fIdActividad'), 'tabla=pv_actividades&IdProyecto='+$('#fIdProyecto').val(), 1);">
                                <option value="">&nbsp;</option>
                                <?=loadSelect2('pv_proyectos','IdProyecto','Denominacion','',0,['IdSubPrograma'],[''],'CodProyecto')?>
                            </select>
                        </td>
                        <td align="right">Partida:</td>
                        <td>
                            <input type="checkbox" onclick="chkCampos2(this.checked, ['fPar','fGen','fEsp','fSub']);" />
                            <input type="text" name="fPar" id="fPar" value="<?=$fPar?>" style="width:27px;" disabled /> .
                            <input type="text" name="fGen" id="fGen" value="<?=$fGen?>" style="width:22px;" disabled /> .
                            <input type="text" name="fEsp" id="fEsp" value="<?=$fEsp?>" style="width:22px;" disabled /> .
                            <input type="text" name="fSub" id="fSub" value="<?=$fSub?>" style="width:22px;" disabled />
                        </td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="right">Programa:</td>
                        <td>
                            <input type="checkbox" onclick="chkCampos(this.checked, 'fIdPrograma');" onChange="loadSelect($('#fIdSubPrograma'), 'tabla=pv_subprogramas&IdPrograma='+$('#fIdPrograma').val(), 1, ['fIdProyecto','fIdActividad']);" />
                            <select name="fIdPrograma" id="fIdPrograma" style="width:225px;" disabled onChange="loadSelect($('#fIdSubPrograma'), 'tabla=pv_subprogramas&IdPrograma='+$('#fIdPrograma').val(), 1, ['fIdProyecto','fIdActividad']);">
                                <option value="">&nbsp;</option>
                                <?=loadSelect2('pv_programas','IdPrograma','Denominacion','',0,['IdSubSector'],[''],'CodPrograma')?>
                            </select>
                        </td>
                        <td align="right">Actividad:</td>
                        <td>
                            <input type="checkbox" onclick="chkCampos(this.checked, 'fIdActividad');" />
                            <select name="fIdActividad" id="fIdActividad" style="width:225px;" disabled>
                                <option value="">&nbsp;</option>
                                <?=loadSelect2('pv_actividades','IdActividad','Denominacion','',0,['IdProyecto'],[''],'CodActividad')?>
                            </select>
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
        if ($('#fCodOrganismo').val() == '') {cajaModal('Debe seleccionar el Organismo','error'); return false;}
        else if ($('#fPeriodoD').val() == '') {cajaModal('Debe ingresar el Periodo','error'); return false;}
        else if ($('#fPeriodoH').val() == '') {cajaModal('Debe ingresar el Periodo','error'); return false;}
        else return true;
    }
</script>