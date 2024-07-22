import { Component, Input, Output, EventEmitter } from '@angular/core';
import { CommonModule } from '@angular/common'; 
import { Servicio } from '../factura';

@Component({
  selector: 'app-modal-servicio',
  standalone: true,
  imports: [CommonModule], 
  templateUrl: './modal-servicio.component.html',
})
export class ModalServicioComponent {
  @Input() data: Servicio | null = null;
  @Output() closeModal = new EventEmitter<void>();

  close() {
    this.closeModal.emit();
  }
}
