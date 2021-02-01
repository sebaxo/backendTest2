<?php
namespace App\Domain;


use Illuminate\Http\Request;

interface TransaccionDomainInterface
{
    public function cargarCaja(Request $request);
    public function vaciarCaja();
    public function estadoCaja();
    public function realizarPago(Request $request);
    public function verLogs();
    public function estadoCajaFechaHora($fecha);
}
