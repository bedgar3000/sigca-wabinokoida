<?php
// ------------------------------------------------####
include("../lib/fphp.php");
define('FPDF_FONTPATH','font/');
require('fpdf.php');
require('fphp.php');
require('tcpdf/tcpdf.php');
connect(); 
mysql_query("SET NAMES 'utf8'");
extract ($_POST);
extract ($_GET);
//global $Periodo;
//echo $_SESSION["MYSQL_BD"];
/// -------------------------------------------------
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
function VCell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false){
    //Output a cell
    $k=$this->k;
    if($this->y+$h>$this->PageBreakTrigger && !$this->InHeader && !$this->InFooter && $this->AcceptPageBreak())
    {
        //Automatic page break
        $x=$this->x;
        $ws=$this->ws;
        if($ws>0)
        {
            $this->ws=0;
            $this->_out('0 Tw');
        }
        $this->AddPage($this->CurOrientation,$this->CurPageFormat);
        $this->x=$x;
        if($ws>0)
        {
            $this->ws=$ws;
            $this->_out(sprintf('%.3F Tw',$ws*$k));
        }
    }
    if($w==0)
        $w=$this->w-$this->rMargin-$this->x;
    $s='';
// begin change Cell function 
    if($fill || $border>0)
    {
        if($fill)
            $op=($border>0) ? 'B' : 'f';
        else
            $op='S';
        if ($border>1) {
            $s=sprintf('q %.2F w %.2F %.2F %.2F %.2F re %s Q ',$border,
                        $this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
        }
        else
            $s=sprintf('%.2F %.2F %.2F %.2F re %s ',$this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
    }
    if(is_string($border))
    {
        $x=$this->x;
        $y=$this->y;
        if(is_int(strpos($border,'L')))
            $s.=sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);
        else if(is_int(strpos($border,'l')))
            $s.=sprintf('q 2 w %.2F %.2F m %.2F %.2F l S Q ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);
            
        if(is_int(strpos($border,'T')))
            $s.=sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);
        else if(is_int(strpos($border,'t')))
            $s.=sprintf('q 2 w %.2F %.2F m %.2F %.2F l S Q ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);
        
        if(is_int(strpos($border,'R')))
            $s.=sprintf('%.2F %.2F m %.2F %.2F l S ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
        else if(is_int(strpos($border,'r')))
            $s.=sprintf('q 2 w %.2F %.2F m %.2F %.2F l S Q ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
        
        if(is_int(strpos($border,'B')))
            $s.=sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
        else if(is_int(strpos($border,'b')))
            $s.=sprintf('q 2 w %.2F %.2F m %.2F %.2F l S Q ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
    }
    if(trim($txt)!='')
    {
        $cr=substr_count($txt,"\n");
        if ($cr>0) { // Multi line
            $txts = explode("\n", $txt);
            $lines = count($txts);
            for($l=0;$l<$lines;$l++) {
                $txt=$txts[$l];
                $w_txt=$this->GetStringWidth($txt);
                if ($align=='U')
                    $dy=$this->cMargin+$w_txt;
                elseif($align=='D')
                    $dy=$h-$this->cMargin;
                else
                    $dy=($h+$w_txt)/2;
                $txt=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));
                if($this->ColorFlag)
                    $s.='q '.$this->TextColor.' ';
                $s.=sprintf('BT 0 1 -1 0 %.2F %.2F Tm (%s) Tj ET ',
                    ($this->x+.5*$w+(.7+$l-$lines/2)*$this->FontSize)*$k,
                    ($this->h-($this->y+$dy))*$k,$txt);
                if($this->ColorFlag)
                    $s.=' Q ';
            }
        }
        else { // Single line
            $w_txt=$this->GetStringWidth($txt);
            $Tz=100;
            if ($w_txt>$h-2*$this->cMargin) {
                $Tz=($h-2*$this->cMargin)/$w_txt*100;
                $w_txt=$h-2*$this->cMargin;
            }
            if ($align=='U')
                $dy=$this->cMargin+$w_txt;
            elseif($align=='D')
                $dy=$h-$this->cMargin;
            else
                $dy=($h+$w_txt)/2;
            $txt=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));
            if($this->ColorFlag)
                $s.='q '.$this->TextColor.' ';
            $s.=sprintf('q BT 0 1 -1 0 %.2F %.2F Tm %.2F Tz (%s) Tj ET Q ',
                        ($this->x+.5*$w+.3*$this->FontSize)*$k,
                        ($this->h-($this->y+$dy))*$k,$Tz,$txt);
            if($this->ColorFlag)
                $s.=' Q ';
        }
    }
// end change Cell function 
    if($s)
        $this->_out($s);
    $this->lasth=$h;
    if($ln>0)
    {
        //Go to next line
        $this->y+=$h;
        if($ln==1)
            $this->x=$this->lMargin;
    }
    else
        $this->x+=$w;
}

function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
{
    //Output a cell
    $k=$this->k;
    if($this->y+$h>$this->PageBreakTrigger && !$this->InHeader && !$this->InFooter && $this->AcceptPageBreak())
    {
        //Automatic page break
        $x=$this->x;
        $ws=$this->ws;
        if($ws>0)
        {
            $this->ws=0;
            $this->_out('0 Tw');
        }
        $this->AddPage($this->CurOrientation,$this->CurPageFormat);
        $this->x=$x;
        if($ws>0)
        {
            $this->ws=$ws;
            $this->_out(sprintf('%.3F Tw',$ws*$k));
        }
    }
    if($w==0)
        $w=$this->w-$this->rMargin-$this->x;
    $s='';
// begin change Cell function
    if($fill || $border>0)
    {
        if($fill)
            $op=($border>0) ? 'B' : 'f';
        else
            $op='S';
        if ($border>1) {
            $s=sprintf('q %.2F w %.2F %.2F %.2F %.2F re %s Q ',$border,
                $this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
        }
        else
            $s=sprintf('%.2F %.2F %.2F %.2F re %s ',$this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
    }
    if(is_string($border))
    {
        $x=$this->x;
        $y=$this->y;
        if(is_int(strpos($border,'L')))
            $s.=sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);
        else if(is_int(strpos($border,'l')))
            $s.=sprintf('q 2 w %.2F %.2F m %.2F %.2F l S Q ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);
            
        if(is_int(strpos($border,'T')))
            $s.=sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);
        else if(is_int(strpos($border,'t')))
            $s.=sprintf('q 2 w %.2F %.2F m %.2F %.2F l S Q ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);
        
        if(is_int(strpos($border,'R')))
            $s.=sprintf('%.2F %.2F m %.2F %.2F l S ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
        else if(is_int(strpos($border,'r')))
            $s.=sprintf('q 2 w %.2F %.2F m %.2F %.2F l S Q ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
        
        if(is_int(strpos($border,'B')))
            $s.=sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
        else if(is_int(strpos($border,'b')))
            $s.=sprintf('q 2 w %.2F %.2F m %.2F %.2F l S Q ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
    }
    if (trim($txt)!='') {
        $cr=substr_count($txt,"\n");
        if ($cr>0) { // Multi line
            $txts = explode("\n", $txt);
            $lines = count($txts);
            for($l=0;$l<$lines;$l++) {
                $txt=$txts[$l];
                $w_txt=$this->GetStringWidth($txt);
                if($align=='R')
                    $dx=$w-$w_txt-$this->cMargin;
                elseif($align=='C')
                    $dx=($w-$w_txt)/2;
                else
                    $dx=$this->cMargin;

                $txt=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));
                if($this->ColorFlag)
                    $s.='q '.$this->TextColor.' ';
                $s.=sprintf('BT %.2F %.2F Td (%s) Tj ET ',
                    ($this->x+$dx)*$k,
                    ($this->h-($this->y+.5*$h+(.7+$l-$lines/2)*$this->FontSize))*$k,
                    $txt);
                if($this->underline)
                    $s.=' '.$this->_dounderline($this->x+$dx,$this->y+.5*$h+.3*$this->FontSize,$txt);
                if($this->ColorFlag)
                    $s.=' Q ';
                if($link)
                    $this->Link($this->x+$dx,$this->y+.5*$h-.5*$this->FontSize,$w_txt,$this->FontSize,$link);
            }
        }
        else { // Single line
            $w_txt=$this->GetStringWidth($txt);
            $Tz=100;
            if ($w_txt>$w-2*$this->cMargin) { // Need compression
                $Tz=($w-2*$this->cMargin)/$w_txt*100;
                $w_txt=$w-2*$this->cMargin;
            }
            if($align=='R')
                $dx=$w-$w_txt-$this->cMargin;
            elseif($align=='C')
                $dx=($w-$w_txt)/2;
            else
                $dx=$this->cMargin;
            $txt=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));
            if($this->ColorFlag)
                $s.='q '.$this->TextColor.' ';
            $s.=sprintf('q BT %.2F %.2F Td %.2F Tz (%s) Tj ET Q ',
                        ($this->x+$dx)*$k,
                        ($this->h-($this->y+.5*$h+.3*$this->FontSize))*$k,
                        $Tz,$txt);
            if($this->underline)
                $s.=' '.$this->_dounderline($this->x+$dx,$this->y+.5*$h+.3*$this->FontSize,$txt);
            if($this->ColorFlag)
                $s.=' Q ';
            if($link)
                $this->Link($this->x+$dx,$this->y+.5*$h-.5*$this->FontSize,$w_txt,$this->FontSize,$link);
        }
    }
// end change Cell function
    if($s)
        $this->_out($s);
    $this->lasth=$h;
    if($ln>0)
    {
        //Go to next line
        $this->y+=$h;
        if($ln==1)
            $this->x=$this->lMargin;
    }
    else
        $this->x+=$w;
}	
//Page header
function Header(){
    
	global $Periodo, $filtro;
	$this->Image('../imagenes/logos/logo.jpg', 10, 10, 10, 10);	
	$this->SetFont('Arial', 'B', 9);
	$this->SetXY(20, 10); $this->Cell(70, 5,utf8_decode('República Bolivariana de Venezuela'), 0, 1, 'L');
	$this->SetXY(20, 14); $this->Cell(70, 5,utf8_decode($_SESSION["NOMBRE_ORGANISMO_ACTUAL"]), 0, 1, 'L'); 
	$this->Ln(4);
	$this->SetFont('Arial', 'B', 9);
	$this->SetXY(200, 10);$this->Cell(25,5,'FORMULARIO BM-2',0,1,'');
	$this->SetXY(200, 15);$this->Cell(10,5,utf8_decode('Hoja N°'),0,1,'');
	//$this->SetXY(183, 20);$this->Cell(7,5,utf8_decode('Año:'),0,0,'');$this->Cell(6,5,date('Y'),0,1,'L');
						   
	
	
	$this->SetFont('Arial', 'B', 8);
	
	$sql = "select 
				   a.*,
				   b.Coddependencia as CodDepend,
				   b.Dependencia as DescripDependencia,
				   b.CodPersona,
				   c.Organismo,
				   d.Descripcion as DescpCentroCosto 
			  from 
			       af_historicotransaccion a 
				   inner join mastdependencias b on (b.CodDependencia=a.CodDependencia and b.CodOrganismo=a.CodOrganismo)
				   inner join mastorganismos c on (c.CodOrganismo=a.CodOrganismo)
				   inner join ac_mastcentrocosto d on (d.CodCentroCosto=a.CentroCosto)
			  where 
			       a.CodOrganismo<>'' $filtro";  
	$qry = mysql_query($sql) or die ($sql.mysql_error());
	$row = mysql_num_rows($qry);
	
	if($row!=0)	$field = mysql_fetch_array($qry); 
	
	
	list($fano, $fmes) = split('[-]',$field['PeriodoTransaccion']);
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
				     a.Secuencia=(select max(Secuencia) from rh_empleadonivelacion where CodPersona='".$field['CodPersona']."') and 
					 a.CodPersona = '".$field['CodPersona']."'"; 
	 $qcon02 = mysql_query($scon02) or die ($scon02.mysql_error());
	 $fcon02 = mysql_fetch_array($qcon02);
	
	 $cod_personaDependencia=$field['CodPersona']; //echo $cod_personaDependencia;
	
	
	$s_estado = "select 
					   a.Direccion,
	                   d.Estado,
					   c.Municipio 
				   from 
				        mastorganismos a
						inner join mastciudades b on (b.CodCiudad = a.CodCiudad) 
						inner join mastmunicipios c on (c.CodMunicipio = b.CodMunicipio) 
						inner join mastestados d on (d.CodEstado = c.CodEstado) 
				  where 
				        a.CodOrganismo = '".$field['CodOrganismo']."'"; 
	$q_estado = mysql_query($s_estado) or die ($s_estado.mysql_error());
	$r_estado = mysql_num_rows($q_estado);
	
	if($r_estado!="")$f_estado=mysql_fetch_array($q_estado);
	
	
	//$cadena=strtoupper(utf8_decode($f_cons['DescpCentroCosto']));
	$cadena= strtoupper($field['DescpCentroCosto']);
	$cadena= strtr($cadena,"àáâãäåæçèéêëìíîïðñòóôõöøùüú","ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ"); 
	$mes = strtr(strtoupper($mes),"àáâãäåæçèéêëìíîïðñòóôõöøùüú","ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ"); 
	
// Para pasar a minúsculas
$texto = strtolower($texto);
// Para pasar a mayúsculas
$texto = strtoupper($texto);
// Para pasar a mayúsculas solo la primera letra de cada palabra
$texto = ucwords($texto);
// Para pasar a mayúsculas solo la primera letra de toda la cadena
$texto = ucfirst($texto) ;
	
	$this->SetXY(10,22);$this->SetFont('Arial', 'B', 8);
	                    $this->Cell(20, 3, 'ESTADO:', 0, 0, 'L');
						$this->SetFont('Arial', '', 8);	$this->Cell(80, 3, utf8_decode($f_estado['Estado']), 0, 1, 'L');
						
	$this->SetXY(10,25);$this->SetFont('Arial', 'B', 8);
						$this->Cell(20, 3, 'DISTRITO:', 0, 0, 'L');
						$this->SetFont('Arial', '', 8);$this->Cell(100, 3, utf8_decode($f_estado['Estado']), 0, 1, 'L');
	
	$this->SetXY(10,28);$this->SetFont('Arial', 'B', 8);
						$this->Cell(20, 3, 'MUNICIPIO:', 0, 0, 'L');
						$this->SetFont('Arial', '', 8);$this->Cell(100, 3, utf8_decode($f_estado['Municipio']), 0, 1, 'L');
	
	$this->SetXY(10,31);$this->SetFont('Arial', 'B', 8);
						$this->Cell(35, 3, 'DIRECCION O LUGAR:', 0, 0, 'L');
						$this->SetFont('Arial', '', 8);$this->Cell(100, 3, utf8_decode($f_estado['Direccion']), 0, 1, 'L');
						
	$this->SetXY(10,34);$this->SetFont('Arial', 'B', 8);
						$this->Cell(55, 3, 'DEPENDENCIA O UNIDAD PRIMARIA:', 0, 0, 'L');
						$this->SetFont('Arial', '', 8);$this->Cell(80, 3, utf8_decode($field['DescripDependencia']), 0, 0, 'L');
						$this->SetFont('Arial', 'B', 8);
						$this->Cell(17, 3, 'SERVICIO:', 0, 0, 'L');
						$this->SetFont('Arial', '', 8);	$this->Cell(80, 3, utf8_decode($cadena), 0, 1, 'L');
						
	$this->SetXY(10,37);$this->SetFont('Arial', 'B', 8);
						$this->Cell(60, 3, 'UNIDAD DE TRABAJO O DEPENDENCIA:', 0, 0, 'L');
						$this->SetFont('Arial', '', 8);$this->Cell(100, 3, utf8_decode($field['DescripDependencia']), 0, 1, 'L');
						
	$this->SetXY(10,40);$this->SetFont('Arial', 'B', 8);
						$this->Cell(40, 3, 'PERIODO DE LA CUENTA:', 0, 0, 'L');
						$this->SetFont('Arial','', 8);$this->Cell(15, 3,  $mes.' '.$fano, 0, 1, 'L'); $this->Ln(3); 
						

	
	$this->SetFont('Arial', 'B', 8);
	$this->Cell(30, 7, utf8_decode('Clasificación(Código)'), 1, 0, 'C');
	$this->VCell(10, 21, utf8_decode('Concepto de Movimiento'), 1, 0, 'C');
    $this->VCell(10, 21, utf8_decode('Cantidad'), 1, 0, 'C');	
	$this->VCell(13, 21, utf8_decode('N° Identificación'), 1, 0, 'C');
	$this->Cell(120, 21, utf8_decode('NOMBRE Y DESCRIPCION DE LOS BIENES, REFERENCIA DE LOS COMPROBANTES Y DE LOS PRECIOS UNITARIOS'), 1, 0, 'C');
	$this->Cell(30, 21, utf8_decode('Incorporaciones Bs.'), 1, 0, 'C');
	$this->Cell(30, 21, utf8_decode('Desincorporaciones Bs.'), 1, 1, 'C');
	
	
	$this->SetXY(10,'53');
	$this->VCell(10, 14, utf8_decode('Grupo'), 1, 0, 'C'); 
	$this->VCell(10, 14, utf8_decode('SubGrupo'), 1, 0, 'C');
	$this->VCell(10, 14, utf8_decode('Sección'), 1, 1, 'C');
	
}
//Page footer
function Footer(){
    //Position at 1.5 cm from bottom
    $this->SetXY(164,13);
    //Arial italic 8
    $this->SetFont('Arial','B',9);
    //Page number
    $this->Cell(0,10,' '.$this->PageNo().'/{nb}',0,0,'C');
}

}
//Instanciation of inherited class
$pdf=new PDF('L','mm','letter');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);

