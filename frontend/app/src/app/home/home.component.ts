import { Component, OnInit, Signal, computed, effect, input, signal  } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FacturasService } from '../factura.service';
import { Servicio } from '../factura';
import { FormsModule } from '@angular/forms';
import { ServiciosTableComponent } from './servicios-table.component';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [ServiciosTableComponent, CommonModule, FormsModule],
  providers: [FacturasService],
  template: `
    <div class="home-container">
        <div class="searchbar-container">
          <label for="search">Buscar</label>
          <input type="text" placeholder="Buscar" [(ngModel)]="listFilter"  />
          </div>
          <servicios-table [filterCriteria]='listFilter' />
    </div>
  `,
  styleUrls: ['./home.component.css'],
})

export class HomeComponent  {

  listFilter = '';
}