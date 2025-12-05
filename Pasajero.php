<?php
    include_once "Persona.php";
    include_once "PasajeroViaje.php";
    class Pasajero extends Persona {
        private $ptelefono;
        private $idViajePertenece;

        //Metodo constructor
        public function __construct () {
            parent::__construct();
            $this -> ptelefono = 0;
            $this->idViajePertenece = null;
        }

        //Metodo constructor con valores
        public function cargarPasajero ($pdocumento, $pnombre, $papellido,  $ptelefono, $idViaje) {
            parent::cargar($pdocumento, $pnombre, $papellido);
            $this ->setPtelefono($ptelefono);
            $this ->setIdViaje($idViaje);
        } 

        //Getters y setters
        public function getPtelefono () {
            return $this -> ptelefono;
        }
        public function getIdViaje () {
            return $this -> idViajePertenece;
        }
        public function getMensajeOperacion () {
            return $this -> mensajeOperacion;
        }
        
        public function setPtelefono ($value) {
            $this -> ptelefono = $value;
        }
        public function setIdViaje ($value) {
            $this -> idViajePertenece = $value;
        }
        public function setMensajeOperacion ($value) {
            $this -> mensajeOperacion = $value;
        }

        public function __toString () {
            $cadena = parent::__toString();
            $cadena .=
            "El telefono es: " . $this->getPtelefono() . "\n";
            if (is_object($this->getIdViaje())) {
                $cadena .= "El id del viaje es: " . $this->getIdViaje()->getIdViaje() . "\n" ."\n";
            }
            return $cadena;
        } 

        //Metodo para insertar un objeto pasajero en la base de datos
        public function insertar () {
            $baseDatos = new DataBase;
            $pasajero = false;

            //verifico si ya existe como Persona en la bd
            $personaObj = new Persona();
            $personaExiste = false;
            if ($personaObj->buscar($this->getDocumento())) {
                $personaExiste = true;
            }

            //si no existe se inserta persona
            if (!$personaExiste) {
                if (!parent::insertar()) {
                    $this->setMensajeOperacion(parent::getMensajeoperacion() ?: $this->getMensajeOperacion());
                    return false;
                }
            }

            // Inserto/actualizo registro en tabla pasajero (telefono)
            $consultaExiste = "SELECT * FROM pasajero WHERE pdocumento = '" . $this->getDocumento() . "'";
            if ($baseDatos->Iniciar()) {
                if ($baseDatos->ejecutar($consultaExiste)) {
                    $existe = $baseDatos->registro();
                    if (!$existe) {
                        $consultaInsertarPasajero = "INSERT INTO pasajero(pdocumento, ptelefono) VALUES ('" . $this->getDocumento() . "', '" . $this->getPtelefono() . "')";
                        if (!$baseDatos->ejecutar($consultaInsertarPasajero)) {
                            $this->setMensajeOperacion($baseDatos->getError());
                            return false;
                        }
                    } else {
                        // si existe, actualizar telefono
                        $consultaUpdate = "UPDATE pasajero SET ptelefono = '" . $this->getPtelefono() . "' WHERE pdocumento = '" . $this->getDocumento() . "'";
                        if (!$baseDatos->ejecutar($consultaUpdate)) {
                            $this->setMensajeOperacion($baseDatos->getError());
                            return false;
                        }
                    }
                } else {
                    $this->setMensajeOperacion($baseDatos->getError());
                    return false;
                }
            } else {
                $this->setMensajeOperacion($baseDatos->getError());
                return false;
            }

            // Si se especificó un viaje, insertar relación en pasajero_viaje
            $idViajeVal = $this->getIdViaje();
            if ($idViajeVal !== null) {
                if (is_object($idViajeVal) && method_exists($idViajeVal, 'getIdViaje')) {
                    $idViajeVal = $idViajeVal->getIdViaje();
                }
                $consultaRel = "INSERT INTO pasajero_viaje(pdocumento, idviaje) VALUES ('" . $this->getDocumento() . "', '" . $idViajeVal . "')";
                if ($baseDatos->Iniciar()) {
                    if (!$baseDatos->ejecutar($consultaRel)) {
                        $this->setMensajeOperacion($baseDatos->getError());
                        return false;
                    }
                } else {
                    $this->setMensajeOperacion($baseDatos->getError());
                    return false;
                }
            }

            return true;
        }

        //Metodo que busca un pasajero segun su documento y viaje
        // Si $idviaje es null, devuelve el primer pasajero encontrado para ese documento (si existe relación)
        public function buscar($pdocumento, $idviaje = null) {
            $baseDatos = new DataBase;
            $respuesta = false;

            // Buscar la relación en pasajero_viaje (opcional)
            $consultaRel = "SELECT * FROM pasajero_viaje WHERE pdocumento = '" . $pdocumento . "'";
            if ($idviaje !== null) {
                $consultaRel .= " AND idviaje = '" . $idviaje . "'";
            }

            $idViajeEncontrado = null;
            if ($baseDatos->Iniciar()) {
                if ($baseDatos->ejecutar($consultaRel)) {
                    $fila = $baseDatos->registro();
                    if ($fila && is_array($fila) && array_key_exists('idviaje', $fila)) {
                        $idViajeEncontrado = $fila['idviaje'];
                    }
                } else {
                    $this->setMensajeOperacion($baseDatos->getError());
                    return false;
                }
            } else {
                $this->setMensajeOperacion($baseDatos->getError());
                return false;
            }

            // Buscar datos en persona
            if (!parent::buscar($pdocumento)) {
                return false;
            }

            // Buscar telefono en tabla pasajero
            $consultaPas = "SELECT * FROM pasajero WHERE pdocumento = '" . $pdocumento . "'";
            if ($baseDatos->Iniciar()) {
                if ($baseDatos->ejecutar($consultaPas)) {
                    $filaP = $baseDatos->registro();
                    if ($filaP && is_array($filaP) && array_key_exists('ptelefono', $filaP)) {
                        $this->setPtelefono($filaP['ptelefono']);
                    }
                } else {
                    $this->setMensajeOperacion($baseDatos->getError());
                    return false;
                }
            } else {
                $this->setMensajeOperacion($baseDatos->getError());
                return false;
            }

            // Si se encontró idviaje, cargar objeto Viaje
            $viajeObj = null;
            if ($idViajeEncontrado !== null) {
                $viajeObj = new Viaje();
                $viajeObj->buscar($idViajeEncontrado);
            }

            $this->cargarPasajero(
                parent::getDocumento(),
                parent::getNombre(),
                parent::getApellido(),
                $this->getPtelefono(),
                $viajeObj
            );

            return true;
        }

        //Metodo para modificar los datos de un pasajero
        public function modificar () {
            $baseDatos = new DataBase();
            $rta = false;
            if (parent::modificar()) {
                $consulta = "UPDATE pasajero SET ptelefono = '" . $this->getPtelefono() . "' WHERE pdocumento = '" . $this->getDocumento() . "'";
                if ($baseDatos->Iniciar()) {
                    if ($baseDatos->ejecutar($consulta)) {
                        $rta = true;
                    } else {
                        $this->setMensajeOperacion($baseDatos->getError());
                    }
                } else {
                    $this->setMensajeOperacion($baseDatos->getError());
                }
            }
            return $rta;
        }

        //Metodo para eliminar un pasajero de un viaje específico
        public function eliminar () {
            $baseDatos = new DataBase;
            $idViajeVal = $this->getIdViaje();
            if (is_object($idViajeVal) && method_exists($idViajeVal, 'getIdViaje')) {
                $idViajeVal = $idViajeVal->getIdViaje();
            }

            $consulta = "DELETE FROM pasajero_viaje WHERE pdocumento= '" . $this->getDocumento() . "' 
                        AND idviaje = '" . $idViajeVal . "'";
            $respuesta = false;
            if ($baseDatos->Iniciar()) { 
                if ($baseDatos->ejecutar($consulta)) {
                    $respuesta = true;
                } else {
                $this->setMensajeOperacion($baseDatos->getError());
                } 
            } else {
                $this->setMensajeOperacion($baseDatos->getError());
            }
            return $respuesta;
        }

        //Metodo para listar pasajeros (relaciones en pasajero_viaje)
        public function listar($condicion = "") {
            $arregloPasajero = null;
            $baseDatos = new DataBase();
            $baseDatosAux = new DataBase();
            $consulta = "SELECT * FROM pasajero_viaje ";
            if ($condicion != "") {
                $consulta .= "WHERE $condicion ";
            } 
            if  ($baseDatos->Iniciar()) {
                if($baseDatos->ejecutar($consulta)) {
                    $arregloPasajero = [];
                    while ($pasajeroEncontrado = $baseDatos->registro()) {
                        if (!is_array($pasajeroEncontrado)) {
                            continue;
                        }
                        $pasajero = new Pasajero();
                        $viaje= New Viaje();
                        $viaje->buscar($pasajeroEncontrado['idviaje']);
                        $persona = new Persona();
                        $documento=$pasajeroEncontrado['pdocumento'];
                        $persona->buscar($documento);
                        // obtener telefono desde tabla pasajero (usar baseDatosAux para no interrumpir el loop)
                        $consultaTel = "SELECT ptelefono FROM pasajero WHERE pdocumento = '" . $documento . "'";
                        $ptelefono = 0;
                        if ($baseDatosAux->Iniciar()) {
                            if ($baseDatosAux->ejecutar($consultaTel)) {
                                $filaTel = $baseDatosAux->registro();
                                if (is_array($filaTel) && array_key_exists('ptelefono', $filaTel)) {
                                    $ptelefono = $filaTel['ptelefono'];
                                }
                            }
                        }
                        $pasajero->cargarPasajero(
                            $persona->getDocumento(),
                            $persona->getNombre(),
                            $persona->getApellido(),
                            $ptelefono,
                            $viaje
                            );
                        array_push($arregloPasajero, $pasajero);
                    }
                } else {
                    $this->setMensajeOperacion($baseDatos->getError());
                }
            } else {
                $this->setMensajeOperacion($baseDatos->getError());
            }
            return $arregloPasajero;
        }
        
    }