<?php
// ------------------------------------- ####
include("../lib/fphp.php");
define('FPDF_FONTPATH','font/');
require('fpdf.php');
require('fphp.php');
connect();


class PDF extends FPDF{

	function Header(){
		
		global $NroActa;
	    global $CodOrganismo;
	    global $Anio;
		//echo $NroActa."-".$CodOrganismo."-".$Anio;
		//// Consulta para obtener el último nro de acta registrada
		$sql = "select 
					  a.*,
					  b.*
				  from 
				       af_actabajaactivo a
					   inner join af_activo b on (b.Activo = b.Activo)
				  where 
				       a.Anio='$Anio' and 
					   a.CodOrganismo='$CodOrganismo' and 
					   a.NroActa = '$NroActa'"; //echo $sql;
	    $qry = mysql_query($sql) or die ($sql.mysql_error());
		$row = mysql_num_rows($qry);
		
		if($row!=0) $field = mysql_fetch_array($qry);
		
		
		
	    if($Pase=="P"){
		    $sentrega = "select max(NroActa) from af_actaentregaactivo where CodOrganismo='$CodOrganismo'"; //echo $sentrega;
			$qentrega = mysql_query($sentrega) or die ($sentrega.mysql_error());
			$rentrega = mysql_num_rows($qentrega); if($rentrega!=0)$fentrega = mysql_fetch_array($qentrega);	
			
			$nroActaEntrega =  $fentrega['0'];
		}
				 
		/*			 
		$sql = "select 
					  b.FechaRevisadoPor,
					  b.InventarioFisicoFecha
				  from 
				     af_actaentregaactivo a 
					 inner join af_activo b on (b.Activo=a.Activo and b.CodOrganismo=a.CodOrganismo and a.NroActa=b.NroActaEntrega)
			    where 
					 a.CodOrganismo='".$CodOrganismo."' and 
					 a.NroActa='".$nroActaEntrega."'";		 
	    $qry = mysql_query($sql) or die ($sql.mysql_error());
	    $row = mysql_num_rows($qry);
		if($row !=0) $field = mysql_fetch_array($qry);*/
		//global $Periodo;
		global $fp_hasta,$fp_desde;
		//echo $Periodo.'/'.$fp_hasta.'****';
		
		list($sano, $smes, $sdia) = split('[-]', $field['FechaActa']);
		list($a, $m, $d) = split('[-]', $field['Fecha']); 
		
		$this->Image('../imagenes/logos/logo.jpg', 20, 10, 15, 15);	
		$this->SetFont('Arial', 'B', 8);
		$this->SetXY(35, 10); $this->Cell(100, 8,utf8_decode('República Bolivariana de Venezuela'), 0, 1, 'L');
		$this->SetXY(35, 14); $this->Cell(100, 8,utf8_decode($_SESSION["NOMBRE_ORGANISMO_ACTUAL"]), 0, 1, 'L');
		$this->SetXY(35, 18); $this->Cell(100, 8,utf8_decode('Dirección de Servicios Generales'), 0, 1, 'L');
							  
		//$this->SetXY(35, 10); $this->Cell(140, 8, 'Fecha:', 0, 0, 'R');$this->Cell(10, 8,date("d-m-Y"),0,1,'');
		$this->SetXY(35, 10); $this->Cell(140, 8, 'Fecha:', 0, 0, 'R');$this->Cell(10, 8, $sdia.'-'.$smes.'-'.$sano, 0, 1,'');
		$this->SetXY(20, 14); $this->Cell(155, 8, utf8_decode('Pág.:'), 0, 1, 'R'); /// NRO DE PÁGINA
		
		$this->SetXY(20, 18); $this->Cell(155, 8, utf8_decode('Nro.:'), 0, 0, 'R');/// NRO DE DOCUMENTO
							  $this->Cell(10, 8, $NroActa.'-'.$Anio, 0, 1, 'L');$this->Ln(5);
		
		$this->SetFont('Arial', 'B', 10);
		   $this->Cell(50, 5, '', 0, 0, 'C');
		   $this->Cell(100, 5, utf8_decode('ACTA DE DESINCORPORACION DE BIENES MUEBLES'), 0, 1, 'C');
		   $this->Ln(4);
	}
	//Page footer
	function Footer(){
	    //Position at 1.5 cm from bottom
	    $this->SetXY(152,14);
	    //Arial italic 8
	    $this->SetFont('Arial','I',8);
	    //Page number
	    $this->Cell(0,8,' '.$this->PageNo().'/{nb}',0,0,'C');
	}
}
//Instanciation of inherited class
$pdf=new PDF('P','mm','letter');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);

