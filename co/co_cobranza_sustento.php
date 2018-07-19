<form name="frmentrada" id="frmentrada" method="post" target="iReporte">
	<input type="hidden" name="sel_registros" id="sel_registros" value="<?=$sel_registros?>" />

	<table style="width:100%;" align="center" cellpadding="0" cellspacing="0">
	    <tr>
	        <td>
	            <div class="header">
		            <ul id="tab">
			            <!-- CSS Tabs -->
			            <li id="li1" onclick="current($(this));" class="current">
			            	<a href="#" onclick="cargarPagina(document.getElementById('frmentrada'), 'co_cobranza_sustento_pdf.php');">Sustento</a>
			            </li>
			            <li id="li2" onclick="current($(this));">
			            	<a href="#" onclick="cargarPagina(document.getElementById('frmentrada'), 'co_cobranza_recibo_pdf.php');">Recibo</a>
			            </li>
		            </ul>
	            </div>
	        </td>
	    </tr>
	</table>
</form>

<center>
	<iframe name="iReporte" id="iReporte" style="border-left:solid 1px #CDCDCD; border-right:solid 1px #CDCDCD; border-bottom:solid 1px #CDCDCD; border-top:0; width:100%; height:50px;" src="co_cobranza_sustento_pdf.php?sel_registros=<?=$sel_registros?>"></iframe>
</center>

<script type="text/javascript" language="javascript">
	$(document).ready(function() {
		var iHeightWindow = $(window).height();
		var iHeight = iHeightWindow - 75;
		$('#iReporte').css('height', iHeight);
	});
</script>