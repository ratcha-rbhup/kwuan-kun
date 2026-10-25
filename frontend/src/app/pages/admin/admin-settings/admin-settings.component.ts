import { Component, signal, inject } from '@angular/core';
import { FeedbackService } from '../../../core/services/feedback.service';
import { MOCK_ADMIN_SETTINGS } from '../../../mock-data';

@Component({
  selector: 'app-admin-settings',
  standalone: false,
  templateUrl: './admin-settings.component.html'
})
export class AdminSettingsComponent {
  private feedback = inject(FeedbackService);

  settings = { ...MOCK_ADMIN_SETTINGS };

  isSaving = signal(false);

  saveSettings(): void {
    this.isSaving.set(true);
    setTimeout(() => {
      this.isSaving.set(false);
      this.feedback.success('บันทึกการตั้งค่าระบบส่วนกลางของแพลตฟอร์มเรียบร้อยแล้ว!', 'บันทึกสำเร็จ');
    }, 500);
  }
}
