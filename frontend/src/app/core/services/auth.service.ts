import { Injectable, signal, computed, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, tap } from 'rxjs';

export interface AuthUser {
  id: number;
  display_name: string;
  avatar_url?: string;
  is_admin: boolean;
  status: string;
}

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private http = inject(HttpClient);
  private tokenKey = 'ak_auth_token';
  private userKey = 'ak_auth_user';

  // Angular 20 Signals for reactive state
  currentUser = signal<AuthUser | null>(this.getStoredUser());
  isLoggedIn = computed(() => !!this.currentUser());
  isPlatformAdmin = computed(() => !!this.currentUser()?.is_admin);

  private getStoredUser(): AuthUser | null {
    const raw = localStorage.getItem(this.userKey);
    return raw ? JSON.parse(raw) : null;
  }

  getToken(): string | null {
    return localStorage.getItem(this.tokenKey);
  }

  /**
   * Login with LINE ID Token verified at Laravel backend.
   * Angular client NEVER sends raw LINE UID to trust directly.
   */
  loginWithLineIdToken(idToken: string): Observable<any> {
    return this.http.post<any>('/api/auth/line', { id_token: idToken }).pipe(
      tap(res => {
        if (res.token && res.user) {
          localStorage.setItem(this.tokenKey, res.token);
          localStorage.setItem(this.userKey, JSON.stringify(res.user));
          this.currentUser.set(res.user);
        }
      })
    );
  }

  logout(): void {
    localStorage.removeItem(this.tokenKey);
    localStorage.removeItem(this.userKey);
    this.currentUser.set(null);
  }
}
