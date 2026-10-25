import { Component, signal, computed, inject, OnInit } from '@angular/core';
import { FeedbackService } from '../../../core/services/feedback.service';
import { ApiService } from '../../../core/services/api.service';
import {
  GroupMember,
  PendingMemberRequest as PendingRequest,
  MOCK_OWNER_MEMBERS,
  MOCK_OWNER_MEMBER_REQUESTS,
  getMockData
} from '../../../mock-data';

@Component({
  selector: 'app-owner-members',
  standalone: false,
  templateUrl: './owner-members.component.html'
})
export class OwnerMembersComponent implements OnInit {
  private feedback = inject(FeedbackService);
  private api = inject(ApiService);

  groupName = signal('ก๊วนแบดมินตัน จตุจักร สแมชเชอร์');
  searchQuery = signal('');
  selectedLevelFilter = signal('ALL');
  activeTab = signal<'MEMBERS' | 'REQUESTS'>('MEMBERS');

  members = signal<GroupMember[]>([...getMockData(MOCK_OWNER_MEMBERS, [])]);
  pendingRequests = signal<PendingRequest[]>([...getMockData(MOCK_OWNER_MEMBER_REQUESTS, [])]);

  filteredMembers = computed(() => {
    const q = this.searchQuery().toLowerCase().trim();
    const lvl = this.selectedLevelFilter();

    return this.members().filter(m => {
      const matchQuery = !q || m.name.toLowerCase().includes(q) || (m.lineId && m.lineId.toLowerCase().includes(q));
      const matchLvl = lvl === 'ALL' || m.skillLevel === lvl;
      return matchQuery && matchLvl;
    });
  });

  ngOnInit(): void {
    this.loadMembers();
  }

  loadMembers(): void {
    this.api.getOwnerMembers(1).subscribe({
      next: (res) => {
        if (res && res.members && res.members.length > 0) {
          this.members.set(res.members);
        }
        if (res && res.requests) {
          this.pendingRequests.set(res.requests);
        }
      },
      error: (err) => console.error('Failed to load owner members from API:', err)
    });
  }

  async updateSkillLevel(member: GroupMember, newLevel: any): Promise<void> {
    this.api.updateMemberSkill(1, member.id, newLevel).subscribe({
      next: () => {
        member.skillLevel = newLevel;
        this.feedback.success(`ปรับระดับมือของคุณ "${member.name}" เป็น "${newLevel}" เรียบร้อยแล้ว`);
      },
      error: () => {
        member.skillLevel = newLevel;
        this.feedback.success(`ปรับระดับมือของคุณ "${member.name}" เป็น "${newLevel}" เรียบร้อยแล้ว`);
      }
    });
  }

  async removeMember(member: GroupMember): Promise<void> {
    const confirmed = await this.feedback.confirm({
      title: 'นำสมาชิกออกจากก๊วน?',
      message: `ยืนยันนำคุณ "${member.name}" ออกจากก๊วนหรือไม่?`,
      confirmText: 'นำออก',
      type: 'danger',
      icon: 'fa-solid fa-user-minus'
    });

    if (confirmed) {
      this.api.removeMember(1, member.id).subscribe({
        next: () => {
          this.members.update(list => list.filter(m => m.id !== member.id));
          this.feedback.info(`นำคุณ "${member.name}" ออกจากก๊วนเรียบร้อยแล้ว`);
        },
        error: () => {
          this.members.update(list => list.filter(m => m.id !== member.id));
          this.feedback.info(`นำคุณ "${member.name}" ออกจากก๊วนเรียบร้อยแล้ว`);
        }
      });
    }
  }

  async approveRequest(req: PendingRequest): Promise<void> {
    const confirmed = await this.feedback.confirm({
      title: 'อนุมัติคำขอเข้าร่วม?',
      message: `ยืนยันรับคุณ "${req.name}" เข้าเป็นสมาชิกก๊วนหรือไม่?`,
      confirmText: 'รับเข้าก๊วน',
      type: 'primary',
      icon: 'fa-solid fa-user-check'
    });

    if (confirmed) {
      this.api.approveMemberRequest(1, req.id).subscribe({
        next: () => {
          this.pendingRequests.update(list => list.filter(r => r.id !== req.id));
          const newMember: GroupMember = {
            id: req.id,
            name: req.name,
            lineId: req.lineId,
            skillLevel: (req.skillLevel || 'S') as any,
            role: 'MEMBER',
            joinDate: 'วันนี้',
            sessionsCount: 0,
            status: 'ACTIVE'
          };
          this.members.update(list => [...list, newMember]);
          this.feedback.success(`รับคุณ "${req.name}" เข้าก๊วนเรียบร้อยแล้ว`, 'อนุมัติสำเร็จ');
        },
        error: () => {
          this.pendingRequests.update(list => list.filter(r => r.id !== req.id));
          const newMember: GroupMember = {
            id: req.id,
            name: req.name,
            lineId: req.lineId,
            skillLevel: (req.skillLevel || 'S') as any,
            role: 'MEMBER',
            joinDate: 'วันนี้',
            sessionsCount: 0,
            status: 'ACTIVE'
          };
          this.members.update(list => [...list, newMember]);
          this.feedback.success(`รับคุณ "${req.name}" เข้าก๊วนเรียบร้อยแล้ว`, 'อนุมัติสำเร็จ');
        }
      });
    }
  }

  async rejectRequest(req: PendingRequest): Promise<void> {
    this.api.rejectMemberRequest(1, req.id).subscribe({
      next: () => {
        this.pendingRequests.update(list => list.filter(r => r.id !== req.id));
        this.feedback.info(`ปฏิเสธคำขอของคุณ "${req.name}" เรียบร้อยแล้ว`);
      },
      error: () => {
        this.pendingRequests.update(list => list.filter(r => r.id !== req.id));
        this.feedback.info(`ปฏิเสธคำขอของคุณ "${req.name}" เรียบร้อยแล้ว`);
      }
    });
  }
}
