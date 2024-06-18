import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [RouterOutlet],
  styleUrl: './app.component.css',
  template: `
    <main>
      <nav>
        <a href="/">Facturas</a>
        <a href="/user">Clientes</a>
        <a href="/user">Opciones</a>
      </nav>
      <router-outlet />
    </main>
  `,
})
export class AppComponent {
  title = 'app';
}
