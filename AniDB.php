<?php

$id_user = 0;
$nameSerie = '';
$codeErr = 0;

include 'conexion.php';

if (isset($_POST['action']))
    $action = $_POST['action'];
else if (isset($_GET['action']))
    $action = $_GET['action'];

switch ($action) {
    case 'regS': //Registrar serie
        actionReg($conexion);
        break;
    case 'upC': //Cambiar capitulo
        upDateChapter($conexion);
        break;
    case 'edS': //Editar serie
        //editSerie($conexion);
        echo "Esta accion aun no esta implementada, comuniquese con el desarrollador.";
        break;
    case 'stA': //Establecer serie actual
        setActual($conexion);
        break;
    case 'current': //Mostrar datos de la serie actual
        currentSerie($conexion);
        break;
    default:
        echo "INSTRUCCIONES: 
        // Use -> !regS [serie], para registrar una serie. 
        // Use -> !stA [serie], para definir una serie como actual. 
        // Use -> !upC [numero], el numero es opcional, si no se añade la serie solo incrementa en 1. 
        // Use -> !Serie, para ver los datos de la serie actual.";
        break;
}

//Funcion para obtener el id de usuario
function getIdUser($conexion, $usuario)
{
    global $id_user;

    $Query = " SELECT u.id FROM usuarios as u WHERE u.usuario = '" . $usuario . "'; ";
    $resultado = mysqli_query($conexion, $Query);

    if (mysqli_num_rows($resultado)) {
        $row = mysqli_fetch_array($resultado);
        $id_user = $row['id'];
    }
}

//Funcion para buscar la serie
function searchSerie($conexion, $serie, $id_user)
{
    global $nameSerie, $codeErr;

    $Query = " SELECT s.serie FROM series as s WHERE s.serie LIKE '%" . $serie . "%' AND s.id_user = '" . $id_user . "' ";
    $resultado = mysqli_query($conexion, $Query);

    if (mysqli_num_rows($resultado) == 1) {
        $row = mysqli_fetch_array($resultado);
        $nameSerie = $row['serie'];
        $codeErr = 0;
    } else if (mysqli_num_rows($resultado) >= 1) {
        $codeErr = 1;
    } else {
        $codeErr = 2;
    }
}

//Funcion para comprovar si la serie existe
function comproveS($conexion, $serie, $id_user)
{
    global $nameSerie, $codeErr;

    $Query = " SELECT s.serie FROM series as s WHERE s.serie = '" . $serie . "' AND s.id_user = '" . $id_user . "' ";
    $resultado = mysqli_query($conexion, $Query);

    if (mysqli_num_rows($resultado) == 1) {
        $row = mysqli_fetch_array($resultado);
        $nameSerie = $row['serie'];
        $codeErr = 0;
    } else if (mysqli_num_rows($resultado) >= 1) {
        $codeErr = 1;
    } else {
        $codeErr = 2;
    }
}

//Funcion para registrar serie
function actionReg($conexion)
{
    global $id_user, $codeErr;

    if (isset($_GET['serie']) && isset($_GET['usuario'])) {
        $serie = $_GET['serie'];
        $usuario = $_GET['usuario'];

        getIdUser($conexion, $usuario);

        if ($id_user >= 1) {

            comproveS($conexion, $serie, $id_user);
            
            if ($codeErr == 2) {
                $Query = " INSERT INTO series (id, serie, capitulo, activo, id_user) VALUES (NULL, '" . $serie . "', '1', '0', '" . $id_user . "'); ";
                $resultado = mysqli_query($conexion, $Query);

                if ($resultado >= 1) {
                    echo "El registro se realizo con exito ";
                } else {
                    echo "A ocurrido un error al crear el registro ";
                }
            } else if ($codeErr != 2) {
                echo "La serie ya existe";
            } 
        } else {
            echo "El usuario no existe";
        }
    } else {
        echo "Faltan datos";
    }
}

