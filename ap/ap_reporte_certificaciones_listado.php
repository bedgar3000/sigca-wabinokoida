<?php
$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
$fFechaD = "01-$MesActual-$AnioActual";
$fFechaH = formatFechaDMA($FechaActual);
if ($lista == "listar-obras") $fCodTipoCertif = '09';
//	------------------------------------
$_titulo = "Gastos Directos";
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
        <form name="frmentrada" id="frmentrada" action="ap_reporte_certificaciones_listado_pdf.php" method="post" autocomplete="off" target="pdf" onsubmit="return validar();">
            <input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
            <input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
            
            <!--FILTRO-->
            <div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
                <table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
                    <tr>
                        <td align="right">Organismo:</td>
                        <td>
                            <input type="checkbox" checked onclick="this.checked=!this.checked;" />
                            <select name="fCodOrganismo" id="fCodOrganismo" style="width:250px;">
                                <?=getOrganismos($fCodOrganismo, 3)?>
                            </select>
                        </td>
                        <td align="right">Tipo:</td>
                        <td>
                            <?php
                            if ($lista == "listar-obras") {
                                ?>
                                <input type="checkbox" checked onclick="this.checked=!this.checked;" />
                                <select name="fCodTipoCertif" id="fCodTipoCertif" style="width:133px;">
                                    <?=loadSelect2("ap_tiposcertificacion","CodTipoCertif","Descripcion",$fCodTipoCertif,11)?>
                                </select>
                                <?php
                            } else {
                                ?>
                                <input type="checkbox" onclick="chkCampos(this.checked, 'fCodTipoCertif');" />
                                <select name="fCodTipoCertif" id="fCodTipoCertif" style="width:133px;" disabled>
                                    <option value="">&nbsp;</option>
                                    <?=loadSelectTiposCertificacion('',10)?>
                                </select>
                                <?php
                            }
                            ?>
                        </td>
                        <td align="right">Fecha: </td>
                        <td>
                            <input type="checkbox" checked onclick="chkCampos2(this.checked, ['fFechaD','fFechaH']);" />
                            <input type="text" name="fFechaD" id="fFechaD" value="<?=$fFechaD?>" style="width:65px;" maxlength="10" class="datepicker" />
                            <input type="text" name="fFechaH" id="fFechaH" value="<?=$fFechaH?>" style="width:65px;" maxlength="10" class="datepicker" />
                        </td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="right">Beneficiario: </td>
                        <td class="gallery clearfix">
                            <input type="checkbox" onClick="ckLista(this.checked,['fCodPersona','fNomPersona'],['btCodPersona'])" />
                            <input type="text" name="fCodPersona" id="fCodPersona" style="width:45px;" readonly />
                            <input type="text" name="fNomPersona" id="fNomPersona" style="width:202px;" readonly />
                            <a href="../lib/listas/gehen.php?anz=lista_personas&filtrar=default&campo1=fCodPersona&campo2=fNomPersona&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" id="btCodPersona" style="visibility:hidden;">
                                <img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
                            </a>
                        </td>
                        <td align="right">Buscar:</td>
                        <td>
                            <input type="checkbox" onclick="chkCampos(this.checked, 'fBuscar');" />
                            <input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:133px;" disabled />
                        </td>
                        <td align="right">Estado: </td>
                        <td>
                            <input type="checkbox" onclick="chkFiltro(this.checked, 'fEstado');" />
                            <select name="fEstado" id="fEstado" style="width:133px;" disabled>
                                <option value="">&nbsp;</option>
                                <?=loadSelectValores("certificaciones-estado")?>
                            </select>
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
        else return true;
    }
</script>