<?php
$conexion = new mysqli("localhost", "root", "root", "sigi_Huanta");
if ($conexion->connect_errno) {
    echo "Fallo al conectar a MySQL: (" . $conexion->connect_errno . ") " . $conexion->connect_errno;
}
$xml = new DOMDocument('1.0', 'UTF-8');
$xml->formatOutput = true;

$et1 = $xml->createElement('programas_estudio');
$xml->appendChild($et1);

$consulta = "SELECT * FROM sigi_programa_estudios";
$resultado = $conexion->query($consulta);
while ($pe = mysqli_fetch_assoc($resultado)) {
    echo $pe['nombre'] . "<br>";
    $num_pe = $xml->createElement('pe_' . $pe['id']);
    $codigo_pe = $xml->createElement('codigo', $pe['codigo']);
    $num_pe->appendChild($codigo_pe);
    $tipo_pe = $xml->createElement('tipo', $pe['tipo']);
    $num_pe->appendChild($tipo_pe);
    $nombre_pe = $xml->createElement('nombre', $pe['nombre']);
    $num_pe->appendChild($nombre_pe);


    //plan de estudios
    $et_plan = $xml->createElement('planes_estudio');
    $consulta_plan = "SELECT * FROM sigi_planes_estudio WHERE id_programa_estudios=" . $pe['id'];
    $resultado_plan = $conexion->query($consulta_plan);
    while ($plan = mysqli_fetch_assoc($resultado_plan)) {
        echo $plan['nombre'] . "<br>";
        $num_pe = $xml->createElement('resultado_plan' . $plan['id']);
        $programa_plan = $xml->createElement('id_programa_estudios', $plan['id_programa_estudios']);
        $num_pe->appendChild($programa_plan);
        $nombre_plan = $xml->createElement('nombre', $plan['nombre']);
        $num_pe->appendChild($nombre_plan);
        $resolucion_plan = $xml->createElement('resolucion', $plan['resolucion']);
        $num_pe->appendChild($resolucion_plan);
        $fecha_plan = $xml->createElement('fecha_registro', $plan['fecha_registro']);
        $num_pe->appendChild($fecha_plan);
        //modulos 
        $et_modulos = $xml->createElement('modulos');
        $consulta_modulos = "SELECT * FROM sigi_modulo_formativo WHERE id_plan_estudio=" . $pe['id'];
        $resultado_modulos = $conexion->query($consulta_modulos);
        while ($modulos = mysqli_fetch_assoc($resultado_modulos)) {
            echo $modulos['descripcion'] . "<br>";
            $num_pe = $xml->createElement('resultado_modulos' . $modulos['id']);
            $descripcion_modulos = $xml->createElement('descripcion', $modulos['descripcion']);
            $num_pe->appendChild($descripcion_modulos);
            $numero_modulos = $xml->createElement('nro_modulo', $modulos['nro_modulo']);
            $num_pe->appendChild($numero_modulos);
            $plan_modulos = $xml->createElement('id_plan_estudio', $modulos['id_plan_estudio']);
            $num_pe->appendChild($plan_modulos);

            //semestres
            $et_semestre = $xml->createElement('semestre');
            $consulta_semestre = "SELECT * FROM sigi_semestre WHERE id_modulo_formativo=" . $pe['id'];
            $resultado_semestre = $conexion->query($consulta_semestre);
            while ($semestre = mysqli_fetch_assoc($resultado_semestre)) {
                echo $semestre['descripcion'] . "<br>";
                $num_pe = $xml->createElement('resultado_semestre' . $semestre['id']);
                $descripcion_semestre = $xml->createElement('descripcion', $semestre['descripcion']);
                $num_pe->appendChild($descripcion_semestre);
                $modulo_semestre = $xml->createElement('id_modulo_formativo', $semestre['id_modulo_formativo']);
                $num_pe->appendChild($modulo_semestre);

                //unidades didacticas
                $et_unidades = $xml->createElement('unidades_didacticas');
                $consulta_unidades = "SELECT * FROM sigi_unidad_didactica WHERE id_=" . $pe['id'];
                $resultado_unidades = $conexion->query($consulta_unidades);
                while ($unidades = mysqli_fetch_assoc($resultado_unidades)) {
                    echo $semestre['descripcion'] . "<br>";
                    $num_pe = $xml->createElement('resultado_unidades' . $unidades['id']);
                    $nombre_unidades = $xml->createElement('nombre', $unidades['nombre']);
                    $num_pe->appendChild($nombre_unidades);
                    $semestre_unidades = $xml->createElement('id_semestre', $unidades['id_semestre']);
                    $num_pe->appendChild($semestre_unidades);
                    $creditos_teorico_unidades = $xml->createElement('creditos_teorico', $unidades['creditos_teorico']);
                    $num_pe->appendChild($creditos_teorico_unidades);
                    $creditos_practico_unidades = $xml->createElement('creditos_practico', $unidades['creditos_practico']);
                    $num_pe->appendChild($creditos_practico_unidades);
                }

            }

        }

    }
  
    $num_pe->appendChild($et_plan);
    $et1->appendChild($num_pe);
}

$archivo = "ies_db.xml";
$xml->save($archivo);
?>