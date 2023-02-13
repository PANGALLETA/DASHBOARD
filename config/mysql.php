<?php
class MySQL
{

    private $host = "localhost";
    private $user = "root";
    private $password = "";
    private $db = "dashboard";
    private $conect;

    public function __construct()
    {
        $connectionString = "mysql:hos=" . $this->host . ";dbname=" . $this->db . ";charset=utf8";
        try {
            $this->conect = new PDO($connectionString, $this->user, $this->password);
            $this->conect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e) {
            $this->conect = 'Error de conexión';
            echo "ERROR: " . $e->getMessage();
        }
    }
    public function connect()
    {
        return $this->conect;    
    }

    public function getVendidos()
    {
        $vendidos = 0;
        try {
            $strQuery = "SELECT SUM(cantidad_vendidos) as vendidos FROM resumen_productos";
            if ($this->connect()) {
                $pQuery = $this->conect->prepare($strQuery);
                $pQuery->execute();
                $vendidos = $pQuery->fetchColumn();
            }
        } catch (PDOException $e) {
            echo "MySQL.getVendidos: " . $e->getMessage() . "\n";
            return -1;
        }
        return $vendidos;

    }

    public function getAlmacen()
    {
        $almacen = 0;
        try {
            $strQuery = "SELECT SUM(en_almacen) as enAlmacen FROM resumen_productos";
            if ($this->connect()) {
                $pQuery = $this->conect->prepare($strQuery);
                $pQuery->execute();
                $almacen = $pQuery->fetchColumn();
            }
        } catch (PDOException $e) {
            echo "MySQL.getAlmacen: " . $e->getMessage() . "\n";
            return -1;
        }
        return $almacen;
    }
    public function getIngresos()
    {
        $ingreso = 0;
        try {
            $strQuery = "SELECT (SUM(precio) * SUM(cantidad_vendidos))/100000 as ingresos FROM resumen_productos";
            if ($this->connect()) {
                $pQuery = $this->conect->prepare($strQuery);
                $pQuery->execute();
                $ingreso = $pQuery->fetchColumn();
            }
        } catch (PDOException $e) {
            echo "MySQL.getIngresos: " . $e->getMessage() . "\n";
            return -1;
        }
        return $ingreso;
    }

    public function getDatosGrafica()
    {
        $jDatos = '';
        $rawdata = array();
        $i = 0;
        try {
            $strQuery = "SELECT sum(precio) as tPrecio, SUM(cantidad_vendidos) as tVendidos
            ,DATE_FORMAT(fecha_alta, '%Y-%m-%d') as fecha FROM resumen_productos GROUP BY DATE_FORMAT(fecha_alta, '%Y-%m-%d')";
            
            if ($this->connect()) {
                $pQuery = $this->conect->prepare($strQuery);
                $pQuery->execute();
                $pQuery->setFetchMode(PDO::FETCH_ASSOC);
                while($producto = $pQuery->fetch()) {
                    $oGrafica = new Grafica();
                    $oGrafica->totalPrecio = $producto['tPrecio'];
                    $oGrafica->totalVendidos = $producto['tVendidos'];
                    $oGrafica->fechaVenta = $producto['fecha'];
                    $rawdata[$i] = $oGrafica;
                    $i++;
                }
                $jDatos = json_encode( $rawdata);
            }
        } catch (PDOException $e) {
            echo "MySQL.getDatosGrafica: " . $e->getMessage() . "\n";
            return -1;
        }
        return $jDatos;
    }

}
class Grafica{
    public $totalVendidos = 0;
    public $totalPrecio = 0;
    public $fechaVenta = 0; 
}
?>