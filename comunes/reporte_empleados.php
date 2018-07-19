<?php
$FechaActual = "$AnioActual-$MesActual-$DiaActual";
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$fCodDependencia = $_SESSION["DEPENDENCIA_ACTUAL"];
	$fAgrupador = "CodDependencia";
	$fEdoReg = "A";
	$fSitTra = "A";
}
//	------------------------------------
$_titulo = "Empleados";
$_sufijo = "reporte_empleados";
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="reporte_empleados_pdf2.php" method="post" autocomplete="off" target="iReporte">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />

<div class="divBorder" style="width:<?=$_width?>px;">
<table width="<?=$_width?>" class="tblFiltro">
	<tr>
		<td align="right" width="125">Organismo:</td>
		<td>
			<input type="checkbox" checked onclick="this.checked=!this.checked" />
			<select name="fCodOrganismo" id="fCodOrganismo" style="width:300px;" onChange="getOptionsSelect(this.value, 'dependencia_filtro', 'fCodDependencia', true, 'fCodCentroCosto');">
				<?=getOrganismos($fCodOrganismo, 3)?>
			</select>
		</td>
		<td align="right" width="125">Edo. Reg: </td>
		<td>
        	<input type="checkbox" checked onclick="chkFiltro(this.checked, 'fEdoReg');" />
            <select name="fEdoReg" id="fEdoReg" style="width:143px;">
                <option value=""></option>
                <?=loadSelectGeneral("ESTADO", $fEdoReg, 0)?>
            </select>
		</td>
	</tr>
	<tr>
		<td align="right">Dependencia:</td>
		<td>
			<input type="checkbox" checked onclick="chkFiltro(this.checked, 'fCodDependencia');" />
			<select name="fCodDependencia" id="fCodDependencia" style="width:300px;" onChange="getOptionsSelect(this.value, 'centro_costo', 'fCodCentroCosto', true);">
            	<option value="">&nbsp;</option>
				<?=getDependencias($fCodDependencia, $fCodOrganismo, 3)?>
			</select>
		</td>
		<td align="right">Sit. Tra.: </td>
		<td>
        	<input type="checkbox" checked onclick="chkFiltro(this.checked, 'fSitTra');" />
            <select name="fSitTra" id="fSitTra" style="width:143px;">
                <option value=""></option>
                <?=loadSelectGeneral("ESTADO", $fSitTra, 0)?>
            </select>
		</td>
	</tr>
	<tr>
		<td align="right">Centro de Costo:</td>
		<td>
			<input type="checkbox" onclick="chkFiltro(this.checked, 'fCodCentroCosto');" />
			<select name="fCodCentroCosto" id="fCodCentroCosto" style="width:300px;" disabled>
            	<option value="">&nbsp;</option>
				<?=loadSelect("ac_mastcentrocosto", "CodCentroCosto", "Descripcion", "", 0)?>
			</select>
		</td>
		<td align="right">Fecha de Ingreso: </td>
		<td>
			<input type="checkbox" onclick="chkFiltro_2(this.checked, 'fFingresoD', 'fFingresoH');" />
			<input type="text" name="fFingresoD" id="fFingresoD" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFecha(this);" disabled /> -
            <input type="text" name="fFingresoH" id="fFingresoH" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFecha(this);" disabled />
        </td>
	</tr>
	<tr>
		<td align="right">Tipo de Nomina:</td>
		<td>
			<input type="checkbox" onclick="chkFiltro(this.checked, 'fCodTipoNom');" />
			<select name="fCodTipoNom" id="fCodTipoNom" style="width:300px;" disabled>
            	<option value="">&nbsp;</option>
				<?=loadSelect("tiponomina", "CodTipoNom", "Nomina", "", 0)?>
			</select>
		</td>
        <td align="right">Fecha de Egreso: </td>
        <td>
            <input type="checkbox" onclick="chkFiltro_2(this.checked, 'fFegresoD', 'fFegresoH');" />
            <input type="text" name="fFegresoD" id="fFegresoD" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFecha(this);" disabled /> -
            <input type="text" name="fFegresoH" id="fFegresoH" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFecha(this);" disabled />
        </td>
	</tr>
	<tr>
		<td align="right">Tipo de Trabajador:</td>
		<td>
			<input type="checkbox" onclick="chkFiltro(this.checked, 'fCodTipoTrabajador');" />
			<select name="fCodTipoTrabajador" id="fCodTipoTrabajador" style="width:300px;" disabled>
            	<option value="">&nbsp;</option>
				<?=loadSelect("rh_tipotrabajador", "CodTipoTrabajador", "TipoTrabajador", "", 0)?>
			</select>
		</td>
		<td align="right">Tipo de Pago: </td>
		<td>
        	<input type="checkbox" onclick="chkFiltro(this.checked, 'fCodTipoPago');" />
            <select name="fCodTipoPago" id="fCodTipoPago" style="width:143px;" disabled="disabled">
                <option value=""></option>
                <?=loadSelect("masttipopago", "CodTipoPago", "TipoPago", "", 0)?>
            </select>
		</td>
	</tr>
	<tr>
		<td align="right">Perfil de N&oacute;mina:</td>
		<td>
			<input type="checkbox" onclick="chkFiltro(this.checked, 'fCodPerfil');" />
			<select name="fCodPerfil" id="fCodPerfil" style="width:300px;" disabled>
            	<option value="">&nbsp;</option>
				<?=loadSelect("tipoperfilnom", "CodPerfil", "Perfil", "", 0)?>
			</select>
		</td>
        <td align="right">Buscar:</td>
        <td>
            <input type="checkbox" onclick="chkFiltro(this.checked, 'fBuscar');" />
            <input type="text" name="fBuscar" id="fBuscar" style="width:137px;" disabled />
        </td>
	</tr>
    <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td align="right">Agrupador: </td>
        <td>
            <input type="checkbox" checked onclick="chkFiltro(this.checked, 'fAgrupador');" />
            <select name="fAgrupador" id="fAgrupador" style="width:143px;">
                <option value=""></option>
                <?=loadSelectValores("EMPLEADOS-AGRUPADOR", $fAgrupador, 0)?>
            </select>
        </td>
    </tr>
    <tr>
    	<td colspan="4">
        	<hr />
        </td>
    </tr>
	<tr>
		<td align="right">Mostrar:</td>
		<td colspan="3">
        	<table width="100%" cellpadding="0" cellspacing="0">
            	<tr>
                	<td>
                    	<input type="checkbox" name="FlagFingreso" id="FlagFingreso" value="S" checked="checked" /> F. Ingreso
                    </td>
                	<td>
                    	<input type="checkbox" name="FlagCodCargo" id="FlagCodCargo" value="S" checked="checked" /> Cargo
                    </td>
                	<td>
                    	<input type="checkbox" name="FlagFegreso" id="FlagFegreso" value="S" /> F. Cese
                    </td>
                	<td>
                    	<input type="checkbox" name="FlagCodTipoNom" id="FlagCodTipoNom" value="S" /> Tipo de N&oacute;mina
                    </td>
                	<td>
                    	<input type="checkbox" name="FlagPaso" id="FlagPaso" value="S" /> Paso
                    </td>
                </tr>
                <tr>
                	<td>
                    	<input type="checkbox" name="FlagCodTipoTrabajador" id="FlagCodTipoTrabajador" value="S" /> Tipo de Trabajador
                    </td>
                	<td>
                    	<input type="checkbox" name="FlagCodPerfil" id="FlagCodPerfil" value="S" checked="checked" /> Perfil de N&oacute;mina
                    </td>
                	<td>
                    	<input type="checkbox" name="FlagCodTipoPago" id="FlagCodTipoPago" value="S" /> Tipo de Pago
                    </td>
                	<td>
                    	<input type="checkbox" name="FlagCodCentroCosto" id="FlagCodCentroCosto" value="S" /> Centro de Costo
                    </td>
                	<td>
                    	<input type="checkbox" name="FlagSueldoAnual" id="FlagSueldoAnual" value="S" /> Sueldo Anual
                    </td>
                </tr>
                <tr>
                	<td>
                    	<input type="checkbox" name="FlagCodDependencia" id="FlagCodDependencia" value="S" /> Dependencia
                    </td>
                	<td>
                    	<input type="checkbox" name="FlagSueldoBasico" id="FlagSueldoBasico" value="S" /> Sueldo B&aacute;sico
                    </td>
                	<td>
                    	<input type="checkbox" name="FlagCodEmpleado" id="FlagCodEmpleado" value="S" /> C&oacute;digo
                    </td>
                	<td>
                    	<input type="checkbox" name="FlagGrado" id="FlagGrado" value="S" /> Grado
                    </td>
                	<td>
                    	<input type="checkbox" name="FlagCuentaBancaria" id="FlagCuentaBancaria" value="S" /> Cta. Bancaria
                    </td>
                </tr>
                <tr>
                	<td>
                    	<input type="checkbox" name="FlagSueldoNormal" id="FlagSueldoNormal" value="S" /> Sueldo Normal
                    </td>
                </tr>
            </table>
		</td>
	</tr>
</table>
</div>
<center><input type="submit" value="Buscar"></center><br />
</form>

<center><iframe name="iReporte" id="iReporte" style="border:solid 1px #CDCDCD; width:<?=$_width?>px; height:400px;"></iframe></center>