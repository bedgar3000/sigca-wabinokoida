<?php
list($Anio, $Mes, $Dia) = split("[/.-]", substr($Ahora, 0, 10));
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$maxlimit = $_SESSION["MAXLIMIT"];
}
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo">Calculo de Fideicomiso</td>
		<td align="right"><a class="cerrar"; href="../framemain.php">[Cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="pr_fideicomiso_calculo_pdf.php" method="post" target="iReporte">
<div class="divBorder" style="width:1000px;">
<table width="1000" class="tblFiltro">
	<tr>
		<td align="right" width="125">Periodo:</td>
		<td>
        	<input type="text" name="Periodo" id="Periodo" style="width:40px;" value="<?=$Anio?>" />
        </td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
	</tr>
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
<br />
<center>
<iframe name="iReporte" id="iReporte" style="border:solid 1px #CDCDCD; width:1000px; height:600px;"></iframe>
</center>
</form> 