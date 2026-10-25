import { Component, signal, inject } from '@angular/core';
import { FeedbackService } from '../../../core/services/feedback.service';
import { MOCK_OWNER_SETTINGS_GROUP, getMockData } from '../../../mock-data';

@Component({
  selector: 'app-owner-settings',
  standalone: false,
  templateUrl: './owner-settings.component.html',
  styleUrls: ['./owner-settings.component.scss']
})
export class OwnerSettingsComponent {
  private feedback = inject(FeedbackService);

  group = { ...getMockData(MOCK_OWNER_SETTINGS_GROUP, MOCK_OWNER_SETTINGS_GROUP) };

  isSaving = signal(false);

  setFemaleDiscount(val: string): void {
    this.group.femaleDiscount = val;
  }

  setBeginnerDiscount(val: string): void {
    this.group.beginnerDiscount = val;
  }

  setWaterSnackFee(val: string): void {
    this.group.waterSnackFee = val;
  }

  setKnockFee(val: string): void {
    this.group.knockFee = val;
  }

  setGameFee(val: string): void {
    this.group.gameFee = val;
  }

  setCourtEntryFee(val: string): void {
    this.group.courtEntryFee = val;
  }

  setShuttlecockFee(val: string): void {
    this.group.shuttlecockFee = val;
  }

  saveSettings(): void {
    this.isSaving.set(true);
    setTimeout(() => {
      this.isSaving.set(false);
      this.feedback.success('บันทึกข้อมูลการตั้งค่าก๊วนและกติกาเรียบร้อยแล้ว!', 'บันทึกสำเร็จ');
    }, 600);
  }
}
