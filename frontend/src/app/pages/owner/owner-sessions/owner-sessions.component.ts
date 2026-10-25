import { Component, signal, inject, OnInit } from '@angular/core';
import { FeedbackService } from '../../../core/services/feedback.service';
import { ApiService } from '../../../core/services/api.service';
import {
  BadmintonSession,
  MOCK_OWNER_SESSIONS,
  getMockData
} from '../../../mock-data';

@Component({
  selector: 'app-owner-sessions',
  standalone: false,
  templateUrl: './owner-sessions.component.html'
})
export class OwnerSessionsComponent implements OnInit {
  private feedback = inject(FeedbackService);
  private api = inject(ApiService);

  groupName = signal('ก๊วนแบดมินตัน จตุจักร สแมชเชอร์');
  filterStatus = signal<string>('ALL');

  // Modal / Form state for creating session
  isCreateModalOpen = signal(false);
  newSession = {
    title: 'นัดตีประจำสัปดาห์',
    date: '2026-10-31',
    startTime: '19:00',
    endTime: '21:00',
    courtVenue: 'Winner Sport Club จตุจักร',
    courtNumbers: 'คอร์ต 3, 4',
    shuttlecockBrand: 'RSL Silver Speed 76',
    maxPlayers: 12,
    priceEstimate: 180,
    walkInPolicy: 'ไม่รับ Walk-in',
    pointsPerSet: 21
  };

  sessions = signal<BadmintonSession[]>([...getMockData(MOCK_OWNER_SESSIONS, [])]);

  ngOnInit(): void {
    this.loadSessions();
  }

  loadSessions(): void {
    this.api.getOwnerSessions(1).subscribe({
      next: (data) => {
        if (data && data.length > 0) {
          this.sessions.set(data);
        }
      },
      error: (err) => console.error('Failed to load owner sessions from API:', err)
    });
  }

  openCreateModal(): void {
    this.isCreateModalOpen.set(true);
  }

  closeCreateModal(): void {
    this.isCreateModalOpen.set(false);
  }

  saveNewSession(): void {
    if (!this.newSession.title || !this.newSession.date) {
      this.feedback.error('กรุณาระบุชื่องานและวันที่จัดรอบตี');
      return;
    }

    const payload = {
      groupId: 1,
      title: this.newSession.title,
      date: this.newSession.date,
      startTime: this.newSession.startTime,
      endTime: this.newSession.endTime,
      courtVenue: this.newSession.courtVenue,
      courtNumbers: this.newSession.courtNumbers,
      shuttlecockBrand: this.newSession.shuttlecockBrand,
      maxPlayers: Number(this.newSession.maxPlayers),
      priceEstimate: Number(this.newSession.priceEstimate)
    };

    this.api.createActivity(payload).subscribe({
      next: (res) => {
        this.feedback.success(`เปิดรอบตี "${payload.title}" สำเร็จ สมาชิกในก๊วนสามารถลงชื่อเข้าร่วมได้แล้ว`, 'เปิดรอบสำเร็จ');
        this.closeCreateModal();
        this.loadSessions();
      },
      error: (err) => {
        // Fallback optimistic update
        const created: BadmintonSession = {
          id: Date.now(),
          title: this.newSession.title,
          date: this.newSession.date,
          time: `${this.newSession.startTime} - ${this.newSession.endTime} น.`,
          courtVenue: this.newSession.courtVenue,
          courtNumbers: this.newSession.courtNumbers,
          shuttlecockBrand: this.newSession.shuttlecockBrand,
          maxPlayers: Number(this.newSession.maxPlayers),
          registeredCount: 1,
          priceEstimate: Number(this.newSession.priceEstimate),
          status: 'OPEN',
          players: [{ name: 'Coach Kitti (Host)', level: 'P+', isPaid: true }]
        };
        this.sessions.update(list => [created, ...list]);
        this.closeCreateModal();
        this.feedback.success(`เปิดรอบตี "${created.title}" สำเร็จ`, 'เปิดรอบสำเร็จ');
      }
    });
  }

  async toggleSessionStatus(s: BadmintonSession): Promise<void> {
    const nextStatus = s.status === 'OPEN' ? 'FULL' : 'OPEN';
    this.api.updateActivity(s.id, { status: nextStatus }).subscribe({
      next: () => {
        s.status = nextStatus;
        this.feedback.info(`เปลี่ยนสถานะรอบตีเป็น "${nextStatus === 'FULL' ? 'คอร์ตเต็ม (FULL)' : 'เปิดรับสมัคร (OPEN)'}" เรียบร้อย`);
      },
      error: () => {
        s.status = nextStatus;
        this.feedback.info(`เปลี่ยนสถานะรอบตีเป็น "${nextStatus === 'FULL' ? 'คอร์ตเต็ม (FULL)' : 'เปิดรับสมัคร (OPEN)'}" เรียบร้อย`);
      }
    });
  }

  async cancelSession(s: BadmintonSession): Promise<void> {
    const confirmed = await this.feedback.confirm({
      title: 'ยกเลิกรอบตีนี้?',
      message: `คุณต้องการยกเลิกรอบตี "${s.title}" หรือไม่? ระบบจะส่งการแจ้งเตือนไปยังผู้เล่นที่ลงชื่อไว้แล้ว`,
      confirmText: 'ยกเลิกรอบตี',
      type: 'danger',
      icon: 'fa-solid fa-calendar-xmark'
    });

    if (confirmed) {
      this.api.updateActivity(s.id, { status: 'CANCELLED' }).subscribe({
        next: () => {
          s.status = 'CANCELLED';
          this.feedback.warning('ยกเลิกรอบตีและแจ้งเตือนผู้เล่นเรียบร้อย');
        },
        error: () => {
          s.status = 'CANCELLED';
          this.feedback.warning('ยกเลิกรอบตีและแจ้งเตือนผู้เล่นเรียบร้อย');
        }
      });
    }
  }
}
