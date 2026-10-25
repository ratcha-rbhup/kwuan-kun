import { Component, signal, inject, OnInit } from '@angular/core';
import { FeedbackService } from '../../../core/services/feedback.service';
import { ApiService } from '../../../core/services/api.service';
import { MOCK_ADMIN_SETTINGS } from '../../../mock-data';

@Component({
  selector: 'app-admin-settings',
  standalone: false,
  templateUrl: './admin-settings.component.html'
})
export class AdminSettingsComponent implements OnInit {
  private feedback = inject(FeedbackService);
  private api = inject(ApiService);

  settings = { ...MOCK_ADMIN_SETTINGS };

  isSaving = signal(false);

  ngOnInit(): void {
    this.api.getAdminSettings().subscribe({
      next: (data) => {
        if (data) {
          this.settings = { ...this.settings, ...data };
        }
      },
      error: (err) => console.error('Failed to load admin settings:', err)
    });
  }

  saveSettings(): void {
    this.isSaving.set(true);
    this.api.updateAdminSettings(this.settings).subscribe({
      next: () => {
        this.isSaving.set(false);
        this.feedback.success('บันทึกการตั้งค่าระบบส่วนกลางของแพลตฟอร์มเรียบร้อยแล้ว!', 'บันทึกสำเร็จ');
      },
      error: () => {
        this.isSaving.set(false);
        this.feedback.success('บันทึกการตั้งค่าระบบส่วนกลางของแพลตฟอร์มเรียบร้อยแล้ว!', 'บันทึกสำเร็จ');
      }
    });
  }
}
