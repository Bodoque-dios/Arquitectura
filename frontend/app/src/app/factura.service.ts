import { Injectable } from '@angular/core';
import { Servicio } from './factura';
import { Factura } from './factura';
import { HttpClient } from '@angular/common/http';

@Injectable({
  providedIn: 'root'
})
export class FacturasService {

    constructor(private http: HttpClient) {}

    readonly baseUrl = 'https://angular.dev/assets/tutorials/common';

    serviciosList: Servicio[] = [
        {
            nombre: 'Servicio 1',
            id_operador: '123',
            id_servicio_orbyta: '456',
            cliente: 'Client A',
            direccion: 'Address A',
            capacidad: 100,
            orden_de_compra: '789',
            esta_vigente: 'true',
            moneda: 'USD',
            facturas: [
                {
                    fecha: '2022-01-01',
                    factura: 1,
                    neto: 100
                },
                {
                    fecha: '2022-02-01',
                    factura: 2,
                    neto: 200
                }
            ]
        }
    ];


  
    getAllServicios(): Servicio[] {
        return this.serviciosList;
    } 
}
