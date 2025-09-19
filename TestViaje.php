<?php
include_once "Empresa.php";
include_once "DataBase.php";
include_once "Viaje.php";
include_once "ResponsableViaje.php";
menuPrincipal();
function menuPrincipal()
{
    $continuarMenu = true;
    do {
        echo "\n -------------------------------Menu principal-------------------------------";
        echo "\n1. Menu Empresas";
        echo "\n2. Menu Viajes";
        echo "\n3. Menu responsables";
        echo "\n4. Menu pasajeros";
        echo "\n5. Salir del programa\n";
        $opcion = trim(fgets(STDIN));
        switch ($opcion) {
            case 1:
                menuEmpresa();
                break;
            case 2:
                menuViajes();
                break;
            case 3:
                menuResponsables();
                break;
            case 4:
                menuPasajeros();
                break;
            case 5:
                $continuarMenu = false;
                break;
            default:
                break;
        }
    } while ($continuarMenu);
}

function menuResponsables(){
    $continuarMenu = true;
    do {
        echo "\n1. Crear un responsable";
        echo "";
    } while($continuarMenu);
}

function menuViajes()
{
    $continuarMenu = true;
    do {
        echo "\n1. Crear viaje.";
        echo "\n2. Modificar un viaje";
        echo "\n3. Eliminar un viaje;";
        echo "\n4. Listar viajes";
        echo "\n5. Volver al menu principal\n";
        $opcion = trim(fgets(STDIN));

        switch ($opcion) {
            case 1:
                menuCrearViaje();
                break;
            case 2:
                modificarViajeMenu();
                break;
            case 3:
                echo "\nIngrese la ID del viaje a eliminar\n";
                $idViaje = trim(fgets(STDIN));
                eliminarViaje($idViaje);
                break;
            case 4:
                listarViajes();
                break;
            case 5:
                $continuarMenu = false;
                break;
            default:
                echo "\nOpcion ingresada invalida. \n";
                break;
        }
    } while ($continuarMenu);
}

function listarViajes(){
    $viaje = new Viaje();
    $viajes = $viaje->listar();
    if($viajes!=null){
        foreach($viajes as $viaje){
            echo "\n$viaje\n";
        }
    } else{
        echo "\nNo hay viajes a listar\n";
    }
}

function eliminarViaje($idViaje){
    $viaje = new Viaje();
    if($viaje->buscar($idViaje)){
        $viaje->eliminar($idViaje);
    } else {
        echo "\nViaje no encontrado.\n";
    }
}
function menuCrearViaje()
{
    echo "Ingrese el destino del viaje: ";
    $destinoViaje = trim(fgets(STDIN));
    echo "Ingrese la cantidad maxima de pasajeros del viaje: ";
    $cantMaxPasajerosViaje = trim(fgets(STDIN));
    echo "Ingrese el numero del empleado responsable: ";
    $nroResponsableViaje = trim(fgets(STDIN));
    echo "Ingrese el costo del viaje: ";
    $importeViaje = trim(fgets(STDIN));
    echo "Ingrese el id de la empresa a la que desea agregar el viaje: ";
    $idEmpresaViaje = trim(fgets(STDIN));
    if (crearViaje($destinoViaje, $cantMaxPasajerosViaje, [], $nroResponsableViaje, $importeViaje, $idEmpresaViaje)) {
        echo "\nViaje agregado con exito.\n";
    } else {
        echo "\nError al intentar agregar el viaje.\n";
    }
}

function crearViaje($destinoViaje, $nuevaCantMaxPasajeros, $pasajeros, $nroResponsable, $nuevoImporteViaje, $idEmpresaViaje) {
    $responsableViaje = new ResponsableViaje();
    if($responsableViaje->buscar($nroResponsable)){
        $empresa = new Empresa();
        if($empresa->buscar($idEmpresaViaje)){
            if(is_numeric($nuevaCantMaxPasajeros)){
                if(is_numeric($nuevoImporteViaje)){
                    $viaje = new Viaje();
                    $viaje->crear($destinoViaje,$nuevaCantMaxPasajeros,$pasajeros,$responsableViaje,$nuevoImporteViaje,$empresa);
                } else {
                    echo"\nImporte invalido.";
                }
            } else {
                echo "\nCantidad maxima de pasajeros invalida.";
            }
        } else {
            echo "\nNo existe una empresa con esa ID.\n";
        }
    } else {
        echo "\nNo existe un responsable de viaje con ese numero\n";
    }
}

