<?php

/*
 * Sencillo Sistema de manejo de Plantillas, para PHP 
 * @autor Ricardo D. Quiroga - L2Radamanthys
 * @licence GPL2
 * @package simple-template-php-manager
 */ 
 
 
//ruta del directorio raiz de plantillas
$TEMPLATE_PATH = 'templates/';


/*
 * Formatea un texto para dejar un salida HTML mediante tags
 * 
 * ejemplo:
 * >> format('hola mundo', 'h1', 'class="title"')
 * imprimira en el documento:
 * <h1 class="title">hola mundo</h1>
 * 
 * @param string $text texto que normalmente se coloca entre los tags
 * @param string $key identificador del tag html
 * @param strign $argv argumentos opcionales dentro del tag, como definicion de estilos y demas
 * @param boolean $end_tag especificar si se escribira el tag de cierre
 * @param boolean $xml formatea como XML, para tags sin cierre 
 */ 
function html_format($text="", $key="p", $argv="", $end_tag=True, $xml=False) {
	if ($end_tag) {
		$cad = "<".$key." ".$argv." >".$text."</".$key.">\n";
	}
	elseif ($key == 'input' or $key == 'img' or $key == 'br' or $key == 'hr') {
		$cad = "<".$key." ".$argv." />".$text."\n";
	}
	else {
		$cad = "<".$key." ".$argv." >".$text."\n";
	}
	echo $cad;
}



function timg($src,  $argv="") {
	html_format('','img', 'src="'.$src.'" '.$argv, False, True);
} 


/*
 * Muestra una plantilla a partir de un archivo de texto
 * 
 * carga el contenido de un archivo de texto, normalmente formateado
 * como HTML para ser volcado en la pagina destino
 * NOTA: es recomendable usar la funcion display() que es un envoltorio 
 * de esta y otras, aunque esta permite cargar plantillas sin tag de remplazo
 * que no se encuentras en el paht del 
 *  
 * @params string $file_path ruta del archivo contenedor de plantilla
 */ 
function draw_txt_block($file_path) {
	$content = file_get_contents($file_path);
	echo $content;
}


/*
 * Similar a draw_txt_block solo que agrega la posibilidad de remplazar 
 * tags definidos, por otro texto
 * 
 * Los tags dentro de la plantila se definen con el siguiente formato
 * <#[key]#> donde el elemnto [key] puede ser cualquier texto sin espacios 
 * siempre y cuando se encuentre encerrado por '<#' '#>' el mismo tiene que 
 * ser definido en el dicionario de remplazo sino sera borrado a la hora de 
 * parsear el documento.
 * 
 * 
 * @param string $file_path ruta del archivo contenedor de plantilla
 * @param array $dict dicionario asociativo, contenedor de las claves y valores
 * 
 */ 
function drawn_and_assign_txt_block($file_path, $dict) {
	$content = file_get_contents($file_path);
	foreach ($dict as $key => $value) {
		$content = preg_replace('/<#'.$key.'#>/', $value, $content);
		//echo '/[$'.$key.'$] -> '.$value;
	}
	//quita los tags que no fueron definidos en el array de reemplazo
	preg_replace('/<#(.*)#>/', '', $content);
	echo $content;
}


/*
 * Permite cargar Plantillas HTML
 * 
 * Envoltorio usado para los metodos drawn_txt_block() y  
 * drawn_and_assign_txt_block(), trabaja con un path relativo al definido
 * por TEMPLATE_PATH
 * 
 * @param string $file_path ruta del archivo contenedor de plantilla
 * @param array $dict [Opcional] dicionario asociativo, contenedor de las claves y valores
 * 
 */
function html_display($file_path, $dict=NULL, $value=NULL) {
	global $TEMPLATE_PATH;
	$file_path = $TEMPLATE_PATH.$file_path;
	
	if (file_exists($file_path)){ 
		if ($dict != NULL) {
			if ($value != NULL) {
				$dict = array ($dict => $value);
			}
			drawn_and_assign_txt_block($file_path, $dict);
		}
	
		else {
			draw_txt_block($file_path);
		}
	}
	else{ 
		html_format('Error: No existe: '.$file_path, 'p', 'style="color:#F00;background: #FFFF00; boder: 1px solid #F00"'); 
	} 
	

}


/*
 * Muestra una consulta SQL en formato tabla
 * 
 * Formatea el resultado de la ejecucion mediante una elemento HTML 
 * <TABLE> de una consulta SQL del tipo SELECT, este metodo no retorna 
 * nada, ya que esta diseñado para escribir sobre el documento HTML que 
 * se mostrara desde el servidor.
 * NOTA: los unicos parametros obligatorios son $data y $fields el resto 
 * son opcionales
 * 
 * @param array $data resultado de la consultaa
 * @param array $field dicionario con los nombres de los campos(key), y titulos(value) para la tabla
 * @param string $caption titulo opcional de la tabla
 * @param string $table_css nombre de la clase (CSS) para la tabla en gral
 * @param string $alter_row_css nombre de la clase (CSS) para las columnas impares
 * @param string $params parametros opcionales para incristacion directa en el HTML
 * 
 */ 
function html_display_sql($data, $fields, $caption=NULL, $table_css=NULL, $alter_row_css=NULL, $params='', $extra_name="", $extra=NULL, $dict=NULL) {
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
	if ($extra != NULL) {
		echo '<th>'.$extra_name.'</th>';
	}
	echo '</tr>';
	
	//campos
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
		if ($extra != NULL) {
			echo '<td>';
			html_display($extra, $dict);
			echo '</td>';
		}
		echo '</tr>';
		$cont += 1;
	}
	echo '</table>';
}


/*
 * Dibuja un tag de cierre
 * 
 * @param string $key nombre del tag, para que sea valido tiene q ser de tipo HTML
 */ 
function tend($key) {
	echo "</".$key.">\n";
}


/*
 * Dibuja varios saltos de linea HTML osea etiquetas <br />
 * 
 * @param integer @num numero de saltos de lineas a dibujar, por defecto es 1
 */ 
function tbr($num=1) {
	for ($i=1; $i < $num; $i++) {
		echo '<br />';
	}
	echo "\n";
}



?>