if($Pase=="P"){
	    $sentrega = "select max(NroActa) from af_actaentregaactivo where CodOrganismo='$CodOrganismo'"; //echo $sentrega;
		$qentrega = mysql_query($sentrega) or die ($sentrega.mysql_error());
		$rentrega = mysql_num_rows($qentrega); if($rentrega!=0)$fentrega = mysql_fetch_array($qentrega);	
		
		$nroActaEntrega =  $fentrega['0'];
}

// BUSCO EL O LOS ACTIVOS SEGUN EL NRO DE ACTA DE ENTREGA
$sa = "select 
              a.*,
			  b.* 
	     from 
		      af_actaentregaactivo a 
			  inner join af_activo b on (b.Activo=a.Activo and b.CodOrganismo=a.CodOrganismo and a.NroActa=b.NroActaEntrega)
		where 
		      a.CodOrganismo='".$CodOrganismo."' and 
			  a.NroActa='".$nroActaEntrega."' and 
			  a.Anio = '".$Anio."'"; 
$qa = mysql_query($sa) or die ($sa.mysql_error());
$ra = mysql_num_rows($qa);
if($ra!=0) $fa = mysql_fetch_array($qa);

// CONSULTA DE DATOS
$scon = "select 
				a.AprobadoPor,
				mp.NomCompleto as NomCompletoAprobadoPor,
				mp.NDocumento as NDocumentoAprobadoPor,
				a.ClasificacionPublic20,
				c.Descripcion as DescripClasificacion20,
				a.Activo,
				a.Descripcion as DescripActivo,
				a.Ubicacion,
				b.Descripcion as DescripUbicacion,
				a.MontoLocal,
				a.FechaRevisadoPor,
				a.UltimaFechaModif,
				mp2.NomCompleto as NomCompletoUsuario,
    			mp2.NDocumento as NDocumentoUsuario,
				mp5.NomCompleto as NomCompletoResponsable,
				a.RevisadoPor,
				a.CargoRevisadoPor,
				a.ConformadoPor,
				a.CargoConformadoPor,
				a.AprobadoPor,
				a.CargoAprobadoPor,
				a.EmpleadoUsuario,
				a.Marca,
				a.Modelo,
				mp3.NomCompleto as NomCompletoConformadoPor,
    			mp3.NDocumento as NDocumentoConformadoPor,
				mp4.NDocumento as NDocumentoRevisadoPor,
				a.CodigoInterno,
				a.EmpleadoResponsable
			from 
				af_activo a
				inner join mastpersonas mp on (mp.CodPersona=a.AprobadoPor)
				inner join mastpersonas mp2 on (mp2.CodPersona=a.EmpleadoUsuario)
				inner join mastpersonas mp3 on (mp3.CodPersona=a.ConformadoPor) 
				inner join mastpersonas mp4 on (mp4.CodPersona=a.RevisadoPor)
				inner join mastpersonas mp5 on (mp5.CodPersona=a.EmpleadoResponsable) 
				inner join af_ubicaciones b on (b.CodUbicacion=a.Ubicacion) 
				inner join af_clasificacionactivo20 c on (c.CodClasificacion=a.ClasificacionPublic20)
		    where 
			    a.Activo='".$fa['Activo']."' and 
				a.Estado='AP' and 
				a.CodOrganismo = '".$_GET['CodOrganismo']."'"; 
$qcon = mysql_query($scon) or die ($scon.mysql_error());
$rcon = mysql_num_rows($qcon);
if($rcon!=0)$fcon=mysql_fetch_array($qcon);

