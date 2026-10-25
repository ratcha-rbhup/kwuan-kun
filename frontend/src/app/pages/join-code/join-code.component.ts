import { Component, signal, OnInit, inject } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { FeedbackService } from '../../core/services/feedback.service';
import { MOCK_INVITE_CODES, getMockData } from '../../mock-data';

@Component({
  selector: 'app-join-code',
  standalone: false,
  templateUrl: './join-code.component.html'
})
export class JoinCodeComponent implements OnInit {
  private feedback = inject(FeedbackService);
  inviteCode = signal('');
  isSearching = signal(false);
  foundGroup = signal<any | null>(null);
  joinSuccess = signal(false);
  errorMessage = signal<string | null>(null);

  // Pre-configured mock invite codes for instant testing
  mockCodes: Record<string, any> = getMockData(MOCK_INVITE_CODES, {});

  constructor(private route: ActivatedRoute, private router: Router) {}

  ngOnInit() {
    this.route.params.subscribe(params => {
      if (params['code']) {
        this.inviteCode.set(params['code'].toUpperCase());
        this.searchGroup();
      }
    });
  }

  searchGroup() {
    const code = this.inviteCode().trim().toUpperCase();
    this.errorMessage.set(null);
    this.foundGroup.set(null);
    this.joinSuccess.set(false);

    if (!code) {
      this.errorMessage.set('กรุณาระบุรหัสคำเชิญเข้าร่วมก๊วน');
      this.feedback.error('กรุณาระบุรหัสคำเชิญเข้าร่วมก๊วน');
      return;
    }

    this.isSearching.set(true);

    setTimeout(() => {
      this.isSearching.set(false);
      const group = this.mockCodes[code];
      if (group) {
        this.foundGroup.set(group);
        this.feedback.success(`พบข้อมูลก๊วน "${group.name}"`, 'ค้นหาสำเร็จ');
      } else {
        // Fallback for custom codes entered by user
        this.foundGroup.set({
          name: `ก๊วนแบดมินตัน (รหัส: ${code})`,
          slug: 'chatuchak-smashers',
          venue: 'สนามแบดมินตันมาตรฐานสากล',
          location: 'กรุงเทพมหานคร',
          owner: 'ผู้ดูแลก๊วน',
          skillLevel: 'ทุกระดับฝีมือ',
          memberCount: 12,
          joinPolicy: 'เข้าร่วมได้ทันที'
        });
        this.feedback.info(`พบก๊วนสำหรับรหัส ${code}`, 'ผลการค้นหา');
      }
    }, 400);
  }

  confirmJoin() {
    this.joinSuccess.set(true);
    this.feedback.success(`เข้าร่วมก๊วน "${this.foundGroup()?.name}" สำเร็จแล้ว!`, 'ยินดีต้อนรับ');
  }
}
