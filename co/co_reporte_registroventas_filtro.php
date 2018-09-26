<?php
$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
$fPeriodo = $PeriodoActual;
//	------------------------------------
$_titulo = "Registro de Ventas";
$_width = 900;
?>
<div class="ui-layout-north">
	<div style="padding:5px;">
        <table width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td class="titulo"><?=$_titulo?></td>
                <td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
            </tr>
        </table><hr width="100%" color="#333333" />
        <form name="frmentrada" id="frmentrada" action="pv_reporte_resumen_sector_pdf.php" method="post" autocomplete="off" target="pdf" onsubmit="return validar();">
            <input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
            <input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
            
            <!--FILTRO-->
            <div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
                <table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
                    <tr>
                        <td align="right">Organismo:</td>
                        <td>
                            <input type="checkbox" checked onclick="this.checked=!this.checked;" />
                            <select name="fCodOrganismo" id="fCodOrganismo" style="width:225px;">
                                <?=getOrganismos($fCodOrganismo, 3);?>
                            </select>
                        </td>
                        <td align="right">Tipo Doc.:</td>
                        <td>
                            <input type="checkbox" onclick="chkFiltro(this.checked, 'fCodTipoDocumento');" />
                            <select name="fCodTipoDocumento" id="fCodTipoDocumento" style="width:150px;" disabled>
                                <option value="">&nbsp;</option>
                                <?=loadSelect2('co_tipodocumento','CodTipoDocumento','Descripcion',$fCodTipoDocumento)?>
                            </select>
                        </td>
                        <td align="right">Sistema Fuente:</td>
                        <td>
                            <input type="checkbox" onclick="chkFiltro(this.checked, 'fSistemaFuente');" />
                            <select name="fSistemaFuente" id="fSistemaFuente" style="width:150px;" disabled>
                                <option value="">&nbsp;</option>
                                <?=loadSelectValores("registro-ventas-sistema-fuente", $fSistemaFuente)?>
                            </select>
                        </td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="right">Cliente:</td>
                        <td class="gallery clearfix">
                            <input type="checkbox" onclick="ckLista(this.checked, ['fCodPersonaCliente','fNombreCliente','fDocFiscalCliente'], ['aCodPersonaCliente']);" />
                            <input type="hidden" name="fDocFiscalCliente" id="fDocFiscalCliente" value="<?=$fDocFiscalCliente?>">
                            <input type="hidden" name="fCodPersonaCliente" id="fCodPersonaCliente" value="<?=$fCodPersonaCliente?>" />
                            <input type="text" name="fNombreCliente" id="fNombreCliente" value="<?=htmlentities($fNombreCliente)?>" style="width:225px;" readonly />
                            <a href="../lib/listas/gehen.php?anz=lista_personas&campo1=fCodPersonaCliente&campo2=fNombreCliente&campo3=fDocFiscalCliente&ventana=&filtrar=default&FlagClasePersona=S&fEsCliente=S&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" style="visibility:hidden;" id="aCodPersonaCliente">
                                <img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
                            </a>
                        </td>
                        <td align="right">Buscar:</td>
                        <td>
                            <input type="checkbox" onclick="chkCampos(this.checked, 'fBuscar');" />
                            <input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:150px;" disabled />
                        </td>
                        <td align="right">Periodo:</td>
                        <td>
                            <input type="checkbox" checked onclick="this.checked=!this.checked;" />
                            <input type="text" name="fPeriodo" id="fPeriodo" value="<?=$fPeriodo?>" style="width:75px;" />
                        </td>
                        <td align="right"><input type="submit" value="Buscar"></td>
                    </tr>
                </table>
            </div>
            <table style="width:100%; min-width:<?=$_width?>px;">
                <tr>
                    <td>
                        <div class="header">
                            <ul id="tab">
                                <!-- CSS Tabs -->
                                <li id="li1" onclick="currentTab('tab', this);" class="current">
                                    <a href="#" onclick="cargarPagina(document.getElementById('frmentrada'), 'co_reporte_registroventas_detallado_pdf.php'); mostrarTab('tab', 1, 3);">
                                        DETALLADO
                                    </a>
                                </li>
                                <li id="li2" onclick="currentTab('tab', this);">
                                    <a href="#" onclick="cargarPagina(document.getElementById('frmentrada'), 'co_reporte_registroventas_documento_pdf.php'); mostrarTab('tab', 2, 3);">
                                        POR DOCUMENTO
                                    </a>
                                </li>
                                <li id="li3" onclick="currentTab('tab', this);">
                                    <a href="#" onclick="cargarPagina(document.getElementById('frmentrada'), 'co_reporte_registroventas_sumarizado_pdf.php'); mostrarTab('tab', 3, 3);">
                                        SUMARIZADO X TIPO
                                    </a>
                                </li>
                            </ul>
                        </div>
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
    function validar() {
        if ($('#fCodOrganismo').val() == '') {cajaModal('Debe seleccionar el Organismo','error'); return false;}
        else if ($('#fPeriodo').val() == '') {cajaModal('Debe ingresar el Periodo','error'); return false;}
        else return true;
    }
</script>