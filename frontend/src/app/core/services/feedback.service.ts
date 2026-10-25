import { Injectable, signal } from '@angular/core';

export type ToastType = 'success' | 'error' | 'warning' | 'info';

export interface ToastItem {
  id: string;
  type: ToastType;
  title?: string;
  message: string;
  icon?: string;
  durationMs?: number;
}

export interface ConfirmDialogOptions {
  title: string;
  message: string;
  confirmText?: string;
  cancelText?: string;
  type?: 'primary' | 'danger' | 'warning';
  icon?: string;
}

export interface AlertDialogOptions {
  title: string;
  message: string;
  buttonText?: string;
  type?: 'primary' | 'danger' | 'warning' | 'info';
  icon?: string;
}

export interface BottomSheetOption {
  id: string;
  label: string;
  icon?: string;
  badge?: string;
  color?: string;
}

export interface BottomSheetOptions {
  title: string;
  subtitle?: string;
  options: BottomSheetOption[];
}

@Injectable({
  providedIn: 'root'
})
export class FeedbackService {
  // --- Reactive Signals for Global Feedback State ---
  toasts = signal<ToastItem[]>([]);
  activeConfirm = signal<{
    options: ConfirmDialogOptions;
    resolve: (value: boolean) => void;
  } | null>(null);

  activeAlert = signal<{
    options: AlertDialogOptions;
    resolve: () => void;
  } | null>(null);

  activeBottomSheet = signal<{
    options: BottomSheetOptions;
    resolve: (optionId: string | null) => void;
  } | null>(null);

  // =========================================================================
  // Toast Notifications (Floating Toast Message)
  // =========================================================================
  showToast(message: string, type: ToastType = 'info', title?: string, durationMs = 3500): void {
    const id = 'toast_' + Math.random().toString(36).substring(2, 9);
    
    // Default FontAwesome icons per type
    const defaultIcons: Record<ToastType, string> = {
      success: 'fa-solid fa-circle-check',
      error: 'fa-solid fa-circle-xmark',
      warning: 'fa-solid fa-triangle-exclamation',
      info: 'fa-solid fa-circle-info'
    };

    const item: ToastItem = {
      id,
      type,
      title,
      message,
      icon: defaultIcons[type],
      durationMs
    };

    this.toasts.update(current => [item, ...current]);

    if (durationMs > 0) {
      setTimeout(() => {
        this.dismissToast(id);
      }, durationMs);
    }
  }

  // Convenient shortcuts
  success(message: string, title?: string): void {
    this.showToast(message, 'success', title);
  }

  error(message: string, title?: string): void {
    this.showToast(message, 'error', title);
  }

  warning(message: string, title?: string): void {
    this.showToast(message, 'warning', title);
  }

  info(message: string, title?: string): void {
    this.showToast(message, 'info', title);
  }

  dismissToast(id: string): void {
    this.toasts.update(current => current.filter(t => t.id !== id));
  }

  // =========================================================================
  // Confirmation Dialog (Promise-based Confirm Modal)
  // =========================================================================
  confirm(options: ConfirmDialogOptions): Promise<boolean> {
    return new Promise(resolve => {
      this.activeConfirm.set({
        options: {
          confirmText: 'ยืนยัน',
          cancelText: 'ยกเลิก',
          type: 'primary',
          icon: options.type === 'danger' ? 'fa-solid fa-triangle-exclamation' : 'fa-solid fa-circle-question',
          ...options
        },
        resolve: (result: boolean) => {
          this.activeConfirm.set(null);
          resolve(result);
        }
      });
    });
  }

  // =========================================================================
  // Alert Dialog (Promise-based Info Modal)
  // =========================================================================
  alert(options: AlertDialogOptions): Promise<void> {
    return new Promise(resolve => {
      this.activeAlert.set({
        options: {
          buttonText: 'เข้าใจแล้ว',
          type: 'primary',
          icon: 'fa-solid fa-circle-info',
          ...options
        },
        resolve: () => {
          this.activeAlert.set(null);
          resolve();
        }
      });
    });
  }

  // =========================================================================
  // Bottom Sheet (LINE LIFF Signature Action Drawer)
  // =========================================================================
  openBottomSheet(options: BottomSheetOptions): Promise<string | null> {
    return new Promise(resolve => {
      this.activeBottomSheet.set({
        options,
        resolve: (selected: string | null) => {
          this.activeBottomSheet.set(null);
          resolve(selected);
        }
      });
    });
  }

  closeBottomSheet(): void {
    const current = this.activeBottomSheet();
    if (current) {
      current.resolve(null);
    }
  }
}
