<?php
include_once "Empresa.php";
include_once "DataBase.php";
include_once "Viaje.php";
include_once "ResponsableViaje.php";
include_once "Pasajero.php";
menuPrincipal();
function menuPrincipal()
{
    $continuarMenu = true;
    do {
        echo "\n -------------------------------Menu principal-------------------------------";
        echo "\n1. Menu Empresas";
        echo "\n2. Menu Viajes";
        echo "\n3. Menu Responsables";
        echo "\n4. Menu Pasajeros";
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

function menuPasajeros()
{
    $continuar = true;
    do {
        echo "\n-------------------------------Menu Pasajeros-------------------------------\n";
        echo "1. Crear y agregar un pasajero a un viaje\n";
        echo "2. Modificar un pasajero\n";
        echo "3. Listar los pasajeros\n";
        echo "4. Eliminar un pasajero de un viaje\n";
        echo "5. Volver al menu principal\n";
        $opcion = trim(string: fgets(STDIN));
        switch ($opcion) {
            case 1:
                agregarPasajeroViaje();
                break;
            case 2:
                listarPasajeros();
                modificarPasajeroMenu();
                break;
            case 3:
                listarPasajeros();
                break;
            case 4:
                listarPasajeros();
                eliminarPasajeros();
                break;
            case 5:
                $continuar = false;
                break;
            default:
                echo "\nOpcion ingresada invalida.\n";
                break;
        }
    } while ($continuar);
}

function eliminarPasajeros(){
    listarViajes();
    echo "\nIngrese la ID del viaje del que desea eliminar el pasajero: ";
    $idViaje = trim(fgets(STDIN));
    echo "Ingrese el documento del pasajero que desea eliminar: ";
    $numDocumento = trim(fgets(STDIN));
    $pasajero = new Pasajero();
    if($pasajero->buscar($numDocumento, $idViaje)){
        if($pasajero->eliminar()){
            echo "Pasajero eliminado con exito.";
        } else {
            echo "No fue posible eliminar el pasajero.";
        }
    } else {
        echo "Pasajero no encontrado en ese viaje.";
    }
}

function modificarPasajero($numDoc, $nombre, $apellido, $telefono, $idViaje)
{
    $pasajero = new Pasajero();
    $respuesta = false;
    if (is_numeric($numDoc)) {
        if ($pasajero->buscar($numDoc)) {
            if ($nombre != "") {
                $pasajero->setNombre($nombre);
            }
            if ($apellido != "") {
                $pasajero->setApellido($apellido);
            }
            if ($telefono != "") {
                $pasajero->setPtelefono($telefono);
            }
            $viaje = new Viaje();
            if (is_numeric($idViaje)) {
                if ($viaje->buscar($idViaje)) {
                    $pasajero->setIdViaje($idViaje);
                } else {
                    echo "\nNo hay un viaje con la ID ingresada\n";
                }
            }
            $respuesta = $pasajero->modificar();
        } else {
            echo "\nNo hay un pasajero con el numero de documento ingresado.\n";
        }
    } else {
        echo "\nEl numero de documento es invalido.\n";
    }
    return $respuesta;
}

function modificarPasajeroMenu()
{
    echo "Ingrese el numero de documento del pasajero a modificar: ";
    $numDocumento = trim(fgets(STDIN));
    echo "Ingrese el nuevo nombre: ";
    $nombre = trim(fgets(STDIN));
    echo "Ingrese el nuevo apellido: ";
    $apellido = trim(fgets(STDIN));
    echo "Ingrese el nuevo numero de telefono: ";
    $numTelefono = trim(fgets(STDIN));
    echo "Ingrese el nuevo id de viaje: ";
    $idViaje = trim(fgets(STDIN));
    if (modificarPasajero($numDocumento, $nombre, $apellido, $numTelefono, $idViaje)) {
        echo "\nEl pasajero ha sido modificado con exito\n";
    } else {
        echo "\nNo se ha podido modificar al pasajero.\n";
    }
}

function listarPasajeros()
{
    $pasajero = new Pasajero();
    $pasajeros = $pasajero->listar();
    if ($pasajeros) {
        foreach ($pasajeros as $pasajero) {
            echo "\n$pasajero\n";
        }
    } else {
        echo "\n No hay pasajeros que listar\n";
    }

}

function agregarPasajeroViaje()
{
    echo "\nIngrese la ID del viaje al que quiere agregar el pasajero\n";
    $idViaje = trim(string: fgets(STDIN));
    $viaje = new Viaje();
    if ($viaje->buscar($idViaje)) {
        echo "\nIngresa el nombre del pasajero: \n";
        $nombre = trim(fgets(STDIN));
        echo "\nIngresa el apellido del pasajero: \n";
        $apellido = trim(fgets(STDIN));
        echo "\nIngresa el numero de documento del pasajero\n";
        $numDoc = trim(fgets(STDIN));
        echo "\nIngrese el numero de telefono del pasajero\n";
        $numTelefono = trim(fgets(STDIN));
        $pasajero = new Pasajero();
        $pasajero->cargarPasajero($numDoc, $nombre, $apellido, $numTelefono, $idViaje);
        if ($pasajero->insertar()) {
            echo "\nPasajero creado exitosamente\n";
        } else {
            echo "\nNo fue posible crear el pasajero\n";
        }
    } else {
        echo "\nNo existe un viaje con ese ID.\n";
    }

}

function menuResponsables()
{
    $continuarMenu = true;
    do {
        echo "\n-------------------------------Menu Responsables-------------------------------";
        echo "\n1. Crear un responsable\n";
        echo "\n2. Listar los responsables\n";
        echo "\n3. Modificar un responsable\n";
        echo "\n4. Eliminar un responsable \n";
        echo "\n5. Volver al menu principal\n";
        $opcion = trim(string: fgets(STDIN));
        switch ($opcion) {
            case 1:
                menuCrearResponsable();
                break;
            case 2:
                listarResponsables();
                break;
            case 3:
                listarResponsables();
                modificarResponsableMenu();
                break;
            case 4:
                listarResponsables();
                eliminarResponsableMenu();
                break;
            case 5:
                $continuarMenu = false;
                break;
            default:
                echo "\Opcion ingresada invalida\n";
                break;
        }
    } while ($continuarMenu);
}

function eliminarResponsableMenu()
{
    echo "Ingrese el numero de empleado a eliminar: ";
    $numeroEmpleado = trim(fgets(STDIN));
    echo "ingrese el numero de documento: ";
    $numDoc = trim(fgets(STDIN));
    if (eliminarResponsable($numeroEmpleado, $numDoc)) {
        echo "\nEl responsable ha sido eliminado con exito!\n";
    } else {
        echo "\nError, el responsable no ha sido eliminado.\n";
    }
}

function eliminarResponsable($numeroEmpleado, $numDoc)
{
    $responsable = new ResponsableViaje;
    $exito = false;
    if (is_numeric($numeroEmpleado)) {
        if ($responsable->buscar($numeroEmpleado)) {
            try{
                $respuesta = $responsable->eliminar();
                $persona = new Persona;
                $persona->buscar($numDoc);
                $persona->eliminar();
                $exito = true;
            }catch(mysqli_sql_exception $e){
                echo "\nEl responsable que intentó eliminar se encuentra como parte de un viaje, primero debe modificar o eliminar dicho viaje";
            }
        } else {
            echo "\No fue encontrado ningun responsable con ese numero de empleado\n";
        }
    } else {
        echo "\nNumero de empleado ingresado invalido.\n";
    }
    return $exito;
}

function modificarResponsableMenu()
{
    echo "Ingrese el numero de empleado del responsable a modificar: \n";
    $numeroEmpleado = trim(fgets(STDIN));
    echo "Ingrese el nuevo numero de licencia: \n";
    $numeroLicencia = trim(fgets(STDIN));
    echo "Ingresa el nuevo nombre: \n";
    $nombre = trim(fgets(STDIN));
    echo "Ingresa el nuevo apellido: \n";
    $apellido = trim(fgets(STDIN));
    if (modificarResponsable($numeroEmpleado, $numeroLicencia, $nombre, $apellido)) {
        echo "\nEl responsable ha sido modificado con exito\n";
    } else {
        echo "\nNo es posible modificar el responsable.\n";
    }
}

function modificarResponsable($numeroEmpleado, $numeroLicencia, $nombreResponsable, $apellidoResponsable)
{
    $exito = false;
    $responsable = new ResponsableViaje();
    if (is_numeric($numeroEmpleado)) {
        if ($responsable->buscar($numeroEmpleado)) {
            $responsable->setRnumeroLicencia($numeroLicencia);
            $responsable->setNombre($nombreResponsable);
            $responsable->setApellido($apellidoResponsable);
            $exito = $responsable->modificarR();
        } else {
            echo "\nNo se encontró un responsable de viaje con el numero de empleado ingresado.\n";
        }
    } else {
        echo "\nNumero de empleado ingresado invalido\n";
    }
    return $exito;
}

function listarResponsables()
{
    $responsable = new ResponsableViaje();
    $responsables = $responsable->listar();
    if ($responsables != null) {
        foreach ($responsables as $responsableViaje) {
            echo "\n$responsableViaje\n";
        }
    } else {
        echo "\nNo existen responsables.\n";
    }
}

function menuCrearResponsable()
{
    echo "\nAgregar responsable\n";
    echo "Ingrese el nombre: ";
    $nombreResponsable = trim(fgets(STDIN));
    echo "Ingrese el apellido: ";
    $apellidoResponsable = trim(fgets(STDIN));
    echo "Ingrese el numero de documento: ";
    $documentoResponsable = trim(fgets(STDIN));
    echo "Ingrese el numero de licencia: ";
    $numeroLicenciaResponsable = trim(fgets(STDIN));
    echo "Ingrese el numero de empleado: ";
    $numeroEmpleadoResponsable = trim(fgets(STDIN));
    crearResponsable($nombreResponsable, $apellidoResponsable, $documentoResponsable, $numeroLicenciaResponsable, $numeroEmpleadoResponsable);
}

function crearResponsable($nombreResponsable, $apellidoResponsable, $documentoResponsable, $numeroLicenciaResponsable, $numeroEmpleadoResponsable)
{
    $responsable = new ResponsableViaje();
    if (!$responsable->buscar($numeroEmpleadoResponsable)) {
        $responsable->cargarRe($documentoResponsable, $nombreResponsable, $apellidoResponsable, $numeroLicenciaResponsable, $numeroEmpleadoResponsable);
        if ($responsable->insertar()) {
            echo "\nResponsable de viaje agregado con exito.\n";
        } else {
            echo "\nNo se pudo agregar al responsable de viaje\n";
        }
    } else {
        echo "\nYa existe un responsable de viaje con ese numero de empleado.\n";
    }
}

function menuViajes()
{
    $continuarMenu = true;
    do {
        echo "\n-------------------------------Menu Viajes-------------------------------";
        echo "\n1. Crear viaje.";
        echo "\n2. Modificar un viaje";
        echo "\n3. Eliminar un viaje";
        echo "\n4. Listar viajes";
        echo "\n5. Volver al menu principal\n";
        $opcion = trim(fgets(STDIN));

        switch ($opcion) {
            case 1:
                menuCrearViaje();
                break;
            case 2:
                listarViajes();
                modificarViajeMenu();
                break;
            case 3:
                listarViajes();
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

function listarViajes()
{
    $viaje = new Viaje();
    $viajes = $viaje->listar();
    if ($viajes != null) {
        foreach ($viajes as $viaje) {
            echo "\n$viaje\n";
        }
    } else {
        echo "\nNo hay viajes a listar\n";
    }
}

function eliminarViaje($idViaje)
{
    $viaje = new Viaje();
    if ($viaje->buscar($idViaje)) {
        if($viaje->eliminar()){
            echo "\nViaje eliminado con exito.\n";
        } else {
            echo $viaje->getMensajeOperacion();
        }
        
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
    echo "Ingrese el id de la empresa encargada del viaje: ";
    $idEmpresaViaje = trim(fgets(STDIN));
    if (crearViaje($destinoViaje, $cantMaxPasajerosViaje, [], $nroResponsableViaje, $importeViaje, $idEmpresaViaje)) {
        echo "\nViaje agregado con exito.\n";
    } else {
        echo "\nError al intentar agregar el viaje.\n";
    }
}

function crearViaje($destinoViaje, $nuevaCantMaxPasajeros, $pasajeros, $nroResponsable, $nuevoImporteViaje, $idEmpresaViaje)
{
    $exito = false;
    $responsableViaje = new ResponsableViaje();
    if ($responsableViaje->buscar($nroResponsable)) {
        $empresa = new Empresa();
        if ($empresa->buscar($idEmpresaViaje)) {
            if (is_numeric($nuevaCantMaxPasajeros)) {
                if (is_numeric($nuevoImporteViaje)) {
                    $viaje = new Viaje();
                    $viaje->crear($destinoViaje, $nuevaCantMaxPasajeros, $pasajeros, $responsableViaje, $nuevoImporteViaje, $empresa);
                    $viaje->insertar();
                    $exito = true;
                } else {
                    echo "\nImporte invalido.";
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
    return $exito;
}

function modificarViajeMenu()
{
    echo "\nIngrese el ID del viaje a modificar: ";
    $idViaje = trim(fgets(STDIN));
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
    if (modificarViaje($idViaje, $nuevoDestino, $nuevaCantidadMaxima, [], $nuevoNroResponsable, $nuevoImporteViaje, $nuevoIdEmpresa)) {
        echo "\nViaje modificado con exito.\n";
    } else {
        echo "\nError al intentar modificar el viaje.\n";
    }
}

function modificarViaje($idViaje, $nuevoDestino, $nuevaCantidadMaxima, $pasajeros, $nuevoNroResponsable, $nuevoImporteViaje, $nuevoIdEmpresa)
{
    $responsableViaje = new ResponsableViaje();
    $exito = false;
    if ($responsableViaje->buscar($nuevoNroResponsable)) {
        $empresa = new Empresa();
        if ($empresa->buscar($nuevoIdEmpresa)) {
            if (is_numeric($nuevaCantidadMaxima)) {
                if (is_numeric($nuevoImporteViaje)) {
                    $viaje = new Viaje();
                    if ($viaje->buscar($idViaje)) {
                        $viaje->setObjEmpresa($empresa);
                        $viaje->setvCantMaxPasajeros($nuevaCantidadMaxima);
                        $viaje->setObjResponsableV($responsableViaje);
                        $viaje->setvImporte($nuevoImporteViaje);
                        $viaje->setvDestino($nuevoDestino);
                        $viaje->modificar();
                        $exito = true;
                    } else {
                        echo "\nViaje a modificar no encontrado\n";
                    }
                } else {
                    echo "\nImporte ingresado invalido\n";
                }
            } else {
                echo "\nCantidad maxima de pasajeros ingresada invalida\n";
            }
        } else {
            echo "\nEmpresa no encontrada\n";
        }
    }
    return $exito;
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
                    echo "\nError al crear la empresa: " . $empresaNueva->getMensajeOperacion() . "\n";
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
            try{
                if ($empresa->eliminar()) {
                    $exito = true;
                    echo "\nEmpresa eliminada con exito. \n";
                } else {
                    echo "\nError al eliminar la empresa";
                }
            } catch(mysqli_sql_exception $e){
                echo "\nLa empresa que intentó eliminar es parte de otros viajes, primero debe modificar o eliminar dichos viajes para eliminar la empresa";
            }
            

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