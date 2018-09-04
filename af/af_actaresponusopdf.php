<?php
// ------------------------------------- ####
include("../lib/fphp.php");
define('FPDF_FONTPATH','font/');
require('fpdf.php');
require('fphp.php');
connect(); 
extract ($_POST);
extract ($_GET);

global $NroActa;
global $CodOrganismo;
global $Anio;

class PDF extends FPDF{

function Header(){
	
	global $NroActa;
    global $CodOrganismo;
    global $Anio;
	global $CodDependencia;
	
	//// Consulta para obtener el último nro de acta registrada
	$sql = "select *  
	          from af_actaresponsabilidaduso 
			 where Anio='$Anio' and 
			       CodOrganismo='$CodOrganismo' and 
				   NroActa = '$NroActa' and 
				   CodDependencia= '$CodDependencia'";
    $qry = mysql_query($sql) or die ($sql.mysql_error());
	if( mysql_num_rows($qry)!=0) $field = mysql_fetch_array($qry);
	
	$sql01 = "select * from mastdependencias where CodDependencia = '$CodDependencia'";
	$qry01 = mysql_query($sql01) or die ($sql01.mysql_error());
	$field01 = mysql_fetch_array($qry01);
	
	$this->Image('../imagenes/logos/logo.jpg', 20, 10, 15, 15);	
	$this->SetFont('Arial', 'B', 8);
	$this->SetXY(35, 10); $this->Cell(100, 8,utf8_decode( 'República Bolivariana de Venezuela'), 0, 1, 'L');
	$this->SetXY(35, 14); $this->Cell(100, 8,utf8_decode($_SESSION["NOMBRE_ORGANISMO_ACTUAL"]), 0, 1, 'L');
	$this->SetXY(35, 18); $this->Cell(100, 8,utf8_decode('Dirección de Servicios Generales'), 0, 1, 'L');
						  
	$this->SetXY(35, 10); $this->Cell(135, 8, 'Fecha:', 0, 0, 'R');$this->Cell(10, 8,date("d-m-Y"),0,1,'');
	$this->SetXY(20, 14); $this->Cell(150, 8, utf8_decode('Pág.:'), 0, 1, 'R'); /// NRO DE PÁGINA
	
	$this->SetXY(20, 18); $this->Cell(150, 8, utf8_decode('Nro.:'), 0, 0, 'R');/// NRO DE DOCUMENTO
						  $this->Cell(10, 8, $field01['CodInterno'].'-'.$NroActa.'-'.$Anio, 0, 1, 'L');$this->Ln(5);
	
	$this->SetFont('Arial', 'B', 10);
	   $this->Cell(50, 5, '', 0, 0, 'C');
	   $this->Cell(100, 5, utf8_decode('ACTA DE RESPONSABILIDAD DE USO'), 0, 1, 'C');
	   $this->Ln(2);
	

}
//Page footer
function Footer(){
    //Position at 1.5 cm from bottom
    $this->SetXY(142,14);
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
$pdf->SetAutoPageBreak(1,4);


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




/// Consulta tabla 
$s_activo = "select 
					a.*,
					b.Ndocumento as cedResponsablePrimario,
					c.Ndocumento as cedResponsableUso,
					e.Descripcion as DescripUbicacion,
					f.Descripcion as DescripClasificacion20,
					d.CodigoInterno,
					d.Descripcion as DescripActivo,
					d.Marca,
					d.Modelo
			  from 
			       af_actaresponsabilidaduso a 
				   inner join mastpersonas b on (b.CodPersona = a.ResponsablePrimario) 
				   inner join mastpersonas c on (c.CodPersona = a.ResponsableUso) 
				   inner join af_activo d on (d.Activo = a.Activo) 
				   inner join af_ubicaciones e on (e.CodUbicacion = d.Ubicacion) 
				   inner join af_clasificacionactivo20 f on (f.CodClasificacion = d.ClasificacionPublic20)
			 where 
			       a.Anio= '$Anio' and 
				   a.NroActa= '$NroActa' and 
				   a.CodOrganismo= '$CodOrganismo' and 
				   a.CodDependencia= '$CodDependencia' ";
$q_activo = mysql_query($s_activo) or die ($s_activo.mysql_error());
$r_activo = mysql_num_rows($q_activo); if($r_activo!=0) $f_activo= mysql_fetch_array($q_activo);

list($A, $B, $C) = split('[-]',$f_activo['FechaActa']);
 $fecha = $C.'/'.$B.'/'.$A;
    switch($B){
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

/// FUNCION QUE DEVUELVE EL NOMBRE DEL DÍA
function nameDate($fecha='')//formato: 00/00/0000
{ 	$fecha= empty($fecha)?date('d/m/Y'):$fecha;
	$dias = array('Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado');
	$dd   = explode('/',$fecha);
	$ts   = mktime(0,0,0,$dd[1],$dd[0],$dd[2]);
	//return $dias[date('w',$ts)].'/'.date('m',$ts).'/'.date('Y',$ts);
	return $dias[date('w',$ts)];
}
$diaNombre = nameDate($fecha);

	 list($nombrePrimario, $cargoPrimario, $nivelPrimario) = getFirma($f_activo['ResponsablePrimario']);
	 list($nombreUso, $cargoEmpleUso, $nivelUso) = getFirma($f_activo['ResponsableUso']);
	
/// Consulta realizada para obtener el cargo actual del empleado Usuario
$s_con = "select 
				  a.CodPersona,
				  b.DescripCargo,
				  c.Dependencia 
			 from 
				  rh_empleadonivelacion a 
				  inner join rh_puestos b on (a.CodCargo=b.CodCargo)
				  inner join mastdependencias c on (c.CodDependencia=a.CodDependencia)
 			where 
				  a.Secuencia=(select max(Secuencia) from rh_empleadonivelacion where CodPersona='".$f_activo['ResponsableUso']."') and 
				  a.CodPersona='".$f_activo['ResponsableUso']."'"; echo $scon03;
$q_con = mysql_query($s_con) or die ($s_con.mysql_error());
if(mysql_num_rows($q_con)!=0)$f_con = mysql_fetch_array($q_con);




//$empResponsable = ucwords(strtr(strtolower(utf8_encode($f_activo['EmpleadoResponsable'])),"ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ", "àáâãäåæçèéêëìíîïðñòóôõöøùüú")); //Empleado Responsable
$empResponsable = ucwords(strtr(strtolower($f_activo['EmpleadoResponsable']),"ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ", "àáâãäåæçèéêëìíîïðñòóôõöøùüú")); //Empleado Responsable
$cargoResponsable = ucwords(strtr(strtolower(utf8_encode($f_activo['DescripCargoRespon'])),"ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ", "àáâãäåæçèéêëìíîïðñòóôõöøùüú"));

$empResponsableUso = ucwords(strtr(strtolower(utf8_encode($f_activo['EmpleadoResponUso'])),"ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ", "àáâãäåæçèéêëìíîïðñòóôõöøùüú")); //Responsable Uso
$cargoUso = ucwords(strtr(strtolower(utf8_encode($f_activo['DescripCargoResponUso'])),"ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ", "àáâãäåæçèéêëìíîïðñòóôõöøùüú"));
$dependenciaUso = ucwords(strtr(strtolower(utf8_encode($f_con['Dependencia'])),"ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ", "àáâãäåæçèéêëìíîïðñòóôõöøùüú"));

$parrafo1 = utf8_decode("El (la) Suscrito(a), ").utf8_decode($empResponsable).utf8_decode(", titular de la Cédula de Identidad N°: ").$f_activo['cedResponsablePrimario'].utf8_decode(", quien desempeña el cargo de ").utf8_decode($cargoResponsable).utf8_decode(" en la contraloría del Estado Delta Amacuro y como Responsable Patrimonial Primario, hace constar que el día de hoy, ").utf8_decode($diaNombre).' '.$C.utf8_decode(" del mes de ").$fmes.utf8_decode(" del Año ").$Anio.utf8_decode(" se asignan en calidad de uso y custodia al funcionario ").utf8_decode($empResponsableUso).utf8_decode(" titular de la Cédula de Identidad N°: ").$f_activo['cedResponsableUso'].utf8_decode(" el bien o bienes que a continuación se especifican: ");

$parrafo2 = utf8_decode("Yo, ").utf8_decode($empResponsableUso).utf8_decode(' como Responsable Patrimonial de Uso declaro: "Recibido el bien o bienes especificados en la presente acta, en buenas condiciones de uso y conservación, para ser utilizado(s) por mi persona, dentro de las instalaciones de la Contraloría del Estado o fuera de su recinto, cuando sea requerido y autorizado para ello, únicamente para el desempeño  de mis funciones como ').utf8_decode($cargoUso).utf8_decode(' adscrito a "').utf8_decode($dependenciaUso).utf8_decode('. En virtud de la presente asignación, en mi condición de Responsable Patrimonial de Uso y custodio de los bienes descritos como propiedad de este ente de Control Fiscal, me comprometo a utilizarlos de manera responsable y tomar las medidas de resguardo necesarias para evitar el deteriorio acelerado, hurto, robo, extravío o perdida de los mismos. Queda entendido que cualquier daño material que pueda ocurrirle al referido bien mueble; con ocasión de negligencia impericia u omisión en el uso del mismo; queda sujeta a las sanciones administrativas previstas en artículo 91 numeral 2 de la Ley Orgánica de la Contraloría General de la República y del Sistema Nacional de Control Fiscal, Disciplinarias prevista en el artículo 33 numeral 7 de la Ley de Estatuto de Función Pública y Penal prevista en el artículo 53 de la Ley Contra la Corrupción, salvo aquellos daños naturales u hechos fortuitos que se presenten, lo cual deberá ser notificado por escrito ante la Dirección de Servicios Generales de ésta Contraloría. Es todo, terminó se leyó y conformes firman.');

$pdf->SetFont('Arial', '', 11);
		//$pdf->SetXY(20,40);
		$pdf->Cell(12,'','','');
		$pdf->MultiCell(175, 6, $parrafo1, 0, 'J');
		$pdf->Ln(1);

$pdf->SetFont('Arial', '', 7);
	$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(200, 200, 200); $pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont('Arial', 'B', 6);
	$pdf->Cell(12,'','','');
	$pdf->Cell(20, 3, 'CLASIFICACION', 1, 0, 'C', 1);
	$pdf->Cell(14, 3, 'CANTIDAD', 1, 0, 'C', 1);
	$pdf->Cell(22, 3, utf8_decode('N°IDENTIFICACION'), 1, 0, 'C', 1);
	$pdf->Cell(50, 3, 'DESCRIPCION', 1, 0, 'C', 1);
	$pdf->Cell(35, 3, 'MARCA', 1, 0, 'C', 1);
	$pdf->Cell(30, 3, 'MODELO', 1, 1, 'C', 1); 
	
	$s_a = "select 
					a.*,
					b.Ndocumento as cedResponsablePrimario,
					c.Ndocumento as cedResponsableUso,
					e.Descripcion as DescripUbicacion,
					f.Descripcion as DescripClasificacion20,
					d.CodigoInterno,
					d.Descripcion as DescripActivo,
					d.Marca,
					d.Modelo
			  from 
			       af_actaresponsabilidaduso a 
				   inner join mastpersonas b on (b.CodPersona = a.ResponsablePrimario) 
				   inner join mastpersonas c on (c.CodPersona = a.ResponsableUso) 
				   inner join af_activo d on (d.Activo = a.Activo) 
				   inner join af_ubicaciones e on (e.CodUbicacion = d.Ubicacion) 
				   inner join af_clasificacionactivo20 f on (f.CodClasificacion = d.ClasificacionPublic20)
			 where 
			       a.Anio= '$Anio' and 
				   a.NroActa= '$NroActa' and 
				   a.CodOrganismo= '$CodOrganismo' and 
				   a.CodDependencia= '$CodDependencia'";
$q_a = mysql_query($s_a) or die ($s_a.mysql_error());
$r_a = mysql_num_rows($q_a);

 if($r_a!=0) 
	for($x=0; $x<$r_a; $x++){
		$f_a= mysql_fetch_array($q_a);
     $pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
     $pdf->Cell(12,'','','');
	 $pdf->SetFont('Arial', '', 7);
	 $pdf->SetWidths(array(20,14,22,50,35,30));
	 $pdf->SetAligns(array('C','C','C','L','L','R'));
	 $pdf->Row(array($f_a['DescripClasificacion20'],'1',$f_a['CodigoInterno'], $f_a['DescripActivo'],$f_a['Marca'],$f_a['Modelo']));
	}
		
    switch($r_activo){
		case "1": $valor= 5;break;  
		case "2": $valor= 4;break; 
		case "3": $valor= 3;break;   
		case "4": $valor= 2;break;   
		case "5": $valor= 1;break;    
    }
	
	for($i=0; $i<$valor; $i++){ 
		 $pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
		 $pdf->Cell(12,'','','');
		 $pdf->SetFont('Arial', '', 6.5);
		 $pdf->SetWidths(array(20,14,22,50,35,30));
		 $pdf->SetAligns(array('C','L','R','R','R','R'));
		 $pdf->Row(array('','','','','',''));
	}
	 $pdf->Ln(3);
	    $pdf->SetFont('Arial', '', 11);
		//$pdf->SetXY(20,128);
		$pdf->Cell(12,'','','');
		$pdf->MultiCell(175, 6, $parrafo2, 0, 'J');
		
		$pdf->SetFont('Arial', '', 12);
		$pdf->SetXY(20,182);
		$pdf->MultiCell(175, 6, $parrafo3, 0, 'J');
	 
	 $pdf->Rect(35,245,50,'');
	 $pdf->Rect(116,245,50,'');
	 /*$pdf->Rect(35,250,50,'');
	 $pdf->Rect(116,250,50,'');*/
	 
	 
	 	 
	 //// ------ Responsable Patrimonial Primario
	 
	 $pdf->SetFont('Arial', 'B', 8);
	 $pdf->SetXY(40, 245); $pdf->Cell(40, 5,$nivelPrimario.$nombrePrimario, 0, 1, 'C');
	 $pdf->SetXY(40, 248); $pdf->Cell(40, 5,$f_activo['DescripCargoRespon'], 0, 1, 'C');
	 //$pdf->SetXY(40, 248); $pdf->Cell(40, 5,$cargoPrimario, 0, 1, 'C'); LINEA MODIFICADA
	 
	 //// ------ Responsable Patrimonial Uso
	 $pdf->SetFont('Arial', 'B', 8);
	 $pdf->SetXY(122, 245); $pdf->Cell(40, 5,$nivelUso.$nombreUso, 0, 1, 'C');
	 $pdf->SetXY(123, 248); $pdf->Cell(40, 5,$f_activo['DescripCargoResponUso'], 0, 1, 'C');
	 //$pdf->SetXY(123, 248); $pdf->Cell(40, 5,$cargoEmpleUso, 0, 1, 'C');
	 
	 $pdf->SetXY(20,270); $pdf->Cell(40, 5, "REF.: FOR-DSG-003");
	 
	 
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