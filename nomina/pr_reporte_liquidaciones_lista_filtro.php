<?php
$fCodOrganismo = $_SESSION["ORGANISMO_ACTUAL"];
//	------------------------------------
$_titulo = "Control de Prestaciones";
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
        <form name="frmentrada" id="frmentrada" action="pr_reporte_liquidaciones_lista_filtro_pdf.php" method="post" autocomplete="off" target="pdf">
            <input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
            <input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />

            <!--FILTRO-->
            <div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
                <table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
                    <tr>
                        <td align="right" width="125">Organismo:</td>
                        <td>
                            <input type="checkbox" checked onclick="this.checked=!this.checked" />
                            <select name="fCodOrganismo" id="fCodOrganismo" style="width:275px;" onChange="getOptionsSelect(this.value, 'dependencia_filtro', 'fCodDependencia', true);">
                                <?=getOrganismos($fCodOrganismo, 3)?>
                            </select>
                        </td>
                        <td align="right" width="125">N&oacute;mina:</td>
                        <td>
                            <input type="checkbox" onclick="chkFiltro(this.checked, 'fCodTipoNom');" />
                            <select name="fCodTipoNom" id="fCodTipoNom" style="width:143px;" disabled>
                                <option value="">&nbsp;</option>
                                <?=loadSelect("tiponomina", "CodTipoNom", "Nomina")?>
                            </select>
                        </td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="right">Dependencia:</td>
                        <td>
                            <input type="checkbox" onclick="chkFiltro(this.checked, 'fCodDependencia');" />
                            <select name="fCodDependencia" id="fCodDependencia" style="width:275px;" disabled>
                                <option value="">&nbsp;</option>
                                <?=getDependencias('', $fCodOrganismo, 3)?>
                            </select>
                        </td>
                        <td align="right">F. Liquidaci&oacute;n: </td>
                        <td>
                            <input type="checkbox" onclick="chkFiltro_2(this.checked, 'fFliquidacionD', 'fFliquidacionH');" />
                            <input type="text" name="fFliquidacionD" id="fFliquidacionD" disabled maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFecha(this);" /> -
                            <input type="text" name="fFliquidacionH" id="fFliquidacionH" disabled maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFecha(this);" />
                        </td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="right">Empleado: </td>
                        <td class="gallery clearfix">
                            <input type="checkbox" onclick="chkFiltroLista_3(this.checked, 'fCodEmpleado', 'fNomEmpleado', 'fCodPersona', 'btEmpleado');" />
                            <input type="hidden" name="fCodPersona" id="fCodPersona" />
                            <input type="hidden" name="fCodEmpleado" id="fCodEmpleado" />
                            <input type="text" name="fNomEmpleado" id="fNomEmpleado" style="width:270px;" class="disabled" readonly />
                            <a href="../lib/listas/listado_empleados.php?filtrar=default&cod=fCodEmpleado&nom=fNomEmpleado&campo3=fCodPersona&iframe=true&width=950&height=525" rel="prettyPhoto[iframe1]" id="btEmpleado" style="visibility:hidden;">
                                <img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
                            </a>
                        </td>
                        <td align="right">Motivo Cese: </td>
                        <td>
                            <input type="checkbox" onclick="chkFiltro(this.checked, 'fCodMotivoCes');" />
                            <select name="fCodMotivoCes" id="fCodMotivoCes" style="width:143px;" disabled>
                                <option value="">&nbsp;</option>
                                <?=loadSelect("rh_motivocese", "CodMotivoCes", "MotivoCese")?>
                            </select>
                        </td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>
                            <input type="checkbox" name="fFlagPendientes" id="fFlagPendientes" value="S" /> Mostrar Solo Pendientes
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