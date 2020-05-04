<?php
//ar -> manejador
class Funciones
{
    public static function Listar($archivo)
    {
        $miarray = array(); 
        if(file_exists($archivo))
        {
            $ar = fopen($archivo,"r"); 
            while(!feof($ar) )
            {
                $linea = fgets($ar);
                if(!empty($linea)) 
                {
                    array_push($miarray,json_decode($linea)); 
                }
            }
            fclose($ar);            
        }
    return ($miarray);
    }

    public static function Leer($archivo)
    {
        $handle = fopen($archivo,'r');
        // Leo archivo
        $size = filesize($archivo);
        // echo $archivo;
        // $listaPersonas = array();
        $listaPersonas = fgets($handle, $size);
        // print_r($listaPersonas);
        // Convierto string a array
        $result = fclose($handle);
        return json_decode($listaPersonas);
    }

    public static function Guardar($objeto,$archivo,$modo)
    {
        // var_dump($objeto);
        $ar = fopen($archivo,$modo); 
        $codificado = json_encode($objeto);
        $retorno = fwrite($ar,$codificado.PHP_EOL);
        fclose($ar);
        if($retorno > 0)
        {
            return 'succes';
        }
        else
        {
            return 'unsucces';
        }
    }

    public static function ModificarxID($id,$objeto,$archivo)
    {   
        $array1 = funciones::Listar($archivo);
        //modificar posición de array segun ID
        //llamar a función guardar por C/id del aray retornado por listar
        

    }

    public static function GuardaTemp($origen,$destiny,$nomarch,$idConcat)
    {
        setlocale(LC_TIME,"es_RA");
        $fecha = date("Y-m-d");
        $hora = date("H-i-s");
        $extension = funciones::obtengoExt($nomarch);
        $concatenado = $idConcat.'_'.$fecha.';'.$hora.$extension;
        $destino = $destiny . $concatenado;
        move_uploaded_file($origen,$destino);
        return $concatenado;
    }

    public static function obtengoExt($nomarch)
    {
        $cantidad = strlen($nomarch);
        $start = $cantidad - 4 ;
        $ext = substr($nomarch, $start, 4);
        
        return $ext;
    }

    public static function GuardaTemp2($archivo,$directorio,$idConcat)
    {       
        setlocale(LC_TIME,"es_RA");
        $fecha = date("Y-m-d");
        $hora = date("H-i-s");
        // $extension = funciones::obtengoExt($nomarch);
        $extension = pathinfo($archivo->getClientFilename(), PATHINFO_EXTENSION);
        // $path= $destino.$idConcat.$extension;
        $filename = $idConcat.'_'.$fecha.';'.$hora.'.'.$extension;
        $archivo->moveTo($directorio . DIRECTORY_SEPARATOR . $filename);
        // move_uploaded_file($origen,$path);
        return $filename;
    }

    public static function BuscaEnArrayxID($archivo,$id)
    {
        $response = new response();
        $listaProd = funciones::Leer($archivo);
        foreach ($listaprod as $key => $value) {
            if ($value->id = $id) {
                $response->status = 'succes';
                $response->data = $value;
                break;
            }
        }
        return $response;
    }
}