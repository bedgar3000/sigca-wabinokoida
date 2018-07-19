<?php
define('FPDF_FONTPATH','font/');
include("../lib/fphp.php");
require('fpdf.php');
//require('fphp.php');

class PDF extends FPDF{

function Header(){

	global $Pase;

	if($Pase=="P"){

	    global $CodOrganismo, $nroActaEntrega, $Anio, $MovimientoNumero, $TipoActa;

		$sql = "select
		              a.FechaActa,
					  b.FechaRevisadoPor,
					  b.InventarioFisicoFecha
				  from
					 af_actaentregaactivo a
					 inner join af_activo b on (b.Activo=a.Activo and b.CodOrganismo=a.CodOrganismo)
				where
					 a.CodOrganismo='".$CodOrganismo."' and
					 a.NroActa='".$nroActaEntrega."' and
					 a.Anio = '".$Anio."' and
					 a.TipoActa = 'AE'";
		$qry = mysql_query($sql) or die ($sql.mysql_error());
		$row = mysql_num_rows($qry);
		if($row !=0) $field = mysql_fetch_array($qry);

		list($fanio, $fmes, $fdia) = split('[-]', $field['FechaActa']);
		$fecha_acta = $fdia.'-'.$fmes.'-'.$fanio;

		$this->Image('../imagenes/logos/logo.jpg', 20, 10, 15, 15);
		$this->SetFont('Arial', 'B', 8);
		$this->SetXY(35, 10); $this->Cell(100, 8,utf8_decode( 'República Bolivariana de Venezuela'), 0, 1, 'L');
		$this->SetXY(35, 14); $this->Cell(100, 8,utf8_decode($_SESSION["NOMBRE_ORGANISMO_ACTUAL"]), 0, 1, 'L');
		$this->SetXY(35, 18); $this->Cell(100, 8,utf8_decode('Dirección de Servicios Generales'), 0, 1, 'L');

		$this->SetXY(35, 10); $this->Cell(140, 8, 'Fecha:', 0, 0, 'R');$this->Cell(10, 8,$fecha_acta,0,1,'');
		$this->SetXY(20, 14); $this->Cell(155, 8, utf8_decode('Pág.:'), 0, 1, 'R'); /// NRO DE PÁGINA

		$this->SetXY(20, 18); $this->Cell(155, 8, utf8_decode('Nro.:'), 0, 0, 'R');/// NRO DE DOCUMENTO
							  $this->Cell(10, 8, $nroActaEntrega.'-'.$fanio, 0, 1, 'L');$this->Ln(5);

		$this->SetFont('Arial', 'B', 10);
		$this->Cell(50, 5, '', 0, 0, 'C');
		$this->Cell(100, 5, utf8_decode('ACTA DE ENTREGA DE BIENES MUEBLES'), 0, 1, 'C');
		$this->Ln(3);


	}
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

//// Instanciation of inherited class
$pdf=new PDF('P','mm','letter');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);
$pdf->SetAutoPageBreak(1,3);

if($Pase=="P"){

    $sa = "select
                a.FechaActa,
        			  a.EmpleadoConform,
        			  a.ConformadoPor as ConformPor,
        			  a.DescripCargoConform as DescCargoConform,
        			  a.EmpleadoAprob,
        			  a.AprobadoPor as AprobPor,
        			  a.DescripCargoAprob,
        			  a.ResponsablePrimario,
        			  a.EmpleadoRespon,
        			  a.DescripCargoRespon,
        			  b.*,
        			  c.Ndocumento,
        			  d.Ndocumento as NroDocRevisa,
        			  e.Ndocumento as NroDocRecive
	        from
		            af_actaentregaactivo a
			          inner join af_activo b on (b.Activo=a.Activo and
			                                     b.CodOrganismo=a.CodOrganismo)
        			  inner join mastpersonas c on (c.CodPersona=a.AprobadoPor)
        			  inner join mastpersonas d on (d.CodPersona=a.ConformadoPor)
        			  inner join mastpersonas e on (e.CodPersona=a.ResponsablePrimario)
		     where
		            a.CodOrganismo= '".$CodOrganismo."' and
			          a.NroActa= '".$nroActaEntrega."' and
			          a.Anio= '".$Anio."' and
			          a.TipoActa= '".$TipoActa."' ";
	  $qa= mysql_query($sa) or die ($sa.mysql_error());
  	$ra= mysql_num_rows($qa);
	  if($ra!=0) $fa= mysql_fetch_array($qa);

    list($ano, $mes, $dia) = split('[-]', $fa['FechaActa']);
    $fechaActa = $dia.'-'.$mes.'-'.$ano;

	 switch($mes){
			case "01": $fmes= Enero;break;
			case "02": $fmes= Febrero;break;
			case "03": $fmes= Marzo;break;
			case "04": $fmes= Abril;break;
			case "05": $fmes= Mayo;break;
			case "06": $fmes= Junio;break;
			case "07": $fmes= Julio; break;
			case "08": $fmes= "Agosto"; break;
			case "09": $fmes= Septiembre; break;
			case "10": $fmes= Octubre; break;
			case "11": $fmes= Noviembre; break;
			case "12": $fmes= Diciembre; break;
	   }



	$montoLocal = number_format($fcon['MontoLocal'],2,',','.');

    list($nombreCompleto03, $cargo03, $nivel03)= getfirma($fa['AprobPor']); // quien aprueba
	list($nombreCompleto02, $cargo02, $nivel02)= getfirma($fa['ConformPor']); // quien conforma
	list($nombreCompleto04, $cargo04, $nivel04)= getfirma($fa['ResponsablePrimario']); // quien recibe


function nameDate($fechaActa)//formato: 00/00/0000
{ 	//$fecha= empty($fecha)?date('d/m/Y'):$fecha;
	$dias = array('Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado');
	$dd   = explode('-',$fechaActa);
	$ts   = mktime(0,0,0,$dd[1],$dd[0],$dd[2]);
	//return $dias[date('w',$ts)].'/'.date('m',$ts).'/'.date('Y',$ts);
	return $dias[date('w',$ts)];
}

$mesNombre = nameDate($fechaActa);

	// BUSCO CANTIDAD DE ACTIVOS INVOLUCRADOS
	$scb= "select count(NroActa) as CantBienes
		    from af_actaentregaactivo a
		   where a.CodOrganismo= '".$CodOrganismo."' and
			     a.NroActa= '".$nroActaEntrega."' and
				 a.Anio= '".$Anio."' and
				 a.TipoActa ='AE'";
	$qcb= mysql_query($scb) or die ($scb.mysql_error());
	$fcb= mysql_fetch_array($qcb);
	
	if($fcb['CantBienes']=='1'){$contenido2= utf8_decode("El mismo es propiedad"); $contenido1= utf8_decode(" del bien mueble que a continuación se especifica:");}
	else{$contenido2= utf8_decode("Los mismos son propiedad"); $contenido1= utf8_decode(" de los bienes muebles que a continuación se especifican:");}
	

//// Quien Revisa
list($nombRevisadoPor, $cargoRevisadoPor, $nivelRevisadoPor)= getfirma($fa['ConformPor']);
$nombRevisa= ucwords(strtr(strtolower(utf8_encode($nombRevisadoPor)),"ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ", "àáâãäåæçèéêëìíîïðñòóôõöøùüú")); //
$cargoRevisa= ucwords(strtr(strtolower(utf8_encode($fa['DescCargoConform'])),"ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ", "àáâãäåæçèéêëìíîïðñòóôõöøùüú")); //

//// Quien Aprueba
list($nombAprobadoPor, $cargoAprobadoPor, $nivelAprobadoPor)= getfirma($fa['AprobPor']);
$nombAprueba= ucwords(strtr(strtolower(utf8_encode($nombAprobadoPor)),"ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ", "àáâãäåæçèéêëìíîïðñòóôõöøùüú")); //
$cargoAprueba= ucwords(strtr(strtolower(utf8_encode($fa['DescripCargoAprob'])),"ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ", "àáâãäåæçèéêëìíîïðñòóôõöøùüú")); //

//// Responsable Primario - quien recive
list($nombPreparadoPor, $cargoPreparadoPor, $nivelPreparadoPor)= getfirma($fa['ResponsablePrimario']);
$nombResponsablePrimario= ucwords(strtr(strtolower(utf8_encode($nombPreparadoPor)),"ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ", "àáâãäåæçèéêëìíîïðñòóôõöøùüú")); //
$cargoResponsablePrimario= ucwords(strtr(strtolower(utf8_encode($fa['DescripCargoRespon'])),"ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ", "àáâãäåæçèéêëìíîïðñòóôõöøùüú")); //

$parrafo1 = utf8_decode("En el día de hoy, ").utf8_decode($mesNombre).' '.$dia.(" del mes de ").$fmes.utf8_decode(" del año ").$ano.utf8_decode(", reunidos en las instalaciones donde funciona la Contraloría del Estado Delta Amacuro, ubicada en la Calle Centurión, Quinta Paola N° 36, Municipio Tucupita, Estado Delta Amacuro, los ciudadanos: ").$nombAprueba.utf8_decode(", titular de la cédula de identidad N°").$fa['Ndocumento'].' '.$cargoAprueba.(", ").$nombRevisa.utf8_decode(", titular de la cédula de identidad N° ").$fa['NroDocRevisa'].' '.$cargoRevisa.(" y ").$nombResponsablePrimario.utf8_decode(", titular de la cédula de identidad N° ").$fa['NroDocRecive'].utf8_decode(" quien desempeña el cargo de ").$cargoResponsablePrimario.utf8_decode(", con el único objeto de hacerle entrega al último de los mencionados como Responsable Patrimonial Primario; en calidad de uso, guarda y custodia ").$contenido1;

$parrafo2 = $contenido2.utf8_decode(" de este ente de Control Fiscal, tal como se desprende del registro de Bienes e Inventarios llevado ante esta Contraloría, el mismo quedará bajo su responsabilidad absoluta, siendo éste responsable de vigilar, conservar y salvaguardar, los bienes muebles entregados mediante la presente Acta; queda entendido que cualquier daño material que pueda ocurrirle a los referidos bienes muebles; con ocasión de negligencia u omisión en su uso; queda sujeta a las sanciones administrativas previstas en articulo 91 numeral 2 de la Ley Orgánica de la Contraloría General de la República y del Sistema Nacional de Control Fiscal,Disciplinarias prevista en el artículo 33 numeral 7 de la Ley del Estatuto de Función Pública y Penal prevista en el artículo 53 de la Ley Contra la Corrupcion, salvo aquellos daños naturales u hechos fortuitos que se presenten, lo cual deberá ser notificado por escrito ante la Dirección de Servicios Generales de ésta Contraloría. Es todo, terminó se leyó y conformes firman.");

$pdf->SetFont('Arial', '', 12);
		//$pdf->SetXY(20,43);
		$pdf->Cell(10,0,"",0,0,"");
		$pdf->MultiCell(175, 6, $parrafo1, 0, 'J');
		$pdf->Ln(2);

$pdf->SetFont('Arial', '', 7);
	$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(200, 200, 200); $pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont('Arial', 'B', 6);
	$pdf->Cell(12,'','','');
	$pdf->Cell(24, 3, 'CLASIFICACION', 1, 0, 'C', 1);
	$pdf->Cell(14, 3, 'CANTIDAD', 1, 0, 'C', 1);
	$pdf->Cell(20, 3, utf8_decode('COD. INTERNO'), 1, 0, 'C', 1);
	$pdf->Cell(50, 3, 'DESCRIPCION', 1, 0, 'C', 1);
	$pdf->Cell(35, 3, 'MARCA', 1, 0, 'C', 1);
	$pdf->Cell(30, 3, 'SERIAL', 1, 1, 'C', 1);

	// BUSCO EL O LOS ACTIVOS SEGUN EL NRO DE ACTA DE ENTREGA
	$sb= "select a.*, b.*
		    from af_actaentregaactivo a
				 inner join af_activo b on (b.Activo = a.Activo and
				                            b.CodOrganismo = a.CodOrganismo)
		   where a.CodOrganismo= '".$CodOrganismo."' and
				 a.NroActa= '".$nroActaEntrega."' and
				 a.Anio= '".$Anio."' and
				 a.TipoActa ='AE'";
	$qb= mysql_query($sb) or die ($sb.mysql_error());
	$rb= mysql_num_rows($qb);

 if($rb!=0)
   for($i=0; $i<$rb; $i++){
	   $fb= mysql_fetch_array($qb);

	   $s_marca= "select * from lg_marcas where CodMarca='".$fb['Marca']."'";
	   $q_marca= mysql_query($s_marca) or die ($s_marca.mysql_error());
	   $r_marca= mysql_num_rows($q_marca);
	   if($r_marca!=0) $f_marca= mysql_fetch_array($q_marca);

	 $pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
	 $pdf->Cell(12,'','','');
	 $pdf->SetFont('Arial', '', 8);
	 $pdf->SetWidths(array(24,14,20,50,35,30));
	 $pdf->SetAligns(array('C','C','C','L','L','l'));
	 $pdf->Row(array($fb['ClasificacionPublic20'], '1', $fb['CodigoInterno'],
	                 $fb['Descripcion'], $f_marca['Descripcion'], $fb['NumeroSerie']));
   }
     $pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
	 $pdf->Cell(12,'','','');
	 $pdf->SetFont('Arial', '', 8);
	 $pdf->SetWidths(array(24,14,20,50,35,30));
	 $pdf->SetAligns(array('C','C','C','L','L','l'));
	 $pdf->Row(array("Total Bienes:", $rb));
     $pdf->Ln(2);

		switch($ra){
				case "1": $valor= 5;break;
				case "2": $valor= 4;break;
				case "3": $valor= 3;break;
				case "4": $valor= 2;break;
				case "5": $valor= 1;break;
				case "6": $valor= 0;break;
		}

	  $pdf->SetFont('Arial', '', 12);
		//$pdf->SetXY(20,150);
		$pdf->Cell(10,0,"",0,0,"");
		$pdf->MultiCell(175, 6, $parrafo2, 0, 'J'); $pdf->Ln(10);

	 //// ------ QUIEN APRUEBA
	 $pdf->SetFont('Arial', 'B', 8);

	 //// ------ QUIEN APRUEBA                                          /// ------ QUIEN REVISA
	 $pdf->Cell(100,3,"_____________________________",0,0,'C');         $pdf->Cell(80,3,"_____________________________",0,1,'C');
	 $pdf->Cell(100,3,$nivelAprobadoPor.' '.$nombAprobadoPor,0,0,'C');	$pdf->Cell(80,3,$nivelRevisadoPor.' '.$nombRevisadoPor,0,1,'C');
	 $pdf->Cell(100,3,$fa['DescripCargoAprob'],0,0,'C');                $pdf->Cell(80,3,$fa['DescCargoConform'],0,1,'C');  $pdf->Ln(5);

	 //// ------ QUIEN RECIBE
	 $pdf->Cell(180,3,"_____________________________",0,1,"C");
	 $pdf->Cell(180,3,$nivelPreparadoPor.' '.$nombPreparadoPor, 0, 1, 'C');
	 $pdf->Cell(180,3,$fa['DescripCargoRespon'], 0, 1, 'C');

	 $pdf->SetXY(20,271); $pdf->Cell(40, 5, "REF.: FOR-DSG-002");
}
//---------------------------------------------------*/
$pdf->Output();
?>