//// ---- Consulta para obtener datos 
$sactivo = "select 
				  a.*,
				  b.Descripcion
			  from
				  af_historicotransaccion a
				  inner join af_activo b on (b.Activo=a.Activo)
			 where 
			      a.CodOrganismo<>'' $filtro"; //echo $sactivo;
$qactivo = mysql_query($sactivo) or die ($sactivo.mysql_error());
$ractivo = mysql_num_rows($qactivo);

if($ractivo!=0)
   for($i=0; $i<$ractivo; $i++){
      $factivo = mysql_fetch_array($qactivo);
	  
	  if($factivo['Nivel']=='4'){
	    $cod = substr($factivo['CodClasificacion'], 0, -8); //echo 'cod=  '.$cod;   //Cola  
	    $cod2 = substr($factivo['CodClasificacion'], 2, -6); //echo 'cod2=  '.$cod2; /// Punta 
		$cod3 = substr($factivo['CodClasificacion'], 4, -3); 
	  }else
	   if($factivo['Nivel']=='3'){
		$cod = substr($factivo['CodClasificacion'], 0, -5); //echo 'cod=  '.$cod;   //Cola  
	    $cod2 = substr($factivo['CodClasificacion'], 2, -3); //echo 'cod2=  '.$cod2; /// Punta 
		$cod3 = substr($factivo['CodClasificacion'], 4);   
	   }else
	   if($factivo['Nivel']=='2'){
	    $cod = substr($factivo['CodClasificacion'], 0, -2); //echo 'cod=  '.$cod;   //Cola  
	    $cod2 = substr($factivo['CodClasificacion'], 2); //echo 'cod2=  '.$cod2; /// Punta 
		$cod3 = ''; 
	   }
	  $CodDependencia = $factivo['CodDependencia'];
	  
      if($factivo['InternoExternoFlag']=='I'){
		  $montoIncorporacion = $factivo['MontoActivo'];
		  $montoDesincorporacion = 0;
		  $montoIncorporacionTotal = $montoIncorporacionTotal + $montoIncorporacion;
	  }else{
		  $montoDesincorporacion = $factivo['MontoActivo'];	  
		  $montoIncorporacion = 0;
	      $montoDesincorporacionTotal = $montoDesincorporacionTotal + $montoDesincorporacion;
	  }
	  
	  $pdf->SetFillColor(255, 255, 255); 
	  $pdf->SetFont('Arial', '', 9);
	  $pdf->SetWidths(array(10,10,10,10,10,13,120,30,30));
	  $pdf->SetAligns(array('C','C','C','C','C','C','L','R', 'R'));
	  $pdf->Row(array($cod, $cod2, $cod3, $factivo['CodTipoMovimiento'], '1', $factivo['CodigoInterno'], utf8_decode($factivo['Descripcion']),number_format($montoIncorporacion, 2, ',', '.'), number_format($montoDesincorporacion, 2, ',', '.')));
   }
   
   $scon03 = "select 
   					 CodPersona
			    from 
				     mastdependencias
				where     
					CodDependencia=(select ValorParam from mastparametros where ParametroClave='FIRMAINVENTARIODEP') and 
					CodOrganismo='".$factivo['CodOrganismo']."' ";
   $qcon03 = mysql_query($scon03) or die ($scon03.mysql_error());
   $fcon03 = mysql_fetch_array($qcon03);
   
   function getFirma2($CodPersona) {
	global $_PARAMETRO;
	$sql = "SELECT
				mp.Apellido1,
				mp.Apellido2,
				mp.Nombres,
				mp.Sexo,
				p1.DescripCargo AS Cargo,
				p2.DescripCargo AS CargoEncargado,
				p2.Grado AS GradoEncargado
			FROM
				mastpersonas mp
				INNER JOIN mastempleado me ON (mp.CodPersona = me.CodPersona)
				INNER JOIN rh_puestos p1 ON (me.CodCargo = p1.CodCargo)
				LEFT JOIN rh_puestos p2 ON (me.CodCargoTemp = p2.CodCargo)
			WHERE mp.CodPersona = '".$CodPersona."'";
	/*
	$sql = "SELECT
				mp.Busqueda,
				mp.Sexo,
				p1.DescripCargo AS Cargo,
				p2.DescripCargo AS CargoEncargado,
				p2.Grado AS GradoEncargado
			FROM
				mastpersonas mp
				INNER JOIN mastempleado me ON (mp.CodPersona = me.CodPersona)
				INNER JOIN rh_puestos p1 ON (me.CodCargo = p1.CodCargo)
				LEFT JOIN rh_puestos p2 ON (me.CodCargoTemp = p2.CodCargo)
			WHERE mp.CodPersona = '".$CodPersona."'";
	*/
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	##
	list($Nombre) = split("[ ]", $field['Nombres']);
	if ($field['Apellido1'] != "") $Apellido = $field['Apellido1']; else $Apellido = $field['Apellido2'];
	$NomCompleto = "$Nombre $Apellido";
	##
	if ($field['CargoEncargado'] != "") {

		if ($field['GradoEncargado'] == "99" && $_PARAMETRO['PROV99'] == $CodPersona) $tmp = "(P)"; else $tmp = "(E)";
		$Cargo = $field['CargoEncargado'];
	}
	else { $Cargo = $field['Cargo']; $tmp = ""; }
	##
	$Cargo = str_replace("(A)", "", $Cargo);
	if ($field['Sexo'] == "M") {
	} else {
		$Cargo = str_replace("JEFE", "JEFA", $Cargo);
		$Cargo = str_replace("DIRECTOR", "DIRECTORA", $Cargo);
		$Cargo = str_replace("CONTRALOR", "CONTRALORA", $Cargo);
	}
	/*
	if ($field['Sexo'] == "M") {
		$Cargo = str_replace("JEFE (A)", "JEFE", $Cargo);
		$Cargo = str_replace("DIRECTOR (A)", "DIRECTOR $tmp", $Cargo);
		$Cargo = str_replace("CONTRALOR (A)", "CONTRALOR $tmp", $Cargo);
	} else {
		$Cargo = str_replace("JEFE (A)", "JEFA", $Cargo);
		$Cargo = str_replace("DIRECTOR (A)", "DIRECTORA $tmp", $Cargo);
		$Cargo = str_replace("CONTRALOR (A)", "CONTRALORA $tmp", $Cargo);
	}
	*/
	##	consulto el nivel de instruccion
	$sql = "SELECT
				ei.Nivel,
				ngi.AbreviaturaM,
				ngi.AbreviaturaF
			FROM
				rh_empleado_instruccion ei
				INNER JOIN rh_nivelgradoinstruccion ngi ON (ngi.CodGradoInstruccion = ei.CodGradoInstruccion AND
														    ngi.Nivel = ei.Nivel)
			WHERE
				ei.CodPersona = '".$CodPersona."' AND
				ei.FechaGraduacion = (SELECT MAX(ei2.Fechagraduacion) FROM rh_empleado_instruccion ei2 WHERE ei2.CodPersona = ei.CodPersona)";
	$query_nivel = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query_nivel) != 0) $field_nivel = mysql_fetch_array($query_nivel);
	if ($field['Sexo'] == "M") $nivel = $field_nivel['AbreviaturaM']; else $nivel = $field_nivel['AbreviaturaF'];
	##
	return array($NomCompleto, $Cargo.$tmp, $nivel);
     }
	 
	 list($nombreCompleto, $cargo, $nivel) = getfirma($fcon03['CodPersona']);
	 
	 $scon04 = "select 
	                  CodPersona 
			     from 
				      mastdependencias 
				where 
				      CodDependencia='$CodDependencia'";
	 $qcon04 = mysql_query($scon04) or die ($scon04.mysql_error());
	 $fcon04 = mysql_fetch_array($qcon04);
	
	 
     list($nombreCompleto02, $cargo02, $nivel02) = getfirma($fcon04['CodPersona']);
   
      $MONTO_TOTAL_IN = number_format($montoIncorporacionTotal, 2, ',', '.');
	  $MONTO_TOTAL_DE = number_format($montoDesincorporacionTotal, 2, ',', '.');
	  
   
    $MONTO_TOTAL = number_format($MONTO_TOTAL,2,',','.');$pdf->Ln(4);
	$pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell(25,10,'Total de Bienes: '.$ractivo,0,0,'L'); 
	$pdf->Cell(159,10,'Total=',0,0,'R'); 
	$pdf->Cell(30,10,$MONTO_TOTAL_IN,0,0,'R'); $pdf->Cell(30,10,$MONTO_TOTAL_DE,0,1,'R');$pdf->Ln(3);
	
/*$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(202, 202, 202); $pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->Cell(80,5,'_____________________________',0,0,'C');$pdf->Cell(100,5,'RECIBI CONFORME: _____________________________',0,1,'C');
	$pdf->Cell(80,2,$nivel.' '.$nombreCompleto,0,0,'C');    $pdf->Cell(127,2,$nivel02.' '.$nombreCompleto02,0,1,'C');
	$pdf->Cell(80,3,$cargo,0,0,'C');
	$pdf->Cell(25,3,'',0,0,'C');                             $pdf->MultiCell(80,3,$cargo02,0,'C');*/
	
	$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(202, 202, 202); $pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->Cell(120,5,'',0,0,'C');$pdf->Cell(100,5,'RECIBI CONFORME: _____________________________',0,1,'C');
	$pdf->Cell(120,2,'',0,0,'C');    $pdf->Cell(127,2,$nivel02.' '.$nombreCompleto02,0,1,'C');
	$pdf->Cell(120,3,'',0,0,'C');
	$pdf->Cell(25,4,'',0,0,'C');                             $pdf->MultiCell(80,4,utf8_decode($cargo02),0,'C');
	
	
$pdf->Output();
?>  