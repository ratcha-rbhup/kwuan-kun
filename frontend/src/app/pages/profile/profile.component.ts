import { Component, signal, inject, OnInit } from '@angular/core';
import { FeedbackService } from '../../core/services/feedback.service';
import { AuthService } from '../../core/services/auth.service';
import { MOCK_PLAYER_PROFILE, getMockData } from '../../mock-data';

@Component({
  selector: 'app-profile',
  standalone: false,
  templateUrl: './profile.component.html',
  styleUrls: ['./profile.component.scss']
})
export class ProfileComponent implements OnInit {
  private feedback = inject(FeedbackService);
  private auth = inject(AuthService);

  // Default LINE Avatar for fallback/reset (matching food-server line_avatar pattern)
  readonly defaultLineAvatar = 'assets/image/player-avatar.jpg';

  profile = signal({ ...getMockData(MOCK_PLAYER_PROFILE, MOCK_PLAYER_PROFILE) });

  // View / Edit mode toggle state
  isEditing = signal(false);

  // Local mutable form model for editing
  editForm: any = { ...getMockData(MOCK_PLAYER_PROFILE, MOCK_PLAYER_PROFILE) };

  saveSuccess = signal(false);


  ngOnInit(): void {
    // Restore from localStorage if available
    const saved = localStorage.getItem('ak_player_profile');
    if (saved) {
      try {
        const parsed = JSON.parse(saved);
        this.profile.update(p => ({ ...p, ...parsed }));
      } catch (e) {
        console.error('Failed to parse saved profile', e);
      }
    }
    this.editForm = { ...this.profile() };
  }

  startEdit(): void {
    this.editForm = { ...this.profile() };
    this.isEditing.set(true);
    this.feedback.info('เข้าสู่โหมดแก้ไขข้อมูลโปรไฟล์ คุณสามารถปรับแต่งข้อมูลแล้วกดบันทึกได้ครับ');
  }

  cancelEdit(): void {
    this.editForm = { ...this.profile() };
    this.isEditing.set(false);
    this.feedback.info('ยกเลิกการแก้ไขข้อมูลแล้ว');
  }

  onAvatarFileSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files[0]) {
      const file = input.files[0];
      // File size validation (Max 5MB)
      if (file.size > 5 * 1024 * 1024) {
        this.feedback.error('ขนาดไฟล์รูปภาพต้องไม่เกิน 5MB', 'ไฟล์มีขนาดใหญ่เกินไป');
        return;
      }

      const reader = new FileReader();
      reader.onload = (e: any) => {
        if (!this.isEditing()) {
          this.startEdit();
        }
        this.editForm.avatar = e.target.result;
        this.editForm.isCustomAvatar = true;
        this.feedback.info('เลือกรูปโปรไฟล์เรียบร้อย กด "บันทึกข้อมูลโปรไฟล์" เพื่อบันทึกใช้งานจริง', 'ตัวอย่างรูปภาพ');
      };
      reader.readAsDataURL(file);
    }
  }

  // Reset Profile Avatar back to original LINE avatar (matching food-server resetProfile pattern)
  resetToLineAvatar(): void {
    this.editForm.avatar = this.defaultLineAvatar;
    this.editForm.isCustomAvatar = false;
    this.feedback.success('รีเซ็ตรูปโปรไฟล์กลับไปใช้รูปภาพต้นฉบับจาก LINE เรียบร้อยแล้ว', 'รีเซ็ตสำเร็จ');
  }

  saveProfile(): void {
    this.profile.set({ ...this.editForm });
    this.isEditing.set(false);
    this.saveSuccess.set(true);

    this.syncWithAuthService();

    // Persist full profile
    localStorage.setItem('ak_player_profile', JSON.stringify(this.profile()));

    this.feedback.success(
      'บันทึกข้อมูลโปรไฟล์ (ชื่อ-นามสกุล, เบอร์โทรศัพท์, Display Name และรูปภาพ) เรียบร้อยแล้ว',
      'บันทึกสำเร็จ'
    );

    setTimeout(() => {
      this.saveSuccess.set(false);
    }, 3000);
  }

  private syncWithAuthService(): void {
    const current = this.auth.currentUser();
    if (current) {
      this.auth.currentUser.set({
        ...current,
        display_name: this.profile().displayName,
        avatar_url: this.profile().avatar
      });
      localStorage.setItem('ak_auth_user', JSON.stringify(this.auth.currentUser()));
    }
  }
}
