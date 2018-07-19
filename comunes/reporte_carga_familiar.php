<?php
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$fCodDependencia = $_SESSION["DEPENDENCIA_ACTUAL"];
	$fEdoReg = "A";
	$fSitTra = "A";
}
//	------------------------------------
$_titulo = "Carga Familiar";
$_sufijo = "reporte_carga_familiar";
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="reporte_carga_familiar_pdf.php" method="post" autocomplete="off" target="iReporte">
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
            <select name="fEdoReg" id="fEdoReg" style="width:125px;">
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
            <select name="fSitTra" id="fSitTra" style="width:125px;">
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
		<td align="right">Sexo:</td>
		<td>
			<input type="checkbox" onclick="chkFiltro(this.checked, 'fSexo');" />
			<select name="fSexo" id="fSexo" style="width:125px;" disabled>
            	<option value="">&nbsp;</option>
				<?=loadSelectGeneral("SEXO", $fSexo, 0)?>
			</select>
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
		<td align="right">Buscar:</td>
		<td>
			<input type="checkbox" onclick="chkFiltro(this.checked, 'fBuscar');" />
			<input type="text" name="fBuscar" id="fBuscar" style="width:200px;" disabled />
		</td>
	</tr>
    <tr>
    	<td colspan="4">
        	<hr />
        </td>
    </tr>
	<tr>
		<td align="right">Edad (Familiar): </td>
		<td>
			<input type="checkbox" onclick="chkFiltro_2(this.checked, 'fEdadD', 'fEdadH');" />
			<input type="text" name="fEdadD" id="fEdadD" maxlength="3" style="width:30px;" disabled /> -
            <input type="text" name="fEdadH" id="fEdadH" maxlength="3" style="width:30px;" disabled />
        </td>
		<td align="right">Sexo (Familiar):</td>
		<td>
			<input type="checkbox" onclick="chkFiltro(this.checked, 'fSexoFam');" />
			<select name="fSexoFam" id="fSexoFam" style="width:125px;" disabled>
            	<option value="">&nbsp;</option>
				<?=loadSelectGeneral("SEXO", $fSexoFam, 0)?>
			</select>
		</td>
	</tr>
	<tr>
		<td align="right">Parentesco: </td>
		<td>
			<input type="checkbox" onclick="chkFiltro(this.checked, 'fParentesco');" />
			<select name="fParentesco" id="fParentesco" style="width:125px;" disabled>
            	<option value="">&nbsp;</option>
				<?=getMiscelaneos($fParentesco, "PARENT", 0)?>
			</select>
		</td>
		<td align="right">&nbsp;</td>
		<td>
			<input type="checkbox" name="fAfiliado" id="fAfiliado" value="S" <?=$cAfiliado?> />
            Afiliado al Seguro M&eacute;dico
		</td>
	</tr>
	<tr>
		<td align="right">Edo. Civil (Familiar): </td>
		<td>
			<input type="checkbox" onclick="chkFiltro(this.checked, 'fEstadoCivil');" />
			<select name="fEstadoCivil" id="fEstadoCivil" style="width:125px;" disabled>
            	<option value="">&nbsp;</option>
				<?=getMiscelaneos($fEstadoCivil, "EDOCIVIL", 0)?>
			</select>
		</td>
		<td align="right">&nbsp;</td>
		<td>
			<input type="checkbox" name="fFlagDiscapacidad" id="fFlagDiscapacidad" value="S" <?=$cFlagDiscapacidad?> />
            Familiar con Discapacidad o Especial
		</td>
	</tr>
	<tr>
		<td align="right">&nbsp;</td>
		<td align="right">&nbsp;</td>
		<td align="right">&nbsp;</td>
		<td>
			<input type="checkbox" name="fFlagEstudia" id="fFlagEstudia" value="S" <?=$cFlagEstudia?> />
            Estudia?
		</td>
	</tr>
</table>
</div>
<center><input type="submit" value="Buscar"></center><br />
</form>

<center><iframe name="iReporte" id="iReporte" style="border:solid 1px #CDCDCD; width:<?=$_width?>px; height:400px;"></iframe></center>