<?php
/*
JRL:
acciones posibles :
	printDocumentoFiscal			parametros		"tipo_documento;numero_documento"		devuelve 0= error;   numero= numero secuencia fiscal
	printX							      parametros		0										devuelve 0= error;   1= genereadoexitosamente
	printZ							      parametros		0										devuelve 0= error;   1= genereadoexitosamente
	UltimaDocumento					  parametros		tipo_documento   						devuelve 0= error;   numero= ultimo numero secuencia fiscal
*/
function imprimir($td, $nd, $ac){

$tipo_documento = $td; //0 factura, 1 nota credito, 2 nota debito.
$numero_documento = $nd; //"DIPO171212-0009"

//constrir los parametros para el ejecutable

//direccion del ejecutable
$exe = 'C:\xampp\htdocs\digicel\Debug\ConsoleApp1.exe';

//accion
$accion = $ac;//string nombre de la funcion a ejecutar "printDocumentoFiscal"

//$parametros = "0;DIPO171212-0009";//string separados por punto y coma(;)
//parametros de la accion
$parametros = $tipo_documento.";".$numero_documento;

// $accion = "UltimaDocumento";
// $parametros = "1" ;

$dir = $exe ." ".$accion." ".$parametros;

//ejecucion

//echo "accion enviada</br>";

 exec($dir, $output, $return);
//echo "Dir returned $return, and output:\n";

if ($return  == 0	)
{
	echo "error al ejecutar la accion.</br>";

}else{
	//echo "accion ejecutada exitosamente </br>";
	echo "Numero de secuencia fiscal: ".$return;
  return $return;
}

}

?>
