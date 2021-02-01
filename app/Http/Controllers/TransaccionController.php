<?php

namespace App\Http\Controllers;

use App\Domain\TransaccionDomainInterface;
use Illuminate\Http\Request;
use App\Models;
use App\ServiceContracts\TransaccionContract;

class TransaccionController extends Controller implements TransaccionContract
{
    /**@var TransaccionDomainInterface**/
    private $domain;
    public function __construct(TransaccionDomainInterface $transaccion_domain)
    {
        $this->domain = $transaccion_domain;
    }

    /**
     * @param Request $request
     * @return Models\Monto
     * Metodo para añadir un Monto a la base de datos
     */
    public function cargarCaja(Request $request){
        $this->validate($request, [
            'Denominacion' => 'required',
            'Cambio' => 'required',
            'Cantidad' => 'required'
        ]);

        return $this->domain->cargarCaja($request);
    }

    /**
     * @param
     * @return boolean
     * Metodo para vaciar la tabla de Montos
     */
    public function vaciarCaja(){
        return $this->domain->vaciarCaja();
    }

    /**
     * @param
     * @return array
     * Metodo para obtener todos los registros de la tabla de Montos
     */
    public function estadoCaja(){
        return $this->domain->estadoCaja();
    }

    /**
     * @param Request $request
     * @return Models\LogTransacciones
     * Metodo para realizar un pago
     */
    public function realizarPago(Request $request){
        //se valida el request
        $this->validate($request, [
            'totalTransaccion' => 'required',
            'listaEfectivo' => 'required'
        ]);

        return $this->domain->realizarPago($request);
    }

    /**
     * @param
     * @return array
     * Metodo para obtener todos los registros de la tabla de logs_transacciones
     */
    public function verLogs(){
        return $this->domain->verLogs();
    }

    /**
     * @param $fecha
     * @return array
     * Metodo para obtener el estado de la caja en una fecha determinada
     */
    public function estadoCajaFechaHora($fecha): string
    {
        return $this->domain->estadoCajaFechaHora($fecha);
    }
}
