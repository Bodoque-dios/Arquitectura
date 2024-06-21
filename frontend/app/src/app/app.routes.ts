import {Routes} from '@angular/router';
import {FacturasComponent} from './facturas/facturas.component';

export const routes: Routes = [
    {
        path: '',
        title: 'App Home Page',
        component: FacturasComponent,
    },
];