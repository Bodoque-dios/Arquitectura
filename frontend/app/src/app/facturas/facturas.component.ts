import { Component, OnInit, Signal, computed, inject, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { DecimalPipe, CommonModule } from '@angular/common';
import { FacturasService } from '../services/factura.service';
import { ModalServicioComponent } from '../modal-servicio/modal-servicio.component';
import { Servicio } from '../factura';

@Component({
	selector: 'app-facturas',
	standalone: true,
	imports: [DecimalPipe, FormsModule, CommonModule, ModalServicioComponent],
	providers: [FacturasService],
	templateUrl: './facturas.component.html',
	styleUrls: ['./facturas.component.css'],
})
export class FacturasComponent implements OnInit {
	listFilter = signal<string>('');
	servicios = signal<Servicio[]>([]);
    
    showModal = signal<boolean>(false);
    selectedServicio: Servicio | null = null;

	//mes_actual = new Date().setDate(1);
    mes_actual = new Date("2024-03-03")
	// Generate an array with the current month and the previous 3 months
	meses = Array.from({ length: 4 }, (_, i) => {
		let date = new Date(this.mes_actual);
		date.setMonth(date.getMonth() - i);
		return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
	});

	// Computed property to filter servicios based on listFilter
	serviciosFiltrados = computed(() => {
		const filter = this.listFilter().toLowerCase();
		return this.servicios().filter(
			s =>
				(s.nombre.toLowerCase().includes(filter) ||
					s.id_operador.toLowerCase().includes(filter) ||
					s.id_servicio_orbyta.toLowerCase().includes(filter) ||
					s.cliente.toLowerCase().includes(filter) ||
					s.direccion.toLowerCase().includes(filter) ||
					s.capacidad.toString().includes(filter) ||
					s.orden_de_compra.toLowerCase().includes(filter) ||
					s.esta_vigente.toLowerCase().includes(filter) ||
					s.moneda.toLowerCase().includes(filter)) &&
				s.esta_vigente === '1'
		);
	});
	facturasService = inject(FacturasService);

	ngOnInit(): void {
		this.facturasService.getAllServicios().subscribe(servicios => {
			this.servicios.set(servicios);
		});
    }

    openModal(servicio: Servicio) {
        this.selectedServicio = servicio;
        this.showModal.set(true);
      }
    
      closeModal() {
        this.selectedServicio = null;
        this.showModal.set(false);
      }
}