list($anos, $meses, $dias, $hora) = split('[-, ]', $fa['UltimaFechaModif']);
 //echo $anos, $meses, $dias, $hora; 
 list($h, $m, $s) = split('[:]', $hora);
 if($h>12)$t = "pm"; else $t = "am";
 if($h==13)$h="01";
 elseif($h==14)$h="02";
 elseif($h==15)$h="03";
 elseif($h==16)$h="04";
 elseif($h==17)$h="05";
 elseif($h==18)$h="06";
 elseif($h==19)$h="07";
 elseif($h==20)$h="08";
 elseif($h==21)$h="09";
 elseif($h==22)$h="10";
 elseif($h==23)$h="11";
 elseif($h==24)$h="12";
 
 $hora = $h.':'.$m.':'.$s;
 
 list($ano, $mes, $dia) = split('[-]', $fa['FechaRevisadoPor']);
 $fecha = $dia.'-'.$mes.'-'.$ano;  //echo $mes;
 
 switch($mes){
		case "01": $fmes= Enero;break;  
		case "02": $fmes= Febrero;break; 
		case "03": $fmes= Marzo;break;   
		case "04": $fmes= Abril;break;   
		case "05": $fmes= Mayo;break;    
		case "06": $fmes= Junio;break;
		case "07": $fmes= Julio; break;
		case "08": $fmes= Agosto; break;
		case "09": $fmes= Septiembre; break;
		case "10": $fmes= Octubre; break;
		case "11": $fmes= Noviembre; break;
		case "12": $fmes= Diciembre; break;
    }

$montoLocal = number_format($fcon['MontoLocal'],2,',','.');
  
/// Consulta realizada para obtener el cargo actual del empleado Usuario
$scon03 = "select 
				  a.CodPersona,
				  b.DescripCargo 
			 from 
				  rh_empleadonivelacion a 
				  inner join rh_puestos b on (a.CodCargo=b.CodCargo)
 			where 
				  a.Secuencia=(select max(Secuencia) from rh_empleadonivelacion where CodPersona='".$fcon['EmpleadoResponsable']."') and 
				  a.CodPersona='".$fcon['EmpleadoResponsable']."'"; //echo $scon03;
$qcon03 = mysql_query($scon03) or die ($scon03.mysql_error());
$fcon03 = mysql_fetch_array($qcon03);

/// Se obtienen la descripcion de cargos de los firmanmtes
$cargo[0]= $fcon['CargoRevisadoPor']; 
$cargo[1]= $fcon['CargoConformadoPor'];
$cargo[2]= $fcon['CargoAprobadoPor'];
$v_cargo = 3;

for($x=0; $x<$v_cargo; $x++){
 $scargos = "select DescripCargo from rh_puestos where CodCargo ='$cargo[$x]'"; //echo $scargos, $x;
 $qcargos = mysql_query($scargos) or die ($scargos.mysql_error());
 $fcargos = mysql_fetch_array($qcargos);

 if($x==0)$d_cargoRevisadoPor=$fcargos['DescripCargo'];
 if($x==1)$d_cargoConformadoPor=$fcargos['DescripCargo'];
 if($x==2)$d_cargoAprobadoPor=$fcargos['DescripCargo']; 
}

/// Se obtienen los nombres de los firmantes + número de cédula
$cedula[0]=$fcon['RevisadoPor'];
$cedula[1]=$fcon['ConformadoPor'];
$cedula[2]=$fcon['AprobadoPor'];
$v_cedula = 3;

for($y=0; $y<$v_cedula; $y++){
  $scn = "select NomCompleto,Ndocumento  from mastpersonas where CodPersona='$cedula[$y]'";
  $qcn = mysql_query($scn) or die ($scn.mysql_error());
  $fcn = mysql_fetch_array($qcn);
  
  if($y==0){$n_RevisadoPor=$fcn['NomCompleto']; $c_RevisadoPor=$fcn['Ndocumento'];}
  if($y==1){$n_ConformadoPor=$fcn['NomCompleto'];$c_ConformadoPor=$fcn['Ndocumento'];}
  if($y==2){$n_AprobadoPor=$fcn['NomCompleto']; $c_AprobadoPor=$fcn['Ndocumento'];}
}


///  Convertidos de datos
$n_ConformadoPor= ucwords(strtr(strtolower(utf8_encode($n_ConformadoPor)),"ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ", "àáâãäåæçèéêëìíîïðñòóôõöøùüú")); //Directora General
$cargo02= ucwords(strtr(strtolower(utf8_encode($cargo02)),"ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ", "àáâãäåæçèéêëìíîïðñòóôõöøùüú")); // Cargo Directora General

$n_AprobadoPor= ucwords(strtr(strtolower(utf8_encode($n_AprobadoPor)),"ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ", "àáâãäåæçèéêëìíîïðñòóôõöøùüú")); // Quien Aprueba
$cargo03= ucwords(strtr(strtolower(utf8_encode($cargo03)),"ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ", "àáâãäåæçèéêëìíîïðñòóôõöøùüú")); // Cargo Quien Aprueba

