<?php

namespace Controllers;
use MVC\Router;
use Classes\Email;
use Model\Usuario;

class LoginController {
    public static function login(Router $router) {
        $alertas = [];
        $auth = new usuario;

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new usuario($_POST);

            $alertas = $auth->validarLogin();

            if(empty($alertas)) {
                //Compobar que exista el usuario
                $usuario = usuario::where('email', $auth->email);

                if($usuario){
                    //verificar el password
                    if($usuario->comprobarPasswordAndVerificado($auth->password)) {
                        //Autentificar el usuario
                        session_start();

                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        //Redireccionamiento
                        if($usuario->admin === '1'){
                            $_SESSION['admin'] = $usuario->admin ?? null;
                            header('Location: /admin');
                        } else {
                            header('Location: /cita');
                        }
                    }
                } else {
                    usuario::setAlerta('error', 'Usuario no registrado');
                }
            }
        }
        $alertas = usuario::getAlertas();

        $router->render('auth/login', [
            'alertas' => $alertas,
            'auth' => $auth
        ]);
    }
    public static function logout() {
        session_start();

        $_SESSION = [];

        header('Location: /');
    }
    public static function olvide(Router $router) {
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new usuario($_POST);
            $alertas = $auth->validarEmail();

            if(empty($alertas)) {
                $usuario = usuario::where('email', $auth->email);
                
                if($usuario && $usuario->confimado === '1') {
                    //Generar token
                    $usuario->creartoken();
                    $usuario->guardar();

                    //Enviar email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();

                    //Alerta de exito
                    usuario::setAlerta('exito', 'Revisa tu email');
                } else {
                    usuario::setAlerta('error', 'El usuario no existe o no esta confirmado');
                }
            }
        }
        $alertas = usuario::getAlertas();
        $router->render('auth/olvide', [
            'alertas' => $alertas
        ]);
    }
    public static function crear(Router $router) {
        $usuario = new Usuario;

        //Alertas vacias
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();
        }

        //Revisar que alertas este vacio
        if(empty($alertas)){
            //Verificar que no este registrado 
            $resultado = $usuario->existeUsuario();

            if($resultado->num_rows) {
                $alertas = usuario::getAlertas();
            } else {
                //Hashear password
                $usuario->hashPassword();
                //Generar token
                $usuario->crearToken();
                 //Enviar el email
                $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                $email->enviarConfirmacion();
                //Crear el usuario 
                $resultado = $usuario->guardar();

                // debuguear ($usuario);

               // debuguear($usuario);
                if($resultado) {
                    header('Location: /mensaje');
                }
            }
        }
        
        $router->render('auth/crear-cuenta', [
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function confirmar(Router $router) {
        $alertas = [];

        $token = s($_GET['token']);

        $usuario = usuario::where('token', $token);

        if(empty($usuario)) {
            //Mostrar mensaje error
            usuario::setAlerta('error', 'Token no valido');
        } else {
            //Modificar a usuario confirmado
            $usuario->confimado = '1';
            $usuario->token = null;
            $usuario->guardar();
            usuario::setAlerta('exito', 'Cuenta verificada correctamente');
        }
        //Obtener alertas
        $alertas = usuario::getAlertas();
        //Renderizar la vista
        $router->render('auth/confirmar-cuenta', ['alertas' => $alertas]);
    }
    public static function mensaje(Router $router) {
        $router->render('auth/mensaje');
    }
    public static function recuperar(Router $router) {
       
        $alertas = [];
        $token = s($_GET['token']);
        $error = false;

        $usuario = usuario::where('token', $token);
       
        if(empty($usuario)) {
            usuario::setAlerta('error', 'Token no valido');
            $error = true;
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = new usuario($_POST);
            $alertas = $password->validarPassword();

            if(empty($alertas)) {
                $usuario->password = null;
                $usuario->password = $password->password;
                $usuario->hashPassword();
                $usuario->token = null;

                $resultado = $usuario->guardar();
                if($resultado) {
                    header('Location: /');
                }
            }
        }

        $alertas = usuario::getAlertas();
        $router->render('auth/recuperar-password', [
            'alertas' => $alertas,
            'error' => $error
        ]);
    }
}