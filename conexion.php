<?php
    $servidor = "localhost";
    $usuario = "root";
    $clave = "";
    $base = "AniDB";

    $conexion = mysqli_connect($servidor,$usuario,$clave,$base);
    mysqli_set_charset($conexion,"utf8");
?>