$empleado_responsable= ucwords(strtr(strtolower(utf8_encode($fcon['NomCompletoResponsable'])),"ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ", "àáâãäåæçèéêëìíîïðñòóôõöøùüú")); // Empleado Responsable
$empleado_responsable_cargo=  ucwords(strtr(strtolower(utf8_encode($fcon03['DescripCargo'])),"ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ", "àáâãäåæçèéêëìíîïðñòóôõöøùüú")); // Cargo Empleado Responsable


/// Obtengo datos del Contralor encargado y servicios generales
$codDep[0]='0001'; // Despacho del contralor
$codDep[1]='0012'; // Servicios Generales
$codDep[2]='0003'; // Dirección General
$v_dep = 3;


for($y=0; $y<$v_dep; $y++){
	$s_busq = "select 
	                 a.*,
					 b.NomCompleto,
					 b.Ndocumento 
			     from 
				     mastdependencias a 
					 inner join mastpersonas b on (b.CodPersona = a.CodPersona) 
			    where 
				     a.CodDependencia='$codDep[$y]' and 
					 a.CodOrganismo='$CodOrganismo'"; //echo $s_busq;
	$q_busq = mysql_query($s_busq) or die ($s_busq.mysql_error());
	$r_busq = mysql_num_rows($q_busq); if($r_busq != 0) $f_busq = mysql_fetch_array($q_busq);

  	if($y==0){$nomContralor= $f_busq['NomCompleto']; $ced_contralor= $f_busq['Ndocumento']; $cod_percontralor = $f_busq['CodPersona'];}
  	if($y==1){$nomServGener= $f_busq['NomCompleto']; $ced_servgener= $f_busq['Ndocumento']; $cod_perservgener = $f_busq['CodPersona'];}
  	if($y==2){$nomDireGener= $f_busq['NomCompleto']; $ced_diregener= $f_busq['Ndocumento']; $cod_perdiregener = $f_busq['CodPersona'];}
}


list($nombContralor, $cargoContralor, $nivelContralor) = getfirma($cod_percontralor);
$nomContralor = ucwords(strtr(strtolower(utf8_encode($nomContralor)),"ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ", "àáâãäåæçèéêëìíîïðñòóôõöøùüú")); //Contralor del Estado
$cargoContralor = ucwords(strtr(strtolower(utf8_encode($cargoContralor)),"ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ", "àáâãäåæçèéêëìíîïðñòóôõöøùüú")); //Cargo Contralor del Estado

list($nombServGener, $cargoServGener, $nivelServGener) = getfirma($cod_perservgener);
$nomServGener = ucwords(strtr(strtolower(utf8_encode($nomServGener)),"ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ", "àáâãäåæçèéêëìíîïðñòóôõöøùüú")); //Servicios Generales
$cargoServGener = ucwords(strtr(strtolower(utf8_encode($cargoServGener)),"ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ", "àáâãäåæçèéêëìíîïðñòóôõöøùüú")); //Cargo Servicios Generales

list($nombDireGener, $cargoDireGener, $nivelDireGener) = getfirma($cod_perdiregener);
$nomServGener = ucwords(strtr(strtolower(utf8_encode($nomServGener)),"ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ", "àáâãäåæçèéêëìíîïðñòóôõöøùüú")); //Dirección General
$cargoServGener = ucwords(strtr(strtolower(utf8_encode($cargoServGener)),"ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ", "àáâãäåæçèéêëìíîïðñòóôõöøùüú")); //Cargo Dirección General

/// Motivo Traslado
$s_mot = "select * from mastmiscelaneosdet where CodMaestro='MMOVEXTER'";
$q_mot = mysql_query($s_mot) or die ($s_mot.mysql_error());
$r_mot = mysql_num_rows($q_mot); if($r_mot != 0) $f_mot = mysql_fetch_array($q_mot);

$concepto = ucwords(strtr(strtolower(utf8_encode($f_mot['Descripcion'])),"ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ", "àáâãäåæçèéêëìíîïðñòóôõöøùüú"));

$parrafo1 = utf8_decode("El (la) Suscrito(a), ").utf8_decode($nivelContralor .' '. $nomContralor).utf8_decode(", titular de la Cedula de Identidad N° ").$ced_contralor.utf8_decode(", en carácter de ").utf8_decode($cargoContralor).utf8_decode(" y el (la) susctrito(a) ").utf8_decode($nivelServGener .' '. $nomServGener).utf8_decode(", titular de la Cedula de Identidad N° ").$ced_servgener.utf8_decode(" quien desempeña el cargo de: ").utf8_decode($cargoServGener).utf8_decode(" de la Contraloría del Estado Delta Amacuro, hace constar por medio de la presente, que el bien o bienes que a continuación se especifican han sido desincorporados del Inventario General de esta institución por el siguiente concepto").' '.utf8_decode($concepto).'.';


