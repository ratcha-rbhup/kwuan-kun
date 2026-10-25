import { Component, signal, inject } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '../../core/services/auth.service';

@Component({
  selector: 'app-main-layout',
  standalone: false,
  templateUrl: './main-layout.component.html'
})
export class MainLayoutComponent {
  auth = inject(AuthService);
  private router = inject(Router);

  isMobileDrawerOpen = signal(false);

  toggleMobileDrawer(): void {
    this.isMobileDrawerOpen.update(v => !v);
  }

  closeMobileDrawer(): void {
    this.isMobileDrawerOpen.set(false);
  }

  switchRole(role: 'member' | 'owner' | 'admin'): void {
    this.closeMobileDrawer();
    if (role === 'member') {
      this.router.navigate(['/member/my-groups']);
    } else if (role === 'owner') {
      this.router.navigate(['/kwuan-owner/dashboard']);
    } else if (role === 'admin') {
      this.router.navigate(['/admin/requests']);
    }
  }

  simulateLineLogin(): void {
    this.auth.currentUser.set({
      id: 1,
      display_name: 'สมาชิกแบดมินตัน (LINE User)',
      is_admin: true,
      status: 'ACTIVE'
    });
  }
}
