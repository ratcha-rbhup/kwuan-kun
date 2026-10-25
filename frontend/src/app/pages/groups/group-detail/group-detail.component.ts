import { Component, signal, Input, inject } from '@angular/core';
import { FeedbackService } from '../../../core/services/feedback.service';
import {
  BadmintonCourtVenue,
  MOCK_GROUP_PARTICIPANTS,
  MOCK_GROUP_DETAIL,
  getMockData
} from '../../../mock-data';

export { BadmintonCourtVenue };

@Component({
  selector: 'app-group-detail',
  standalone: false,
  templateUrl: './group-detail.component.html',
  styleUrls: ['./group-detail.component.scss']
})
export class GroupDetailComponent {
  private feedback = inject(FeedbackService);
  @Input() slug = 'chatuchak-smashers';
  activeTab = signal<'info' | 'courts' | 'activities' | 'members' | 'announcements'>('info');

  isInterested = signal(false);
  interestCount = signal(5);

  participants = signal([...getMockData(MOCK_GROUP_PARTICIPANTS, [])]);

  group = signal({ ...getMockData(MOCK_GROUP_DETAIL, MOCK_GROUP_DETAIL) });

  // Action: Toggle Interest (🧡 สนใจ)
  toggleInterest(): void {
    this.isInterested.update(val => !val);
    this.interestCount.update(c => this.isInterested() ? c + 1 : c - 1);
    if (this.isInterested()) {
      this.feedback.success('บันทึกความสนใจเรียบร้อย ระบบจะแจ้งเตือนเมื่อใกล้ถึงเวลากิจกรรม', 'กดสนใจแล้ว 🧡');
    }
  }

  // Action: Share Party (📤 แชร์)
  shareParty(): void {
    if (navigator?.clipboard) {
      navigator.clipboard.writeText(window.location.href);
    }
    this.feedback.success('คัดลอกลิงก์ก๊วนเรียบร้อยแล้ว ส่งแชร์ต่อให้เพื่อนใน LINE หรือ Facebook ได้ทันที', 'คัดลอกลิงก์สำเร็จ 📤');
  }

  // Action: Open Google Maps Route Navigation
  openRoute(court: BadmintonCourtVenue): void {
    window.open(court.googleMapsUrl, '_blank');
  }

  // Action: Open Apple Maps
  openAppleMaps(court: BadmintonCourtVenue): void {
    window.open(court.appleMapsUrl, '_blank');
  }

  // Action: Copy Address to clipboard
  copyAddress(court: BadmintonCourtVenue): void {
    if (navigator?.clipboard) {
      navigator.clipboard.writeText(`${court.name} - ${court.address}`);
    }
    this.feedback.success(`คัดลอกที่อยู่สนาม "${court.name}" เรียบร้อยแล้ว นำไปวางใน Google Maps / Grab / LINE MAN ได้ทันที`, 'คัดลอกพิกัดสำเร็จ');
  }

  // Upload/Change Cover Banner
  onCoverFileSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = (e: any) => {
        this.group.update(g => ({ ...g, coverImage: e.target.result }));
        this.feedback.success('อัปเดตรูปภาพ Background ปกก๊วนเรียบร้อยแล้ว', 'เปลี่ยนรูปปกสำเร็จ');
      };
      reader.readAsDataURL(input.files[0]);
    }
  }

  // Upload/Change Kwuan Profile Logo
  onProfileFileSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = (e: any) => {
        this.group.update(g => ({ ...g, profileImage: e.target.result }));
        this.feedback.success('อัปเดตรูปโปรไฟล์โลโก้ก๊วนเรียบร้อยแล้ว', 'เปลี่ยนรูปโปรไฟล์ก๊วนสำเร็จ');
      };
      reader.readAsDataURL(input.files[0]);
    }
  }

  async applyToJoin(): Promise<void> {
    await this.feedback.alert({
      title: 'ส่งคำขอเข้าร่วมก๊วนสำเร็จ!',
      message: `ระบบได้ส่งคำขอเข้าร่วมก๊วนของคุณไปยังเจ้าของก๊วน (${this.group().ownerName}) แล้ว เมื่อได้รับการตอบรับ ระบบจะแจ้งเตือนผ่าน LINE ทันที`,
      buttonText: 'เข้าใจแล้ว',
      type: 'primary',
      icon: 'fa-solid fa-circle-check'
    });

    this.feedback.success('ส่งคำขอเข้าร่วมก๊วนเรียบร้อย', 'สมัครสำเร็จ');
  }
}

