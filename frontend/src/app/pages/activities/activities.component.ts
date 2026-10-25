import { Component, signal, inject } from '@angular/core';
import { FeedbackService } from '../../core/services/feedback.service';
import { MOCK_ACTIVITIES, getMockData } from '../../mock-data';

@Component({
  selector: 'app-activities',
  standalone: false,
  templateUrl: './activities.component.html'
})
export class ActivitiesComponent {
  private feedback = inject(FeedbackService);
  filterScope = signal<'all' | 'today' | 'weekend'>('all');

  activities = signal(getMockData(MOCK_ACTIVITIES, []));

  async toggleRsvp(activity: any) {
    if (activity.isJoined) {
      // Prompt confirmation before canceling
      const confirmed = await this.feedback.confirm({
        title: 'ยืนยันการยกเลิกการลงชื่อ?',
        message: `คุณต้องการยกเลิกการลงชื่อในกิจกรรม "${activity.title}" ใช่หรือไม่?`,
        confirmText: 'ยืนยันยกเลิก',
        cancelText: 'คงสถานะไว้',
        type: 'danger'
      });

      if (confirmed) {
        activity.isJoined = false;
        activity.confirmedPlayers -= 1;
        this.feedback.info(`ยกเลิกการลงชื่อใน "${activity.title}" เรียบร้อยแล้ว`, 'ยกเลิกเรียบร้อย');
      }
    } else {
      activity.isJoined = true;
      activity.confirmedPlayers += 1;
      this.feedback.success(`ลงชื่อร่วมก๊วน "${activity.title}" สำเร็จ! เตรียมชุดและอุปกรณ์ให้พร้อม`, 'ลงชื่อสำเร็จ');
    }
  }
}
