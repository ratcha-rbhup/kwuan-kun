import { Component, inject } from '@angular/core';
import { AuthService } from '../../core/services/auth.service';

@Component({
  selector: 'app-owner-layout',
  standalone: false,
  templateUrl: './owner-layout.component.html'
})
export class OwnerLayoutComponent {
  auth = inject(AuthService);
}
