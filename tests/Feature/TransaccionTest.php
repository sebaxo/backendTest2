<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;

class TransaccionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**@test**/
    public function testVaciarCajaMethodRespond(){
        $response = $this->get('/vaciarCaja');
        $this->assertSame($response->getContent(), '1');
    }


    /**@test**/
    public function testCargarCajaMethodRespond(){
        $response = $this->post('/cargarCaja', ['Denominacion' => 20000, 'Cambio' => 'Billete', 'Cantidad' => 3]);
        $this->assertSame($response->getContent(),'{"Denominacion":20000,"Cambio":"Billete","Cantidad":3,"id":1}');
        $this->get('/vaciarCaja');
    }

    /**@test**/
    public function testEstadoCajaMethodRespond(){
        $this->get('/vaciarCaja');
        $this->post('/cargarCaja', ['Denominacion' => 20000, 'Cambio' => 'Billete', 'Cantidad' => 3]);
        $response = $this->get('/estadoCaja');
        $this->assertSame($response->getContent(),'[{"id":1,"Denominacion":"20000","Cambio":"Billete","Cantidad":"3"}]');
        $this->get('/vaciarCaja');
    }

    /**@test**/
    public function testRealizarPagoMethodRespond(){
        $this->get('/vaciarCaja');
        $this->post('/cargarCaja', ['Denominacion' => 20000, 'Cambio' => 'Billete', 'Cantidad' => 3]);
        $this->post('/cargarCaja', ['Denominacion' => 50000, 'Cambio' => 'Billete', 'Cantidad' => 2]);

        $this->post('/realizarPago', ['totalTransaccion' => 60000, 'listaEfectivo' => '[{"Denominacion":50000,"Cambio": "Billete","Cantidad": 2}]']);
        $response = $this->get('/estadoCaja');
        $this->assertSame($response->getContent(),'[{"id":1,"Denominacion":"20000","Cambio":"Billete","Cantidad":"1"},{"id":2,"Denominacion":"50000","Cambio":"Billete","Cantidad":"4"}]');
        $this->get('/vaciarCaja');
    }

    /**@test**/
    public function testVerLogsMethodRespond(){
        $response = $this->get('/verLogs');
        $response->assertStatus(200);
    }

    /**@test**/
    public function testEstadoCajaFechaHoraMethodRespond(){
        $this->post('/cargarCaja', ['Denominacion' => 20000, 'Cambio' => 'Billete', 'Cantidad' => 3]);
        $this->post('/cargarCaja', ['Denominacion' => 50000, 'Cambio' => 'Billete', 'Cantidad' => 2]);

        $this->post('/realizarPago', ['totalTransaccion' => 60000, 'listaEfectivo' => '[{"Denominacion":50000,"Cambio":"Billete","Cantidad":2}]']);

        $response = $this->get('/estadoCajaFechaHora/'.Carbon::now());
        $this->assertStringStartsWith('{[{"id":1,"Denominacion":"20000","Cambio":"Billete","Cantidad":1},{"id":2,"Denominacion":"50000","Cambio":"Billete","Cantidad":4}], Fecha:', $response->getContent());
    }
}
