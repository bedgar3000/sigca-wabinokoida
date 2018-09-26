<?php
$CodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
$Periodo = $PeriodoActual;
$SistemaFuente = 'DO';
##
$_titulo = "Importación de Registro de Ventas";
$accion = "importar";
$label_submit = "Importar";
$focus = "btSubmit";
$clkCancelar = "document.getElementById('frmentrada').submit();";
//	------------------------------------
$_width = 500;
if ($origen == 'framemain') $action = '../framemain.php';
elseif (!empty($origen)) $action = "gehen.php?anz=$origen";
else $action = "gehen.php?anz=co_registroventas_lista";
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="<?=$action?>" method="POST" enctype="multipart/form-data" onsubmit="return validarImportar();" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="SistemaFuente" id="SistemaFuente" value="<?=$SistemaFuente?>" />

	<table style="width:100%; max-width:<?=$_width?>px;" class="tblForm">
		<tr>
	    	<td colspan="2" class="divFormCaption">IMPORTAR</td>
	    </tr>
	    <tr>
			<td class="tagForm">* Organismo:</td>
			<td>
				<select name="CodOrganismo" id="CodOrganismo" style="width:295px;">
					<?=getOrganismos($CodOrganismo, 3)?>
				</select>
			</td>
		</tr>
		<tr>
			<td align="right">* Periodo:</td>
			<td>
				<input type="text" name="Periodo" id="Periodo" value="<?=$Periodo?>" maxlength="10" style="width:60px;" />
	        </td>
		</tr>
	</table>

	<center>
		<input type="submit" value="<?=$label_submit?>" style="width:75px;" id="btSubmit" />
		<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
	</center>
</form>
<div style="width:100%; max-width:<?=$_width?>px;" class="divMsj">Campos Obligatorios *</div>
<br><br>

<div class="ui-state-highlight ui-corner-all" style="max-width:<?=$_width?>px; margin: auto;">
    <p>
    <span class="ui-icon ui-icon-info" style="float: left;"></span>
    <strong>Este proceso considera todos los documentos marcados como exportables al Registro de Ventas en el maestro de tipos de documentos. Los documentos anulados pasarán con montos en 0.</strong>
    </p>
</div>

<script type="text/javascript">
	function validarImportar() {
		//	ajax
		$.post('co_registroventas_ajax.php', 'modulo=validar&accion=importar&'+$('#frmentrada').serialize(), function(data) {
			if (data != '') {
				$("#cajaModal").dialog({
					buttons: {
						"Si": function() {
							$(this).dialog("close");
							formSubmitImportar();
						},
						"No": function() {
							$(this).dialog("close");
						}
					}
				});
				$("#cajaModal").dialog({ title: "<img src='../imagenes/info.png' width='24' align='absmiddle' />Aviso", width: 600 });
				$("#cajaModal").html(data);
				$('#cajaModal').dialog('open');
			} else {
				formSubmitImportar();
			}
	    });

	    return false;
	}

	function formSubmitImportar() {
		$.post('co_registroventas_ajax.php', 'modulo=formulario&accion=importar&'+$('#frmentrada').serialize(), function(data) {
			var datos = data.split('|');
			if (datos[0].trim() != '') cajaModal(datos[0]);
			else if (datos[1].trim() != '') cajaModal(datos[1], 'success', 400, "document.getElementById('frmentrada').submit();");
			else document.getElementById('frmentrada').submit();
	    });
	}
</script>