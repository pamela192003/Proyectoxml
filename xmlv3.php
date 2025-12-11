<?php
$xml = simplexml_load_file('ies_db.xml') or die('error no se cargo el xml. escribe correctamente el nombre del archivo');

foreach ($xml as $i_pe => $pe) {
    //imprimir los nombres de los programas de estudios
    echo 'Codigo:' . $pe->codigo . "<br>";
    echo 'Tipo:' . $pe->tipo . "<br>";
    echo 'Nombre:' . $pe->nombre . "<br>";

    //PLANES DE ESTUDIO
    foreach ($pe->planes_estudio[0] as $i_ple => $plan) {
        echo '--' . $plan->nombre . "<br>";
        echo '--' . $plan->resolucion . "<br>";
        echo '--' . $plan->fecha_registro . "<br>";
        //MODULOS FORMATIVOS
        foreach ($plan->modulos_formativos[0] as $id_mod => $modulo) {
            echo '--' . $modulo->descripcion . "<br>";
            echo '--' . $modulo->nro_modulo . "<br>";
            //PERIODOS
            foreach ($modulo->periodos[0] as $i_per => $periodo) {
                echo '--' . $periodo->descripcion . "<br>";
            }
            //UNIDADES DIDACTICAS
            foreach ($periodos->unidades_didacticas[0] as $id_ud => $unidades) {
                echo '--' . $unidades->nombre . "<br>";
            }
        }
    }
}
/*echo $xml->pe_1->nombre."<br>";
echo $xml->pe_2->nombre;*/
