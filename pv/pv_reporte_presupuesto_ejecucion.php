<?php
$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
$sql = "SELECT MAX(Ejercicio) FROM pv_reformulacionmetas";
$Ejercicio = getVar3($sql);
$fEjercicio = ($Ejercicio?$AnioActual:$AnioActual);
//	------------------------------------
$_titulo = "Ejecuci&oacute;n Presupuestaria";
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
        <form name="frmentrada" id="frmentrada" action="pv_reporte_presupuesto_ejecucion_pdf.php" method="post" autocomplete="off" target="pdf" onsubmit="return validar();">
            <input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
            <input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
            
            <!--FILTRO-->
            <div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
                <table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
                    <tr>
                        <td align="right" width="125">Organismo:</td>
                        <td>
                            <input type="checkbox" checked onclick="this.checked=!this.checked;" />
                            <select name="fCodOrganismo" id="fCodOrganismo" style="width:225px;" onChange="loadSelect($('#fCodUnidadEjec'), 'tabla=pv_unidadejecutora&CodOrganismo='+$(this).val(), 1); loadSelect($('#fCodDependencia'), 'tabla=dependencia_filtro&opcion='+$(this).val(), 1);">
                                <?=getOrganismos($fCodOrganismo, 3);?>
                            </select>
                        </td>
                        <td align="right" width="100">Sub-Sector:</td>
                        <td>
                            <input type="checkbox" onclick="chkCampos(this.checked, 'fIdSubSector');" onChange="loadSelect($('#fIdPrograma'), 'tabla=pv_programas&IdSubSector='+$('#fIdSubSector').val(), 1, ['fIdSubPrograma','fIdProyecto','fIdActividad']);" />
                            <select name="fIdSubSector" id="fIdSubSector" style="width:225px;" disabled onChange="loadSelect($('#fIdPrograma'), 'tabla=pv_programas&IdSubSector='+$('#fIdSubSector').val(), 1, ['fIdSubPrograma','fIdProyecto','fIdActividad']);">
                                <option value="">&nbsp;</option>
                                <?=loadSelect2('pv_subsector','IdSubSector','Denominacion','',0,NULL,NULL,'CodClaSectorial')?>
                            </select>
                        </td>
                        <td align="right" width="100">Proyecto:</td>
                        <td>
                            <input type="checkbox" onclick="chkCampos(this.checked, 'fIdProyecto');" onChange="loadSelect($('#fIdActividad'), 'tabla=pv_actividades&IdProyecto='+$('#fIdProyecto').val(), 1);" />
                            <select name="fIdProyecto" id="fIdProyecto" style="width:225px;" disabled onChange="loadSelect($('#fIdActividad'), 'tabla=pv_actividades&IdProyecto='+$('#fIdProyecto').val(), 1);">
                                <option value="">&nbsp;</option>
                                <?=loadSelect2('pv_proyectos','IdProyecto','Denominacion','',0,['IdSubPrograma'],[''],'CodProyecto')?>
                            </select>
                        </td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="right">Dependencia: </td>
                        <td>
                            <input type="checkbox" onclick="chkFiltro(this.checked, 'fCodDependencia');" />
                            <select name="fCodDependencia" id="fCodDependencia" style="width:225px;" disabled>
                                <option value="">&nbsp;</option>
                                <?=getDependencias('', $fCodOrganismo, 0);?>
                            </select>
                        </td>
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
                    </tr>
                    <tr>
                        <td align="right">Unidad Ejecutora: </td>
                        <td>
                            <input type="checkbox" onclick="chkFiltro(this.checked, 'fCodUnidadEjec');" />
                            <select name="fCodUnidadEjec" id="fCodUnidadEjec" style="width:225px;" disabled>
                                <option value="">&nbsp;</option>
                                <?=loadSelect2('pv_unidadejecutora','CodUnidadEjec','Denominacion','',10,['CodOrganismo'],[$fCodOrganismo]);?>
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
                        <td align="right">Partida:</td>
                        <td>
                            <input type="checkbox" onclick="chkCampos2(this.checked, ['fPar','fGen','fEsp','fSub']);" />
                            <input type="text" name="fPar" id="fPar" style="width:27px;" disabled /> .
                            <input type="text" name="fGen" id="fGen" style="width:22px;" disabled /> .
                            <input type="text" name="fEsp" id="fEsp" style="width:22px;" disabled /> .
                            <input type="text" name="fSub" id="fSub" style="width:22px;" disabled />
                        </td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td align="right">Ejercicio:</td>
                        <td>
                            <input type="checkbox" checked onclick="this.checked=!this.checked;" />
                            <input type="text" name="fEjercicio" id="fEjercicio" value="<?=$fEjercicio?>" style="width:47px;" maxlength="4" />
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
        if ($('#fCodOrganismo').val() == '') {cajaModal('Debe seleccionar el Organismo','error'); return false;}
        else if ($('#fEjercicio').val() == '') {cajaModal('Debe ingresar el Ejercicio','error'); return false;}
        else return true;
    }
</script>