import { Component, signal, inject } from '@angular/core';
import { FeedbackService } from '../../../core/services/feedback.service';
import { MOCK_GROUP_REQUESTS, getMockData } from '../../../mock-data';

@Component({
  selector: 'app-group-requests',
  standalone: false,
  templateUrl: './group-requests.component.html'
})
export class GroupRequestsComponent {
  private feedback = inject(FeedbackService);

  requests = signal(getMockData(MOCK_GROUP_REQUESTS, []));

  async approve(request: any): Promise<void> {
    const confirmed = await this.feedback.confirm({
      title: 'อนุมัติการสร้างก๊วน?',
      message: `ระบบจะสร้างก๊วน "${request.proposedName}" และแต่งตั้งผู้ยื่นเป็น OWNER โดยอัตโนมัติใน Database Transaction เดียวกัน`,
      confirmText: 'ยืนยันอนุมัติ',
      type: 'primary',
      icon: 'fa-solid fa-circle-check'
    });

    if (confirmed) {
      request.status = 'APPROVED';
      this.feedback.success(`อนุมัติก๊วน "${request.proposedName}" เรียบร้อยแล้ว!`, 'อนุมัติสำเร็จ');
    }
  }

  async reject(request: any): Promise<void> {
    const confirmed = await this.feedback.confirm({
      title: 'ไม่อนุมัติคำขอนี้?',
      message: `คุณต้องการปฏิเสธคำขอเปิดก๊วน "${request.proposedName}" ใช่หรือไม่?`,
      confirmText: 'ปฏิเสธคำขอ',
      cancelText: 'ย้อนกลับ',
      type: 'danger'
    });

    if (confirmed) {
      request.status = 'REJECTED';
      this.feedback.warning(`ปฏิเสธคำขอเปิดก๊วน "${request.proposedName}" แล้ว`, 'บันทึกเรียบร้อย');
    }
  }
}
