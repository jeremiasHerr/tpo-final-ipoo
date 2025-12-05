<?php
class Persona
{

    //Atributos persona
    private $nombre;
    private $apellido;
    private $documento;
    protected $mensajeOperacion;

    //Metodo constructor de la clase
    public function __construct()
    {
        $this->nombre = "";
        $this->apellido = "";
        $this->documento = "";
    }
    //Setters y getters
    public function getNombre()
    {
        return $this->nombre;
    }

    public function getApellido()
    {
        return $this->apellido;
    }
    
    public function getDocumento()
    {
        return $this->documento;
    }

    public function getMensajeoperacion()
    {
        return $this->mensajeOperacion;
    }

    public function setDocumento($value)
    {
        $this->documento = $value;
    }
    public function setNombre($value)
    {
        $this->nombre = $value;
    }

    public function setApellido($value)
    {
        $this->apellido = $value;
    }

    public function setMensajeoperacion($mensaje)
    {
        $this->mensajeoperacion = $mensaje;
    }

    public function __toString()
    {
        return "Nombre: " . $this->getNombre() . "\n" .
            "Apellido: " . $this->getApellido() . "\n".
            "Documento: " . $this->getDocumento() . "\n";

    }
    //Constructor con valores
    public function cargar($pdocumento, $pnombre, $papellido)
    {
        $this->setNombre($pnombre);
        $this->setApellido($papellido);
        $this->setDocumento($pdocumento);
    }

    //Metodo para insertar los datos de un objeto en la base de datos
    public function insertar()
    {
        $database = new DataBase;
        $persona = false;
        $consultaInsertar = "INSERT INTO persona(nombre, apellido ,documento) VALUES (
        '"  . $this->getNombre() . "',
        '" . $this->getApellido() . "',
        '" . $this->getDocumento() . "'
        )";

        if ($database->Iniciar()) {

            if ($database->Ejecutar($consultaInsertar)) {
                $persona =  true;
            } else {
                $this->setMensajeoperacion($database->getError());
            }
        } else {
            $this->setMensajeoperacion($database->getError());
        }
        return $persona;
    }

    public function buscar($documento)
    {
        $database = new DataBase;
        $consulta = "SELECT * FROM persona WHERE documento = '". $documento ."'";
        $rta = false;
        if ($database->Iniciar()) {
            if ($database->Ejecutar($consulta)) {
                if ($persona = $database->Registro()) {
                    $this->cargar(
                        $persona['documento'],
                        $persona['nombre'],
                        $persona['apellido']
                    );
                    $rta = true;
                }
            } else {
                $this->setMensajeoperacion($database->getError());
            }
        } else {
            $this->setMensajeoperacion($database->getError());
        }
        return $rta;
    }


    public function modificar()
    {
        $database = new DataBase;
        $consulta = "UPDATE persona SET 
                    nombre = '" . $this->getNombre() . "',
                    apellido = '" . $this->getApellido() . "' 
                    WHERE documento = '" . $this->getDocumento() . "'";
        $rta = false;
        if ($database->Iniciar()) {
            if ($database->Ejecutar($consulta)) {
                $rta = true;
            } else {
                $this->setMensajeoperacion($database->getError());
            }
        } else {
            $this->setMensajeoperacion($database->getError());
        }
        return $rta;
    }



    public function eliminar()
    {
        $database = new DataBase;
        $consulta = "DELETE FROM persona WHERE documento = " . $this->getDocumento() . " ";
        $rta = false;
        if ($database->Iniciar()) {
            if ($database->Ejecutar($consulta)) {
                $rta = true;
            } else {
                $this->setMensajeoperacion($database->getError());
            }
        } else {
            $this->setMensajeoperacion($database->getError());
        }
        return $rta;
    }

    public function listar($condicion = ""){
        $arregloPersona = null;
        $database = new DataBase;
        $consulta = "SELECT * FROM persona ";
        if ($condicion != ""){
            $consulta .= "WHERE $condicion ";
        }
        $consulta .= "ORDER BY apellido";

        if ($database->Iniciar()){
            if ($database->Ejecutar($consulta)){
                $arregloPersona = [];
                while ($personaEncontrada = $database->Registro()){
                    $persona = new self;
                    $persona->cargar(
                        $personaEncontrada["nombre"],
                        $personaEncontrada["apellido"],
                        $personaEncontrada["documento"]
                    );
                    array_push($arregloPersona, $persona);
                }
            } else {
                $this->setMensajeoperacion($database->getError());
            }
        } else {
            $this->setMensajeoperacion($database->getError());
        }

        return $arregloPersona;
    }
}