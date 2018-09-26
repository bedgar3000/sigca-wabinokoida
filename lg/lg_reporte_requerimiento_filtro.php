<?php
$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
$fCodDependencia = $_SESSION["DEPENDENCIA_ACTUAL"];
$fEstado = "AP/CO";
$fFechaPreparacionD = "01-$MesActual-$AnioActual";
$fFechaPreparacionH = "$DiaActual-$MesActual-$AnioActual";
//	------------------------------------
$_titulo = "Listado de Requerimientos";
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
        <form name="frmentrada" id="frmentrada" action="lg_reporte_requerimiento_listado_pdf.php" method="post" autocomplete="off" target="pdf">
        <input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
        <input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
        
        <!--FILTRO-->
        <div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
        <table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
            <tr>
                <td align="right">Organismo:</td>
                <td>
                    <input type="checkbox" checked onclick="this.checked=!this.checked" />
                    <select name="fCodOrganismo" id="fCodOrganismo" style="width:260px;" onChange="loadSelect($('#fCodDependencia'), 'opcion='+$('#fCodOrganismo').val()+'&tabla=dependencia_filtro', 1, 'fCodCentrocosto');">
                        <?=getOrganismos($fCodOrganismo,3)?>
                    </select>
                </td>
                <td align="right">Estado: </td>
                <td>
                    <input type="checkbox" checked onclick="chkFiltro(this.checked, 'fEstado');" />
                    <select name="fEstado" id="fEstado" style="width:140px;">
                        <option value="">&nbsp;</option>
                        <?=loadSelectValores("ESTADO-REQUERIMIENTO2",$fEstado)?>
                    </select>
                </td>
                <td align="right">Almacen:</td>
                <td>
                    <input type="checkbox" onclick="chkFiltro(this.checked, 'fCodAlmacen')" />
                    <select name="fCodAlmacen" id="fCodAlmacen" style="width:140px;" disabled>
                        <option value="">&nbsp;</option>
                        <?=loadSelect("lg_almacenmast","CodAlmacen","Descripcion")?>
                    </select>
                </td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td align="right">Dependencia:</td>
                <td>
                    <input type="checkbox" checked onclick="chkFiltro(this.checked, 'fCodDependencia');" />
                    <select name="fCodDependencia" id="fCodDependencia" style="width:260px;" onChange="loadSelect($('#fCodCentroCosto'), 'opcion='+$(this).val()+'&tabla=centro_costo', 1);">
                        <option value="">&nbsp;</option>
                        <?=getDependencias($fCodDependencia,$fCodOrganismo,3)?>
                    </select>
                </td>
                <td align="right">F.Preparaci&oacute;n: </td>
                <td>
                    <input type="checkbox" checked onclick="chkFiltro_2(this.checked, 'fFechaPreparacionD', 'fFechaPreparacionH');" />
                    <input type="text" name="fFechaPreparacionD" id="fFechaPreparacionD" value="<?=$fFechaPreparacionD?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" />-
                    <input type="text" name="fFechaPreparacionH" id="fFechaPreparacionH" value="<?=$fFechaPreparacionH?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" />
                </td>
                <td align="right">Clasificaci&oacute;n:</td>
                <td>
                    <input type="checkbox" onclick="chkFiltro(this.checked, 'fClasificacion')" />
                    <select name="fClasificacion" id="fClasificacion" style="width:140px;" disabled>
                        <option value="">&nbsp;</option>
                        <?=loadSelect("lg_clasificacion","Clasificacion","Descripcion")?>
                    </select>
                </td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td align="right">Centro de Costo:</td>
                <td>
                    <input type="checkbox" onclick="chkFiltro(this.checked, 'fCodCentroCosto');" />
                    <select name="fCodCentroCosto" id="fCodCentroCosto" style="width:260px;" disabled>
                        <option value="">&nbsp;</option>
                        <?=loadSelect2("ac_mastcentrocosto","CodCentroCosto","Descripcion","",0,array('CodDependencia'),array($fCodDependencia))?>
                    </select>
                </td>
                <td align="right">F.Aprobaci&oacute;n: </td>
                <td>
                    <input type="checkbox" onclick="chkFiltro_2(this.checked, 'fFechaAprobacionD', 'fFechaAprobacionH');" />
                    <input type="text" name="fFechaAprobacionD" id="fFechaAprobacionD" value="<?=$fFechaAprobacionD?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" disabled />-
                    <input type="text" name="fFechaAprobacionH" id="fFechaAprobacionH" value="<?=$fFechaAprobacionH?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" disabled />
                </td>
                <td align="right">Dirigido a:</td>
                <td>
                    <input type="checkbox" onclick="chkFiltro(this.checked, 'fTipoClasificacion')" />
                    <select name="fTipoClasificacion" id="fTipoClasificacion" style="width:140px;" disabled>
                        <option value="">&nbsp;</option>
                        <?=loadSelectValores("DIRIGIDO")?>
                    </select>
                </td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td align="right">Buscar:</td>
                <td>
                    <input type="checkbox" onclick="chkFiltro(this.checked, 'fBuscar');" />
                    <input type="text" name="fBuscar" id="fBuscar" style="width:254px;" disabled />
                </td>
                <td align="right">&nbsp;</td>
                <td>
                    <input type="checkbox" name="fFlagCajaChica" id="fFlagCajaChica" value="S" /> Requerimiento para Caja Chica
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
</script>