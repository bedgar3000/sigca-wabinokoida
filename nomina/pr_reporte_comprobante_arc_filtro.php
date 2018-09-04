<?php
$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
$fCodDependencia = $_SESSION["DEPENDENCIA_ACTUAL"];
//	------------------------------------
$_titulo = "Comprobante de Retención AR-C";
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
        <form name="frmentrada" id="frmentrada" action="pr_reporte_comprobante_arc_pdf.php" method="post" autocomplete="off" target="pdf">
        <input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
        <input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
        
        <!--FILTRO-->
        <div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
        <table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
            <tr>
                <td align="right">Persona:</td>
                <td class="gallery clearfix">
                    <input type="checkbox" checked onclick="this.checked=!this.checked" />
                    <input type="text" name="fCodPersona" id="fCodPersona" style="width:40px;" readonly />
                    <input type="text" name="fNomPersona" id="fNomPersona" style="width:255px;" readonly />
                    <a href="../lib/listas/gehen.php?anz=lista_empleados&filtrar=default&campo1=fCodPersona&campo2=fNomPersona&ventana=selLista&iframe=true&width=950&height=430" rel="prettyPhoto[iframe1]" id="aCodPersona">
                        <img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
                    </a>
                </td>
                <td align="right">A&ntilde;o:</td>
                <td>
                    <input type="checkbox" checked onclick="this.checked=!this.checked" />
                    <input type="text" name="fAnio" id="fAnio" value="<?=$fAnio?>" style="width:60px;" />
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