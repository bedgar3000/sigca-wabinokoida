// -------------------------------------------------------------- ####
//     Funcion para validar al introducir fechas
function validFecha(id, valor, AnioActual, MesActual, DiaActual){
  var id= document.getElementById(id).value; 
  var cantidad= id.length;

  var valor2 = id.split('-'); 
  var Dia = valor2[0]; 
  var Mes = valor2[1]; 
  var Anio = valor2[2];



  if(cantidad<10) alert('Formato fecha Incorrecta en '+valor+ '!!!');
  else if(Dia>'31') alert('Corrija el Día de fecha introducida en '+valor+ '!!!');
  else if(Mes>'12') alert('Corrija el Mes de fecha introducida en '+valor+ '!!!');
  else if(Anio>AnioActual) alert('Corrija Año de fecha introducida en '+valor+ '!!!');


 /* else if(Anio<1990 || Anio>AnioActual) alert('Corrija Año de fecha introducida en '+valor+ '!!!');
  else if(Mes<01 || Mes>'12') alert('Corrija Mes de fecha introducida en '+valor+ '!!!');
  else if(Anio==AnioActual && Mes>MesActual) alert('Corrija Mes de fecha introducida en '+valor+ '!!!');
  else if(Anio==AnioActual && Mes==MesActual && Dia>DiaActual) alert('Corrija Dia de fecha introducida en '+valor+ '!!!');
  else if(Dia<01 || Dia>'31') alert('Corrija Mes de fecha introducida en '+valor+ '!!!');*/
}
// -------------------------------------------------------------- ####
//
// -------------------------------------------------------------- ####
//
// -------------------------------------------------------------- ####
//
// -------------------------------------------------------------- ####
//
// -------------------------------------------------------------- ####
//