//Funcion para cambiar el capitulo
function upDateChapter($conexion)
{
    global $id_user;

    if (isset($_GET['usuario']) && empty($_GET['cap'])) {
        $usuario = $_GET['usuario'];

        getIdUser($conexion, $usuario);

        if ($id_user >= 1) {
            $Query = " SELECT s.capitulo FROM series as s WHERE s.activo = '1' AND s.id_user = '" . $id_user . "'; ";
            $resultado = mysqli_query($conexion, $Query);
            $numRegistro = mysqli_num_rows($resultado);

            if ($numRegistro == 1) {
                $registro = mysqli_fetch_array($resultado);
                $response['cap'] = $registro["capitulo"];

                $nChap = $response['cap'] + 1; //Realizo el incremento del capitulo

                $Query2 = " UPDATE series SET capitulo = '" . $nChap . "' WHERE series.activo = '1' AND series.id_user = '" . $id_user . "'; ";
                $res = mysqli_query($conexion, $Query2);

                if ($res >= 1) {
                    echo "El Capitulo se a actualizado: " . $nChap;
                } else {
                    echo "Un error innesperado ocurrio";
                }
            } else if ($numRegistro > 1) {
                echo "Error: Existe mas de una serie activa";
            } else {
                echo "Error: No existe serie activa";
            }
        } else {
            echo "El usuario no existe";
        }
    } else if (isset($_GET['usuario']) && isset($_GET['cap'])) {
        $usuario = $_GET['usuario'];
        $cap = $_GET['cap'];

        getIdUser($conexion, $usuario);

        if ($id_user >= 1) {
            $Query = " UPDATE series SET capitulo = '" . $cap . "' WHERE series.activo = '1' AND series.id_user = '" . $id_user . "'; ";
            $resultado = mysqli_query($conexion, $Query);

            if ($resultado >= 1) {
                echo "El capitulo se a actualizado: " . $cap;
            } else {
                echo "A ocurrido un error innesperado";
            }
        } else {
            echo "El usuario no existe";
        }
    } else {
        echo "User is missing";
    }
}

//Funcion para editar serie
function editSerie($conexion)
{
    global $id_user, $nameSerie, $codeErr;

    if (isset($_GET['usuario']) && isset($_GET['serie'])) {
        $usuario = $_GET['usuario'];
        $serie = $_GET['serie'];

        getIdUser($conexion, $usuario);

        if ($id_user >= 1) {
            searchSerie($conexion, $serie, $id_user);

            if ($codeErr == 0) {
                echo "AAAAAAAAAAAAAAAAAAAAAAAAA";
            } else if ($codeErr == 1) {
                echo "Error: Existe mas de una serie, especifique mas datos";
            } else if ($codeErr == 2) {
                echo "Error: La serie no existe";
            }
        } else {
            echo "El usuario no existe";
        }
    } else {
        echo "Faltan datos";
    }
}

//Funcion para establecer la serie actual
function setActual($conexion)
{
    global $id_user, $nameSerie, $codeErr;

    if (isset($_GET['usuario']) && isset($_GET['serie'])) {
        $usuario = $_GET['usuario'];
        $serie = $_GET['serie'];

        getIdUser($conexion, $usuario);

        if ($id_user >= 1) {
            searchSerie($conexion, $serie, $id_user);

            if ($codeErr == 0) {

                $Query = " UPDATE series SET activo = '0' WHERE series.id_user = '" . $id_user . "'; ";
                $resultado = mysqli_query($conexion, $Query);

                if ($resultado >= 1) {
                    $Query2 = " UPDATE series SET activo = '1' WHERE series.serie = '" . $nameSerie . "' AND series.id_user = '" . $id_user . "'; ";
                    $res = mysqli_query($conexion, $Query2);

                    if ($res >= 1) {
                        echo "Serie activa: " . $nameSerie;
                    } else {
                        echo "Error innesperado";
                    }
                } else {
                    echo "El usuario no tiene series";
                }
            } else if ($codeErr == 1) {
                echo "Error: Existe mas de una serie, especifique mas datos";
            } else if ($codeErr == 2) {
                echo "Error: No existe la serie";
            }
        } else {
            echo "Error: El usuario no existe";
        }
    } else {
        echo "Error: Faltan datos";
    }
}

//Mostrar la serie actual
function currentSerie($conexion)
{
    global $id_user;

    if (isset($_GET['usuario'])) {
        $usuario = $_GET['usuario'];

        getIdUser($conexion, $usuario);

        $response = array();
        if ($id_user >= 1) {
            $Query = " SELECT s.serie, s.capitulo FROM series as s WHERE s.activo = 1 AND s.id_user = '" . $id_user . "'; ";
            $resultado = mysqli_query($conexion, $Query);
            $numRes = mysqli_num_rows($resultado);

            if ($numRes == 1) {
                $data = mysqli_fetch_array($resultado);

                $response['serie'] = $data['serie'];
                $response['capitulo'] = $data['capitulo'];

                echo "La serie que estamos viendo es: " . $response['serie'] . ". En el capitulo: " . $response['capitulo'];
            } else if ($numRes >= 1) {
                echo "Error: hay mas de una serie activa";
            } else {
                echo "Error: no hay series activas";
            }
        } else {
            echo "El usuario no existe";
        }
    } else {
        echo "User is missing";
    }
}
