import { Component, OnInit, Signal, computed, inject, input, signal  } from '@angular/core';
import { DecimalPipe } from '@angular/common';
import { FacturasService } from '../factura.service';
import { Servicio } from '../factura';

@Component({
  selector: 'servicios-table',
  standalone: true,
  imports: [DecimalPipe],
  providers: [FacturasService],
  templateUrl: './servicios-table.component.html',
  styleUrls: ['./servicios-table.component.css'],
})

export class ServiciosTableComponent implements OnInit {

    servicios = signal<Servicio[]>([])

    filterCriteria = input('', {
        transform: (value: string) => value.toLowerCase()
    })

    // serviciosFiltrados: Servicio[] = [...this.servicios]
    serviciosFiltrados: Signal<Servicio[]> = computed(() => 
        this.servicios().filter(s => (
            s.nombre.toLowerCase().includes(this.filterCriteria()) ||
            s.id_operador.toLowerCase().includes(this.filterCriteria()) ||
            s.cliente.toLowerCase().includes(this.filterCriteria()) ||
            s.direccion.toLowerCase().includes(this.filterCriteria())
        ))
    )

    facturasService = inject(FacturasService)

    ngOnInit(): void {
        this.facturasService.getAllServicios().subscribe((servicios) => {
        this.servicios.set(servicios);
        });
    }
}