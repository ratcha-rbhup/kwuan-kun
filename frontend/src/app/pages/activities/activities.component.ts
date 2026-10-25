import { Component, signal, inject, OnInit } from '@angular/core';
import { FeedbackService } from '../../core/services/feedback.service';
import { ApiService } from '../../core/services/api.service';
import { MOCK_ACTIVITIES, getMockData } from '../../mock-data';

@Component({
  selector: 'app-activities',
  standalone: false,
  templateUrl: './activities.component.html'
})
export class ActivitiesComponent implements OnInit {
  private feedback = inject(FeedbackService);
  private api = inject(ApiService);
  filterScope = signal<'all' | 'today' | 'weekend'>('all');

  activities = signal<any[]>(getMockData(MOCK_ACTIVITIES, []));

  ngOnInit(): void {
    this.loadActivities();
  }

  loadActivities(): void {
    this.api.getActivities().subscribe({
      next: (data) => {
        if (data && data.length > 0) {
          this.activities.set(data);
        }
      },
      error: (err) => console.error('Failed to load activities from API:', err)
    });
  }

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
        this.api.leaveActivity(activity.id).subscribe({
          next: () => {
            activity.isJoined = false;
            activity.confirmedPlayers = Math.max(0, (activity.confirmedPlayers || 1) - 1);
            activity.slotsAvailable = (activity.slotsAvailable || 0) + 1;
            this.feedback.info(`ยกเลิกการลงชื่อใน "${activity.title}" เรียบร้อยแล้ว`, 'ยกเลิกเรียบร้อย');
          },
          error: () => {
            activity.isJoined = false;
            activity.confirmedPlayers = Math.max(0, (activity.confirmedPlayers || 1) - 1);
            this.feedback.info(`ยกเลิกการลงชื่อใน "${activity.title}" เรียบร้อยแล้ว`, 'ยกเลิกเรียบร้อย');
          }
        });
      }
    } else {
      this.api.joinActivity(activity.id).subscribe({
        next: (res) => {
          activity.isJoined = true;
          activity.confirmedPlayers = (activity.confirmedPlayers || 0) + 1;
          activity.slotsAvailable = Math.max(0, (activity.slotsAvailable || 1) - 1);
          this.feedback.success(`ลงชื่อร่วมก๊วน "${activity.title}" สำเร็จ! เตรียมชุดและอุปกรณ์ให้พร้อม`, 'ลงชื่อสำเร็จ');
        },
        error: () => {
          activity.isJoined = true;
          activity.confirmedPlayers = (activity.confirmedPlayers || 0) + 1;
          this.feedback.success(`ลงชื่อร่วมก๊วน "${activity.title}" สำเร็จ! เตรียมชุดและอุปกรณ์ให้พร้อม`, 'ลงชื่อสำเร็จ');
        }
      });
    }
  }
}
