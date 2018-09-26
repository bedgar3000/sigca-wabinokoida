<?php
$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
$sql = "SELECT MAX(Ejercicio) FROM pv_reformulacionmetas";
$Ejercicio = getVar3($sql);
$fEjercicio = ($Ejercicio?$AnioActual:$AnioActual);
//	------------------------------------
$_titulo = "Recursos Humanos por Escala de Sueldos (F.2109)";
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
        <form name="frmentrada" id="frmentrada" action="pv_reporte_f2109_pdf.php" method="post" autocomplete="off" target="pdf" onsubmit="return validar();">
            <input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
            <input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
            
            <!--FILTRO-->
            <div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
                <table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
                    <tr>
                        <td align="right" width="125">Organismo:</td>
                        <td>
                            <input type="checkbox" checked onclick="this.checked=!this.checked;" />
                            <select name="fCodOrganismo" id="fCodOrganismo" style="width:275px;">
                                <?=getOrganismos($fCodOrganismo, 3);?>
                            </select>
                        </td>
                        <td align="right" width="125">Ejercicio:</td>
                        <td class="gallery clearfix">
                            <input type="checkbox" checked onclick="this.checked=!this.checked;" />
                            <input type="text" name="fEjercicio" id="fEjercicio" value="<?=$fEjercicio?>" style="width:40px;" maxlength="4" />
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
        else if ($('#fEjercicio').val() == '') {cajaModal('Debe seleccionar el Ejercicio','error'); return false;}
        else return true;
    }
</script>