$pdf->SetFont('Arial', '', 12);
		$pdf->SetXY(20,43);
		$pdf->MultiCell(175, 6, $parrafo1, 0, 'J');
		$pdf->Ln(4);

$pdf->SetFont('Arial', '', 7);
	$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(200, 200, 200); $pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont('Arial', 'B', 6);
	$pdf->Cell(12,'','','');
	$pdf->Cell(20, 3, 'CLASIFICACION', 1, 0, 'C', 1);
	$pdf->Cell(14, 3, 'CANTIDAD', 1, 0, 'C', 1);
	$pdf->Cell(18, 3, utf8_decode('COD. INTERNO'), 1, 0, 'C', 1);
	$pdf->Cell(60, 3, 'DESCRIPCION', 1, 0, 'C', 1);
	$pdf->Cell(40, 3, utf8_decode('UBICACIÓN'), 1, 0, 'C', 1);
	$pdf->Cell(20, 3, 'PRECIO', 1, 1, 'C', 1); //$pdf->Ln();

// BUSCO EL O LOS ACTIVOS SEGUN EL NRO DE ACTA  DE BAJA
$sa = "select 
              a.*,
			  b.* ,
			  c.Descripcion as DescpUbicacion
	     from 
		      af_actabajaactivo a 
			  inner join af_activo b on (b.Activo = a.Activo and b.CodOrganismo = a.CodOrganismo)
			  inner join af_ubicaciones c on (c.CodUbicacion = b.Ubicacion)
		where 
		      a.CodOrganismo='".$CodOrganismo."' and 
			  a.NroActa='".$NroActa."' and 
			  a.Anio = '".$Anio."'"; 
$qa = mysql_query($sa) or die ($sa.mysql_error());
$ra = mysql_num_rows($qa);

if($ra!=0) 
   for($i=0; $i<$ra; $i++){
	    $fa = mysql_fetch_array($qa);
		$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
		//$pdf->Cell(12,'','','');
		/*$pdf->SetFont('Arial', '', 8);
		$pdf->Cell(12,'','','');
		$pdf->Cell(20, 3, $fa['ClasificacionPublic20'], 1, 0, 'C', 1);
		$pdf->Cell(14, 3, '1', 1, 0, 'C', 1);
		$pdf->Cell(18, 3, $fa['CodigoInterno'], 1, 0, 'C', 1);
		$pdf->Cell(50, 3, utf8_decode($fa['Descripcion']), 1, 0, 'C', 1);
		$pdf->Cell(35, 3, utf8_decode($fa['DescpUbicacion']), 1, 0, 'C', 1);
		$pdf->Cell(30, 3, number_format($fa['MontoLocal'],2,',','.'), 1, 1, 'C', 1); //$pdf->Ln();*/
		//$pdf->SetXY(12, 20);
	
			
	 $pdf->Cell(12,'','','');
	 $pdf->SetFont('Arial', '', 7);
	 $pdf->SetWidths(array(20,14,18,60,40,20));
	 $pdf->SetAligns(array('C','C','C','L','L','R'));
	 $pdf->Row(array($fa['ClasificacionPublic20'], '1', $fa['CodigoInterno'], utf8_decode($fa['Descripcion']), utf8_decode($fa['DescpUbicacion']), number_format($fa['MontoLocal'],2,',','.')));
	 $cont_bienes++;
	 $precio_total = $precio_total + $fa['MontoLocal'];
   }

switch($ra){
		case "1": $valor= 11;break;  
		case "2": $valor= 10;break; 
		case "3": $valor= 9;break;   
		case "4": $valor= 8;break;   
		case "5": $valor= 7;break;    
		case "6": $valor= 6;break;
		case "7": $valor= 5; break;
		case "8": $valor= 4; break;
		case "9": $valor= 3; break;
		case "10": $valor= 2; break;
		case "11": $valor= 1; break;
		case "12": $valor= 0; break;
    }
 
  for($i=0; $i<$valor; $i++){ 
	 $pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
	 $pdf->Cell(12,'','','');
	 $pdf->SetFont('Arial', '', 9);
	 $pdf->SetWidths(array(24,14,20,50,35,30));
	 $pdf->SetAligns(array('C','C','C','L','L','L'));
	 $pdf->Row(array('','','','','',''));
  }
 
