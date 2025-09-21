<?php


Class Viaje {
    //Atributos
    private $idviaje;
    private $vdestino;
    private $origen;
    private $fecha;
    private $objEmpresa;
    private $vimporte;
    private $vCantMaxPasajeros;
    private $mensajeOperacion;
    private $objResponsableV;
    private $objPasajeros;

    //Constructor de la clase (inicializa)
    public function __construct() {
        $this->idviaje = "";
        $this->vdestino = "";
        $this->vCantMaxPasajeros = 0;
        $this->objResponsableV = null;
        $this->objEmpresa = null;
        $this->vimporte = 0;
        $this->objPasajeros = null;
    }

    //Constructor de la clase con valores
    public function crear(string $vdestino, int $vCantMaxPasajeros, array $objPasajeros, ResponsableViaje $objResponsableV, float $vimporte, Empresa $objEmpresa) {
        $this->setvDestino($vdestino);
        $this->setvCantMaxPasajeros($vCantMaxPasajeros);
        $this->setObjResponsableV($objResponsableV);
        $this->setvImporte($vimporte);
        $this->setObjEmpresa($objEmpresa);
        $this->setObjPasajeros($objPasajeros);
    }


    public function __toString() {
        $cadena ="ID del viaje: " . $this->getIdViaje() . "\n";  
        $cadena .= "Destino: " . $this->getvDestino() . "\n"; 
        $cadena .="Costo del viaje: " . $this->getvImporte() . "\n";
        $cadena .="Cantidad maxima de pasajeros: " . $this->getvCantMaxPasajeros() . "\n";
        $cadena.="-------------------------------------------\n". "Responsable: \n" .
        $this->getObjResponsableV();
        $arrayPasajeros = $this->getobjPasajeros();
        if (count($arrayPasajeros) !=0) {
            $cadena .= "-------------------------------------------\n";
            $cadena .= "Pasajeros: \n";
            for ($i=0; $i <count($arrayPasajeros); $i++) {
                $unPasajero = $arrayPasajeros[$i];
                $cadena .= $unPasajero ."\n";
            }
        } else {
            $cadena .= "-------------------------------------------\n";
            $cadena .= "No hay Pasajeros.\n";
        }
        return $cadena;
    }

    //Setters y getters
    public function getIdViaje() {
        return $this->idviaje;
    }
    public function getObjPasajeros() {
        return $this->objPasajeros;
    }
    public function getOrigen() {
        return $this->origen;
    }
    public function getvDestino() {
        return $this->vdestino;
    }
    public function getvCantMaxPasajeros() {
        return $this->vCantMaxPasajeros;
    }
    public function getvImporte() {
        return $this->vimporte;
    }
    public function getObjResponsableV() {
        return $this->objResponsableV;
    }
    public function getMensajeOperacion() {
        return $this->mensajeOperacion;
    }
    public function getObjEmpresa() {
        return $this->objEmpresa;
    }

    public function setIdViaje($value)  {
        $this->idviaje = $value;    
    }

    public function setOrigen($value) {
        $this->origen = $value;
    }
    public function setvDestino($value) {
        $this->vdestino = $value;
    }

    public function setObjPasajeros($valor){
        $this->objPasajeros = $valor;
    }

    public function setObjResponsableV($value) {
        $this->objResponsableV = $value;
    }
    public function setvCantMaxPasajeros($value) {
        $this->vCantMaxPasajeros = $value;
    }
    public function setvImporte($value) {
        $this->vimporte = $value; 
    }
    public function setMensajeOperacion($value) {
        $this->mensajeOperacion = $value;
    }
    public function setObjEmpresa($value) {
        $this->objEmpresa = $value;
    }

    //Metodo para insertar un objeto viaje a la base de datos
    public function insertar() {
        $dataBase = new DataBase();
        $resp = false;
        $consultaInsertar = "INSERT INTO viaje(vdestino, vcantmaxpasajeros, idempresa, rnumeroempleado, vimporte) VALUES
         ('" . $this->getvDestino() . "','" . $this->getvCantMaxPasajeros() . "','" . $this->getObjEmpresa()->getIdEmpresa() . "','" . 
         $this->getObjResponsableV()->getRnumeroEmpleado() . "','" . $this->getvImporte() . "')";

         if ($dataBase->Iniciar()) {
            if ($id = $dataBase->devuelveIDInsercion($consultaInsertar)) {
                $this->setIdViaje($id);
                $resp = true;
            } else {
                $this->setMensajeOperacion($dataBase->getError());
            }
         } else {
            $this->setMensajeOperacion($dataBase->getError());
         }
         return $resp;
    }

    //Busca viaje por su ID
    public function buscar(int $idviaje) {
        $dataBase = new DataBase();
        $rta = false;
        $consulta = "SELECT * FROM viaje WHERE idviaje = " . $idviaje;
        if ($dataBase->Iniciar()) {
            if ($dataBase->ejecutar($consulta)) {
                if ($viaje = $dataBase->registro()) {
                    $pasajero = new Pasajero();
                    $this->setIdViaje($idviaje);
                    $empresa = new Empresa();
                    $empresa->setIdEmpresa($viaje['idempresa']);
                    $empleado = new ResponsableViaje();
                    $empleado->setRnumeroEmpleado($viaje['rnumeroempleado']);
                    $empleado->buscar($viaje['rnumeroempleado']);
                    $this->cargar(
                        $viaje['vdestino'],
                        $viaje['vcantmaxpasajeros'],
                        [],
                        $empleado,
                        $viaje['vimporte'],
                        $empresa,
                    );
                    $rta = true;
                } 
            } else {
                $this->setMensajeOperacion($dataBase->getError());
            }
        } else {
            $this->setMensajeOperacion($dataBase->getError());
        }
        return $rta;
    }

    //Metodo para modificar los datos de un viaje
    public function modificar() {
        $dataBase = new DataBase();

        $consulta = "UPDATE viaje SET 
        vdestino='" . $this->getvDestino() . "',
        vcantmaxpasajeros= '" . $this->getvCantMaxPasajeros() . "',
        idempresa= '" . $this->getObjEmpresa()->getIdEmpresa() . "',
        rnumeroempleado= '" . $this->getObjResponsableV()->getRnumeroEmpleado() . "',
        vimporte= '" . $this->getvImporte() . "'
        WHERE idviaje= " . $this->getIdViaje();
        $rta = false;

        if ($dataBase->Iniciar()) {
            if ($dataBase->ejecutar($consulta)) {
                $rta = true;
            } else {
                $this->setMensajeOperacion($dataBase->getError());
            }
        } else {
            $this->setMensajeOperacion($dataBase->getError());
        }
        return $rta;
    }

    //Metodo para eliminar un viaje de la base de datos
    public function eliminar() {
        $dataBase = new DataBase();
        $rta = false;
        $consulta = "DELETE FROM viaje WHERE idviaje = '" . $this->getIdViaje() . "'";

        if ($dataBase->Iniciar()) {
            if($dataBase->ejecutar($consulta)) {
                $rta = true;
            } else {
                $this->setMensajeOperacion($dataBase->getError());
            }
        } else {
            $this->setMensajeOperacion($dataBase->getError());
        }
        return $rta;
    }

    //Metodo para listar los viajes
    public function listar($condicion = "") {
        //Si se envia un valor por parametro se pisa el valor de condicion
        $viajes = null;
        $dataBase = new DataBase();
        $consulta = "SELECT * FROM viaje ";
        //Si la condicion es diferente de vacio, concatenamos la condicion
        if ($condicion != "") {
            $consulta .= "WHERE $condicion ";
        }
        if ($dataBase->Iniciar()) {
            if ($dataBase->ejecutar($consulta)) {
                $viajes = [];
                while ($viajeEncontrado = $dataBase->registro()) {
                    $pasajero = new Pasajero();
                    $responsable = new ResponsableViaje();
                    $responsable->buscar($viajeEncontrado['rnumeroempleado']);
                    $empresa = new Empresa();
                    $empresa->buscar($viajeEncontrado['idempresa']);                    
                    $viaje = new Viaje();
                    $viaje->setIdViaje($viajeEncontrado['idviaje']);
                    $listaPasajeros = $pasajero->listar("idviaje= ".$viaje->getIdViaje());
                    $viaje->cargar(
                        $viajeEncontrado['vdestino'],
                        $viajeEncontrado['vcantmaxpasajeros'],
                        $listaPasajeros,
                        $responsable,
                        $viajeEncontrado['vimporte'],
                        $empresa
                        );
                    array_push($viajes, $viaje);
                }
            } else {
                $this->setMensajeOperacion($dataBase->getError());
            }
        } else {
            $this->setMensajeOperacion($dataBase->getError());
        }
        return $viajes;
    }
}