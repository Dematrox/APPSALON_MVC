<?php  

namespace Model;

class Usuario extends ActiveRecord {
    //Base de datos
    protected static $tabla ='usuarios';
    protected static $columnasDB = ['id', 'nombre', 'apellido', 'email', 'password', 'telefono', 'admin', 'confimado', 'token'];

    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $telefono;
    public $admin;
    public $confimado;
    public $token;

    public function __construct($args = []) {
        $this->id = $args ['id'] ?? null;
        $this->nombre = $args ['nombre'] ?? '';
        $this->apellido = $args ['apellido'] ?? '';
        $this->email = $args ['email'] ?? '';
        $this->password = $args ['password'] ?? '';
        $this->telefono = $args ['telefono'] ?? '';
        $this->admin = $args ['admin'] ?? '0';
        $this->confimado = $args ['confimado'] ?? '0';
        $this->token = $args ['token'] ?? '';
    }

    //Mensajes de validacion para la creacion de una cuenta
    public function validarNuevaCuenta () {
        if(!$this->nombre){
             self::$alertas['error'][] = 'El Nombre es Obligatorio';
        }
        if(!$this->apellido){
             self::$alertas['error'][] = 'El apellido es Obligatorio';
        }
        if(!$this->email){
            self::$alertas['error'][] = 'El email es Obligatorio';
        }
        if(!$this->password){
            self::$alertas['error'][] = 'El password es Obligatorio';
        }
        if(strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El password debe tener minimo 6 caracteres';
        }
        return self::$alertas;
    }

    public function validarLogin () {
        if(!$this->email){
            self::$alertas['error'][] = 'El email no esta registrado';
        }
        if(!$this->password){
            self::$alertas['error'][] = 'El password es Obligatorio';
        }    
        return self::$alertas;
    }

    public function validarEmail () {
        if(!$this->email){
            self::$alertas['error'][] = 'El email no esta registrado';
        }
        return self::$alertas;
    }
    public function validarPassword () {
        if(!$this->password){
            self::$alertas['error'][] = 'El password es Obligatorio';
        }    
        if(strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El password debe tener minimo 6 caracteres';
        }
        return self::$alertas;
    }
    
    //Revisa si existe el usuario
    public function existeUsuario(){
        $query = " SELECT * FROM " . self::$tabla . " WHERE email = '" . $this->email . "' LIMIT 1";
        
        $resultado = self::$db->query($query);

        if($resultado->num_rows) {
            self::$alertas['error'][] = 'El usuario ya esta registrado';
        }

        return $resultado;
    }
    public function hashPassword(){
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }
    public function crearToken() {
        $this->token = uniqid();
    }
    public function comprobarPasswordAndVerificado($password) {
        $resultado = password_verify($password, $this->password);
        if(!$resultado || !$this->confimado) {
            self::$alertas['error'][] = 'Password incorrecto o cuenta no confirmada';
        } else {
            return true;
        }
    }


}