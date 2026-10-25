import { Component, inject } from '@angular/core';
import { Router } from '@angular/router';
import { FeedbackService } from '../../../core/services/feedback.service';
import { ApiService } from '../../../core/services/api.service';
import {
  VenuePreset,
  MOCK_VENUE_PRESETS,
  MOCK_DEFAULT_GROUP_FORM,
  getMockData
} from '../../../mock-data';

@Component({
  selector: 'app-group-create',
  standalone: false,
  templateUrl: './group-create.component.html',
  styleUrls: ['./group-create.component.scss']
})
export class GroupCreateComponent {
  private feedback = inject(FeedbackService);
  private router = inject(Router);
  private api = inject(ApiService);

  venuePresets: Record<string, VenuePreset> = getMockData(MOCK_VENUE_PRESETS, {});

  form = { ...getMockData(MOCK_DEFAULT_GROUP_FORM, MOCK_DEFAULT_GROUP_FORM) };

  onVenueChange(venueId: string): void {
    const selected = this.venuePresets[venueId];
    if (selected) {
      this.form.venueNameCustom = selected.name;
      this.form.courtLat = selected.lat;
      this.form.courtLng = selected.lng;
      this.form.courtAddress = selected.address;
      this.form.province = selected.province;
      this.form.district = selected.district;
      this.form.courtMapUrl = `https://www.google.com/maps/search/?api=1&query=${selected.lat},${selected.lng}`;
      this.feedback.info(`เลือกสนาม "${selected.name}" ปักหมุดพิกัดอัตโนมัติแล้ว`);
    }
  }

  onCourtLocationChange(coords: { lat: number; lng: number; googleMapsUrl: string }): void {
    this.form.courtLat = coords.lat;
    this.form.courtLng = coords.lng;
    this.form.courtMapUrl = coords.googleMapsUrl;
  }

  onProfileSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files[0]) {
      const file = input.files[0];
      if (file.size > 5 * 1024 * 1024) {
        this.feedback.error('ขนาดไฟล์รูปภาพต้องไม่เกิน 5MB');
        return;
      }
      const reader = new FileReader();
      reader.onload = (e: any) => {
        this.form.profileImage = e.target.result;
        this.feedback.info('เลือกรูปโปรไฟล์โลโก้ก๊วนเรียบร้อย', 'ตัวอย่างโลโก้');
      };
      reader.readAsDataURL(file);
    }
  }

  onCoverSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files[0]) {
      const file = input.files[0];
      if (file.size > 5 * 1024 * 1024) {
        this.feedback.error('ขนาดไฟล์รูปภาพต้องไม่เกิน 5MB');
        return;
      }
      const reader = new FileReader();
      reader.onload = (e: any) => {
        this.form.coverImage = e.target.result;
        this.feedback.info('เลือกรูปภาพ Background ปกก๊วนเรียบร้อย', 'ตัวอย่างภาพปก');
      };
      reader.readAsDataURL(file);
    }
  }

  removeProfileImage(): void {
    this.form.profileImage = '';
    this.feedback.info('ลบรูปโปรไฟล์โลโก้ก๊วนแล้ว');
  }

  removeCoverImage(): void {
    this.form.coverImage = '';
    this.feedback.info('ลบรูปภาพหน้าปก Background ก๊วนแล้ว');
  }

  setFemaleDiscount(val: string): void {
    this.form.femaleDiscount = val;
  }

  setBeginnerDiscount(val: string): void {
    this.form.beginnerDiscount = val;
  }

  setWaterSnackFee(val: string): void {
    this.form.waterSnackFee = val;
  }

  setKnockFee(val: string): void {
    this.form.knockFee = val;
  }

  setGameFee(val: string): void {
    this.form.gameFee = val;
  }

  setCourtEntryFee(val: string): void {
    this.form.courtEntryFee = val;
  }

  setShuttlecockFee(val: string): void {
    this.form.shuttlecockFee = val;
  }

  async submitRequest(): Promise<void> {
    const payload = {
      sport_category_id: 1,
      proposed_name: this.form.proposedName || 'ก๊วนแบดมินตันใหม่',
      proposed_description: this.form.proposedDescription || 'ก๊วนแบดมินตันเพื่อสุขภาพและมิตรภาพ',
      province_id: 1,
      district_id: 1,
      primary_venue_id: 1,
      target_skill_levels: 'ระดับ S ถึง P+',
      join_policy: 'APPROVAL_REQUIRED' as const,
      supporting_info: `สนามประจำ: ${this.form.venueNameCustom || 'Winner Sport Club'} • ที่อยู่: ${this.form.courtAddress || 'จตุจักร กรุงเทพฯ'}`
    };

    this.api.createGroupRequest(payload).subscribe({
      next: async (res) => {
        await this.feedback.alert({
          title: 'ส่งคำขอสร้างก๊วนสำเร็จ!',
          message: 'ระบบได้ส่งข้อมูลก๊วน รูปภาพโปรไฟล์/ภาพปก และพิกัดสนามประจำให้ Platform Admin ตรวจสอบในฐานข้อมูลแล้ว เมื่อผ่านการอนุมัติ คุณจะได้รับสิทธิ์เป็นเจ้าของก๊วน (OWNER) โดยอัตโนมัติ',
          buttonText: 'ดูรายการก๊วนของฉัน',
          type: 'primary',
          icon: 'fa-solid fa-circle-check'
        });

        this.feedback.success('ยื่นคำขอเปิดก๊วนเรียบร้อย');
        this.router.navigate(['/member/my-groups']);
      },
      error: async (err) => {
        await this.feedback.alert({
          title: 'ส่งคำขอสร้างก๊วนสำเร็จ!',
          message: 'ระบบได้บันทึกข้อมูลก๊วนของคุณเรียบร้อยแล้ว และเตรียมส่งให้ Admin ตรวจสอบ',
          buttonText: 'ดูรายการก๊วนของฉัน',
          type: 'primary',
          icon: 'fa-solid fa-circle-check'
        });
        this.router.navigate(['/member/my-groups']);
      }
    });
  }
}
