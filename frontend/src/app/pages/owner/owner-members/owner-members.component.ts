import { Component, signal, computed, inject } from '@angular/core';
import { FeedbackService } from '../../../core/services/feedback.service';
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
export class OwnerMembersComponent {
  private feedback = inject(FeedbackService);

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
      const matchQuery = !q || m.name.toLowerCase().includes(q) || m.lineId.toLowerCase().includes(q);
      const matchLvl = lvl === 'ALL' || m.skillLevel === lvl;
      return matchQuery && matchLvl;
    });
  });

  async updateSkillLevel(member: GroupMember, newLevel: any): Promise<void> {
    member.skillLevel = newLevel;
    this.feedback.success(`ปรับระดับมือของคุณ "${member.name}" เป็น "${newLevel}" เรียบร้อยแล้ว`);
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
      this.members.update(list => list.filter(m => m.id !== member.id));
      this.feedback.info(`นำคุณ "${member.name}" ออกจากก๊วนเรียบร้อยแล้ว`);
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
      this.pendingRequests.update(list => list.filter(r => r.id !== req.id));
      const newMember: GroupMember = {
        id: req.id,
        name: req.name,
        lineId: req.lineId,
        skillLevel: 'S',
        role: 'MEMBER',
        joinDate: 'วันนี้',
        sessionsCount: 0,
        status: 'ACTIVE'
      };
      this.members.update(list => [...list, newMember]);
      this.feedback.success(`รับคุณ "${req.name}" เข้าก๊วนเรียบร้อยแล้ว`, 'อนุมัติสำเร็จ');
    }
  }

  async rejectRequest(req: PendingRequest): Promise<void> {
    this.pendingRequests.update(list => list.filter(r => r.id !== req.id));
    this.feedback.info(`ปฏิเสธคำขอของคุณ "${req.name}" เรียบร้อยแล้ว`);
  }
}
