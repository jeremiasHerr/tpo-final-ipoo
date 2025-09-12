<?php

Class Empresa {
    private $idEmpresa;
    private $nombre;
    private $direccion;
    private $mensajeOperacion;

    //Metodo constructor inicializador
    public function __construct() {
        $this->nombre = "";
        $this->direccion = "";
    }

    //Constructor con valores
    public function cargar(string $nombre, string $direccion) {
        $this->setNombre($nombre);
        $this->setDireccion($direccion);
    }                      

    //Setters y getters
    public function getNombre() {
        return $this->nombre;
    }
    public function getDireccion() {
        return $this->direccion;
    }
    public function getIdEmpresa() {
        return $this->idEmpresa;
    }
    public function getMensajeOperacion() {
        return $this->mensajeOperacion;
    }

    public function setNombre($value) {
        $this->nombre = $value;
    }
    public function setDireccion($value) {
        $this->direccion = $value;
    }
    public function setIdEmpresa($value) {
        $this->idEmpresa = $value;
    }
    public function setMensajeOperacion($value) {
        $this->mensajeOperacion = $value;
    }

    
    //Metodo para insertar un objeto empresa a la base de datos
    public function insertar() {
        $base = new DataBase();
        $resp = false;
        $consultaInsertar = "INSERT INTO empresa(enombre, edireccion) VALUES ('". $this->getNombre() ."', '".$this->getDireccion()."')";
        if ($base->Iniciar()) {

            if ($id = $base->devuelveIDInsercion($consultaInsertar)) {
                $this->setIdEmpresa($id);
                $resp = true;
            } else {
                $this->setMensajeOperacion($base->getError());
            }
        } else {
            $this->setMensajeOperacion($base->getError());
        }
        return $resp;
    }

    //Metodo que busca una empresa por su ID
    public function buscar(int $idEmpresa) {
        $base = new DataBase();
        $consultaPersona = "SELECT * FROM empresa WHERE idEmpresa='" . $idEmpresa . "'";
        $rta = false;
        if ($base->Iniciar()) {
            if($base->ejecutar($consultaPersona)) {
                if($empresa = $base->registro()) {
                    if(is_array($empresa)){
                        $this->cargar($empresa['enombre'], $empresa['edireccion']);
                        $this->setIdEmpresa($empresa['idempresa']);
                        $rta = true;
                    }
                } 
            } else {
                $this->setMensajeOperacion($base->getError());
            }
        } else {
            $this->setMensajeOperacion($base->getError());
        }
        return $rta;
    }

    //Metodo para modificar los datos de una empresa
    public function modificar() {
        $base = new DataBase();
        $consulta = "UPDATE empresa SET enombre = '" .$this->getNombre() . "', edireccion = '". $this->getDireccion() . "' WHERE idempresa = " . $this->getIdEmpresa();
        $rta = false;

        if ($base->Iniciar()) {
            if ($base->ejecutar($consulta)) {
                $rta = true;
            } else {
                $this->setMensajeOperacion($base->getError());
            }
        } else {
            $this->setMensajeOperacion($base->getError());
        }
        return $rta;
    }

    //Metodo para eliminar una empresa de la base de datos
    public function eliminar() {
        $base = new DataBase();
        $consulta = "DELETE FROM empresa WHERE idempresa = " . $this->getIdEmpresa();
        $rta = false;
        if ($base->Iniciar()) {
            if ($base->ejecutar($consulta)) {
            $rta =true;
            } else {
                $this->setMensajeOperacion($base->getError());
            }
        } else {
            $this->setMensajeOperacion($base->getError());
        }
        return $rta;
    }

    //Metodo para listar empresas segun una condicion, devuelve en un arreglo cada una
    public function listar($condicion = "") {
        $arregloEmpresa = null;
        $base = new DataBase();
        $consulta = "SELECT * FROM empresa ";
        if ($condicion != "") {
            $consulta .= "WHERE $condicion ";
        }
        $consulta.= "ORDER BY enombre";

        if ($base->Iniciar()) {
            if ($base->ejecutar($consulta)) {
                $arregloEmpresa = [];
                while ($empresaEncontrada = $base->registro()) {
                    $empresa = new Empresa();
                    $empresa->cargar(
                        $empresaEncontrada["enombre"],
                        $empresaEncontrada["edireccion"]
                    );
                    $empresa->setIdEmpresa($empresaEncontrada["idempresa"]);
                    array_push($arregloEmpresa, $empresa);
                }
            } else {
                $this->setMensajeOperacion($base->getError());
            }
        } else {
            $this->setMensajeOperacion($base->getError());
        }
        return $arregloEmpresa;
    }

    public function __toString() {
        return 
        "Nombre: " . $this->getNombre() . "\n" . 
        "Direccion: " . $this->getDireccion() . "\n" . 
        "ID Empresa: " . $this->getIdEmpresa() . "\n";
    }

 }