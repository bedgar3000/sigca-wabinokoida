<?php
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$fEstado = "P";
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "Postulante";
}
$filtro = '';
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (p.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (p.Postulante LIKE '%".$fBuscar."%' OR
					  p.Expediente LIKE '%".$fBuscar."%' OR
					  p.Nombres LIKE '%".$fBuscar."%' OR
					  p.Apellido1 LIKE '%".$fBuscar."%' OR
					  p.Apellido2 LIKE '%".$fBuscar."%' OR
					  CONCAT(p.Nombres, ' ', p.Apellido1, ' ', p.Apellido2) LIKE '%".$fBuscar."%' OR
					  p.Ndocumento LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fSexo != "") { $cSexo = "checked"; $filtro.=" AND (p.Sexo = '".$fSexo."')"; } else $dSexo = "disabled";
if ($fEdadD != "" || $fEdadH != "") {
	$cEdad = "checked";
	if ($fEdadD != "") $filtro .= " AND antiguedad_anios(p.Fnacimiento, CURRENT_DATE()) >= $fEdadD";
	if ($fEdadH != "") $filtro .= " AND antiguedad_anios(p.Fnacimiento, CURRENT_DATE()) <= $fEdadH";
} else $dEdad = "disabled";
if ($fCodCargo != "") { $cCodCargo = "checked"; $filtro.=" AND (pc.CodCargo = '".$fCodCargo."')"; } else $dCodCargo = "visibility:hidden;";
if ($fCodGradoInstruccion != "") { $cCodGradoInstruccion = "checked"; $filtro.=" AND (pi.CodGradoInstruccion = '".$fCodGradoInstruccion."')"; } else $dCodGradoInstruccion = "disabled";
if ($fArea != "") { $cArea = "checked"; $filtro.=" AND (pi.Area = '".$fArea."')"; } else $dArea = "disabled";
if ($fCodProfesion != "") { $cCodProfesion = "checked"; $filtro.=" AND (pi.CodProfesion = '".$fCodProfesion."')"; } else $dCodProfesion = "disabled";
if ($fCodCentroEstudio != "") { $cCodCentroEstudio = "checked"; $filtro.=" AND (pi.CodCentroEstudio = '".$fCodCentroEstudio."')"; } else $dCodCentroEstudio = "visibility:hidden;";
if ($fCodCurso != "") { $cCodCurso = "checked"; $filtro.=" AND (pcs.CodCurso = '".$fCodCurso."')"; } else $dCodCurso = "visibility:hidden;";
if ($fCodIdioma != "") { $cCodIdioma = "checked"; $filtro.=" AND (pid.CodIdioma = '".$fCodIdioma."')"; } else $dCodIdioma = "disabled";
//	------------------------------------
$_titulo = "Registro de Postulantes";
$_width = 800;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=rh_postulantes_lista" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />

<!--FILTRO-->
<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
	<tr>
		<td align="right">Cargo Aplicable: </td>
		<td class="gallery clearfix">
            <input type="checkbox" <?=$cCodCargo?> onclick="chkListado(this.checked, 'btCargo', 'fCodCargo', 'fDescripCargo');" />
            <input type="hidden" name="fCodCargo" id="fCodCargo" value="<?=$fCodCargo?>" />
			<input type="text" name="fDescripCargo" id="fDescripCargo" style="width:150px;" value="<?=$fDescripCargo?>" readonly />
            <a href="../lib/listas/gehen.php?anz=lista_cargos&filtrar=default&campo1=fCodCargo&campo2=fDescripCargo&iframe=true&width=100%&height=400" rel="prettyPhoto[iframe1]" id="btCargo" style=" <?=$dCodCargo?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
		<td align="right">Centro de Estudio: </td>
		<td class="gallery clearfix">
            <input type="checkbox" <?=$cCodCentroEstudio?> onclick="chkListado(this.checked, 'btCentro', 'fCodCentroEstudio', 'fNomCentroEstudio');" />
            <input type="hidden" name="fCodCentroEstudio" id="fCodCentroEstudio" value="<?=$fCodCentroEstudio?>" />
			<input type="text" name="fNomCentroEstudio" id="fNomCentroEstudio" style="width:150px;" value="<?=$fNomCentroEstudio?>" readonly />
            <a href="../lib/listas/gehen.php?anz=lista_centro_estudio&filtrar=default&campo1=fCodCentroEstudio&campo2=fNomCentroEstudio&iframe=true&width=100%&height=410" rel="prettyPhoto[iframe2]" id="btCentro" style=" <?=$dCodCentroEstudio?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
		<td align="right">Buscar:</td>
		<td>
			<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
			<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:150px;" <?=$dBuscar?> />
		</td>
        <td>&nbsp;</td>
	</tr>
	<tr>
		<td align="right">G. Instrucci&oacute;n:</td>
		<td>
			<input type="checkbox" <?=$cCodGradoInstruccion?> onclick="chkCampos(this.checked, 'fCodGradoInstruccion');" />
			<select name="fCodGradoInstruccion" id="fCodGradoInstruccion" style="width:156px;" onChange="getOptionsSelect2('profesiones', 'fCodProfesion', true, this.value, $('#fArea').val());" <?=$dCodGradoInstruccion?>>
            	<option value="">&nbsp;</option>
				<?=loadSelect("rh_gradoinstruccion", "CodGradoInstruccion", "Descripcion", $fCodGradoInstruccion, 0)?>
			</select>
		</td>
		<td align="right">Curso: </td>
		<td class="gallery clearfix">
            <input type="checkbox" <?=$cCodCurso?> onclick="chkListado(this.checked, 'btCurso', 'fCodCurso', 'fNomCurso');" />
            <input type="hidden" name="fCodCurso" id="fCodCurso" value="<?=$fCodCurso?>" />
			<input type="text" name="fNomCurso" id="fNomCurso" style="width:150px;" value="<?=$fNomCurso?>" readonly />
            <a href="../lib/listas/gehen.php?anz=lista_cursos&filtrar=default&cod=fCodCurso&nom=fNomCurso&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe3]" id="btCurso" style=" <?=$dCodCurso?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
		<td align="right">Sexo: </td>
		<td>
            <input type="checkbox" <?=$cSexo?> onclick="chkCampos(this.checked, 'fSexo');" />
            <select name="fSexo" id="fSexo" style="width:156px;" <?=$dSexo?>>
                <option value=""></option>
                <?=loadSelectGeneral("SEXO", $fSexo, 0)?>
            </select>
		</td>
        <td>&nbsp;</td>
	</tr>
	<tr>
		<td align="right">Area Profesional:</td>
		<td>
			<input type="checkbox" <?=$cArea?> onclick="chkCampos(this.checked, 'fArea');" />
			<select name="fArea" id="fArea" style="width:156px;" onChange="getOptionsSelect2('profesiones', 'fCodProfesion', true, $('#fCodGradoInstruccion').val(), this.value);" <?=$dArea?>>
            	<option value="">&nbsp;</option>
				<?=getMiscelaneos($fArea, "AREA", 0)?>
			</select>
		</td>
		<td align="right">Idioma:</td>
		<td>
			<input type="checkbox" <?=$cCodIdioma?> onclick="chkCampos(this.checked, 'fCodIdioma');" />
			<select name="fCodIdioma" id="fCodIdioma" style="width:156px;" <?=$dCodIdioma?>>
            	<option value="">&nbsp;</option>
				<?=loadSelect("mastidioma", "CodIdioma", "DescripcionLocal", $fCodIdioma, 0)?>
			</select>
		</td>
		<td align="right">Edad:</td>
		<td>
			<input type="checkbox" <?=$cEdad?> onclick="chkCampos(this.checked, 'fEdadD', 'fEdadH');" />
			<input type="text" name="fEdadD" id="fEdadD" value="<?=$fEdadD?>" style="width:25px;" <?=$dEdad?> /> -
			<input type="text" name="fEdadH" id="fEdadH" value="<?=$fEdadH?>" style="width:25px;" <?=$dEdad?> />
		</td>
        <td>&nbsp;</td>
	</tr>
	<tr>
		<td align="right">Profesi&oacute;n:</td>
		<td>
			<input type="checkbox" <?=$cCodProfesion?> onclick="chkCampos(this.checked, 'fCodProfesion');" />
			<select name="fCodProfesion" id="fCodProfesion" style="width:156px;" <?=$dCodProfesion?>>
            	<option value="">&nbsp;</option>
				<?=loadSelectDependiente2("rh_profesiones", "CodProfesion", "Descripcion", "CodGradoInstruccion", "Area", $fCodProfesion, $fCodGradoInstruccion, $fArea, 0)?>
			</select>
		</td>
		<td align="right">Estado: </td>
		<td>
            <input type="checkbox" <?=$cEstado?> onclick="chkCampos(this.checked, 'fEstado');" />
            <select name="fEstado" id="fEstado" style="width:156px;" <?=$dEstado?>>
                <option value=""></option>
                <?=loadSelectValores("ESTADO-POSTULANTE", $fEstado, 0)?>
            </select>
		</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td align="right"><input type="submit" value="Buscar"></td>
	</tr>
</table>
</div>
<div class="sep"></div>

<!--REGISTROS-->
<center>
<input type="hidden" name="sel_registros" id="sel_registros" />
<table class="tblBotones" style="width:100%; min-width:<?=$_width?>px;">
    <tr>
        <td><div id="rows"></div></td>
        <td align="right" class="gallery clearfix">
            <input type="button" value="Nuevo" style="width:75px;" class="insert" onclick="cargarPagina(this.form, 'gehen.php?anz=rh_postulantes_form&opcion=nuevo');" />
            <input type="button" value="Modificar" style="width:75px;" class="update" onclick="cargarOpcion2(this.form, 'gehen.php?anz=rh_postulantes_form&opcion=modificar', 'SELF', '', $('#sel_registros').val());" />
            <input type="button" value="Eliminar" style="width:75px;" class="delete" onclick="opcionRegistro3(this.form, $('#sel_registros').val(), 'formulario', 'eliminar', 'rh_postulantes_ajax.php');" />
            <input type="button" value="Ver" style="width:75px;" class="ver" onclick="cargarOpcion2(this.form, 'gehen.php?anz=rh_postulantes_form&opcion=ver', 'SELF', '', $('#sel_registros').val());" />
        </td>
    </tr>
</table>

<div style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:250px;">
<table class="tblLista" style="width:100%; min-width:<?=$_width?>px;">
	<thead>
    <tr>
		<th width="60" onclick="order('Postulante');">C&oacute;digo</th>
		<th width="75" onclick="order('Expediente');">Nro. Expediente</th>
		<th onclick="order('NomCompleto');" align="left">Nombre Completo</th>
		<th width="75" onclick="order('Ndocumento');">Nro. Documento</th>
		<th width="30" onclick="order('Sexo');">Sexo</th>
		<th width="30" onclick="order('Edad');">Edad</th>
		<th width="60" onclick="order('FechaRegistro');">Fecha de Registro</th>
		<th width="75" onclick="order('Estado');">Estado</th>
    </tr>
    </thead>
    
    <tbody id="lista_registros">
	<?php
	//	consulto todos
	$sql = "SELECT *
			FROM 
				rh_postulantes p
				LEFT JOIN rh_postulantes_instruccion pi ON (pi.Postulante = p.Postulante)
				LEFT JOIN rh_postulantes_cargos pc ON (pc.Postulante = p.Postulante)
				LEFT JOIN rh_postulantes_cursos pcs ON (pcs.Postulante = p.Postulante)
				LEFT JOIN rh_postulantes_idioma pid ON (pid.Postulante = p.Postulante)
			WHERE 1 $filtro
			GROUP BY p.Postulante";
	$rows_total = getNumRows3($sql);
	//	consulto lista
	$sql = "SELECT
				p.Postulante,
				CONCAT(p.Nombres, ' ', p.Apellido1, ' ', p.Apellido2) AS NomCompleto,
				p.Sexo,
				p.FechaRegistro,
				p.Expediente,
				p.Ndocumento,
				p.Estado,
				antiguedad_anios(p.Fnacimiento, CURRENT_DATE()) AS Edad
			FROM 
				rh_postulantes p
				LEFT JOIN rh_postulantes_instruccion pi ON (pi.Postulante = p.Postulante)
				LEFT JOIN rh_postulantes_cargos pc ON (pc.Postulante = p.Postulante)
				LEFT JOIN rh_postulantes_cursos pcs ON (pcs.Postulante = p.Postulante)
				LEFT JOIN rh_postulantes_idioma pid ON (pid.Postulante = p.Postulante)
			WHERE 1 $filtro
			GROUP BY p.Postulante
			ORDER BY $fOrderBy
			LIMIT ".intval($limit).", ".intval($maxlimit);
	$field = getRecords($sql);
	$rows_lista = count($field);
	foreach($field as $f) {
		$id = $f['Postulante'];
		?>
		<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
			<td align="center"><?=$f['Postulante']?></td>
			<td align="center"><?=$f['Expediente']?></td>
			<td><?=$f['NomCompleto']?></td>
			<td><?=$f['Ndocumento']?></td>
			<td align="center"><?=$f['Sexo']?></td>
			<td align="center"><?=$f['Edad']?></td>
			<td align="center"><?=formatFechaDMA(substr($f['FechaRegistro'], 0, 10))?></td>
			<td align="center"><?=printValores("ESTADO-POSTULANTE", $f['Estado'])?></td>
		</tr>
		<?php
	}
	?>
    </tbody>
</table>
</div>

<table style="width:100%; min-width:<?=$_width?>px;">
	<tr>
    	<td>
        	Mostrar: 
            <select name="maxlimit" style="width:50px;" onchange="this.form.submit();">
                <?=loadSelectGeneral("MAXLIMIT", $maxlimit, 0)?>
            </select>
        </td>
        <td align="right">
        	<?=paginacion(intval($rows_total), intval($rows_lista), intval($maxlimit), intval($limit));?>
        </td>
    </tr>
</table>
</center>
</form>