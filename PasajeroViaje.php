<?php

class PasajeroViaje {
    private $pdocumento;
    private $idviaje;
    private $mensajeOperacion;

    public function __construct() {
        $this->pdocumento = "";
        $this->idviaje = "";
    }

    public function cargar($pdocumento, $idviaje) {
        $this->setPdocumento($pdocumento);
        $this->setIdViaje($idviaje);
    }

    public function getPdocumento() {
        return $this->pdocumento;
    }
    public function getIdViaje() {
        return $this->idviaje;
    }
    public function getMensajeOperacion() {
        return $this->mensajeOperacion;
    }

    public function setPdocumento($val) {
        $this->pdocumento = $val;
    }
    public function setIdViaje($val) {
        $this->idviaje = $val;
    }
    public function setMensajeOperacion($val) {
        $this->mensajeOperacion = $val;
    }

    public function __toString() {
        return "Documento: " . $this->getPdocumento() . "\n" .
               "IdViaje: " . $this->getIdViaje() . "\n";
    }

    public function insertar() {
        $baseDatos = new DataBase();
        $resp = false;
        $consulta = "INSERT INTO pasajero_viaje(pdocumento, idviaje) VALUES ('" . $this->getPdocumento() . "', '" . $this->getIdViaje() . "')";
        if ($baseDatos->Iniciar()) {
            if ($baseDatos->ejecutar($consulta)) {
                $resp = true;
            } else {
                $this->setMensajeOperacion($baseDatos->getError());
            }
        } else {
            $this->setMensajeOperacion($baseDatos->getError());
        }
        return $resp;
    }

    public function buscar($pdocumento, $idviaje) {
        $baseDatos = new DataBase();
        $rta = false;
        $consulta = "SELECT * FROM pasajero_viaje WHERE pdocumento = '" . $pdocumento . "' AND idviaje = '" . $idviaje . "'";
        if ($baseDatos->Iniciar()) {
            if ($baseDatos->ejecutar($consulta)) {
                if ($registro = $baseDatos->registro()) {
                    if (is_array($registro)) {
                        $this->cargar($registro['pdocumento'], $registro['idviaje']);
                        $rta = true;
                    }
                }
            } else {
                $this->setMensajeOperacion($baseDatos->getError());
            }
        } else {
            $this->setMensajeOperacion($baseDatos->getError());
        }
        return $rta;
    }

    public function eliminar() {
        $baseDatos = new DataBase();
        $resp = false;
        $consulta = "DELETE FROM pasajero_viaje WHERE pdocumento = '" . $this->getPdocumento() . "' AND idviaje = '" . $this->getIdViaje() . "'";
        if ($baseDatos->Iniciar()) {
            if ($baseDatos->ejecutar($consulta)) {
                $resp = true;
            } else {
                $this->setMensajeOperacion($baseDatos->getError());
            }
        } else {
            $this->setMensajeOperacion($baseDatos->getError());
        }
        return $resp;
    }

    public function listar($condicion = "") {
        $arreglo = null;
        $baseDatos = new DataBase();
        $consulta = "SELECT * FROM pasajero_viaje ";
        if ($condicion != "") {
            $consulta .= "WHERE $condicion ";
        }
        if ($baseDatos->Iniciar()) {
            if ($baseDatos->ejecutar($consulta)) {
                $arreglo = [];
                while ($registro = $baseDatos->registro()) {
                    if (!is_array($registro)) continue;
                    $obj = new PasajeroViaje();
                    $obj->cargar($registro['pdocumento'], $registro['idviaje']);
                    array_push($arreglo, $obj);
                }
            } else {
                $this->setMensajeOperacion($baseDatos->getError());
            }
        } else {
            $this->setMensajeOperacion($baseDatos->getError());
        }
        return $arreglo;
    }
}
