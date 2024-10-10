<?php
declare(strict_types=1);


/**
 * Sección en la que, tras la comprobación de haber enviado el formulario, saneamos, validamos y procesamos el contenido aportado por el usuario
 */
//Comprobamos si se ha enviado algo al enviar el formulario
if (!empty($_POST)) {
  //Saneamos el texto introducido por el usuario para tenerlo disponible para mostrar en la vista
  $data['input_texto'] = filter_var($_POST['texto'], FILTER_SANITIZE_SPECIAL_CHARS);

  //Validamos el texto introducido para conocer si cumple los criterios necesarios para su procesamiento
  $errores = checkForm($_POST['texto']);

  //Teniendo en cuenta la aparición de errores, si los hay los mostramos y sino se pasa al procesamiento
  if ($errores > 0) {
    $data['errores'] = $errores;
  } else {

  }

}
/**
 * Función que comprueba la presencia de errores teniendo en cuenta:
 * - Si se ha introducido texto
 * - Si el JSON es válido
 * - Si el texto tiene el formato establecido en la práctica
 *
 * @param string $texto texto recibido a través del textarea
 * @return array conjunto de errores detectados
 */
function checkForm(string $texto): array
{
  $errores = [];

  //Comprobamos si el texto enviado tiene contenido
  if (empty($texto)) {
    $errores['texto'][] = 'Inserte un texto a analizar';
  } else {
    //Decodificar un texto en json
    $json = json_decode($texto, true);

    //Comprobamos si hay errores en la decodificación
    if (is_null($json)) {
      $errores['texto'][] = 'JSON incorrecto: Inserte un texto con formato JSON válido';
    } else {
      //comprobamos que se introduzca un conjunto de elementos
      if (!is_array($json)) {
        $errores['texto'][] = 'Contenido inválido: debe introducir un conjunto de asignaturas';
      } else {

        //Comprobamos si la estructura del JSON tiene el formato indicado en la práctica
        foreach ($json as $asignaturas => $alumnos) {

          //Comprobamos que el primer elemento es un tipo texto seguido de un array
          if (!is_string($asignaturas)) {
            $errores['texto'][] = "El nombre de la asignatura '$asignaturas' no es válido, debe ser tipo texto";
          }
          if (!is_array($alumnos)) {
            $errores['texto'][] = "Error en el contenido de la asignatura '$asignaturas'. Debe tener un conjunto de alumnos con sus notas";
          } else {

            foreach ($alumnos as $alumno => $notas) {
              //Comprobamos que el segundo elemento es un tipo texto seguido de un array
              if (!is_string($alumno)) {
                $errores['texto'][] = "El nombre del alumno '$alumno' no es válido, debe ser tipo texto";
              }
              if (!is_array($notas)) {
                $errores['texto'][] = "Error en el contenido del alumno '$alumno'. Debe tener un conjunto de notas";
              } else {

                foreach ($notas as $nota) {
                  //Por cada nota
                  if (!is_numeric($nota)) {
                    $errores['texto'][] = "Error en la nota '$nota'. Debe ser un número válido";
                  }
                }//foreach notas
              }
            }//foreach alumnos
          }
        }//foreach asignaturas
      }
    }
  }
    return $errores;
}

/*
* Llamamos a las vistas
*/
include 'views/templates/header.php';
include 'views/lara_comesana_varela.view.php';
include 'views/templates/footer.php';
?>