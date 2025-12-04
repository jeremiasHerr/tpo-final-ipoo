<?php
    include_once "Persona.php";
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
            "El telefono es: " . $this->getPtelefono() . "\n".
            "El id del viaje es: " . $this->getIdViaje()->getIdViaje() . "\n" ."\n";
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

            //si no existe se inserta normalmente
            if (!$personaExiste) {
                if (!parent::insertar()) {
                    // No se pudo insertar la persona -> no seguimos
                    $this->setMensajeOperacion(parent::getMensajeoperacion() ?: $this->getMensajeOperacion());
                    return false;
                }
            }

            // Determinar valor de idviaje (puede ser objeto Viaje o int)
            $idViajeVal = $this->getIdViaje();
            if (is_object($idViajeVal) && method_exists($idViajeVal, 'getIdViaje')) {
                $idViajeVal = $idViajeVal->getIdViaje();
            }

            $consultaInsertar = 
            "INSERT INTO pasajero_viaje(pdocumento, ptelefono, idviaje) 
            VALUES ('" . $this->getDocumento() . "',
                    '" . $this->getPtelefono() . "',
                    '" . $idViajeVal . "'
                    )";

            if ($baseDatos->Iniciar()) {
                if ($baseDatos->ejecutar($consultaInsertar)) {
                    $pasajero = true;
                } else {
                    $this->setMensajeOperacion($baseDatos->getError());
                }
            } else {
                $this->setMensajeOperacion($baseDatos->getError());
            }

            return $pasajero;
        }

        //Metodo que busca un pasajero segun su documento y viaje
        // Si $idviaje es null, devuelve el primer pasajero encontrado para ese documento
        public function buscar($pdocumento, $idviaje = null) {
            $baseDatos = new DataBase;
            $consulta = "SELECT * FROM pasajero_viaje WHERE pdocumento ='" . $pdocumento . "'";
            if ($idviaje !== null) {
                $consulta .= " AND idviaje = '" . $idviaje . "'";
            }
            $respuesta = false;
            if ($baseDatos->Iniciar()) {
                if ($baseDatos->ejecutar($consulta)) {
                    if ($pasajero = $baseDatos->registro()) {
                        if (!is_array($pasajero)) {
                            $this->setMensajeOperacion($baseDatos->getError());
                        } else {
                            $viaje = new Viaje;
                            $viaje->buscar($pasajero['idviaje']);
                            parent::buscar($pasajero['pdocumento']);
                            $this->setPtelefono($pasajero['ptelefono']);
                            $this -> cargarPasajero(
                                parent::getDocumento(),
                                parent::getNombre(),
                                parent::getApellido(),
                                $pasajero['ptelefono'],
                                $viaje
                            );
                            $respuesta = true;
                        }
                    }
                } else {
                    $this->setMensajeOperacion($baseDatos->getError());
                }
            } else {
                $this->setMensajeOperacion($baseDatos->getError());
            }
            return $respuesta;
        }

        //Metodo para modificar los datos de un pasajero
        public function modificar () {
            $baseDatos = new DataBase();
            if (parent::modificar()) {
                // Determinar valor de idviaje (puede ser objeto Viaje o int)
                $idViajeVal = $this->getIdViaje();
                if (is_object($idViajeVal) && method_exists($idViajeVal, 'getIdViaje')) {
                    $idViajeVal = $idViajeVal->getIdViaje();
                }

                $consulta = "UPDATE pasajero_viaje SET 
                            ptelefono= '". $this->getPtelefono() ."'
                            WHERE pdocumento ='". $this->getDocumento() ."' 
                            AND idviaje = '". $idViajeVal ."'";
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
            }
            return $respuesta;
        }

        //Metodo para eliminar un pasajero de un viaje específico
        public function eliminar () {
            $baseDatos = new DataBase;
            // Determinar valor de idviaje (puede ser objeto Viaje o int)
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
                        $pasajero->cargarPasajero(
                            $persona->getDocumento(),
                            $persona->getNombre(),
                            $persona->getApellido(),
                            $pasajeroEncontrado['ptelefono'],
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