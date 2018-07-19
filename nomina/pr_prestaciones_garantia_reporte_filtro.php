<?php
//	------------------------------------
$_titulo = "Garantia de Prestaciones Sociales";
$_width = 800;
?>
<div class="ui-layout-north">
	<div style="padding:5px;">
        <table width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td class="titulo"><?=$_titulo?></td>
                <td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
            </tr>
        </table><hr width="100%" color="#333333" />
        <form name="frmentrada" id="frmentrada" action="pr_prestaciones_garantia_reporte_pdf.php" method="post" autocomplete="off" target="pdf" onsubmit="return validar();">
        <input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
        <input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
        
        <!--FILTRO-->
        <div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
        <table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
            <tr>
                <td width="80" align="right">Empleado:</td>
                <td width="375" class="gallery clearfix">
                    <input type="hidden" name="CodPersona" id="CodPersona" />
                    <input type="text" name="NomPersona" id="NomPersona" style="width:300px;" readonly="readonly" />
                    <a href="../lib/listas/gehen.php?anz=lista_empleados&filtrar=default&ventana=prestaciones_filtro&campo1=CodPersona&campo2=NomPersona&campo3=Ndocumento&campo4=Fingreso&campo5=Anios&campo6=Meses&campo7=Dias&iframe=true&width=950&height=430" rel="prettyPhoto[iframe1]">
                        <img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
                    </a>
                </td>
                <td align="right">Documento:</td>
                <td><input type="text" name="Ndocumento" id="Ndocumento" style="width:100px;" readonly="readonly" /></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td align="right">Antiguedad:</td>
                <td>
                    <input type="text" name="Anios" id="Anios" style="width:25px; text-align:right;" readonly /><i>Anios</i> &nbsp; &nbsp;
                    <input type="text" name="Meses" id="Meses" style="width:25px; text-align:right;" readonly /><i>Meses</i> &nbsp; &nbsp;
                    <input type="text" name="Dias" id="Dias" style="width:25px; text-align:right;" readonly /><i>Dias</i> &nbsp; &nbsp;
                </td>
                <td align="right">Fecha de Ingreso:</td>
                <td><input type="text" name="Fingreso" id="Fingreso" style="width:100px;" readonly /></td>
                <td width="60"><input type="submit" value="Buscar"></td>
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
	if ($('#CodPersona').val() == '') {
		cajaModal('Debe seleccionar el empleado','error');
		return false;
	} else return true;
}
</script>