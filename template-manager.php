<?php
/*
 * Sencillo Sistema de manejo de Plantillas para PHP 
 * @autor Ricardo D. Quiroga - L2Radamanthys
 * @licence GPL2
 * @package simple-template-php-manager
 * 
 * Nota: todas las funciones que comiencen con '_' retornan el contenido en ves
 * de volcarlo directamente a la salida
 *
 * Ultima Actualizacion: 30/06/2013
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


function _html_format($text="", $key="p", $argv="", $end_tag=True, $xml=False) {
    if ($end_tag) {
        $cad = "<".$key." ".$argv." >".$text."</".$key.">\n";
    }
    elseif ($key == 'input' or $key == 'img' or $key == 'br' or $key == 'hr') {
        $cad = "<".$key." ".$argv." />".$text."\n";
    }
    else {
        $cad = "<".$key." ".$argv." >".$text."\n";
    }
    return $cad;
}


/*
 * Dibuja un tag de cierre
 * 
 * @param string $key nombre del tag, para que sea valido tiene q ser de tipo HTML
 */ 
function tend($key) {
    echo "</".$key.">\n";
}


function _tend($key) {
    return "</".$key.">\n";
}


/*
 * Dibuja varios saltos de linea HTML osea etiquetas <br />
 * 
 * @param integer @num numero de saltos de lineas a dibujar, por defecto es 1
 */ 
function tbr($num=1) {
    for ($i=1; $i <= $num; $i++) {
        echo '<br />';
    }
    echo "\n";
}


function _tbr($num=1) {
    $cad= "";
    for ($i=1; $i <= $num; $i++) {
        $cad .= "<br />\n";
    }
    return $cad;
}


/**
 * Dibuja un <img> tag
 * 
 * @param string $scr ruta de la imagen
 * @param string $argv parametros opcionales
 */
function timg($src,  $argv="") {
    html_format('','img', 'src="'.$src.'" '.$argv, False, True);
} 

function _timg($src,  $argv="") {
    return _html_format('','img', 'src="'.$src.'" '.$argv, False, True);
} 


/* 
 *  Carga una plantilla, sin aplicar remplazo de TAGS 
 *
 *  En si es mas un wraper para cargar archivos que un cargador de plantillas
 *
 * @param string $file_path ruta de la plantilla 
 */
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


