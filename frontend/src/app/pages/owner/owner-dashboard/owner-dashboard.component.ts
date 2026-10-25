import { Component, signal, inject } from '@angular/core';
import { FeedbackService } from '../../../core/services/feedback.service';
import {
  MOCK_OWNER_STATS,
  MOCK_OWNER_ACTIVE_SESSION,
  MOCK_OWNER_PENDING_MEMBERS,
  getMockData
} from '../../../mock-data';

@Component({
  selector: 'app-owner-dashboard',
  standalone: false,
  templateUrl: './owner-dashboard.component.html'
})
export class OwnerDashboardComponent {
  private feedback = inject(FeedbackService);

  groupName = signal('ก๊วนแบดมินตัน จตุจักร สแมชเชอร์');
  venueName = signal('Winner Sport Club (คอร์ต 4-6)');

  // Key KPI Metrics for Badminton Host
  stats = signal({ ...getMockData(MOCK_OWNER_STATS, MOCK_OWNER_STATS) });

  // Upcoming Scheduled Badminton Session
  activeSession = signal({ ...getMockData(MOCK_OWNER_ACTIVE_SESSION, MOCK_OWNER_ACTIVE_SESSION) });

  // Pending Member Applications for Owner Review
  pendingMembers = signal([...getMockData(MOCK_OWNER_PENDING_MEMBERS, [])]);

  async approveMember(m: any): Promise<void> {
    const confirmed = await this.feedback.confirm({
      title: 'อนุมัติสมาชิกเข้าก๊วน?',
      message: `ยืนยันรับคุณ "${m.name}" เข้าเป็นสมาชิกก๊วน ${this.groupName()} หรือไม่?`,
      confirmText: 'รับเข้าก๊วน',
      type: 'primary',
      icon: 'fa-solid fa-user-plus'
    });

    if (confirmed) {
      this.pendingMembers.update(list => list.filter(item => item.id !== m.id));
      this.stats.update(s => ({
        ...s,
        totalMembers: s.totalMembers + 1,
        pendingJoinRequests: Math.max(0, s.pendingJoinRequests - 1)
      }));
      this.feedback.success(`รับคุณ "${m.name}" เข้าก๊วนเรียบร้อย ระบบส่งการแจ้งเตือนทาง LINE แล้ว`, 'อนุมัติสำเร็จ');
    }
  }

  async rejectMember(m: any): Promise<void> {
    const confirmed = await this.feedback.confirm({
      title: 'ปฏิเสธคำขอ?',
      message: `คุณต้องการปฏิเสธคำขอของ "${m.name}" ใช่หรือไม่?`,
      confirmText: 'ปฏิเสธคำขอ',
      type: 'danger',
      icon: 'fa-solid fa-user-xmark'
    });

    if (confirmed) {
      this.pendingMembers.update(list => list.filter(item => item.id !== m.id));
      this.stats.update(s => ({
        ...s,
        pendingJoinRequests: Math.max(0, s.pendingJoinRequests - 1)
      }));
      this.feedback.info(`ปฏิเสธคำขอเรียบร้อยแล้ว`, 'ดำเนินการเสร็จสิ้น');
    }
  }

  async createNewSession(): Promise<void> {
    await this.feedback.alert({
      title: 'ระบบเปิดรอบตีใหม่',
      message: 'ฟอร์มกำหนดวันเวลา จำนวนคอร์ต และลูกแบดมินตัน พร้อมเปิดให้สมาชิกลงชื่อล่วงหน้า',
      buttonText: 'ตกลง',
      type: 'primary',
      icon: 'fa-solid fa-calendar-plus'
    });
  }

  async openMatchMaker(): Promise<void> {
    await this.feedback.alert({
      title: 'เครื่องมือจัดคู่ลงคอร์ท',
      message: 'ระบบแบ่งสายอัตโนมัติตามระดับมือ (N, S, P) ให้ทุกคู่สูสีและได้เล่นครบทุกคนอย่างเท่าเทียม',
      buttonText: 'เริ่มจัดคู่',
      type: 'primary',
      icon: 'fa-solid fa-shuffle'
    });
  }

  async openExpenseQr(): Promise<void> {
    await this.feedback.alert({
      title: 'QR Code เก็บเงินก๊วน',
      message: 'สร้าง QR PromptPay ตามยอดค่าคอร์ดและลูกแบดหารเฉลี่ย พร้อมระบบตรวจสอบสลิป',
      buttonText: 'ดู QR Code',
      type: 'primary',
      icon: 'fa-solid fa-qrcode'
    });
  }
}
