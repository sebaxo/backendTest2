<?php
namespace App\Domain;


use App\Repositories\LogTransaccionesRepository\LogTransaccionesInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Repositories\MontoRepository\MontoRepositoryInterface;
use App\Models;

class TransaccionDomainImp implements TransaccionDomainInterface
{

    /**@var LogTransaccionesInterface**/
    private $log_repository;
    /**@var MontoRepositoryInterface**/
    private $monto_repository;

    /**
     * TransaccionDomainImp constructor.
     * @param LogTransaccionesInterface $log_repository
     * @param MontoRepositoryInterface $monto_repository
     * se inyectan los repositorios de base de datos
     */
    public function __construct(MontoRepositoryInterface $monto_repository, LogTransaccionesInterface $log_repository)
    {
        $this->log_repository = $log_repository;
        $this->monto_repository = $monto_repository;
    }

    public function cargarCaja(Request $request){

        $monto = new Models\Monto;
        $monto->Denominacion = $request->input('Denominacion');
        $monto->Cambio = $request->input('Cambio');
        $monto->Cantidad = $request->input('Cantidad');

        $this->monto_repository->createMonto($monto);

        return $monto;
    }

    public function vaciarCaja(){
        return $this->monto_repository->deleteAllMontos();
    }

    public function estadoCaja(){
        return $this->monto_repository->getAllMontos();
    }

    public function realizarPago(Request $request){

        //se trae la caja
        $caja = $this->monto_repository->getAllMontosPorDenominacion('desc');

        //se calcula el total de la caja
        $totalCaja = totalEfectivo($caja);

        $totalPagar = $request->input('totalTransaccion');
        $listaEfectivo = json_decode ($request->input('listaEfectivo'));

        //se calcula el total de dinero con el que esta pagando
        $totalEntrada = totalEfectivo($listaEfectivo);

        //se calcula el total a retornar
        $totalRetornar = $totalEntrada-$totalPagar;

        //se realiza una validacion inicial del monto a retornar y el total de la caja
        if($totalRetornar>$totalCaja)
            return 'No hay suficiente dinero en caja para el cambio.';

        $listaSalida = [];

        //se recorre la caja
        foreach ($caja as $m){
            //se calcula el total de dinero que se tiene por nominacion
            $totalDenominacion = $m->Denominacion*$m->Cantidad;

            //si la suma de dinero de esta denominacion no es suficiente para pagar se entrega todo lo de esta nominacion
            if($totalDenominacion < $totalRetornar){
                //$listaSalida[$m->id] = $m->Cantidad;
                array_push($listaSalida,
                    array('id' => $m->id,
                        'Denominacion' => $m->Denominacion,
                        'Cambio' => $m->Cambio,
                        'Cantidad' => $m->Cantidad));
                $totalRetornar -= $totalDenominacion;
            }else{
                //si es suficiente para pagar calculamos cuantas unidades de esta nominacion damos
                $div = floor( $totalRetornar / $m->Denominacion );

                //si la division anterior es 0 no daremos ninguna unidad de esta nominacion
                if($div>0){
                    array_push($listaSalida,
                        array('id' => $m->id,
                            'Denominacion' => $m->Denominacion,
                            'Cambio' => $m->Cambio,
                            'Cantidad' => $div));
                    $totalRetornar -= $div*$m->Denominacion;
                }
            }

            //si el total a retornar es 0 es que ya terminamos por lo que salimos del for
            if($totalRetornar == 0)
                break;
        }

        //si al terminar el for el total a retornar no es 0 es que no tenemos suficiente suelto para el cambio
        if($totalRetornar != 0)
            return 'No hay suficiente dinero en caja para el cambio.';

        //quitamos el dinero que salio para cada denominacion de la caja
        foreach ($listaSalida as $l){
            $mont = traerMonto($caja, $l['Denominacion']);
            $mont->Cantidad = $mont->Cantidad - $l['Cantidad'];
        }

        //agregamos el dinero que entro para cada nominacion de la caja
        foreach ($listaEfectivo as $e){
            $mont = traerMonto($caja, $e->Denominacion);
            $mont->Cantidad = $mont->Cantidad + $e->Cantidad;
        }

        //no encontre forma de actualizar todos los registros al tiempo asi que toco uno por uno
        foreach ($caja as $c)
            $this->monto_repository->updateMonto($c);

        $cajaArray = $caja->toArray();
        usort($cajaArray, function ($a , $b) {
            return $a['id'] > $b['id'] ? 1 : 0;
        });

        $transaccion = new Models\LogTransacciones();
        $transaccion->Entrada = json_encode($listaEfectivo);
        $transaccion->Salida = json_encode($listaSalida);
        $transaccion->Fecha = Carbon::now();
        $transaccion->CajaDespues = json_encode($cajaArray);//Models\Monto::get();
        $transaccion->save();

        $this->log_repository->createLog($transaccion);

        return $transaccion;
    }

    public function verLogs(){
        return $this->log_repository->getAllLogs();
    }

    public function estadoCajaFechaHora($fecha): string
    {
        //calculamos el log con la fecha menor mas cercano a la fecha solicitada
        $est = $this->log_repository->getLogCercanoPorFecha($fecha);

        if($est == null)
            return 'No se encontro ningun monto previo a esta fecha.';

        //retornamos un json con el arreglo del estado de la caja y la fecha de ese log
        return "{".$est->CajaDespues . ", Fecha: ".$est->Fecha."}";
    }
}


function totalEfectivo( $arrayEfectivo ){
    $totalCaja = 0;

    foreach ($arrayEfectivo as $m){
        $totalCaja += $m->Denominacion*$m->Cantidad;
    }

    return $totalCaja;
}

function traerMonto( $listaEfectivo, $denominacion ){
    foreach ($listaEfectivo as $l)
        if($l->Denominacion == $denominacion)
            return $l;
    return 0;
}

function sortById($a , $b){
    return $a['id']>$b['id'];
}