function obj_key_val($content, $result, $dict) {
    foreach($result as $res) {
        $tx = $res;
        $or = $res;
        $tx = str_replace(' ', '', $tx); #quitar espacio en blanco
        $tx = str_replace('<#', '', $tx); #quitar prefijo
        $tx = str_replace('#>', '', $tx); #quitar posfijo
        $ar = explode('::', $tx);
        if (isset($dict[$ar[0]])) {
            $val = $dict[$ar[0]]->$ar[1]();
            $content = str_replace($or, $val, $content);
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

            if (preg_match_all('/<#(.+?)::(.+?)#>/', $content, $result) != 0) {
                $content = obj_key_val($content, $result[0], $dict);
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


function _html_display($file_path, $dict=NULL, $value=NULL) {
    $content = _template_get($file_path);
    if($content) {
        $content = _include_sub_templates($content);
        if ($dict != NULL) {
            if ($value != NULL) {
                $dict = array ($dict => $value);
            }
            
            if (preg_match_all('/<#(.+?)::(.+?)#>/', $content, $result) != 0) {
                $content = obj_key_val($content, $result[0], $dict);
            }
            
            foreach ($dict as $key => $value) {
                //if (!is_object($value)) {
                $content = str_replace('<#'.$key.'#>', $value, $content);
                $content = str_replace('<# '.$key.' #>', $value, $content);
                //}
            }


        }
        
        $content = preg_replace('/<#(.*)#>/', '', $content);
        $content = preg_replace('/<# (.*) #>/', '', $content);
    }
    return $content;
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
 * @param array $data resultado de la consulta
 * @param array $field dicionario con los nombres de los campos(key), y titulos(value) para la tabla
 * @param string $caption titulo opcional de la tabla
 * @param string $table_css nombre de la clase (CSS) para la tabla en gral
 * @param string $alter_row_css nombre de la clase (CSS) para las columnas impares
 * @param string $params parametros opcionales para incristacion directa en el HTML
 * @param string $extra_name nombre de la columna extra, normalmente para opciones 
 * @param string $extra contenido de la colummna extra
 * @param array $dict array asociativo con claves opcionales, para los datos de la colummna extra
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
    while($reg = mysql_fetch_assoc($data)) {
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
            $cad .= _html_display($extra, array_merge($dict, $reg));
            echo '</td>';
        }
        echo '</tr>';
        $cont += 1; //para hoja stilo de las colummnas alternativas
    }
    echo '</table>';
}



function _html_display_sql($data, $fields, $caption=NULL, $table_css=NULL, $alter_row_css=NULL, $params='', $extra_name="", $extra=NULL, $dict=NULL) {
    $cad = "";
    if ($table_css != NULL) {
        $cad .= '<table class="'.$table_css.'" '.$params.' >';
    }    
    else {
        $cad .= '<table '.$params.' >';
    }
    
    //titulo tabla
    if ($caption != NULL) {
        $cad .= '<caption>'.$caption.'</caption>';
    }
    
    //columnas nombres
    $cad .= '<tr>';
    foreach($fields as $value) {
        $cad .= '<th>'.$value.'</th>';
    }
    if ($extra != NULL) {
        $cad .= '<th>'.$extra_name.'</th>';
    }
    $cad .= '</tr>';
    
    //campos
    $cont = 0;
    while($reg = mysql_fetch_assoc($data)) {
        if ($cont % 2 == 0) { 
            $cad .= '<tr class="'.$alter_row_css.'">';
        }
        else {
            $cad .= '<tr>';
        }
        
        foreach($fields as $key => $value) {
            #if (isset($fields['rpr_'.$key])) {
            #    $m_dict = $fields['rpr_'.$key];
            #    $cad .= '<td>'.$m_dict[$reg[$key]].'</td>';    
            #}
            #else {
            $cad .= '<td>'.$reg[$key].'</td>';    
            #}
        }
        if ($extra != NULL) {
            $cad .= '<td>';
            $cad .= _html_display($extra, array_merge($dict, $reg));
            $cad .= '</td>';
        }
        $cad .= '</tr>';
        $cont += 1;
    }
    $cad .= '</table>';
    return $cad;
}


/**
    Similar a html_display_sql solo que en ves de trabajar con registros de consultas 
    MySQL usa arrays multidimencionales
*/
function html_display_matrix($data, $fields, $caption=NULL, $table_css=NULL, $alter_row_css=NULL, $params='', $extra_name="", $extra=NULL, $dict=NULL) {
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
            $cad .= _html_display($extra, array_merge($dict, $reg));
            echo '</td>';
        }
        echo '</tr>';
        $cont += 1; //para hoja stilo de las colummnas alternativas
    }
    echo '</table>';
}


function _html_display_matrix($data, $fields, $caption=NULL, $table_css=NULL, $alter_row_css=NULL, $params='', $extra_name="", $extra=NULL, $dict=NULL) {
    $cad = "";
    if ($table_css != NULL) {
        $cad .= '<table class="'.$table_css.'" '.$params.' >';
    }    
    else {
        $cad .= '<table '.$params.' >';
    }
    
    //titulo tabla
    if ($caption != NULL) {
        $cad .= '<caption>'.$caption.'</caption>';
    }
    
    //columnas nombres
    $cad .= '<tr>';
    foreach($fields as $value) {
        $cad .= '<th>'.$value.'</th>';
    }
    if ($extra != NULL) {
        $cad .= '<th>'.$extra_name.'</th>';
    }
    $cad .= '</tr>';
    
    //campos
    $cont = 0;
    foreach($data as $reg) {
        if ($cont % 2 == 0) { 
            $cad .= '<tr class="'.$alter_row_css.'">';
        }
        else {
            $cad .= '<tr>';
        }
        
        foreach($fields as $key => $value) {
            $cad .= '<td>'.$reg[$key].'</td>';    
        }
        if ($extra != NULL) {
            $cad .= '<td>';
            $cad .= _html_display($extra, array_merge($dict, $reg));
            $cad .= '</td>';
        }
        $cad .= '</tr>';
        $cont += 1;
    }
    $cad .= '</table>';
    return $cad;
}


/*
 * Tag de inclucion para hojas de Estilo
 */
function css_include_tag($src) {
    return '<link href="'.$src.'" rel="stylesheet" type="text/css" />';
}





















?>
