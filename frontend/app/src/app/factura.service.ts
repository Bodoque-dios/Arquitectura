import { Injectable } from '@angular/core';
import { Servicio } from './factura';
import { Factura } from './factura';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class FacturasService {

    constructor(private http: HttpClient) {}

    readonly baseUrl = 'http://localhost';

    serviciosList: Servicio[] = [
        {
            nombre: 'Starlink',
            id_operador: '123',
            id_servicio_orbyta: '456',
            cliente: 'Client A',
            direccion: 'Address A',
            capacidad: 100,
            orden_de_compra: '789',
            esta_vigente: '1',
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
        },
        {
            nombre: 'Starlink',
            id_operador: '123',
            id_servicio_orbyta: '456',
            cliente: 'Client A',
            direccion: 'Address A',
            capacidad: 100,
            orden_de_compra: '789',
            esta_vigente: '0',
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
  
    getAllServicios(): Observable<Servicio[]> {
        return this.http.get<Servicio[]>(`${this.baseUrl}/query.php?action=data`);
        // return new Observable((observer) => {
        //     observer.next(this.serviciosList);
        //     observer.complete();
        // });
    } 
}
