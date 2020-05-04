<?php
require_once __DIR__ .'./vendor/autoload.php';
use \Firebase\JWT\JWT;
// require './clases/paises.php';
require_once './clases/funciones.php';
require_once './clases/usuarios.php';
require_once './clases/pizzas.php';
require_once './clases/response.php';

//metodo y path al que se accedio
$metodo = $_SERVER["REQUEST_METHOD"];
$path = $_SERVER['PATH_INFO'];

//formato de JWT
$key = "pro3-parcial";
$payload = array(
        "iss" => "http://example.org",
        "aud" => "http://example.com",
        "iat" => 1356999524,
        "nbf" => 1357000000,
        "email" => 'mail@mail.com',
        "tipo" => 'no registrado'
);

//JSEND a devolver
$response = new response();

if($metodo == 'POST')
{
    if(!empty($_POST))
    {
        switch($path)
        {
            case '/usuario':
                // echo "estoy en SIGNIN <br>";
                $archivo = './files/users.json';
                // echo "POST con datos <br>";
                if(isset($_POST['email'])&&isset($_POST['clave'])&&isset($_POST['tipo']) &&
                $_POST['email'] != '' && $_POST['clave'] != '' && $_POST['tipo'] != '')
                    {
                        // echo "datos OK <br>";
                        $email = $_POST['email'];
                        $pass = $_POST['clave'];
                        $tipo = $_POST['tipo'];
                        // echo "usuario: $nombre apellido: $apellido , email: $email";
                        if ($tipo != 'encargado' && $tipo != 'cliente') {
                            $response->data = 'Tipo de usuario incorrecto, validos: -encargado- -cliente-';
                            echo json_encode($response);
                        }
                        else
                        {
                            $cliente = new usuario($email, $pass,  $tipo);
                            // echo "USER: <br>";
                            // var_dump($user);
                            // echo "<br>";
                            $respuesta = $cliente->guardarUsuario($archivo);
                            echo($respuesta);
                        }
                    }
                    else {
                        $response->data = 'falta informar algun parametro en el POST';
                        echo json_encode($response);
                    }
                break;
                case '/login':
                    $archivo = './files/users.json';
                    if(isset($_POST['email'])&&isset($_POST['clave']) &&
                    $_POST['email'] != '' && $_POST['clave'] != '')
                    {
                        // echo "datos OK <br>";
                        $email = $_POST['email'];
                        $pass = $_POST['clave'];
                        // echo "usuario: $nombre apellido: $apellido , email: $email";
                        $response = usuario::verificarLogin($archivo,$email,$pass);
                        if($response->status == 'unsucces')
                        {
                            $response->data = 'datos erroneos, verifique';
                            echo json_encode($response);
                        }
                        else 
                        {
                            // $payload = array(
                            //     "iss" => "http://example.org",
                            //     "aud" => "http://example.com",
                            //     "iat" => 1356999524,
                            //     "nbf" => 1357000000,
                            //     "email" => 'mail@mail.com',
                            //     "tipo" => 'no registrado'
                            // );
                            $datos = $response->data;
                            $payload["email"] = $datos->mail;
                            $payload["tipo"] = $datos->tipo;
                            $jwt = JWT::encode($payload, $key);
                            $response->data = $jwt;
                            echo json_encode($response);
                        }
                    }
                    else {
                        $response->data = 'falta informar algun parametro en el POST';
                        echo json_encode($response);
                    }
                break;
                case '/pizzas':
                    // echo "estoy en pizzas";
                        $archivo = './files/pizzas.json';
                        $verifica = true;
                        //obtengo token
                        $headers = getallheaders();
                        //verifico token
                        $token = $headers['token'];
                        // echo $token;
                        if ($token == '') {
                            $verifica = false;
                        }
                        else{
                            try {
                                //code...
                                $decoded = JWT::decode($token, $key, array('HS256'));
                                if ($decoded->tipo != 'encargado') {
                                    $verifica = false;
                                }
                            } catch (\Throwable $th) {
                                //throw $th;
                                $verifica = false;
                                // $response->data = 'token incorrecto';
                                // echo json_encode($response);
                            }
                        }
                        if(!empty($_POST) && $verifica == true)
                        {
                            // echo "token OK";                   
                            if(isset($_POST['tipo'])&&isset($_POST['precio'])&&isset($_POST['stock'])&&isset($_POST['sabor'])&&isset($_POST['stock'])&&isset($_FILES['foto'])
                               && $_POST['tipo'] != '' && $_POST['precio'] != '' && $_POST['stock'] != '' && $_POST['sabor']!= '' && $_POST['stock'] != '')
                            {
                                // echo "datos OK <br>";
                                // var_dump($_FILES);
                                $tipo = $_POST['tipo'];
                                $precio = $_POST['precio'];
                                $stock = $_POST['stock'];
                                $sabor = $_POST['sabor'];
                                // $venta = new pizza($idProd, $cantidad, $usuario);
                                //verifico datos de pizza
                                if(($tipo != 'molde' && $tipo != 'piedra') || ($sabor != 'jamon' && $sabor =!'napo' && $sabor =! 'muzza') )
                                {
                                    $response->data = 'tipo o sabor invalidos';
                                    $verifica = false;
                                    echo json_encode($response);
                                }
                                else
                                {
                                    $verifica = true;
                                }
                                if($verifica == true)
                                {
                                    $foto = $_FILES['foto'];
                                    //obtengo path foto y guardo
                                    //parametros para guardar foto
                                    $fotoName = $foto['name'];
                                    $path = $foto['tmp_name'];
                                    $destino = './imagenes/';
                                    $destiny = funciones::GuardaTemp($path, $destino, $fotoName, $tipo . $sabor); 
                                    if($destino != $destiny)
                                    {
                                        $pizza = new pizza($tipo, $precio, $stock, $sabor, $destiny);
                                        $response = $pizza->guardarProducto($archivo);
                                        echo $response;
                                    }
                                    else
                                    {
                                        $response->data = 'error al subir imagen de producto';
                                        echo $response;
                                    }
                                }
                            }
                            else
                            {
                                $response->data = 'datos POST con errores';
                                echo json_encode($response);
                            }
                    }
                    else
                    {
                        $response->data = 'datos POST o TOKEN con errores';
                        echo json_encode($response);
                    }
                }
            }
            else
            {
                $response->data = 'datos POST vacios';
                        echo json_encode($response);
            }
        }
else
{
    if($metodo == 'GET')
    {

    }
    else
    {
        $response->data = 'metodo de acceso invalido';
    }
}
?>