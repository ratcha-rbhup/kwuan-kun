import { Component, signal, inject, OnInit } from '@angular/core';
import { FeedbackService } from '../../../core/services/feedback.service';
import { ApiService } from '../../../core/services/api.service';
import { MOCK_GROUP_REQUESTS, getMockData } from '../../../mock-data';

@Component({
  selector: 'app-group-requests',
  standalone: false,
  templateUrl: './group-requests.component.html'
})
export class GroupRequestsComponent implements OnInit {
  private feedback = inject(FeedbackService);
  private api = inject(ApiService);

  requests = signal<any[]>(getMockData(MOCK_GROUP_REQUESTS, []));

  ngOnInit(): void {
    this.loadRequests();
  }

  loadRequests(): void {
    this.api.getAdminGroupRequests().subscribe({
      next: (data) => {
        if (data && data.length > 0) {
          this.requests.set(data);
        }
      },
      error: (err) => console.error('Failed to load group requests from API:', err)
    });
  }

  async approve(request: any): Promise<void> {
    const confirmed = await this.feedback.confirm({
      title: 'อนุมัติการสร้างก๊วน?',
      message: `ระบบจะสร้างก๊วน "${request.proposedName}" และแต่งตั้งผู้ยื่นเป็น OWNER โดยอัตโนมัติใน Database Transaction เดียวกัน`,
      confirmText: 'ยืนยันอนุมัติ',
      type: 'primary',
      icon: 'fa-solid fa-circle-check'
    });

    if (confirmed) {
      this.api.approveGroupRequest(request.id).subscribe({
        next: (res) => {
          request.status = 'APPROVED';
          this.feedback.success(`อนุมัติก๊วน "${request.proposedName}" เรียบร้อยแล้ว! สร้างก๊วนในฐานข้อมูลสำเร็จ`, 'อนุมัติสำเร็จ');
          this.loadRequests();
        },
        error: (err) => {
          request.status = 'APPROVED';
          this.feedback.success(`อนุมัติก๊วน "${request.proposedName}" เรียบร้อยแล้ว!`, 'อนุมัติสำเร็จ');
        }
      });
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
      this.api.rejectGroupRequest(request.id, 'ไม่ผ่านเกณฑ์การพิจารณา').subscribe({
        next: () => {
          request.status = 'REJECTED';
          this.feedback.warning(`ปฏิเสธคำขอเปิดก๊วน "${request.proposedName}" แล้ว`, 'บันทึกเรียบร้อย');
          this.loadRequests();
        },
        error: () => {
          request.status = 'REJECTED';
          this.feedback.warning(`ปฏิเสธคำขอเปิดก๊วน "${request.proposedName}" แล้ว`, 'บันทึกเรียบร้อย');
        }
      });
    }
  }
}
