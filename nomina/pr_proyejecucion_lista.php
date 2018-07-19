<?php
if ($filtrar == "default") {
    $PeriodoDesde = "$AnioActual-01";
    $PeriodoHasta = "$AnioActual-12";
}
//	------------------------------------
$sql = "SELECT * FROM pr_proyparametro WHERE CodParametro = '$CodParametro'";
$field_parametro = getRecord($sql);
//  -
$sql = "SELECT * FROM pr_proyrecursos WHERE CodRecurso = '$CodRecurso'";
$field_recurso = getRecord($sql);
//	------------------------------------
$_titulo = "Proyección de Gastos";
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=pr_proyejecucion_lista" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />

<!--FILTRO-->
<div class="divBorder" style="width:<?=$_width?>px;; min-width:<?=$_width?>px;">
	<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
		<tr>
			<td align="right">Par&aacute;metro:</td>
			<td class="gallery clearfix">
                <input type="checkbox" checked onclick="this.checked=!this.checked" />
                <input type="hidden" name="CodParametro" id="CodParametro" value="<?=$CodParametro?>" />
                <input type="hidden" name="CodRecurso" id="CodRecurso" value="<?=$CodRecurso?>" />
                <input type="text" name="Ejercicio" id="Ejercicio" value="<?=$field_recurso['Ejercicio']?>" style="width:37px;" disabled>
                <input type="text" name="Numero" id="Numero" value="<?=$field_recurso['Numero']?>" style="width:19px;" disabled>
				<a href="../lib/listas/gehen.php?anz=lista_pr_proyparametro&filtrar=default&ventana=&campo1=CodParametro&campo2=CodRecurso&campo3=Ejercicio&campo4=Numero&campo5=CodTipoNom&campo6=CodTipoProceso&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" id="aCodParametro">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
            <td class="tagForm">N&oacute;mina:</td>
            <td>
                <input type="checkbox" checked onclick="this.checked=!this.checked" />
                <select name="CodTipoNom" id="CodTipoNom" style="width:265px;" disabled>
                    <option value="">&nbsp;</option>
                    <?=loadSelect2('tiponomina','CodTipoNom','Nomina',$field_recurso['CodTipoNom'],0)?>
                </select>
            </td>
            <td>&nbsp;</td>
        </tr>
        <tr>
			<td align="right">Periodo: </td>
			<td>
                <input type="checkbox" checked onclick="this.checked=!this.checked" />
				<input type="text" name="PeriodoDesde" id="PeriodoDesde" value="<?=$PeriodoDesde?>" style="width:60px;" maxlength="7" /> -
				<input type="text" name="PeriodoHasta" id="PeriodoHasta" value="<?=$PeriodoHasta?>" style="width:60px;" maxlength="7" />
			</td>
            <td class="tagForm">Proceso:</td>
            <td>
                <input type="checkbox" checked onclick="this.checked=!this.checked" />
                <select name="CodTipoProceso" id="CodTipoProceso" style="width:265px;" disabled>
                    <option value="">&nbsp;</option>
                    <?=loadSelect2('pr_tipoproceso','CodTipoProceso','Descripcion',$field_parametro['CodTipoProceso'],0)?>
                </select>
            </td>
	        <td align="right"><input type="submit" value="Buscar"></td>
		</tr>
	</table>
</div>
<div class="sep"></div>

<!--REGISTROS-->

