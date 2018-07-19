<?php
session_start();
if (!isset($_SESSION['USUARIO_ACTUAL']) || !isset($_SESSION['ORGANISMO_ACTUAL'])) header("Location: ../index.php");
//	------------------------------------
include("fphp_nomina.php");
connect();
list ($_SHOW, $_ADMIN, $_INSERT, $_UPDATE, $_DELETE) = opcionesPermisos('02', $concepto);
//	------------------------------------
$ftiponom = $_SESSION["NOMINA_ACTUAL"];
$forganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
$dSeleccion1 = "disabled"; 
$dSeleccion2 = "disabled"; 
$dfsittra = "disabled";
//	---------------------------------	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="css1.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../js/jquery-1.7.min.js" charset="utf-8"></script>
<script type="text/javascript" language="javascript" src="fscript_nomina.js"></script>
</head>

<body>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo">Generar TXT de N&oacute;mina</td>
		<td align="right"><a class="cerrar"; href="../framemain.php">[Cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" onsubmit="return txtNomina(this);">
<div class="divBorder" style="width:1000px;">
<table width="1000" class="tblFiltro">
    <tr>
        <td align="right">Organismo:</td>
        <td>
        	<input type="checkbox" name="chkorganismo" id="chkorganismo" value="1" onclick="forzarCheck('chkorganismo');" checked="checked" />
			<select name="forganismo" id="forganismo" class="selectBig">
				<?=getOrganismos($forganismo, 3)?>
			</select>
        </td>
        <td align="right">Per&iacute;odo:</td>
        <td>
        	<input type="checkbox" name="chkperiodo" id="chkperiodo" value="1" onclick="forzarCheck('chkperiodo');" checked="checked" />
			<select name="fperiodo" id="fperiodo" style="width:100px;" onchange="getFOptions_Proceso(this.id, 'ftproceso', 'chktproceso', document.getElementById('ftiponom').value, document.getElementById('forganismo').value, '6');">
				<option value=""></option>
				<?=getPeriodosTXT($forganismo);?>
			</select>
		</td>
    </tr>
    <tr>
        <td align="right">Proceso:</td>
        <td>
        	<input type="checkbox" name="chktproceso" id="chktproceso" value="1" onclick="forzarCheck('chktproceso');" checked="checked" />
			<select name="ftproceso" id="ftproceso" class="selectBig">
				<option value=""></option>
                <?=loadSelect('pr_tipoproceso', 'CodTipoProceso','Descripcion','',0)?>
			</select>
		</td>
        <td align="right">N&oacute;mina:</td>
        <td>
            <input type="checkbox" onclick="$('#fCodTipoNom').attr('disabled', !this.checked).val('');" checked />
            <select name="fCodTipoNom" id="fCodTipoNom" style="width:250px;">
                <option value=''>&nbsp;</option>
                <?=loadSelect('tiponomina', 'CodTipoNom','Nomina','',0)?>
            </select>
        </td>
    </tr>
    <tr>
        <td align="right">Nombre del Archivo:</td>
        <td>
            <input type="text" name="archivo" id="archivo" size="50" />
        </td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
</table>
</div>
<center><input type="submit" name="btBuscar" value="Buscar"></center>
</form>



<script type="text/javascript">
    function txtNomina(form) {
        var num = 0;
        var seleccionados = "";
        var empleados = "";
        var organismo = document.getElementById("forganismo").value;
        //var nomina = document.getElementById("ftiponom").value;
        var periodo = document.getElementById("fperiodo").value;
        var nombre_archivo = document.getElementById("archivo").value;
        var proceso = document.getElementById("ftproceso");
        var fCodTipoNom = document.getElementById("fCodTipoNom").value;
        var codproceso = proceso.value;
        var nomproceso = proceso.options[proceso.selectedIndex].text;
        
        if (organismo == "" || periodo == "" || codproceso == "" || nombre_archivo == "") alert("¡Debe ingresar todos los valores del filtro!");
        else {
            //  CREO UN OBJETO AJAX PARA VERIFICAR QUE EL NUEVO REGISTRO NO EXISTA EN LA BASE DE DATOS
            var ajax=nuevoAjax();
            ajax.open("POST", "txt_nomina_venezuela.php", true);
            ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            ajax.send("codproceso="+codproceso+"&nomproceso="+nomproceso+"&periodo="+periodo+"&organismo="+organismo+"&nombre_archivo="+nombre_archivo+"&fCodTipoNom="+fCodTipoNom);
            ajax.onreadystatechange=function() {
                if (ajax.readyState==4) {
                    window.open("descarga_txt.php?nombre_archivo="+nombre_archivo, "wPrincipal", "toolbar=no, menubar=no, location=no, scrollbars=yes, height=800, width=800, left=200, top=200, resizable=yes");
                }
            }
        }
        return false;
    }
</script>


</body>
</html>