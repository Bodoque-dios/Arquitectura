import {Routes} from '@angular/router';
import {HomeComponent} from './home/home.component';

export const routes: Routes = [
    {
        path: '',
        title: 'App Home Page',
        component: HomeComponent,
    },
];