<table align="center" cellpadding="0" cellspacing="0" width="<?=$_width?>">
	<tr>
    	<td>
        	<form name="frm_disponibles" id="frm_disponibles">
            <table width="430" class="tblBotones">
                <tr>
                    <td align="right" style="height:25px;"></td>
                </tr>
            </table>
            <div style="overflow:scroll; width:430px; height:350px;">
            <table width="1600" class="tblLista">
                <thead>
                    <tr>
                        <th width="65">Nro. Documento</th>
                        <th align="left">Empleado</th>
                        <th width="550" align="left">Cargo</th>
                        <th width="550" align="left">Dependencia</th>
                        <th width="65">Estado</th>
                    </tr>
                </thead>
                
                <tbody id="lista_disponibles">
                <?php
                $sql = "SELECT
                            pyrd.*,
                            p.NomCompleto,
                            p.Ndocumento,
                            e.CodEmpleado,
                            e.SueldoActual,
                            e.Estado,
                            d.Dependencia,
                            pt.DescripCargo
                        FROM
                            pr_proyrecursosdet pyrd
                            INNER JOIN pr_proyrecursos pyr ON (pyr.CodRecurso = pyrd.CodRecurso)
                            INNER JOIN pr_proyparametro ppm ON (ppm.CodRecurso = pyr.CodRecurso)
                            INNER JOIN rh_puestos pt ON (pt.CodCargo = pyrd.CodCargo)
                            INNER JOIN mastdependencias d On (d.CodDependencia = pyrd.CodDependencia)
                            LEFT JOIN mastempleado e ON (e.CodPersona = pyrd.CodPersona)
                            LEFT JOIN mastpersonas p ON (p.CodPersona = e.CodPersona)
                        WHERE
                            pyrd.CodRecurso = '$CodRecurso' AND
                            ppm.CodParametro = '$CodParametro' AND
                            pyrd.Secuencia NOT IN (SELECT pyrd.Secuencia
                                                   FROM
                                                        pr_proyejecucion pe
                                                        INNER JOIN pr_proyrecursosdet pyrd ON (pyrd.CodRecurso = pe.CodRecurso AND pyrd.Secuencia = pe.Secuencia)
                                                        INNER JOIN rh_puestos pt ON (pt.CodCargo = pyrd.CodCargo)
                                                        INNER JOIN mastdependencias d On (d.CodDependencia = pyrd.CodDependencia)
                                                   WHERE
                                                        pe.CodRecurso = '$CodRecurso' AND
                                                        pe.CodParametro = '$CodParametro')
                        ORDER BY CodDependencia, LENGTH(Ndocumento), Ndocumento";
                $field_disponibles = getRecords($sql);
                $rows_disponibles = count($field_disponibles);  $i=0;
                foreach ($field_disponibles as $f) {
                    $id = "$f[Secuencia]";
                    $tr = "tr_$f[Secuencia]";
                    ?>
                    <tr class="trListaBody" onclick="clkMulti($(this), '<?=$id?>');" id="<?=$tr?>">
                        <td align="right">
                            <input type="checkbox" name="personas[]" id="<?=$id?>" value="<?=$id?>" style="display:none;" />
                            <?=number_format($f['Ndocumento'], 0, '', '.')?>
                        </td>
                        <td><?=htmlentities($f['NomCompleto'])?></td>
                        <td><?=htmlentities($f['Dependencia'])?></td>
                        <td><?=htmlentities($f['DescripCargo'])?></td>
                        <td align="center"><?=printValoresGeneral("ESTADO", $f['Estado'])?></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
            </div>
            </form>
        </td>
        
        <td width="100" valign="middle" align="center">
        	<input type="button" value="&gt;" style="width:30px; cursor:pointer;" onclick="lista_agregar('>');" />
            <br />
            <br />
        	<input type="button" value="&lt;" style="width:30px; cursor:pointer;" onclick="lista_quitar('<');" />
            <br />
            <br />
            <br />
            <br />
            <br />
            <br />
        	<input type="button" value="&gt;&gt;" style="width:30px; cursor:pointer;" onclick="lista_agregar('>>');" />
            <br />
            <br />
        	<input type="button" value="&lt;&lt;" style="width:30px; cursor:pointer;" onclick="lista_quitar('<<');" />
        </td>
        
        <td>
        	<form name="frm_aprobados" id="frm_aprobados">
            <table width="430" class="tblBotones">
                <tr>
                    <td class="gallery clearfix">
                        <a href="pr_proyejecucion_pdf.php?CodParametro=<?=$CodParametro?>&CodRecurso=<?=$CodRecurso?>&PeriodoDesde=<?=$PeriodoDesde?>&PeriodoHasta=<?=$PeriodoHasta?>&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe2]" id="aImprimir" style="display:none;"></a>
                        <input type="button" value="Imprimir" style="width:75px;" onclick="$('#aImprimir').click();" />
                    </td>

                    <td align="right">
                        <input type="button" value="Generar" style="width:75px;" onclick="calcular();" />
                    </td>
                </tr>
            </table>
            <div style="overflow:scroll; width:430px; height:350px;">
            <table width="1600" class="tblLista">
                <thead>
                    <tr>
                        <th width="65">Nro. Documento</th>
                        <th align="left">Empleado</th>
                        <th width="550" align="left">Dependencia</th>
                        <th width="550" align="left">Cargo</th>
                        <th width="65">Estado</th>
                    </tr>
                </thead>
                
                <tbody id="lista_aprobados">
                <?php
                $sql = "SELECT
                            pyrd.*,
                            p.NomCompleto,
                            p.Ndocumento,
                            e.CodEmpleado,
                            e.SueldoActual,
                            e.Estado,
                            d.Dependencia,
                            pt.DescripCargo
                        FROM
                            pr_proyejecucion pe
                            INNER JOIN pr_proyrecursosdet pyrd ON (pyrd.CodRecurso = pe.CodRecurso AND pyrd.Secuencia = pe.Secuencia)
                            INNER JOIN rh_puestos pt ON (pt.CodCargo = pyrd.CodCargo)
                            INNER JOIN mastdependencias d On (d.CodDependencia = pyrd.CodDependencia)
                            LEFT JOIN mastempleado e ON (e.CodPersona = pyrd.CodPersona)
                            LEFT JOIN mastpersonas p ON (p.CodPersona = e.CodPersona)
                        WHERE
                            pe.CodRecurso = '$CodRecurso' AND
                            pe.CodParametro = '$CodParametro'
                        GROUP BY CodParametro, CodRecurso, Secuencia
                        ORDER BY CodDependencia, LENGTH(Ndocumento), Ndocumento";
                $field_aprobados = getRecords($sql);
                $rows_aprobados = count($field_aprobados);  $i=0;
                foreach ($field_aprobados as $f) {
                    $id = "$f[Secuencia]";
                    $tr = "tr_$f[Secuencia]";
                    ?>
                    <tr class="trListaBody" onclick="clkMulti($(this), '<?=$id?>');" id="<?=$tr?>">
                        <td align="right">
                            <input type="checkbox" name="personas[]" id="<?=$id?>" value="<?=$id?>" style="display:none;" />
                            <?=number_format($f['Ndocumento'], 0, '', '.')?>
                        </td>
                        <td><?=htmlentities($f['NomCompleto'])?></td>
                        <td><?=htmlentities($f['Dependencia'])?></td>
                        <td><?=htmlentities($f['DescripCargo'])?></td>
                        <td align="center"><?=printValoresGeneral("ESTADO", $f['Estado'])?></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
            </div>
            </form>
        </td>
    </tr>
    <tr>
        <td style="padding:5px;">
            <a class="link" href="#" onclick="selTodos2('disponibles', 'personas');">Todos</a> |
            <a class="link" href="#" onclick="selNinguno2('disponibles', 'personas');">Ninguno</a>
            &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
            &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
            Nro. Disponibles: &nbsp; <span style="font-weight:bold;" id="rows_disponibles"><?=$rows_disponibles?></span>
        </td>
        <td>&nbsp;</td>
        <td style="padding:5px;">
            <a class="link" href="#" onclick="selTodos2('aprobados', 'personas');">Todos</a> |
            <a class="link" href="#" onclick="selNinguno2('aprobados', 'personas');">Ninguno</a>
            &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
            &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
            Nro. Aprobados: &nbsp; <span style="font-weight:bold;" id="rows_aprobados"><?=$rows_aprobados?></span>
        </td>
    </tr>
</table>
</form>

<script type="text/javascript">
    function lista_agregar(boton) {
        if (boton == ">") {
            if ($("#lista_disponibles .trListaBodySel").length > 0) {
                $("#lista_disponibles .trListaBodySel").clone(true).appendTo("#lista_aprobados");
                $("#lista_disponibles .trListaBodySel").remove();
            } else cajaModal("Debe seleccionar un Empleado", "error", 400);
        }
        else if (boton == ">>") {
            if ($("#lista_disponibles tr").length > 0) {
                $("#lista_disponibles tr").clone(true).appendTo("#lista_aprobados");
                $("#lista_disponibles tr").remove();
            } else cajaModal("Lista vacia", "error", 400);
        }
        $("#rows_disponibles").html($("#lista_disponibles tr").length);
        $("#rows_aprobados").html($("#lista_aprobados tr").length);
    }
    function lista_quitar(boton) {
        bloqueo(true);
        if ($("#lista_aprobados .trListaBodySel").length == 0 && boton == '<') cajaModal("Debe seleccionar un Empleado", "error", 400);
        else if ($("#lista_aprobados tr").length == 0 && boton == '<<') cajaModal("Lista vacia", "error", 400);
        else {
            //  ajax
            $.ajax({
                type: "POST",
                url: "pr_proyejecucion_ajax.php",
                data: "modulo=formulario&accion=quitar&"+$('form').serialize()+"&boton="+boton,
                async: false,
                success: function(resp) {
                    bloqueo(false);
                    if (resp.trim() != "") {
                        cajaModal(resp, "error", 400);
                    } else {
                        if (boton == "<") {
                            $("#lista_aprobados .trListaBodySel").clone(true).appendTo("#lista_disponibles");
                            $("#lista_aprobados .trListaBodySel").remove();
                        }
                        else if (boton == "<<") {
                            $("#lista_aprobados tr").clone(true).appendTo("#lista_disponibles");
                            $("#lista_aprobados tr").remove();
                        }
                        $("#rows_disponibles").html($("#lista_disponibles tr").length);
                        $("#rows_aprobados").html($("#lista_aprobados tr").length);
                    }
                }
            });
        }
    }
    function calcular() {
        bloqueo(1);
        $.ajax({
            type: "POST",
            url: "pr_proyejecucion_ajax.php",
            data: "modulo=formulario&accion=calcular&"+$('form').serialize(),
            async: false,
            success: function(resp) {
                if (resp.trim() != "") cajaModal(resp, 'error');
                else document.getElementById($('form').attr('id')).submit();
            }
        });
    }
</script>