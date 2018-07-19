<?php
// ------------------------------------------------ ####
include("../lib/fphp.php");
define('FPDF_FONTPATH','font/');
require('fpdf.php');
require('fphp.php');
connect(); 
mysql_query("SET NAMES 'utf8'");
extract ($_POST);
extract ($_GET);
//global $Periodo;
//echo $_SESSION["MYSQL_BD"];
/// ----------------------------------------------------
//---------------------------------------------------
$filtro=strtr($filtro, "*", "'");
//global $filtro;
//$Periodo = $Periodo;
//$filtro=strtr($filtro, ";", "%");
//---------------------------------------------------
//---------------------------------------------------
//echo $Periodo;
class PDF extends FPDF
{
//Page header
function Header(){
    
	global $Periodo, $filtro;
	$this->Image('../imagenes/logos/logo.jpg', 10, 10, 10, 10);	
	$this->SetFont('Arial', 'B', 9);
	$this->SetXY(20, 10); $this->Cell(70, 5,utf8_decode('República Bolivariana de Venezuela'), 0, 1, 'L');
	$this->SetXY(20, 14); $this->Cell(70, 5,utf8_decode($_SESSION["NOMBRE_ORGANISMO_ACTUAL"]), 0, 1, 'L'); 
	$this->Ln(4);
	
	$this->SetXY(220, 10);$this->Cell(11,5,'Fecha: ',0,0,'L');$this->Cell(10,5,date('d/m/Y'),0,1,'');
	$this->SetXY(220, 15);$this->Cell(11,5,utf8_decode('Página:'),0,1,'');
	//$this->SetXY(183, 20);$this->Cell(7,5,utf8_decode('Año:'),0,0,'');$this->Cell(6,5,date('Y'),0,1,'L');
						   
	list($fano, $fmes) = SPLIT('[-]',$Periodo);
    switch ($fmes) {
		case "01": $mes = Enero; break;  
		case "02": $mes = Febrero;break; 
		case "03": $mes = Marzo;break;   
		case "04": $mes = Abril;break;   
		case "05": $mes = Mayo;break;    
		case "06": $mes = Junio;break;
		case "07": $mes = Julio; break;
		case "08": $mes = Agosto; break;
		case "09": $mes = Septiembre; break;
		case "10": $mes = Octubre; break;
		case "11": $mes = Noviembre; break;
		case "12": $mes = Diciembre; break;
    }
	//echo $fmes;					   
	/*$this->SetFont('Arial', 'B', 10);
	$this->Cell(105, 10, '', 0, 0, 'C');
	$this->Cell(47, 10, utf8_decode('Ejecución Presupuestaria'), 0, 0, 'C');
    $this->Cell(13, 10, $mes, 0, 0, 'C'); $this->Cell(13, 10, $fano, 0, 1, 'C');*/
	///// PRUEBA ***********
	$this->SetFont('Arial', 'B', 8);
	
	/*$sql = "select a.* from af_activo a where CodOrganismo<>'' $filtro"; //echo $sql;
	$qry = mysql_query($sql) or die ($sql.mysql_error());
	$field = mysql_fetch_array($qry); */
	
	$scon01 = "select 
					 CodDependencia, Dependencia, CodPersona 
				from 
				     mastdependencias a 
			   where 
			   	     CodDependencia='".$field['CodDependencia']."'";
	$qcon01 = mysql_query($scon01) or die ($scon01.mysql_error());
	$fcon01 = mysql_fetch_array($qcon01);
	
	$scon02 = "select 
					 a.*,
					 b.DescripCargo,
					 c.NomCompleto,
					 c.CodPersona 
				 from 
				     rh_empleadonivelacion a 
					 inner join rh_puestos b on (b.CodCargo=a.CodCargo) 
					 inner join mastpersonas c on (c.CodPersona=a.CodPersona)
				where 
				     a.Secuencia=(select max(Secuencia) from rh_empleadonivelacion where CodPersona='".$fcon01['CodPersona']."') and 
					 a.CodPersona = '".$fcon01['CodPersona']."'";
	 $qcon02 = mysql_query($scon02) or die ($scon02.mysql_error());
	 $fcon02 = mysql_fetch_array($qcon02);
	
	 $cod_personaDependencia=$fcon02['CodPersona']; //echo $cod_personaDependencia;
	
	
	
	/*$this->SetFont('Arial', '', 8);
	$this->SetXY(10,22);$this->Cell(19, 3, 'Dependencia:', 0, 0, 'L');$this->Cell(3, 3, $fcon01['Dependencia'], 0,1, 'L');
	$this->SetXY(10,25);$this->Cell(19, 3, 'Responsable:', 0, 0, 'L');$this->Cell(3, 3, $fcon02['NomCompleto'], 0, 1, 'L');
	$this->SetXY(10,28);$this->Cell(19, 3, 'Cargo:', 0, 0, 'L');$this->Cell(3, 3, $fcon02['DescripCargo'], 0, 1, 'L');*/
	 
	$this->SetFont('Arial', 'B', 9);
	$this->Cell(240, 3, utf8_decode('Activos Asignados (Pendientes por Activar)'), 0, 1, 'C');$this->Ln(4);
	
	$this->SetFont('Arial', '', 8);
	  $this->Cell(130,4,'__________________________________________________________________________________________________________________________________________________________________________________',0,1,'L');
	/*$this->SetDrawColor(0, 0, 0); $this->SetFillColor(200, 200, 200); $this->SetTextColor(0, 0, 0);
	$this->SetFont('Arial', 'B', 8);
	$this->Cell(17, 4, 'Voucher', 0, 0, 'C', 0);
	$this->Cell(17, 4, '#Documento', 0, 0, 'C',0);
	//$this->Cell(20, 4, utf8_decode('Fecha Adquisición'), 0, 0, 'C', 0);
	$this->MultiCell(20,4,utf8_decode('Fecha Adquisición'),0,'C',0);
	$this->MultiCell(17, 4, 'Activo', 0, 0, 'C', 0);
	$this->Cell(20, 4, 'Cód. Interno', 0, 0, 'C', 0);
	$this->Cell(17, 4, utf8_decode('Descripción'), 0, 0, 'C', 0);
	$this->Cell(90, 4, 'Nuevo', 0, 0, 'C', 0);
	$this->Cell(50, 4, 'Anterior', 0, 1, 'L', 0);*/
	
	$this->SetDrawColor(255, 255, 255);
	//$this->SetDrawColor(0, 0, 0);
	$this->SetFillColor(255, 255, 255);
	$this->SetWidths(array(20,20,20,20,20,40,20,20,20,20,20,20,20));
	$this->SetAligns(array('C','C','C','C','C','C','C','C','C','C','C','C','C'));
	$this->Row(array('Voucher', '#Documento', utf8_decode('Fecha Adquisición'), 'Activo', utf8_decode('Cód. Interno'), utf8_decode('Descripción'), 'Orden Compra', 'Serie', utf8_decode('Ubicación Física'), 'Monto Local', utf8_decode('Período Ingreso'), utf8_decode('Período Deprec.'), utf8_decode('Período Inflación')));
	//$this->SetFillColor(255, 255, 255);
	
	$this->SetFont('Arial', '', 8);
	  $this->Cell(130,2,'__________________________________________________________________________________________________________________________________________________________________________________',0,1,'L');
	///// ******************	
}
//Page footer
function Footer(){
    //Position at 1.5 cm from bottom
    $this->SetXY(187,13);
    //Arial italic 8
    $this->SetFont('Arial','B',8);
    //Page number
    $this->Cell(0,9,' '.$this->PageNo().'/{nb}',0,0,'C');
}
}
//Instanciation of inherited class
$pdf=new PDF('L','mm','A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);

//// -----------------------------------------------------------------------------------------
if($_POST['forganismo']!="") $filtro.= " AND (CodOrganismo = '".$forganismo."')"; 
if($_POST['fDependencia']!="") $filtro.="AND (CodDependencia='".$fDependencia."')";
if($_POST['fNaturaleza']!="") $filtro.="and(Naturaleza='".$fNaturaleza."')"; 
/*if(($_POST['fFechaAprobacionDesde']!="")and($_POST['fFechaAprobacionHasta']!="")) 
   $filtro.=" and FechaRevisado>='".$fFechaAprobacionDesde."' and FechaRevisado<='".$fFechaAprobacionHasta."'";*/
if(($_POST['fFechaPreparacionDesde']!="")and($_POST['fFechaPreparacionHasta']!="")){
   
   list($fdd, $fdm, $fda) = split('[-]', $_POST['fFechaPreparacionDesde']);
   $fdesde = $fda.'-'.$fdm.'-'.$fdd; 
   
   list($fhd, $fhm, $fha) = split('[-]', $_POST['fFechaPreparacionHasta']);
   $fhasta = $fha.'-'.$fhm.'-'.$fhd;
   
   //echo $fdesde.'////'.$fhasta;
   
   $filtro.=" and FechaIngreso>='".$fdesde."' and FechaIngreso<='".$fhasta."'"; 
} 
if(($_POST['fFechaFacturaDesde']!="")and($_POST['fFechaFacturaHasta']!="")){
   
   list($fdd, $fdm, $fda) = split('[-]', $_POST['fFechaFacturaDesde']);
   $facturadesde = $fda.'-'.$fdm.'-'.$fdd; 
   
   list($fhd, $fhm, $fha) = split('[-]', $_POST['fFechaFacturaHasta']);
   $facturahasta = $fha.'-'.$fhm.'-'.$fhd;
   
   //echo $fdesde.'////'.$fhasta;
   
   $filtro.=" and FacturaFecha>='".$facturadesde."' and FacturaFecha<='".$facturahasta."'"; 
} 


//// -----------------------------------------------------------------------------------------


$s_con_01 = "select 
					*
			   from 
			    	af_activo
			   where 
			        Estado='PE' and CodOrganismo<>'' and Categoria<>'' $filtro 
			  order by 
			        Categoria"; //echo $s_con_01;
$q_con_01 = mysql_query($s_con_01) or die ($s_con_01.mysql_error());
$r_con_01 = mysql_num_rows($q_con_01); //echo $r_con_01;

if($r_con_01!=0)
   for($i=0; $i<$r_con_01; $i++){
      $f_con_01 = mysql_fetch_array($q_con_01);
	  
	  $s_categoria = "select * from af_categoriadeprec where CodCategoria='".$f_con_01['Categoria']."'"; //echo $s_categoria;
	  $q_categoria = mysql_query($s_categoria) or die ($s_categoria.mysql_error());
	  $r_categoria = mysql_num_rows($q_categoria);
	  
	  if($r_categoria!=0)$f_categoria=mysql_fetch_array($q_categoria);
	  
	  if($f_categoria['CodCategoria']!=$categoria_capturada){
		 
		 $cantidad_categorias += 1; /// Contador de activos por categoria
		 $cantidad_activos += 1; /// Contador de activos general
		 $contador_principal+= 1;
		 $valor+=1;
		 if($valor>=2){
		  $pdf->SetFont('Arial','B','7');$pdf->Cell(195,4,utf8_decode('Total Categoría:'),0,0,'R');
							             $pdf->Cell(5,4,$cont_activos_x_categoria,0,0,'L');
							             $pdf->Cell(8,4,'- - - - - - - - - - - - - ',0,1,'L'); 
						       
		  $pdf->SetFont('Arial','B','7');$pdf->Cell(195,4,'Total:',0,0,'R');
		  								 $pdf->Cell(30,4,number_format($monto_x_partidas,2,',','.'),0,1,'C');					   
							   $pdf->Ln();
		  $monto_x_partidas = 0;
		  $cont_activos_x_categoria = 0;
		 }
		 if($f_con_01['FacturaTipoDocumento']!="")$obligacion='-';
		 else $obligacion="";
		 if($f_con_01['FacturaFecha']!='0000-00-00'){
		   list($f_ano, $f_mes, $f_dia) = split('[-]', $f_con_01['FacturaFecha']); $fecha_factura = $f_dia.'-'.$f_mes.'-'.$f_ano;
		 }else $fecha_factura ="";
		 $pdf->SetFont('Arial', 'B', 7);$pdf->Cell(20,4,utf8_decode('Categoría:'), 0,0,'C');
		 $pdf->SetFont('Arial', '', 7); $pdf->Cell(70,4,utf8_decode($f_categoria['DescripcionLocal']), 0,0,'L');
		 $pdf->SetFont('Arial', 'B', 7);$pdf->Cell(10,4,'Cuenta:', 0,0,'L');
		 $pdf->SetFont('Arial', '', 7); $pdf->Cell(30,4,$f_categoria['CodCategoria'], 0,1,'L');
										
		 $pdf->SetFont('Arial', '', 7); $pdf->Cell(20,4,$f_con_01['VoucherIngreso'], 0,0,'C');
		 								$pdf->Cell(20,4,$f_con_01['FacturaTipoDocumento'].$obligacion.$f_con_01['FacturaNumeroDocumento'], 0,0,'C');
										$pdf->Cell(20,4,$fecha_factura, 0,0,'C');
										$pdf->Cell(20,4,$f_con_01['Activo'], 0,0,'C');
										$pdf->Cell(20,4,$f_con_01['CodigoInterno'], 0,0,'C');
										$pdf->Cell(40,4,substr(utf8_decode($f_con_01['Descripcion']),0,25), 0,0,'L');
										$pdf->Cell(20,4,$f_con_01['NumeroOrden'], 0,0,'C');
										$pdf->Cell(20,4,$f_con_01['NumeroSerie'], 0,0,'C');
										$pdf->Cell(20,4,$f_con_01['Ubicacion'], 0,0,'C');
										$pdf->Cell(20,4,number_format($f_con_01['MontoLocal'],2,',','.'), 0,0,'R');
										$pdf->Cell(20,4,$f_con_01['PeriodoIngreso'], 0,0,'C');
										$pdf->Cell(20,4,$f_con_01['PeriodoInicioDepreciacion'], 0,0,'C');
										$pdf->Cell(20,4,$f_con_01['PeriodoInicioRevaluacion'], 0,1,'C'); 
		$categoria_capturada=$f_con_01['Categoria'];
		$valor+=1;
		$cont_activos_x_categoria += 1;
		$monto_x_partidas += $f_con_01['MontoLocal']; /// Sumador de Monto Activo
		$Total_General += $f_con_01['MontoLocal'];
	  }else{
		$contador_principal+= 1;  
		$monto_x_partidas += $f_con_01['MontoLocal'];
		$Total_General += $f_con_01['MontoLocal'];
		$cont_activos_x_categoria += 1;
		$valor=1;
		$cantidad_activos += 1;  
		if($f_con_01['FacturaTipoDocumento']!="")$obligacion='-';
		 else $obligacion="";
		if($f_con_01['FacturaFecha']!='0000-00-00'){
		   list($f_ano, $f_mes, $f_dia) = split('[-]', $f_con_01['FacturaFecha']); $fecha_factura = $f_dia.'-'.$f_mes.'-'.$f_ano;
		 }else $fecha_factura ='';
		
	    $pdf->SetFont('Arial', '', 7);  $pdf->Cell(20,4,$f_con_01['VoucherIngreso'], 0,0,'C');
		 								$pdf->Cell(20,4,$f_con_01['FacturaTipoDocumento'].$obligacion.$f_con_01['FacturaNumeroDocumento'], 0,0,'C');
										$pdf->Cell(20,4,$fecha_factura, 0,0,'C');
										$pdf->Cell(20,4,$f_con_01['Activo'], 0,0,'C');
										$pdf->Cell(20,4,$f_con_01['CodigoInterno'], 0,0,'C');
										$pdf->Cell(40,4,substr(utf8_decode($f_con_01['Descripcion']),0,25), 0,0,'L');
										$pdf->Cell(20,4,$f_con_01['NumeroOrden'], 0,0,'C');
										$pdf->Cell(20,4,$f_con_01['NumeroSerie'], 0,0,'C');
										$pdf->Cell(20,4,$f_con_01['Ubicacion'], 0,0,'C');
										$pdf->Cell(20,4,number_format($f_con_01['MontoLocal'],2,',','.'), 0,0,'R');
										$pdf->Cell(20,4,$f_con_01['PeriodoIngreso'], 0,0,'C');
										$pdf->Cell(20,4,$f_con_01['PeriodoInicioDepreciacion'], 0,0,'C');
										$pdf->Cell(20,4,$f_con_01['PeriodoInicioRevaluacion'], 0,1,'C'); 
	  }
	  
	  ///echo $contador_principal;
	   if($contador_principal==$r_con_01){
		  $pdf->SetFont('Arial','B','7');$pdf->Cell(195,4,utf8_decode('Total Categoría:'),0,0,'R');
							             $pdf->Cell(5,4,$cont_activos_x_categoria,0,0,'L');
							             $pdf->Cell(8,4,'- - - - - - - - - - - - - ',0,1,'L'); 
						       
		  $pdf->SetFont('Arial','B','7');$pdf->Cell(195,4,'Total:',0,0,'R');
		  								 $pdf->Cell(30,4,number_format($monto_x_partidas,2,',','.'),0,1,'C');					   
							   $pdf->Ln();
		}
	  
	  
	  
	  /// Consultas ///
	  $s_con_02 = "select 
	  					  a.Descripcion as DescpActual,
						  b.Descripcion as DescpAnterior 
					 from 
					      af_ubicaciones a,
						  af_ubicaciones b 
					where 
					      a.CodUbicacion='".$f_con_01['Ubicacion']."' and 
						  b.CodUbicacion='".$f_con_01['UbicacionAnterior']."'";
	  $q_con_02 = mysql_query($s_con_02) or die ($s_con_02.mysql_error());
	  $r_con_02 = mysql_num_rows($q_con_02);
	  if($r_con_02!=0) $f_con_02 = mysql_fetch_array($q_con_02);
	  
	  if($f_con_01['InternoExternoFlag']=='I') $cod_maestro= 'MMOVINTER';
	  else $cod_maestro= 'MMOVINTER';
	  
	   $s_con_03 = "select Descripcion from mastmiscelaneosdet where CodMaestro='$cod_maestro'";
	   $q_con_03 = mysql_query($s_con_03) or die ($s_con_03.mysql_query());
	   $r_con_03 = mysql_num_rows($q_con_03); 
       if($r_con_03!=0)$f_con_03=mysql_fetch_array($q_con_03);	  
	 
	   $s_con_04 = "select 
	                      a.NomCompleto as NombPreparado,
						  b.NomCompleto as NombAprobado,
						  c.NomCompleto as NombEmpleadoUsuario,
						  d.NomCompleto as NombEmpleadoUsuarioAnterior 
				      from 
					       mastpersonas a,
						   mastpersonas b,
						   mastpersonas c,
						   mastpersonas d 
					  where 
					       a.CodPersona='".$f_con_01['PreparadoPor']."' and 
						   b.CodPersona='".$f_con_01['AprobadoPor']."' and 
						   c.CodPersona='".$f_con_01['EmpleadoUsuario']."' and 
						   d.CodPersona='".$f_con_01['EmpleadoUsuarioAnterior']."'";
		$q_con_04 = mysql_query($s_con_04) or die ($s_con_04.mysql_error());
		$r_con_04 = mysql_num_rows($q_con_04);
		if($r_con_04!=0)$f_con_04=mysql_fetch_array($q_con_04);
		
	   list($fano, $fmes, $fdia) = split('[-]', $f_con_01['FechaPreparacion']);
	   list($fa, $fm, $fd )= split('[-]', $f_con_01['FechaAprobacion']);
	   $FechaPreparacion = $fdia.'-'.$fmes.'-'.$fano;
	   $FechaAprobacion = $fd.'-'.$fm.'-'.$fa;
	 
	   $s_con_05 = "select 
	                      a.Descripcion as DescpCentroCostoActual,
						  b.Descripcion as DescpCentroCostoAnterior 
				      from 
					       ac_mastcentrocosto a,
						   ac_mastcentrocosto b 
					  where 
					       a.CodCentroCosto='".$f_con_01['CentroCosto']."' and 
						   b.CodCentroCosto='".$f_con_01['CentroCostoAnterior']."'";
		$q_con_05 = mysql_query($s_con_05) or die ($s_con_05.mysql_error());
		$r_con_05 = mysql_num_rows($q_con_05);
		if($r_con_05!=0) $f_con_05=mysql_fetch_array($q_con_05);
	   
	   $s_con_06 = "select 
	                      a.Dependencia as DependenciaActual,
						  b.Dependencia as DependenciaAnterior,
						  c.Organismo as OrganismoActual,
						  d.Organismo as OrganismoAnterior 
				      from 
					       mastdependencias a,
						   mastdependencias b,
						   mastorganismos c,
						   mastorganismos d 
					  where 
					       a.CodDependencia='".$f_con_01['Dependencia']."' and 
						   b.CodDependencia='".$f_con_01['DependenciaAnterior']."' and 
						   c.CodOrganismo='".$f_con_01['OrganismoActual']."' and
						   d.CodOrganismo='".$f_con_01['Organismoanterior']."'";
		$q_con_06 = mysql_query($s_con_06) or die ($s_con_06.mysql_error());
		$r_con_06 = mysql_num_rows($q_con_06);
		if($r_con_06!=0) $f_con_06=mysql_fetch_array($q_con_06);
	   
	 
	 $DescpActual = myTruncate($f_con_02['DescpActual'], '56', '', '...');
	 $DescpAnterior = myTruncate($f_con_02['DescpAnterior'], '56', '', '...');
	 $DescpCentroCostoActual = myTruncate($f_con_05['DescpCentroCostoActual'], '60', '', '...');
	 $DescpCentroCostoAnterior = myTruncate($f_con_05['DescpCentroCostoAnterior'], '60', '', '...');
	 /*$DependenciaActual = myTruncate($f_con_06['DependenciaActual'], '50', '', '...');
	 $DependenciaAnterior = myTruncate($f_con_06['DependenciaAnterior'], '50', '', '...');*/
	 
	 
	  
   }


//// Muestra de totalizadores
//$pdf->Ln();
$pdf->SetFont('Arial','B','7');$pdf->Cell(195,1,'',0,0,'R');
							   $pdf->Cell(5,1,'',0,0,'L');
							   $pdf->Cell(10,1,'- - - - - - - - - - - - -',0,1,'L');$pdf->Ln();
$pdf->SetFont('Arial','B','7');$pdf->Cell(195,4,'TOTAL GENERAL:',0,0,'R');
							   $pdf->Cell(12,4,'',0,0,'L');
							   $pdf->Cell(10,4, number_format($Total_General,2,',','.'),0,1,'L');
							   
$pdf->SetFont('Arial','B','7'); $pdf->Cell(40,4,'Nro. de Activos para el Organismo:',0,0,'L');
								$pdf->Cell(10,4,$cantidad_activos,0,1,'C');
$pdf->SetFont('Arial','B','7'); $pdf->Cell(25,4,'Total General Activos:',0,0,'L');
								$pdf->Cell(10,4,$cantidad_activos,0,1,'C');


	
   list($nombreCompleto, $cargo, $nivel) = getFirmaxDependencia($_PARAMETRO['FIRMAINVENTARIODEP']);
	 
   list($nombreCompleto02, $cargo02, $nivel02) = getFirmaxDependencia($CodDependencia);
   /*
    $pdf->Cell(20,10,'Total de Bienes: '.$ractivo,0,1,'L');
$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(202, 202, 202); $pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->Cell(80,5,'_____________________________',0,0,'C');$pdf->Cell(100,5,'RECIBI CONFORME: _____________________________',0,1,'C');
	$pdf->Cell(80,2,$nivel.' '.$nombreCompleto,0,0,'C');    $pdf->Cell(127,2,$nivel02.' '.$nombreCompleto02,0,1,'C');
	$pdf->Cell(80,3,$cargo,0,0,'C');
	$pdf->Cell(25,3,'',0,0,'C');                             $pdf->MultiCell(80,3,$cargo02,0,'C');*/
$pdf->Output();
?>  