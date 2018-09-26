<?php
$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
$fCodContabilidad = 'F';
$fPeriodo = $AnioActual;
//	------------------------------------
$_titulo = "Cierre Anual";
$_width = 860;
?>
<div class="ui-layout-north">
	<div style="padding:5px;">
        <table width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td class="titulo"><?=$_titulo?></td>
                <td align="right"><a class="cerrar" href="framemain.php">[cerrar]</a></td>
            </tr>
        </table><hr width="100%" color="#333333" />
        <form name="frmentrada" id="frmentrada" action="ac_cierre_anual_pdf.php" method="post" autocomplete="off" target="pdf">
	        <input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	        <input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	        <!--FILTRO-->
	        <div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
		        <table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
		            <tr>
		                <td align="right" width="115">Organismo:</td>
		                <td>
		                    <input type="checkbox" checked onclick="this.checked=!this.checked" />
		                    <select name="fCodOrganismo" id="fCodOrganismo" style="width:260px;" onChange="loadSelect($('#fCodDependencia'), 'opcion='+$('#fCodOrganismo').val()+'&tabla=dependencia_filtro', 1, 'fCodCentrocosto');">
		                        <?=getOrganismos($fCodOrganismo, 3)?>
		                    </select>
		                </td>
		                <td align="right">Periodo:</td>
		                <td>
		                    <input type="checkbox" checked onclick="this.checked=!this.checked" />
		                    <select name="fPeriodo" id="fPeriodo" style="width:75px;">
		                        <?=loadSelectAniosVoucher($fPeriodo)?>
		                    </select>
		                </td>
		                <td>&nbsp;</td>
		            </tr>
		            <tr>
		                <td class="tagForm" width="100">Contabilidad:</td>
		                <td>
		                    <input type="checkbox" checked onclick="this.checked=!this.checked" />
		                    <select name="fCodContabilidad" id="fCodContabilidad" style="width:260px;">
		                        <?=loadSelect2("ac_contabilidades","CodContabilidad","Descripcion",$fCodContabilidad)?>
		                    </select>
		                </td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td align="right"><input type="submit" value="Buscar" onclick="$('#btEjecutar').prop('disabled',false);" /></td>
		            </tr>
		        </table>
	        </div>
	        <table style="width:100%;">
	        	<tr>
	        		<td align="right">
	        			<input type="button" value="Ejecutar Cierre" id="btEjecutar" style="width:100px;" onclick="cierre_anual();" disabled />
	        		</td>
	        	</tr>
	        </table>
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

	function cierre_anual() {
		bloqueo(true);
		var fPeriodo = new Number($('#fPeriodo').val());
		$("#cajaModal").dialog({
			buttons: {
				"Si": function() {
					$(this).dialog("close");
					$.ajax({
						type: "POST",
						url: 'ac_cierre_anual_ajax.php',
						data: 'modulo=formulario&accion=cierre&'+$('form').serialize(),
						async: false,
						success: function(resp) {
							bloqueo(false);
							var datos = resp.split("|");
							if (datos[0]) {
								cajaModal(datos[0],'error');
							} else {
								cajaModal(datos[1],'exito');
							}
						}
					});
				},
				"No": function() {
					bloqueo(false);
					$(this).dialog("close");
				}
			}
		});
		cajaModalConfirm("<strong>AVISO:</strong> Este proceso cerrará la contabilidad del <strong>Año: " + fPeriodo + "</strong> e iniciará los Balances de Cuentas en el Año: <strong>Año: " + (++fPeriodo) + "</strong>. NO se podrá volver a generar el proceso.<br/ >¿Está seguro de continuar?", 400);
	}
</script>