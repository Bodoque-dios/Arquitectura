export interface Factura {
    fecha: string;
    factura: number;
    neto: number;
}

export interface Servicio {
    nombre: string;
    id_operador: string;
    id_servicio_orbyta: string;
    cliente: string;
    direccion: string;
    capacidad: number;
    orden_de_compra: string;
    esta_vigente: string; // o number ? o boolean?
    moneda: string;
    facturas: Factura[];
}