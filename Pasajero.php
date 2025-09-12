<?php
    include_once "Persona.php";
    class Pasajero extends Persona {
        private $ptelefono;
        private $objViaje;
        private $mensajeOperacion;

        //Metodo constructor
        public function __construct () {
            parent::__construct();
            $this -> ptelefono = 0;
            $this->objViaje = null;
        }

        //Getters y setters
        public function getPtelefono () {
            return $this -> ptelefono;
        }
        public function getObjViaje () {
            return $this -> objViaje;
        }
        public function getMensajeOperacion () {
            return $this -> mensajeOperacion;
        }
        
        public function setPtelefono ($value) {
            $this -> ptelefono = $value;
        }
        public function setObjViaje ($value) {
            $this -> objViaje = $value;
        }
        public function setMensajeOperacion ($value) {
            $this -> mensajeOperacion = $value;
        }

        public function __toString () {
            $cadena = parent::__toString();
            $cadena .=
            "El telefono es: " . $this->getPtelefono() . "\n".
            "El id del viaje es: " . $this->getObjViaje()->getIdViaje() . "\n" ."\n";
            return $cadena;
        } 

        //Metodo constructor con valores
        public function cargarPasajero ($pdocumento, $pnombre, $papellido,  $ptelefono, $objViaje) {
            parent::cargar($pdocumento, $pnombre, $papellido);
            $this ->setPtelefono($ptelefono);
            $this ->setObjViaje($objViaje);
        } 

        //Metodo para insertar un objeto pasajero en la base de datos
        public function insertar () {
            $baseDatos = new DataBase;
            $pasajero = false;
            if (parent::insertar()) {
            $consultaInsertar = 
            "INSERT INTO pasajero(pdocumento, ptelefono, idviaje) 
            VALUES ('" . $this->getDocumento() . "',
                    '" . $this->getPtelefono() . "',
                    '" . $this->getObjViaje()->getIdViaje() . "'
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
            }
            return $pasajero;
        }

        //Metodo que busca un pasajero segun su documento
        public function buscar($pdocumento) {
            $baseDatos = new DataBase;
            $consulta = "SELECT * FROM pasajero WHERE pdocumento ='" . $pdocumento . "'";
            $respuesta = false;
            if ($baseDatos->Iniciar()) {
                if ($baseDatos->ejecutar($consulta)) {
                    if ($pasajero = $baseDatos->registro()) {
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
            $consulta = "UPDATE pasajero SET 
                        ptelefono= '". $this->getPtelefono() ."',
                        idviaje= '". $this->getObjViaje()->getIdViaje() ."'
                        WHERE pdocumento ='". $this->getDocumento() ."' ";
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

        //Metodo para eliminar un pasajero de la base de datos
        public function eliminar () {
            $baseDatos = new DataBase;
            $consulta = "DELETE FROM pasajero WHERE pdocumento= '" . $this->getDocumento() . "'";
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

        //Metodo para listar pasajeros
        public function listar($condicion = "") {
            $arregloPasajero = null;
            $baseDatos = new DataBase();
            $consulta = "SELECT * FROM pasajero ";
            if ($condicion != "") {
                $consulta .= "WHERE $condicion ";
            } 
            if  ($baseDatos->Iniciar()) {
                if($baseDatos->ejecutar($consulta)) {
                    $arregloPasajero = [];
                    while ($pasajeroEncontrado = $baseDatos->registro()) {
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