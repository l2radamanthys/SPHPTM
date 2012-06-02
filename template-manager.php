<?php

/*
 * Sencillo Sistema de Plantillas, para PHP 
 * Autor: Ricardo D. Quiroga
 * Licencia: GPL2
 * 
 */ 
 
 
//ruta del directorio raiz de plantillas
$TEMPLATE_PATH = 'templates/';


/*
 * Muestra una consulta SQL en formato tabla, osea normalmente resultado 
 * de una consulta SELECT, 
 * 
 * NOTA: los unicos parametros obligatorios son $data y $fields el resto 
 * son opcionales
 * 
 * @params
 * array $data : resultado de la consultaa
 * array $field : dicionario con los nombres de los campos(key), y titulos(value) para la tabla
 * string $caption : titulo opcional de la tabla
 * string $table_css : nombre de la clase (CSS) para la tabla en gral
 * string $alter_row_css : nombre de la clase (CSS) para las columnas impares
 * string $params : parametros opcionales para incristacion directa en el HTML
 */ 
function display_consulta_sql($data, $fields, $caption=NULL, $table_css=NULL, $alter_row_css=NULL, $params='') {
	if ($table_css != NULL) {
		echo '<table class="'.$table_css.'" '.$params.' >';
	}	
	else {
		echo '<table '.$params.' >';
	}
	
	//titulo tabla
	if ($caption != NULL) {
		echo '<caption>'.$caption.'</caption>';
	}
	
	
	//columnas nombres
	echo '<tr>';
	foreach($fields as $value) {
		echo '<th>'.$value.'</th>';
	}
	echo '</tr>';
	
	$cont = 0;
	foreach($data as $reg) {
		if ($cont % 2 == 0) { 
			echo '<tr class="'.$alter_row_css.'">';
		}
		else {
			echo '<tr>';
		}
		foreach($fields as $key => $value) {
			echo '<td>'.$reg[$key].'</td>';	
		}
		echo '</tr>';
		$cont += 1;
	}
	echo '</table>';
	
}


/*
 * Formatea un texto para dejar un salida HTML mediante tags
 * por ejemplo
 * 
 * >> format('hola mundo', 'h1', 'class="title"')
 * imprimira en el documento:
 * <h1 class="title">hola mundo</h1>
 * 
 */ 
function format($text="", $key="p", $argv="", $end_tag=True) {
	if ($end_tag) {
		$cad = "<".$key." $argv>".$text."</".$key.">\n";
	}
	else {
		$cad = "<".$key." $argv>".$text."\n";
	}
	echo $cad;
}

?>
