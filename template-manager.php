<?php
/*
 * Sencillo Sistema de manejo de Plantillas, para PHP 
 * @autor Ricardo D. Quiroga - L2Radamanthys
 * @licence GPL2
 * @package simple-template-php-manager
 */ 
 
 
/* ruta del directorio raiz de plantillas, toda plantilla que desee cargarse
 * debe estar en el correspondiente directorio o subdirectorio de la misma
*/
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


function timg($src,  $argv="") {
	html_format('','img', 'src="'.$src.'" '.$argv, False, True);
} 


function _template_get($file_path) {
	global $TEMPLATE_PATH;
	$file_path = $TEMPLATE_PATH.$file_path;
	if (file_exists($file_path)) {
		$content = file_get_contents($file_path);
		return $content;
	}
	
	else { 
		html_format('Error: No existe: '.$file_path, 'p', 'style="color:#F00;background: #FFFF00; boder: 1px solid #F00"'); 
		return False;
	} 
}


function _include_sub_templates($content) {
	$result = preg_match_all('/<&\s(.*)\s&>/', $content, $salida);
	if ($result) {
		foreach($salida[1] as $tmp_path) {
			//$sub_cont = _template_get($tmp_path);
			$sub_cont_child = _template_get($tmp_path);
			$sub_cont = _include_sub_templates($sub_cont_child);
			if ($sub_cont) {
				$content = str_replace('<& '.$tmp_path.' &>', $sub_cont, $content);
			}
			else {
				$content = str_replace('<& '.$tmp_path.' &>', '', $content);
			}
		}
	}
	return $content;
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
	$content = _template_get($file_path);
	if($content) {
		$content = _include_sub_templates($content);
		if ($dict != NULL) {
			if ($value != NULL) {
				$dict = array ($dict => $value);
			}			
			foreach ($dict as $key => $value) {
				$content = str_replace('<#'.$key.'#>', $value, $content);
				$content = str_replace('<# '.$key.' #>', $value, $content);
			}
		}
		$content = preg_replace('/<#(.*)#>/', '', $content);
		$content = preg_replace('/<# (.*) #>/', '', $content);
	}
	echo $content;
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



























?>
