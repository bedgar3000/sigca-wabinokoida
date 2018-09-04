<?php
##	consulto datos generales
$sql = "SELECT
			ppm.CodParametro,
			ppm.CodRecurso,
			ppm.CodTipoProceso,
			ppm.Estado,
			ppr.Ejercicio,
			ppr.Numero,
			ppr.CodTipoNom
		FROM
			pr_proyparametro ppm
			INNER JOIN pr_proyrecursos ppr ON (ppr.CodRecurso = ppm.CodRecurso)
		WHERE ppm.CodParametro = '$sel_registros'";
$field = getRecord($sql);
//	------------------------------------
$clkCancelar = "document.getElementById('frmentrada').submit();";
//	------------------------------------
$_titulo = "Planificaci&oacute;n de Par&aacute;metros / Nuevo Registro";
$_width = 800;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=pr_proyparametro_lista" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('pr_proyparametro_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
	<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
	<input type="hidden" name="fCodTipoProceso" id="fCodTipoProceso" value="<?=$fCodTipoProceso?>" />
	<input type="hidden" name="fEjercicio" id="fEjercicio" value="<?=$fEjercicio?>" />
	<input type="hidden" name="CodParametro" id="CodParametro" value="<?=$field['CodParametro']?>" />
	
	<table width="<?=$_width?>" class="tblForm">
		<tr>
	    	<td colspan="2" class="divFormCaption">Datos Generales</td>
	    </tr>
	    <tr>
			<td class="tagForm" width="125">* Recurso:</td>
			<td class="gallery clearfix">
				<input type="hidden" name="CodRecurso" id="CodRecurso" value="<?=$field['CodRecurso']?>" />
				<input type="text" name="Ejercicio" id="Ejercicio" value="<?=$field['Ejercicio']?>" style="width:37px;" readonly>
				<input type="text" name="Numero" id="Numero" value="<?=$field['Numero']?>" style="width:19px;" readonly>
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* N&oacute;mina:</td>
			<td>
				<select name="CodTipoNom" id="CodTipoNom" style="width:265px;" disabled>
					<option value="">&nbsp;</option>
					<?=loadSelect2('tiponomina','CodTipoNom','Nomina',$field['CodTipoNom'],0)?>
				</select>
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Proceso:</td>
			<td>
				<select name="CodTipoProceso" id="CodTipoProceso" style="width:265px;" disabled>
					<option value="">&nbsp;</option>
					<?=loadSelect2('pr_tipoproceso','CodTipoProceso','Descripcion',$field['CodTipoProceso'],0)?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="tagForm">Estado:</td>
			<td>
	            <input type="radio" name="Estado" id="Activo" value="A" <?=chkOpt($field['Estado'], "A");?> disabled /> Activo
	            &nbsp; &nbsp;
	            <input type="radio" name="Estado" id="Inactivo" value="I" <?=chkOpt($field['Estado'], "I");?> disabled /> Inactivo
			</td>
		</tr>
	    <tr>
			<td class="tagForm">&Uacute;ltima Modif.:</td>
			<td>
				<input type="text" value="<?=$field['UltimoUsuario']?>" style="width:150px;" disabled="disabled" />
				<input type="text" value="<?=$field['UltimaFecha']?>" style="width:110px" disabled="disabled" />
			</td>
		</tr>
	</table>

	<center>
		<input type="submit" value="Guardar" style="width:75px; <?=$display_submit?>" id="btSubmit" />
		<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
	</center>

	<input type="hidden" id="sel_conceptos" />
	<table width="<?=$_width?>;" class="tblBotones">
		<thead>
			<tr>
				<th class="divFormCaption">PAR&Aacute;METROS DE C&Aacute;LCULO</th>
			</tr>
		</thead>
        <tbody>
            <tr>
                <td align="right" class="gallery clearfix">
                    <input type="button" style="width:100px;" value="Obtener Valores" onclick="calcular();" <?=$disabled_ver?> />
                </td>
            </tr>
        </tbody>
	</table>
	<div style="overflow:scroll; height:400px; width:<?=$_width?>px; margin:auto;">
		<?php
		$sql = "SELECT
					ppmd.CodConcepto,
					c.Abreviatura AS Concepto
				FROM
					pr_proyparametrodet ppmd
					INNER JOIN pr_concepto c On (c.CodConcepto = ppmd.CodConcepto)
				WHERE
					ppmd.CodParametro = '$field[CodParametro]' AND
					ppmd.FlagParametrizable = 'S'
				ORDER BY CodConcepto";
		$field_conceptos = getRecords($sql);
		$min_width = 1750 + (count($field_conceptos) * 75);
		?>
		<table class="tblLista" style="width:100%; min-width:<?=$min_width?>px;">
			<thead>
			    <tr>
			    	<th width="30">#</th>
			        <th width="60">C&oacute;digo</th>
			        <th align="left">Empleado</th>
			        <th width="60" align="right">Documento</th>
			        <th align="left">Dependencia</th>
			        <th align="left">Cargo</th>
			        <th width="75">Categoria</th>
			        <th width="30">Grado</th>
			        <th width="30">Paso</th>
			        <?php
					foreach ($field_conceptos as $f) {
						?><th width='75'><?=$f['Concepto']?></th><?php
					}
					?>
			    </tr>
		    </thead>
	    
		    <tbody id="lista_conceptos">
			<?php
			$nro_conceptos = 0;
			//	consulto lista
			$sql = "SELECT
						pyrd.CodPersona,
						pyrd.CodRecurso,
						pyrd.Secuencia,
						pyr.CodOrganismo,
						p.NomCompleto,
						p.Ndocumento,
						e.CodEmpleado,
						d.CodDependencia,
						d.Dependencia,
						pt.DescripCargo,
						pyrd.Grado,
						pyrd.Paso,
						md.Descripcion AS NomCategoriaCargo
					FROM
						pr_proyrecursosdet pyrd
						INNER JOIN pr_proyrecursos pyr ON (pyr.CodRecurso = pyrd.CodRecurso)
						INNER JOIN pr_proyparametro ppm ON (ppm.CodRecurso = pyr.CodRecurso)
						INNER JOIN mastdependencias d ON (d.CodDependencia = pyrd.CodDependencia)
						INNER JOIN rh_puestos pt ON (pt.CodCargo = pyrd.CodCargo)
						LEFT JOIN mastmiscelaneosdet md ON (md.CodDetalle = pyrd.CategoriaCargo AND md.CodMaestro = 'CATCARGO')
						LEFT JOIN mastpersonas p ON (p.CodPersona = pyrd.CodPersona)
						LEFT JOIN mastempleado e ON (e.CodPersona = p.CodPersona AND e.CodTipoNom = pyr.CodTipoNom)
					WHERE ppm.CodParametro = '$field[CodParametro]'
					ORDER BY CodDependencia, LENGTH(Ndocumento), Ndocumento";
			$field_empleados = getRecords($sql);
			foreach($field_empleados as $f) {
				$id = ++$nro_conceptos;
				?>
				<tr class="trListaBody" onclick="clk($(this), 'conceptos', '<?=$id?>');">
					<th><?=$nro_conceptos?></th>
					<td align="center">
						<input type="hidden" name="conceptos_CodRecurso[]" value="<?=$f['CodRecurso']?>">
						<input type="hidden" name="conceptos_Secuencia[]" value="<?=$f['Secuencia']?>">
						<?=$f['CodPersona']?>
					</td>
					<td><?=htmlentities($f['NomCompleto'])?></td>
					<td align="right"><?=number_format($f['Ndocumento'],0,'','.')?></td>
					<td><?=htmlentities($f['Dependencia'])?></td>
					<td><?=htmlentities($f['DescripCargo'])?></td>
					<td><?=htmlentities($f['NomCategoriaCargo'])?></td>
					<td align="center"><?=$f['Grado']?></td>
					<td align="center"><?=$f['Paso']?></td>
			        <?php
					$sql = "SELECT
								ppmd.CodConcepto,
								ppmd.Formula,
								c.Abreviatura AS Concepto,
								prp.Valor
							FROM
								pr_proyparametrodet ppmd
								INNER JOIN pr_concepto c On (c.CodConcepto = ppmd.CodConcepto)
								LEFT JOIN pr_proyrecursosparametros prp ON (prp.CodParametro = ppmd.CodParametro AND prp.CodConcepto = ppmd.CodConcepto AND prp.CodRecurso = '$f[CodRecurso]' AND prp.Secuencia = '$f[Secuencia]')
							WHERE
								ppmd.CodParametro = '$field[CodParametro]' AND
								ppmd.FlagParametrizable = 'S'
							ORDER BY CodConcepto";
					$field_conceptos = getRecords($sql);
					foreach ($field_conceptos as $fc) {
						//	mt_rand(0, 6);
						?><td align="center"><input type="text" name="conceptos_<?=$fc['CodConcepto']?>[]" value="<?=$fc['Valor']?>" class="cell" style="text-align:center;"></td><?php
					}
					?>
				</tr>
				<?php
			}
			?>
		    </tbody>
		</table>
	</div>
	<input type="hidden" id="nro_conceptos" value="<?=$nro_conceptos?>" />
	<input type="hidden" id="can_conceptos" value="<?=$nro_conceptos?>" />
</form>
<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript">
	function calcular() {
		$(".div-progressbar").css("display", "block");
		$.ajax({
			type: "POST",
			url: "pr_proyparametro_ajax.php",
			data: "modulo=ajax&accion=calcular&CodParametro="+$('#CodParametro').val(),
			async: false,
			success: function(data) {
				$('#lista_conceptos').html(data);
				$(".div-progressbar").css("display", "none");
				inicializar();
			}
		});
	}
</script>