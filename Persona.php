<?php
    include_once "Persona.php";
    class Pasajero extends Persona {
        //Definimo el atributos de la clase 
        private $ptelefono;
        private $objViaje;
        private $mensajeOperacion;

        //Insertamos el nuevo atributo a la clase padre
        public function __construct () {
            parent::__construct();
            $this -> ptelefono = 0;
            $this->objViaje = null;
        }

        //Definimos los get y set
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

        //Redefinimos el metodo __toString
        public function __toString () {
            $cadena = parent::__toString();
            $cadena .=
            "El telefono es: " . $this->getPtelefono() . "\n".
            "El id del viaje es: " . $this->getObjViaje()->getIdViaje() . "\n" ."\n";
            return $cadena;
        } 

        //Definimos la funcion de carga
        public function cargarPa ($pdocumento, $pnombre, $papellido,  $ptelefono, $objViaje) {
            parent::cargar($pdocumento, $pnombre, $papellido);
            $this ->setPtelefono($ptelefono);
            $this ->setObjViaje($objViaje);
        } 

        //Definimos los metodos necesarios para la base de datos
        //Primero realizamos el metodo para insertar informacion a la base sobre pasajero
        public function insertar () {
            $baseDatos = new BaseDatos;
            $pasajero = false;
            if (parent::insertar()) {
            $consultaInsertar = 
            "INSERT INTO pasajero(pdocumento, ptelefono, idviaje) 
            VALUES ('" . $this->getDocumento() . "',
                    '" . $this->getPtelefono() . "',
                    '" . $this->getObjViaje()->getIdViaje() . "'
                    )";
            //El primer if nos ayuda a Iniciar la base de datos
            if ($baseDatos->Iniciar()) {
                //Este segundo if nos ayuda a averiguar si ya hay una consulta insertada
                if ($baseDatos->Ejecutar($consultaInsertar)) {
                    $pasajero = true;
                    //Los siguientes else son por si da error
                } else {
                    $this->setMensajeOperacion($baseDatos->getError());
                }
            } else {
                $this->setMensajeOperacion($baseDatos->getError());
            }
            }
            //Retornamos el booleano
            return $pasajero;
        }

        //Definimos la funcion buscar que nos ayuda a ubicar al pasajero por su id
        public function buscar($pdocumento) {
            $baseDatos = new BaseDatos;
            $consulta = "SELECT * FROM pasajero WHERE pdocumento ='" . $pdocumento . "'";
            $respuesta = false;
            if ($baseDatos->Iniciar()) {
                if ($baseDatos->Ejecutar($consulta)) {
                    if ($pasajero = $baseDatos->Registro()) {
                        $viaje = new Viaje;
                        $viaje->buscar($pasajero['idviaje']);
                        parent::buscar($pasajero['pdocumento']);
                        $this->setPtelefono($pasajero['ptelefono']);
                        $this -> cargarPa(
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

        //Definimos el metodo modificar para modificar la informacion de un pasajero
        public function modificar () {
            $baseDatos = new BaseDatos();
            if (parent::modificar()) {
            $consulta = "UPDATE pasajero SET 
                        ptelefono= '". $this->getPtelefono() ."',
                        idviaje= '". $this->getObjViaje()->getIdViaje() ."'
                        WHERE pdocumento ='". $this->getDocumento() ."' ";
            $respuesta = false;
            if ($baseDatos->Iniciar()) {
                if ($baseDatos->Ejecutar($consulta)) {
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

        //Definimos el metodo eliminar que nos ayuda a eliminar a un pasajero de la tabla
        public function eliminar () {
            $baseDatos = new BaseDatos;
            $consulta = "DELETE FROM pasajero WHERE pdocumento= '" . $this->getDocumento() . "'";
            $respuesta = false;
            if ($baseDatos->Iniciar()) { 
                if ($baseDatos->Ejecutar($consulta)) {
                    $respuesta = true;
                } else {
                $this->setMensajeOperacion($baseDatos->getError());
                } 
            } else {
                $this->setMensajeOperacion($baseDatos->getError());
            }
            return $respuesta;
        }

        //Definimos el metodo listar para ayudarnos todas las tablas de pasajero con o sin condicion
        public function listar($condicion = "") {
            $arregloPasajero = null;
            $baseDatos = new BaseDatos();
            $consulta = "SELECT * FROM pasajero ";
            if ($condicion != "") {
                $consulta .= "WHERE $condicion ";
            } 
            if  ($baseDatos->Iniciar()) {
                if($baseDatos->Ejecutar($consulta)) {
                    $arregloPasajero = [];
                    while ($pasajeroEncontrado = $baseDatos->Registro()) {
                        $pasajero = new Pasajero();
                        $viaje= New Viaje();
                        $viaje->buscar($pasajeroEncontrado['idviaje']);
                        $persona = new Persona();
                        $documento=$pasajeroEncontrado['pdocumento'];
                        $persona->buscar($documento);
                        $pasajero->cargarPa(
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