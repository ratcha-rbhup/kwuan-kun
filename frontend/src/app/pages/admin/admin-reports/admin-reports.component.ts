import { Component, signal, inject } from '@angular/core';
import { FeedbackService } from '../../../core/services/feedback.service';
import { UserReport, MOCK_ADMIN_REPORTS, getMockData } from '../../../mock-data';

@Component({
  selector: 'app-admin-reports',
  standalone: false,
  templateUrl: './admin-reports.component.html'
})
export class AdminReportsComponent {
  private feedback = inject(FeedbackService);

  reports = signal<UserReport[]>(getMockData(MOCK_ADMIN_REPORTS, []));

  async warnUser(r: UserReport): Promise<void> {
    const confirmed = await this.feedback.confirm({
      title: 'ส่งคำเตือนอย่างเป็นทางการ?',
      message: `ระบบจะส่งข้อความแจ้งเตือนความประพฤติไปยัง "${r.targetName}" ผ่าน LINE OA`,
      confirmText: 'ส่งคำเตือน',
      type: 'warning',
      icon: 'fa-solid fa-triangle-exclamation'
    });

    if (confirmed) {
      r.status = 'RESOLVED';
      this.feedback.success(`ส่งคำเตือนไปยัง "${r.targetName}" และปิดเคสเรียบร้อย`);
    }
  }

  async banTarget(r: UserReport): Promise<void> {
    const confirmed = await this.feedback.confirm({
      title: `แบน ${r.targetType === 'USER' ? 'ผู้ใช้งาน' : 'ก๊วนนี้'} ถาวร?`,
      message: `ยืนยันการระงับบัญชี "${r.targetName}" ออกจากแพลตฟอร์มทันทีหรือไม่?`,
      confirmText: 'ระงับบัญชี (BAN)',
      type: 'danger',
      icon: 'fa-solid fa-user-slash'
    });

    if (confirmed) {
      r.status = 'RESOLVED';
      this.feedback.warning(`ระงับบัญชี "${r.targetName}" สำเร็จ`);
    }
  }

  dismissReport(r: UserReport): void {
    r.status = 'DISMISSED';
    this.feedback.info('ยกเลิกรายงานนี้เรียบร้อย (ไม่มีมูล)');
  }
}
