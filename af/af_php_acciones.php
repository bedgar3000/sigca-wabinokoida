<? 
// ----------------------------------------------------------------------- ####
include("../lib/fphp.php");  
include("fphp.php");
//// --------------------------------------------------------------------- ####
//// 						GUARDAR ACTIVO MENORES
if($accion=="GuardarActivosMenores"){
  connect();
   /// Consultar activo
   $scon = "select max(Activo) from af_activo where CodOrganismo = '".$CodOrganismo."'";
   $qcon = mysql_query($scon) or die ($scon.mysql_error());
   $fcon = mysql_fetch_array($qcon);
   
   $activo = (int) ($fcon[0]+1);
   $activo = (string) str_repeat("0",10-strlen($activo)).$activo; 
   
   if($_POST['FechaIngreso']!="00-00-0000")$FechaIngreso= date("Y-m-d", strtotime($_POST['FechaIngreso']));
   if($_POST['NumeroOrdenFecha']!="00-00-0000")$NumeroOrdenFecha= date("Y-m-d", strtotime($_POST['NumeroOrdenFecha']));
   if($_POST['NumeroGuiaFecha']!="00-00-0000")$NumeroGuiaFecha= date("Y-m-d", strtotime($_POST['NumeroGuiaFecha'])); 
   if($_POST['DocAlmacenFecha']!="00-00-0000")$DocAlmacenFecha= date("Y-m-d", strtotime($_POST['DocAlmacenFecha'])); 
   if($_POST['InventarioFisicoFecha']!="00-00-0000")$InventarioFisicoFecha= date("Y-m-d", strtotime($_POST['InventarioFisicoFecha'])); 
   
   if($_POST['FechaObligacion']!="00-00-0000")$FechaObligacion= date("Y-m-d", strtotime($_POST['FechaObligacion']));
   if($_POST['FacturaFecha']!="00-00-0000")$FacturaFecha= date("Y-m-d", strtotime($_POST['FacturaFecha']));
    
   $MontoCatastro = cambioFormato($_POST['MontoCatastro']);	
   $MontoLocal = cambioFormato($_POST['MontoLocal']);
   $MontoReferencia = cambioFormato($_POST['MontoReferencia']);
   $MontoMercado = cambioFormato($_POST['MontoMercado']);
   
   $s_prepor = "select CodPersona from usuarios where Usuario='".$_SESSION['USUARIO_ACTUAL']."'";
   $q_prepor = mysql_query($s_prepor) or die ($s_prepor.mysql_error());
   $r_prepor = mysql_num_rows($q_prepor);
   if($r_prepor!=0) $f_prepor = mysql_fetch_array($q_prepor);
   
   
    
   $s_insert ="insert into af_activo(Activo, CodOrganismo, CodDependencia, Descripcion, DescpCorta,
									 CodigoBarras, CodigoInterno, Clasificacion,  Ubicacion,
									 ActivoConsolidado, EmpleadoResponsable, CentroCosto, Marca,
									 Modelo, NumeroSerie, Dimensiones, Color, FabricacionPais,
									 FabricacionAno, CodProveedor, NumeroOrden, NumeroOrdenFecha, 
									 NumeroGuia, NumeroGuiaFecha, FechaIngreso, PeriodoIngreso, 
									 MontoLocal, UltimoUsuario, UltimaFechaModif, SituacionActivo, 
									 FlagParaOperaciones, Naturaleza, PreparadoPor, EstadoRegistro, 
									 ClasificacionPublic20, CodTipoMovimiento, OrigenActivo, Estado,
									 FechaPreparacion, Categoria, EmpleadoUsuario, FacturaTipoDocumento, 
									 FacturaNumeroDocumento, FacturaFecha, NroFactura, FechaObligacion)
                    values
                          ('$activo', '".$CodOrganismo."', '".$CodDependencia."', '".$Descripcion."', '".$DescpCorta."',
									'".$CodigoBarras."', '".$CodigoInterno."', '".$Clasificacion."', '".$Ubicacion."',
									'".$ActivoConsolidado."', '".$EmpleadoResponsable."', '".$CentroCosto."', '".$Marca."',
									'".$Modelo."', '".$NumeroSerie."', '".$Dimensiones."', '".$Color."', '".$FabricacionPais."',
									'".$FabricacionAno."', '".$CodProveedor."', '".$NumeroOrden."', '$NumeroOrdenFecha', 
									'".$NumeroGuia."', '$NumeroGuiaFecha', '$FechaIngreso', '".$PeriodoIngreso."', 
									'$MontoLocal', '".$_SESSION['USUARIO_ACTUAL']."', '".$Ahora."', '".$SituacionActivo."',
									'".$FlagParaOperaciones."', '".$Naturaleza."', '".$f_prepor['CodPersona']."', 'PE', 
									'".$ClasificacionPublic20."', '".$CodTipoMovimiento."', 'MA', 'PE',	
									'".date("Y-m-d")."', '".$Categoria."', '".$EmpleadoUsuario."', '".$ObligacionTipoDocumento."',
									'".$ObligacionNroDocumento."', '$FacturaFecha', '".$NumeroFactura."', '$FechaObligacion' )";
   $q_insert = mysql_query($s_insert) or die ($s_insert.mysql_error());
}
//// --------------------------------------------------------------------- ####
////						MODIFICAR ACTIVO MENORES
if($accion=="GuardarModificacionesActivosMenores"){
  connect();

   
   if($_POST['FechaIngreso']!="00-00-0000")$FechaIngreso = date("Y-m-d", strtotime($_POST['FechaIngreso']));
   if($_POST['NumeroOrdenFecha']!="00-00-0000")$NumeroOrdenFecha = date("Y-m-d", strtotime($_POST['NumeroOrdenFecha']));
   if($_POST['NumeroGuiaFecha']!="00-00-0000")$NumeroGuiaFecha = date("Y-m-d", strtotime($_POST['NumeroGuiaFecha'])); 
   if($_POST['DocAlmacenFecha']!="00-00-0000")$DocAlmacenFecha = date("Y-m-d", strtotime($_POST['DocAlmacenFecha'])); 
   if($_POST['InventarioFisicoFecha']!="00-00-0000")$InventarioFisicoFecha = date("Y-m-d", strtotime($_POST['InventarioFisicoFecha'])); 

   if($_POST['FechaObligacion']!="00-00-0000")$FechaObligacion= date("Y-m-d", strtotime($_POST['FechaObligacion']));
   if($_POST['FacturaFecha']!="00-00-0000")$FacturaFecha= date("Y-m-d", strtotime($_POST['FacturaFecha']));

    
   $MontoCatastro = cambioFormato($_POST['MontoCatastro']);	
   $MontoLocal = cambioFormato($_POST['MontoLocal']);
   $MontoReferencia = cambioFormato($_POST['MontoReferencia']);
   $MontoMercado = cambioFormato($_POST['MontoMercado']); 
    
   $s_update = "update 
                      af_activo 
				   set
						CodDependencia='".$CodDependencia."', Descripcion='".$Descripcion."', DescpCorta='".$DescpCorta."', 
						CodigoBarras='".$CodigoBarras."', CodigoInterno='".$CodigoInterno."', Clasificacion='".$Clasificacion."',
						Ubicacion='".$Ubicacion."', ActivoConsolidado='".$ActivoConsolidado."', EmpleadoResponsable='".$EmpleadoResponsable."',
						CentroCosto='".$CentroCosto."', Marca='".$Marca."', Modelo='".$Modelo."', NumeroSerie='".$NumeroSerie."', Dimensiones='".$Dimensiones."',
						Color='".$Color."',	FabricacionPais='".$FabricacionPais."', FabricacionAno='".$FabricacionAno."', CodProveedor='".$CodProveedor."',
						FacturaTipoDocumento='".$ObligacionTipoDocumento."', NroFactura='".$NumeroFactura."', FacturaFecha='$FacturaFecha',
						NumeroOrden='".$NumeroOrden."', NumeroOrdenFecha='".$NumeroOrdenFecha."', NumeroGuia='".$NumeroGuia."', NumeroGuiaFecha='$NumeroGuiaFecha',
						MontoLocal='$MontoLocal', UltimoUsuario='".$_SESSION['USUARIO_ACTUAL']."', UltimaFechaModif='".$Ahora."',
						SituacionActivo='".$SituacionActivo."', FlagParaOperaciones='".$FlagParaOperaciones."', EmpleadoUsuario='".$EmpleadoUsuario."',
                  FacturaNumeroDocumento='".$ObligacionNroDocumento."', FechaObligacion='$FechaObligacion' 
				where  
				      Activo='".$Activo."' and 
						CodOrganismo = '".$CodOrganismo."'";
   $q_update = mysql_query($s_update) or die ($s_update.mysql_error());
}
?>