<?php
// Routes

$app->get('/[{name}]', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});


class Order extends Illuminate\Database\Eloquent\Model {

    protected $fillable = ['title'];
    public $timestamps = false;
}

class Pic extends Illuminate\Database\Eloquent\Model {

    protected $table = 'pic';
    protected $fillable = ['deviceId','date','nombreImagen','latitud','longitud','positive','negative','warning'];
    public $timestamps = false;
}

$app->get('/json/t', function ($request, $response, $args) {

    $json = '{"foo-bar": 12345}';
//
    $obj = json_decode($json);
    $arr = array('a' => 'asdas+dsa', 'b' => "asds/adsada");
    $ot = base64_encode("prueba//finprueba");
    //print $obj->{'foo-bar'}; // 12345

    return $response->withJson($arr);

});


$app->post('/obtener/pic', function (){
    $dato = file_get_contents('php://input');
    $pic = json_decode($dato,true);

    $enlace = mysqli_connect('localhost','root','','pictwin');
    $query = "SELECT * FROM picture WHERE deviceId <> '".$pic['deviceId']."' ORDER BY RAND() LIMIT 1";

    $resultado = $enlace->query($query);

    $arreglo = $resultado->fetch_assoc();

    mysqli_close($enlace);

    $path = 'imagenes/'.$arreglo['file'];
    $data = file_get_contents($path);
    $code = base64_encode($data);
    $arreglo['imagen'] =$code;
    $json = json_encode($arreglo);
    return $json;
});

$app->post('/insertar/pic',function(){
    $json = file_get_contents('php://input');
    $pic = json_decode($json,true);

    $ifp = fopen('imagenes/'.$pic['nombre'], "w+");
    fwrite($ifp, base64_decode($pic['file']));
    fclose($ifp);

    $enlace = mysqli_connect('localhost','root','','pictwin');
    $query = "INSERT INTO picture (deviceId,latitud,longitud,fecha,positive,negative,warnings,file) VALUES ('".$pic['deviceId']."','".$pic['latitude']."','".$pic['longitude']."','".$pic['date']."',0,0,0,'".$pic['nombre']."')";
    $enlace->query($query);
    mysqli_close($enlace);

});

$app->post('/insertar/positive',function(){
    $json = file_get_contents('php://input');
    $pic = json_decode($json,true);

    $enlace = mysqli_connect('localhost','root','','pictwin');

    $querySelect = "SELECT * FROM picture WHERE deviceId = '".$pic['deviceId']."' AND file = '".$pic['nombre']."'";
    $resultado = $enlace->query($querySelect);
    $arreglo = $resultado->fetch_assoc();
    $arreglo['positive'] = $arreglo['positive'] +1;

    $query = "UPDATE picture SET positive='".$arreglo['positive']."' WHERE deviceId = '".$pic['deviceId']."' AND file = '".$pic['nombre']."'";

    $enlace->query($query);
    mysqli_close($enlace);

    $params = array('positive' => $arreglo['positive'],'negative' => $arreglo['negative']);
    $json = json_encode($params);

    return $json;

});

$app->post('/insertar/negative',function(){
    $json = file_get_contents('php://input');
    $pic = json_decode($json,true);

    $enlace = mysqli_connect('localhost','root','','pictwin');

    $querySelect = "SELECT * FROM picture WHERE deviceId = '".$pic['deviceId']."' AND file = '".$pic['nombre']."'";
    $resultado = $enlace->query($querySelect);
    $arreglo = $resultado->fetch_assoc();
    $arreglo['negative'] = $arreglo['negative'] +1;

    $query = "UPDATE picture SET negative='".$arreglo['negative']."' WHERE deviceId = '".$pic['deviceId']."' AND file = '".$pic['nombre']."'";

    $enlace->query($query);
    mysqli_close($enlace);

    $params = array('positive' => $arreglo['positive'],'negative' => $arreglo['negative']);
    $json = json_encode($params);

    return $json;

});

$app->post('/obtener/parametros',function(){

    $datos = file_get_contents('php://input');
    $pic = json_decode($datos,true);

    $enlace = mysqli_connect('localhost','root','','pictwin');
    $query = "SELECT * FROM picture WHERE deviceId = '".$pic['deviceId']."' AND file = '".$pic['nombre']."'";

    $resultado = $enlace->query($query);

    $arreglo = $resultado->fetch_assoc();

    mysqli_close($enlace);

    //$path = 'imagenes/'.$arreglo['file'];
    //$data = file_get_contents($path);
    //$code = base64_encode($data);

    //$arreglo['imagen'] =$code;
    $params = array('positive' => $arreglo['positive'],'negative' => $arreglo['negative']);
    //$positive = array('positive' => $pic['deviceId'],'negative' => $pic['nombre']);
    $json = json_encode($params);

    return $json;

});
