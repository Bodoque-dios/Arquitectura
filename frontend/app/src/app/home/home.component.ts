import { Component  } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FacturasService } from '../factura.service';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [CommonModule],
  providers: [FacturasService],
  template: `
  <div>
    <h1>Home Component !!</h1>
    <p>aca va la tabla</p>
  </div>
  `,
  styleUrls: ['./home.component.css'],
})

export class HomeComponent {

  constructor() {
  }
}