function modificarViajeMenu()
{
    echo "\nIngrese el ID del viaje a modificar: ";
    $nuevoIdViaje = trim(fgets(STDIN));
    echo "\nIngrese el nuevo destino: ";
    $nuevoDestino = trim(fgets(STDIN));
    echo "\nIngrese la cantidad maxima de pasajeros: ";
    $nuevaCantidadMaxima = trim(fgets(STDIN));
    echo "\nIngrese el numero del empleado responsable: ";
    $nuevoNroResponsable = trim(fgets(STDIN));
    echo "\nIngrese el importe del viaje: ";
    $nuevoImporteViaje = trim(fgets(STDIN));
    echo "\nIngrese el ID de la empresa que realiza el viaje: ";
    $nuevoIdEmpresa = trim(fgets(STDIN));
    if (modificarViaje($nuevoIdViaje, $nuevoDestino, $nuevaCantidadMaxima, [], $nuevoNroResponsable, $nuevoImporteViaje, $nuevoIdEmpresa)) {
        echo "\nViaje modificado con exito.\n";
    } else {
        echo "\nError al intentar modificar el viaje.\n";
    }
}


function menuEmpresa()
{
    $continuarMenu = true;
    do {
        echo "\n -------------------------------Menu Empresas-------------------------------";
        echo "\n1. Crear una empresa";
        echo "\n2. Cambiar el nombre de una empresa";
        echo "\n3. Cambiar la direccion de una empresa";
        echo "\n4. Eliminar una empresa";
        echo "\n5. Volver al menu principal\n";
        $opcion = trim(fgets(STDIN));
        switch ($opcion) {
            case 1:
                $empresaNueva = new Empresa();
                echo "Ingrese el nombre de la empresa: ";
                $nombreEmpresa = trim(fgets(STDIN));
                echo "Ingrese la dirección de la empresa: ";
                $direccionEmpresa = trim(fgets(STDIN));
                $empresaNueva->setNombre($nombreEmpresa);
                $empresaNueva->setDireccion($direccionEmpresa);
                $empresaNueva->cargar($nombreEmpresa, $direccionEmpresa);
                if ($empresaNueva->insertar()) {
                    echo "\nEmpresa creada con exito.\n";
                } else {
                    echo "\nError al crear la empresa: " . $empresaNueva->getMensajeError() . "\n";
                }
                break;
            case 2:
                cambiarNombreEmpresaMenu();
                break;
            case 3:
                cambiarDireccionEmpresaMenu();
                break;
            case 4:
                echo "Ingrese el id de la empresa a eliminar: ";
                $idEmpresa = trim(fgets(STDIN));
                eliminarEmpresa($idEmpresa);
                break;
            case 5:
                $continuarMenu = false;
                break;
            default:
                echo "\nOpcion ingresada invalida.\n";
                break;
        }
    } while ($continuarMenu);
}

function eliminarEmpresa($idEmpresa)
{

    $empresa = new Empresa();
    $exito = false;
    if (is_numeric($idEmpresa) && $idEmpresa > 0) {
        if ($empresa->buscar($idEmpresa)) {
            $exito = $empresa->eliminar();
            echo "\nEmpresa eliminada con exito. \n";
        } else {
            echo "\nEmpresa no encontrada";
        }
    } else {
        echo "\nID invalido.";
    }
    return $exito;
}

function cambiarNombreEmpresaMenu()
{
    echo "Ingrese el id de la empresa a modificar: ";
    $idEmpresa = trim(fgets(STDIN));
    echo "Ingrese el nuevo nombre de la empresa: ";
    $nombreEmpresa = trim(fgets(STDIN));
    if (cambiarNombreEmpresa($idEmpresa, $nombreEmpresa)) {
        echo "\nEmpresa modificada con exito.\n";
    } else {
        echo "\nError al intentar modificar la empresa.\n";
    }
}

function cambiarNombreEmpresa($idEmpresa, $nombreEmpresa)
{
    $empresa = new Empresa();
    $exito = false;
    if ($idEmpresa > 0 && is_numeric($idEmpresa)) {
        if ($empresa->buscar($idEmpresa)) {
            if ($nombreEmpresa != "") {
                $empresa->setNombre($nombreEmpresa);
            }
            $exito = $empresa->modificar();
        } else {
            echo "\nID no encontrado.";
        }
    } else {
        echo "\n ID invalido.";
    }
    return $exito;
}

function cambiarDireccionEmpresaMenu()
{
    echo "Ingrese el id de la empresa a modificar: ";
    $idEmpresa = trim(fgets(STDIN));
    echo "Ingrese la nueva dirección de la empresa: ";
    $direccionEmpresa = trim(fgets(STDIN));
    if (cambiarDireccionEmpresa($idEmpresa, $direccionEmpresa)) {
        echo "\nEmpresa modificada con exito.\n";
    } else {
        echo "\nError al intentar modificar la empresa.\n";
    }
}

function cambiarDireccionEmpresa($idEmpresa, $direccionEmpresa)
{
    $empresa = new Empresa();
    $exito = false;
    if ($idEmpresa > 0 && is_numeric($idEmpresa)) {
        if ($empresa->buscar($idEmpresa)) {
            if ($direccionEmpresa != "") {
                $empresa->setDireccion($direccionEmpresa);
            }
            $exito = $empresa->modificar();
        } else {
            echo "\nID no encontrado.";
        }
    } else {
        echo "\n ID invalido.";
    }
    return $exito;
}



?>