<?php
$conexion = new mysqli("localhost", "root", "root", "ejercicio");
if ($conexion->connect_errno) {
    die("Error de conexión: " . $conexion->connect_error);
}

$xml = simplexml_load_file('ies_db.xml') or die('Error: no se cargó el XML correctamente');

try {
    $conexion->begin_transaction();

    foreach ($xml as $i_pe => $pe) {

        echo 'nombre: ' . $pe->nombre . "<br>";
        echo 'codigo: ' . $pe->codigo . "<br>";
        echo 'tipo: ' . $pe->tipo . "<br>";

        //  PROGRAMA DE ESTUDIOS
        $stmt = $conexion->prepare("INSERT INTO sigi_programa_estudios (codigo, tipo, nombre) VALUES (?, ?, ?)");
        if (!$stmt) throw new Exception("Prepare programa: " . $conexion->error);
        $stmt->bind_param("sss", $pe->codigo, $pe->tipo, $pe->nombre);
        $stmt->execute();
        $id_programa = (int)$stmt->insert_id;
        $stmt->close();


        // PLANES DE ESTUDIO
        foreach ($pe->planes_estudio[0] as $i_ple => $plan) {

            echo '--' . $plan->nombre . "<br>";
            echo '--' . $plan->resolucion . "<br>";
            echo '--' . $plan->fecha_registro . "<br>";

            $stmt = $conexion->prepare("
                INSERT INTO sigi_planes_estudio (id_programa_estudios, nombre, resolucion, fecha_registro, perfil_egresado)
                VALUES (?, ?, ?, ?, ?)
            ");
            if (!$stmt) throw new Exception("Prepare plan: " . $conexion->error);

            $perfil = isset($plan->perfil_egresado) ? (string)$plan->perfil_egresado : null;
            $stmt->bind_param("issss",
                $id_programa,
                $plan->nombre,
                $plan->resolucion,
                $plan->fecha_registro,
                $perfil
            );
            $stmt->execute();
            $id_plan = (int)$stmt->insert_id;
            $stmt->close();


            // MODULOS FORMATIVOS
            foreach ($plan->modulos_formativos[0] as $id_mod => $modulo) {

                echo '----' . $modulo->descripcion . "<br>";
                echo '----' . $modulo->nro_modulo . "<br>";

                $nro_mod = isset($modulo->nro_modulo) ? (int)$modulo->nro_modulo : null;

                $stmt = $conexion->prepare("INSERT INTO sigi_modulo_formativo (descripcion, nro_modulo, id_plan_estudio)
                    VALUES (?, ?, ?)");
                if (!$stmt) throw new Exception("Prepare modulo: " . $conexion->error);

                $stmt->bind_param("sii",
                    $modulo->descripcion,
                    $nro_mod,
                    $id_plan
                );
                $stmt->execute();
                $id_modulo = (int)$stmt->insert_id;
                $stmt->close();


                // PERIODOS 
                foreach ($modulo->periodos[0] as $i_pe => $per) {

                    echo '------' . $per->descripcion . "<br>";

                    $stmt = $conexion->prepare("INSERT INTO sigi_semestre (descripcion, id_modulo_formativo)
                        VALUES (?, ?)");
                    if (!$stmt) throw new Exception("Prepare semestre: " . $conexion->error);

                    $stmt->bind_param("si", $per->descripcion, $id_modulo);
                    $stmt->execute();
                    $id_semestre = (int)$stmt->insert_id;
                    $stmt->close();


                    // UNIDADES DIDACTICAS
                    $orden = 1;
                    foreach ($per->unidades_didacticas[0] as $i_ud => $ud) {

                        echo "--------UD: " . $ud->nombre . "<br>";
                        echo "----------Créditos Teóricos: " . $ud->creditos_teorico . "<br>";
                        echo "----------Créditos Prácticos: " . $ud->creditos_practico . "<br>";
                        echo "----------Tipo: " . $ud->tipo . "<br>";
                        echo "----------Horas semanal: " . $ud->horas_semanal . "<br>";
                        echo "----------Horas semestral: " . $ud->horas_semestral . "<br>";

                        $ct = isset($ud->creditos_teorico) ? (int)$ud->creditos_teorico : 0;
                        $cp = isset($ud->creditos_practico) ? (int)$ud->creditos_practico : 0;
                        $tipo = isset($ud->tipo) ? (string)$ud->tipo : '';

                        $stmt = $conexion->prepare("INSERT INTO sigi_unidad_didactica
                            (nombre, id_semestre, creditos_teorico, creditos_practico, tipo, orden)
                            VALUES (?, ?, ?, ?, ?, ?)");
                        if (!$stmt) throw new Exception("Prepare unidad: " . $conexion->error);

                        $stmt->bind_param("siiisi",
                            $ud->nombre,
                            $id_semestre,
                            $ct,
                            $cp,
                            $tipo,
                            $orden
                        );
                        $stmt->execute();
                        $stmt->close();

                        $orden++;
                    }
                }
            }
        }
    }

    $conexion->commit();

} catch (Exception $e) {
    $conexion->rollback();
    die("Error en importación: " . $e->getMessage());
}

$conexion->close();
?>
