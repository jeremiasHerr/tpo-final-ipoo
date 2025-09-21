<?php
include_once "Persona.php";

    class ResponsableViaje extends Persona {

        private $rnumeroEmpleado;
        private $rnumeroLicencia;
        
        public function __construct () {
            parent::__construct();
            //Este atributo luego ira incrementando
            $this -> rnumeroLicencia = 0;

        }

        //Setters y getters
        public function getRnumeroEmpleado () {
            return $this -> rnumeroEmpleado;
        }
        public function getRnumeroLicencia () {
            return $this -> rnumeroLicencia;
        }
        public function getMensajeOperacion () {
            return $this -> mensajeOperacion;
        }

        public function setRnumeroEmpleado ($value) {
            $this -> rnumeroEmpleado = $value;
        }
        public function setRnumeroLicencia ($value) {
            $this -> rnumeroLicencia = $value;
        }
        public function setMensajeOperacion ($value) {
            $this -> mensajeOperacion = $value;
        }

        public function __toString() {
            $cadena = parent::__toString();
            $cadena .=
            "El numero de empleado es: " . $this->getRnumeroEmpleado() . "\n" . 
            "El numero de licencia es: " . $this->getRnumeroLicencia() . "\n";
            return $cadena;
        }


        //Constructor con valores
        public function cargarRe($pdocumento, $pnombre, $papellido, $numeroLicencia,$numeroEmpleado) {
            parent::cargar($pdocumento, $pnombre, $papellido);
            $this ->setRnumeroLicencia($numeroLicencia);
            $this ->setRnumeroEmpleado($numeroEmpleado);
        }

        //Metodo para instertar los datos de un objeto a la base de datos
        public function insertar() {
            $baseDatos = new DataBase;
            $resultado = false;
            if (parent::insertar()) {
            $consultaInsertar = "INSERT INTO responsable(rnumeroLicencia, rdocumento) 
                                 VALUES (". $this->getRnumeroLicencia() .",
                                       '". $this->getDocumento() ."')";
            if ($baseDatos->Iniciar()) {
                if ($id = $baseDatos->devuelveIDInsercion($consultaInsertar)) {
                    $this->setRnumeroEmpleado($id);
                    $resultado = true;
                } else {
                    $this->setMensajeOperacion($baseDatos->getError());
                }
            } else {
                $this->setMensajeOperacion($baseDatos->getError());
            }
            return $resultado;
            }
        }

        //Metodo para buscar responsable por su numeroEmpleado
        public function buscar ($rnumeroEmpleado) {
            $baseDatos = new DataBase();
            $consulta = "SELECT * FROM responsable WHERE rnumeroempleado =". $rnumeroEmpleado ;
            $respuesta = false;
            if($baseDatos->Iniciar()) {
                if($baseDatos->Ejecutar($consulta)) {
                    if($responsableViaje = $baseDatos->Registro()) {
                        parent::buscar($responsableViaje['rdocumento']);
                        $this->setRnumeroLicencia($responsableViaje['rnumerolicencia']);
                        $this->setRnumeroEmpleado($responsableViaje['rnumeroempleado']);
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

        //Metodo para modificar los datos de un responsable en la base de datos
        public function modificar () {
            $baseDatos = new DataBase;
            $persona = new Persona;
            $respuesta = false;
            if (Persona::modificar()) {
            $consulta = "UPDATE responsable 
                         SET rnumerolicencia = ". $this->getRnumeroLicencia() ."
                         WHERE rnumeroempleado = '". $this->getRnumeroEmpleado() ."' ";

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

        //Eliminar de la base de datos
        public function eliminar () {
            $baseDatos = new DataBase;
            $consulta = "DELETE FROM responsable WHERE rnumeroempleado = ". $this->getRnumeroEmpleado() ." ";
            $respuesta = false;
            if ($baseDatos->Iniciar()) {
                if($baseDatos->Ejecutar($consulta)) {
                    $respuesta = true;
                } else {
                    $this->setMensajeOperacion($baseDatos->getError());
                }
            } else {
                $this->setMensajeOperacion($baseDatos->getError());
            }
            return $respuesta;
        }

        //Metodo para listar responsables
        public function listar ($condicion = "") {
            $arregloResponsableV = null;
            $baseDatos = new DataBase();
            $consulta = "SELECT * FROM responsable ";
            if ($condicion != "") {
                $consulta .= "WHERE ".$condicion;
            }
            if ($baseDatos->Iniciar()) {
                if ($baseDatos->Ejecutar($consulta)) {
                    $arregloResponsableV = [];
                    while ($responsablevEncontrado = $baseDatos->Registro()) {
                        $responsableV = new ResponsableViaje();
                        $responsableV->buscar($responsablevEncontrado['rnumeroempleado']);                        array_push($arregloResponsableV, $responsableV);
                    }
                } else {
                    $this->setMensajeOperacion($baseDatos->getError());
                }
            } else {
                $this->setMensajeOperacion($baseDatos->getError());
            }
            return $arregloResponsableV;
        }
    }