$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
	 $pdf->Cell(12,'','','');
	 $pdf->SetFont('Arial', '', 8);
	 $pdf->SetWidths(array(58,85,29));
	 $pdf->SetAligns(array('L','R','R'));
	 $pdf->Row(array('Total de Bienes: '.$cont_bienes,'Total en Bs. ==>', number_format($precio_total,2,',','.'))); 
	 
	 $pdf->Rect(42,205,50,''); // Preparado por Registrador de Bienes
	 $pdf->Rect(135,205,50,'');// Revisado por Director de Servicios Generales
	 
	 $pdf->Rect(42,235,50,''); // Aprobado por Contralor del Estado
	 $pdf->Rect(135,235,50,''); // Conformado por Directora General
	 
	 
	 $s_transbaja = "select * 
					   from af_transaccionbaja 
					  where 
					        CodTransaccionBaja=(select max(CodTransaccionBaja) from af_transaccionbaja where Organismo='".$CodOrganismo."') and 
							Organismo='".$CodOrganismo."'";
	  $q_transbaja = mysql_query($s_transbaja) or die ($s_transbaja.mysql_error());
	  $r_transbaja = mysql_num_rows($q_transbaja);
	  
	  if($r_transbaja != 0) $f_transbaja = mysql_fetch_array($q_transbaja);
	  
	 
	 list($nombPreparado, $cargoPreparado, $nivelPreparado) = getfirma($f_transbaja['PreparadoPor']);
	 list($nombServGener, $cargoServGener, $nivelServGener) = getfirma($cod_perservgener);
	 list($nombContralor, $cargoContralor, $nivelContralor) = getfirma($cod_percontralor);
	 
	 //// ------ quien prepara
	 $pdf->SetFont('Arial', 'B', 8);
	 $pdf->SetXY(42, 207); $pdf->Cell(50, 5, $nivelPreparado.$nombPreparado, 0, 1, 'C');
	 $pdf->SetXY(42, 212); $pdf->Cell(50, 5, $cargoPreparado, 0, 1, 'C');
	 
	 /// ------ quien revisa
	 $pdf->SetFont('Arial', 'B', 8);  
	 $pdf->SetXY(135, 207); $pdf->Cell(50, 5,$nivelServGener.$nombServGener, 0, 1, 'C');
	 $pdf->SetXY(135, 212); $pdf->Cell(50, 5,$cargoServGener, 0, 1, 'C');
	 
	 //// ------ quien aprueba
	 $pdf->SetFont('Arial', 'B', 8);
	 $pdf->SetXY(135, 237); $pdf->Cell(50, 5,$nivelContralor.$nombContralor, 0, 1, 'C');
	 $pdf->SetXY(135, 242); $pdf->Cell(50, 5,$cargoContralor, 0, 1, 'C');
	 
	 //// ------ quien conforma
	 $pdf->SetFont('Arial', 'B', 8); 
	 $pdf->SetXY(42, 237); $pdf->Cell(50, 5, $nivelDireGener.$nombDireGener, 0, 1, 'C');
	 $pdf->SetXY(42, 242); $pdf->Cell(50, 5, $cargoDireGener, 0, 1, 'C'); 
	 
	 
	 $pdf->SetXY(20,254); $pdf->Cell(40, 5, "REF.: FOR-DSG-007");	 
	 
//---------------------------------------------------*/
/*$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(202, 202, 202); $pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->Cell(100,10,'',0,1,'L');
	$pdf->Cell(100,10,'ELABORADO POR:',0,0,'L');$pdf->Cell(120,10,'REVISADO POR:',0,0,'L');$pdf->Cell(100,10,'CONFORMADO POR:',0,1,'L');
	$pdf->Cell(100,5,'',0,0,'L');$pdf->Cell(120,5,'',0,0,'L');$pdf->Cell(100,5,'',0,1,'L');
	$pdf->Cell(100,5,'T.S.U. MARIANA SALAZAR',0,0,'L');$pdf->Cell(120,5,'LCDA. YOSMAR GREHAM',0,0,'L');$pdf->Cell(100,5,'LCDA. ROSIS REQUENA',0,1,'L');
	$pdf->Cell(100,2,'ASISTENTE DE PRESUPUESTI I',0,0,'L');$pdf->Cell(120,2,'JEFE(A) DIV. ADMINISTRACION Y PRESUPUESTO',0,0,'L');$pdf->Cell(100,2,'DIRECTORA GENERAL',0,1,'L');*/


$pdf->Output();
?>  