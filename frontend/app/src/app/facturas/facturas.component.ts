import { Component, OnInit, Signal, computed, inject, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { DecimalPipe, CommonModule } from '@angular/common';
import { FacturasService } from '../services/factura.service';
import { Servicio } from '../factura';

@Component({
  selector: 'app-facturas',
  standalone: true,
  imports: [DecimalPipe, FormsModule, CommonModule],
  providers: [FacturasService],
  templateUrl: './facturas.component.html',
  styleUrls: ['./facturas.component.css']
})
export class FacturasComponent implements OnInit {
  listFilter = signal<string>('');

  servicios = signal<Servicio[]>([]);

  // Get current month in yyyy-mm format
  mes_actual = new Date().toISOString().slice(0, 7);

  // Computed property to filter servicios based on listFilter
  serviciosFiltrados = computed(() => {
    const filter = this.listFilter().toLowerCase();
    return this.servicios().filter(s => 
      s.nombre.toLowerCase().includes(filter) ||
      s.id_operador.toLowerCase().includes(filter) ||
      s.id_servicio_orbyta.toLowerCase().includes(filter) ||
      s.cliente.toLowerCase().includes(filter) ||
      s.direccion.toLowerCase().includes(filter) ||
      s.capacidad.toString().includes(filter) ||
      s.orden_de_compra.toLowerCase().includes(filter) ||
      s.esta_vigente.toLowerCase().includes(filter) ||
      s.moneda.toLowerCase().includes(filter)
    );
  });
  facturasService = inject(FacturasService);

  ngOnInit(): void {
    this.facturasService.getAllServicios().subscribe((servicios) => {
      this.servicios.set(servicios);
    });
  }
}
