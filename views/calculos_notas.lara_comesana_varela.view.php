<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Cálculos de Notas</h1>

</div>
<!-- Content Row -->

<!--Introducimos la sección que mostrará la tabla-->
<?php if (isset($data['json_calculos'])) { ?>
    <div class="row">
        <div class="col-12">
            <div class="alert">
                <table class="table table-responsive-lg table-bordered table-hover" style="text-align: left">
                <caption>Tabla resumen de las asignaturas</caption>
                <thead class="thead-dark" style="text-align: center">
                    <tr>
                        <th>Asignatura</th>
                        <th>Nota media</th>
                        <th>Nº Suspensos</th>
                        <th>Nº Aprobados</th>
                        <th>Nota máxima</th>
                        <th>Nota mínima</th>
                    </tr>
                    </thead>
                    <tbody style="border: 1px solid black">
                    <?php foreach ($data['json_calculos'] as $asignatura => $datos) { ?>
                        <tr>
                            <td>
                                <strong><?php echo ucwords($asignatura) ?></strong>
                            </td>
                            <td>
                              <?php echo number_format($datos['media'],2,',') ?>
                            </td>
                            <td>
                              <?php echo $datos['suspensos'] ?>
                            </td>
                            <td>
                              <?php echo $datos['aprobados'] ?>
                            </td>
                            <td>
                              <?php echo ucwords($datos['max']['alumno']) ?>
                                : <?php echo number_format(round($datos['max']['nota'],1),2,',') ?>
                            </td>
                            <td>
                              <?php echo ucwords($datos['min']['alumno']) ?>
                                : <?php echo number_format(round($datos['min']['nota'],1),2,',') ?>
                            </td>
                        </tr>
                    <?php }; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php } ?>
<!--Introducimos las sección en la que se hace una distribución en divs-->
<?php if (isset($data['listados'])) { ?>
    <div class="row">
        <div class="col-12 col-lg-6 mb-4">
            <div class="alert alert-success h-100">
                <h5>Alumnos que aprueban todo:</h5>
              <?php foreach ($data['listados']['sinSuspensos'] as $alumno) { ?>
                  <ul><li><?php echo $alumno; ?></php></li></ul>
              <?php } ?>
            </div>
        </div>
        <div class="col-12 col-lg-6 mb-4">
            <div class="alert alert-warning h-100">
                <h5>Alumnos que suspenden al menos 1:</h5>
              <?php foreach ($data['listados']['conSuspensos'] as $alumno) { ?>
                  <ul><li><?php echo $alumno; ?></php></li></ul>
              <?php } ?>
            </div>
        </div>
        <div class="col-12 col-lg-6 mb-4">
            <div class="alert alert-primary h-100">
                <h5>Alumnos que promocionan:</h5>
              <?php foreach ($data['listados']['promocionan'] as $alumno) { ?>
                  <ul><li><?php echo $alumno; ?></php></li></ul>
              <?php } ?>
            </div>
        </div>
        <div class="col-12 col-lg-6 mb-4">
            <div class="alert alert-danger h-100">
                <h5>Alumnos que no promocionan:</h5>
              <?php foreach ($data['listados']['noPromocionan'] as $alumno) { ?>
                  <ul><li><?php echo $alumno; ?></php></li></ul>
              <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>
<!--Sección que recoge los datos del usuario y muestra los errores-->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Cálculos de notas</h6>
            </div>
            <div class="card-body">
                <form action="" method="post">
                    <div class="mb-3 col-12">
                        <label for="textarea">Introduce el JSON: </label>
                        <textarea class="form-control" id="texto" name="texto"
                                  rows="3"><?php echo $data['input_texto'] ?? ''; ?></textarea>
                        <p class="text-danger small">
                          <?php if (isset($data['errores'])) {
                            foreach ($data['errores']['texto'] as $error => $mensaje) {
                              echo $mensaje . "<br/>";
                            }
                          }
                          ?>
                        </p>
                        <br/>
                    </div>
                    <div class="mb-3">
                        <input type="submit" value="Enviar" name="enviar" class="btn btn-primary">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>