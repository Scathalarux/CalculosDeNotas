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
  if (count($errores) > 0) {
    $data['errores'] = $errores;
  } else {
    //Decodificar un texto en json
    $data['json'] = json_decode($_POST['texto'], true);
    //Hacemos el cálculo de los datos pedidos
    calculosNotas($data['json']);
    //Hacemos una lista con la distribución de alumnos
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

        foreach ($json as $asignaturas => $alumnos) {

          //Comprobamos que el primer elemento es un tipo texto no vacío seguido de un array
          if (!is_string($asignaturas) || mb_strlen(trim($asignaturas)) < 1) {
            $errores['texto'][] = "El nombre de la asignatura '$asignaturas' no es válido, debe ser tipo texto";
          }
          if (!is_array($alumnos)) {
            $errores['texto'][] = "Error en el contenido de la asignatura '$asignaturas'. Debe tener un conjunto de alumnos con sus notas";
          } else {

            foreach ($alumnos as $alumno => $notas) {
              //Comprobamos que el segundo elemento es un tipo texto no vacío seguido de un array
              if (!is_string($alumno) || mb_strlen(trim($alumno)) < 1) {
                $errores['texto'][] = "El nombre del alumno '$alumno' en '$asignaturas' no es válido, debe ser tipo texto";
              }
              if (!is_array($notas)) {
                $errores['texto'][] = "Error en el contenido del alumno '$alumno' en '$asignaturas'. Debe tener un conjunto de notas";
              } else {

                foreach ($notas as $nota) {
                  //Comprobamos que cada una de las notas introducidas es de tipo numérico
                  if (!is_numeric($nota)) {
                    $errores['texto'][] = "Error en la nota '$nota' del alumno '$alumno' en '$asignaturas'. Debe ser un número válido";
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

/**
 * Función que calcula la nota media de un alumno
 *
 * @param array $notas notas del alumno con las que se quiere hacer la media
 * @return float nota media de tipo decimal
 */
function calcMediaAlumno(array $notas): float
{
  $notasAlumno = 0;
  foreach ($notas as $nota) {
    $notasAlumno += $nota;
  }//foreach $notas

  return round($notasAlumno / count($notas), 2);
}

/**
 * Función que para cada asignatura calcula:
 * - Nota media
 * - Número de suspensos
 * - Número de aprobados
 * - La nota máxima y quién la sacó
 * - La nota mínima y quién la sacó
 *
 * @param array $json JSON decodificado proveniente del texto introducido por el usuario
 * @return array conjunto de datos pedidos y formateados según se pide para cada asignatura
 */
function calculosNotas(array $json): array
{
  $resultados = [];
  foreach ($json as $asignaturas => $alumnos) {
    $notasAsignatura = 0;
    $asignatura['suspensos'] = 0;
    $asignatura['aprobados'] = 0;
    $max = [];
    $max['alumno'] = 'nobody';
    $max['nota'] = -1;

    $min = [];
    $min['alumno'] = 'nobody';
    $min['nota'] = 11;


    foreach ($alumnos as $alumno => $notas) {

      $mediaAlumno = calcMediaAlumno($notas);
      $notasAsignatura += $mediaAlumno;


      //Según la media que tiene cada alumno, lo clasificamos como aprobado o suspenso en esa asignatura
      if ($mediaAlumno >= 5) {
        $asignatura['aprobados']++;
      } else {
        $asignatura['suspensos']++;
      }

      //Comprobamos si el alumno tiene la nota máxima
      if ($mediaAlumno > $max['nota']) {
        $max['nota'] =
          number_format($mediaAlumno, 2, ',');
        $max['alumno'] = $alumno;
      }

      //Comprobamos si el alumno tiene la nota mínima
      if ($mediaAlumno < $min['nota']) {
        $min['nota'] = number_format($mediaAlumno, 2, ',');
        $min['alumno'] = $alumno;
      }

    }//foreach $alumnos
    $asignatura['media'] = number_format((round($notasAsignatura / count($alumnos), 1)), 2, ',');

    //Nos aseguramos de que hay alumnos a los que analizar y clasificar
    if (!empty($alumnos)) {
      $asignatura['max'] = $max;
      $asignatura['min'] = $min;
    } else {
      $asignatura['max'] = [
        'alumno' => '-',
        'nota' => '-',
      ];
      $asignatura['min'] = [
        'alumno' => '-',
        'nota' => '-',
      ];
    }
    $resultados[$asignaturas] = $asignatura;
  }//foreach $json
  return $resultados;
}

/**
 * Función que clasifica al alumnado teniendo en cuenta el número de aprobados y suspensos, de forma:
 * - Lista con 0 suspensos
 * - Lista con 1 o más suspensos
 * - Lista de los que promocionan (máx 1 suspenso)
 * - Lista de los que no promocionan (más de 1 suspenso)
 *
 * @param array $json JSON decodificado proveniente del texto introducido por el usuario
 * @return array conjunto de listados solicitados
 */
function listadoAlumnos(array $json): array
{
  foreach ($json as $asingaturas => $alumnos) {
    foreach ($alumnos as $alumno => $notas) {
      $mediaAlumno = calcMediaAlumno($notas);
    }
  }
}


/*
* Llamamos a las vistas
*/
include 'views/templates/header.php';
include 'views/calculos_notas.lara_comesana_varela.view.php';
include 'views/templates/footer.php';
?>