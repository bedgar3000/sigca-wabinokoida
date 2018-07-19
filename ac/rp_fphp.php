<?
//include ("fphp.php");
//// ---------------------------------------------------------- */ 
//// REPORTE LIBRO DIARIO
function getContabilidad( $fContabilidad, $opt){
   //connect();
	switch ($opt) {
		case 0:
			$sql="select CodContabilidad, Descripcion FROM ac_contabilidades ";
			$qry=mysql_query($sql) or die ($sql.mysql_error());
			$row = mysql_num_rows($qry);
			if($row!=0){ 
			  for($i=0; $i<$row; $i++){
				$field=mysql_fetch_array($qry);
				if($field['0']==$fContabilidad)echo "<option value='".$field[0]."' selected>".htmlentities($field[1])."</option>";
				else echo "<option value='".$field[0]."'>".htmlentities($field[1])."</option>";
			  }
			}
			break;
	}
}
//// ---------------------------------------------------------- */ 
function getObtenerSaldo($tabla, $Rubro, $fContabilidad, $forganismo, $fanio, $fmes, $filtro1){
 //connect();
   $sql_a= "select a.*,
			 	   b.Descripcion,
			       b.Nivel
		      from ac_balancecuenta a 
			       inner join $tabla b on (b.CodCuenta=a.CodCuenta and b.Rubro='".$Rubro."') 
	         where 
				   a.Anio= '".$fanio."' and 
				   a.CodContabilidad= '".$fContabilidad."' and 
				   a.CodOrganismo= '".$forganismo."' $filtro1
		  order by a.CodCuenta"; //echo $sql_a;
   $qry_a= mysql_query($sql_a) or die ($sql_a.mysql_error());
   $row_a= mysql_num_rows($qry_a);

   	 $saldo=0;
     for($i=0; $i<$row_a; $i++){
          
          $field_a= mysql_fetch_array($qry_a);
   
	  
			if($fmes>='01') $saldo+= $field_a['SaldoInicial'] + $field_a['SaldoBalance01']; 
			if($fmes>='02') $saldo+= $field_a['SaldoBalance02']; 
			if($fmes>='03') $saldo+= $field_a['SaldoBalance03']; 
			if($fmes>='04') $saldo+= $field_a['SaldoBalance04']; 
			if($fmes>='05') $saldo+= $field_a['SaldoBalance05']; 
			if($fmes>='06') $saldo+= $field_a['SaldoBalance06']; 
			if($fmes>='07') $saldo+= $field_a['SaldoBalance07']; 
			if($fmes>='08') $saldo+= $field_a['SaldoBalance08'];
			if($fmes>='09') $saldo+= $field_a['SaldoBalance09']; 
			if($fmes>='10') $saldo+= $field_a['SaldoBalance10']; 
			if($fmes>='11') $saldo+= $field_a['SaldoBalance11']; 
			if($fmes=='12') $saldo+= $field_a['SaldoBalance12'];
      }

     return $saldo;
}
//// ---------------------------------------------------------- */ 
function getObtenerSaldoGP($tabla, $Rubro, $CodCuenta, $fContabilidad, $forganismo, $fanio, $fmes, $filtro1){
 //connect();
   $sql_a= "select a.*,
			 	   b.Descripcion,
			       b.Nivel
		      from ac_balancecuenta a 
			       inner join $tabla b on (b.CodCuenta=a.CodCuenta and b.Rubro='".$Rubro."' ) 
	         where 
				   a.Anio= '".$fanio."' and 
				   a.CodContabilidad= '".$fContabilidad."' and 
				   a.CodOrganismo= '".$forganismo."' and 
				   b.CodCuenta= '".$CodCuenta."' $filtro1
		  order by a.CodCuenta"; //echo $sql_a;
   $qry_a= mysql_query($sql_a) or die ($sql_a.mysql_error());
   $row_a= mysql_num_rows($qry_a);

   	 $saldo=0;
     for($i=0; $i<$row_a; $i++){
          
          $field_a= mysql_fetch_array($qry_a);
   
	  
			if($fmes>='01') $saldo+= $field_a['SaldoInicial'] + $field_a['SaldoBalance01']; 
			if($fmes>='02') $saldo+= $field_a['SaldoBalance02']; 
			if($fmes>='03') $saldo+= $field_a['SaldoBalance03']; 
			if($fmes>='04') $saldo+= $field_a['SaldoBalance04']; 
			if($fmes>='05') $saldo+= $field_a['SaldoBalance05']; 
			if($fmes>='06') $saldo+= $field_a['SaldoBalance06']; 
			if($fmes>='07') $saldo+= $field_a['SaldoBalance07']; 
			if($fmes>='08') $saldo+= $field_a['SaldoBalance08'];
			if($fmes>='09') $saldo+= $field_a['SaldoBalance09']; 
			if($fmes>='10') $saldo+= $field_a['SaldoBalance10']; 
			if($fmes>='11') $saldo+= $field_a['SaldoBalance11']; 
			if($fmes=='12') $saldo+= $field_a['SaldoBalance12'];
      }

     return $saldo;
}
//// ---------------------------------------------------------- */ 


?>