<form name="frmentrada" id="frmentrada" method="post" target="iReporte">
<input type="hidden" name="sel_registros" value="<?=$sel_registros?>" />
<input type="hidden" name="fTipoEnte" value="<?=$fTipoEnte?>" />
<input type="hidden" name="fMotivoCese" value="<?=$fMotivoCese?>" />
<input type="hidden" name="fAreaExperiencia" value="<?=$fAreaExperiencia?>" />
<input type="hidden" name="fFechaD" value="<?=$fFechaD?>" />
<input type="hidden" name="fFechaH" value="<?=$fFechaH?>" />
</form>
<table width="98%" align="center" cellpadding="0" cellspacing="0">
    <tr>
        <td>
            <div class="header">
            <ul id="tab">
            <!-- CSS Tabs -->
            <li id="li1" onclick="currentTab('tab', this);" class="current">
            	<a href="#" onclick="cargarPagina(document.getElementById('frmentrada'), 'rh_reporte_antecedentes_actuacion_pdf.php'); mostrarTab('tab', 1, 3);">
                	Actuaci&oacute;n en el Organismo
                </a>
            </li>
            <li id="li2" onclick="currentTab('tab', this);">
            	<a href="#" onclick="cargarPagina(document.getElementById('frmentrada'), 'rh_reporte_antecedentes_experiencia_pdf.php'); mostrarTab('tab', 2, 3);">
                	Experiencia Laboral
                </a>
            </li>
            <li id="li3" onclick="currentTab('tab', this);">
            	<a href="#" onclick="cargarPagina(document.getElementById('frmentrada'), 'rh_reporte_antecedentes_consolidado_pdf.php'); mostrarTab('tab', 3, 3);">
                	Consolidado
                </a>
            </li>
            </ul>
            </div>
        </td>
    </tr>
</table>

<center>
<div id="tab1" style="display:block;"></div>

<div id="tab2" style="display:none;"></div>

<div id="tab3" style="display:none;"></div>

<iframe name="iReporte" id="iReporte" style="border:solid 1px #CDCDCD; width:98%; height:400px;" src="rh_reporte_antecedentes_actuacion_pdf.php?sel_registros=<?=$sel_registros?>"></iframe>
</center>