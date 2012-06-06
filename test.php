
<style type="text/css">

* {
	font-family: Sans, Verdana;
}

.tbl {
	font-family: Verdana;
	font-size: 87%;
	border: 1px solid #000;
	border-spacing: 0;
	border-collapse: collapse;
	width: 800px;
}

.tbl caption {
	font-weight: bold;
	background-color: #0080C0;
	color: #FFF;
	padding: 8px;
	text-align: center;
	border: 1px solid #000;
	border-bottom: 0px;
}

.tbl th {
	background-color: #0080C0;
	color: #FFF;
	text-align: center;
	padding: 5px;
	border: 1px solid #000;
}

.tbl td{
	//background-color: #F7F5EA;
	padding: 5px;
	border: 1px solid #000;
	
}

.tbl tr{
	background-color: #F7F5EA;
}

.tbl tr:hover{
	background-color: #00FF40;
}

.alter {
	background-color: #FAF8B8 !important;
}

.tbl tr:hover{
	background-color: #00FF40;
}

.alter:hover {
	background-color: #00FF40 !important;
}

</style>

<?php

include('template-manager.php');

$camp = array( 'nomb' => 'Nombre', 'apell' => 'Apellido');
$data = array(
	array( 'nomb' => 'juan', 'apell' => 'jose'),
	array( 'nomb' => 'pablo', 'apell' => 'hola'),
	array( 'nomb' => 'daniel', 'apell' => 'sonse')
);

html_format('Hola Mundo Cruel', 'h1');
timg('http://www.google.com.ar/images/srpr/logo3w.png');

html_display_sql($data, $camp, 'Tabla Prueba', 'tbl', 'alter');

tbr(2);
html_format('Con columna Extra', 'label');
$dic = array('MSJ1'=>'Hola Mundo..!', 'MSJ2' => 'Adios Mundo Cruel :D' );
html_display_sql($data, $camp, 'Tabla Prueba 2', 'tbl', 'alter', '', 'opciones', 'row.tpl', $dic);
tbr(2);
tpl_display('tmp.tpl');

?>
