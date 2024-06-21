import { Injectable } from '@angular/core';
import { Servicio } from '../factura';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class FacturasService {

    constructor(private http: HttpClient) {}

    readonly baseUrl = 'http://localhost';

    getAllServicios(): Observable<Servicio[]> {
        return this.http.get<Servicio[]>(`${this.baseUrl}/query.php?action=data`);
    } 
}
