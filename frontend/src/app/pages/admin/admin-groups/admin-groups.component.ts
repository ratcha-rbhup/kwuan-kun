import { Component, signal, computed, inject } from '@angular/core';
import { FeedbackService } from '../../../core/services/feedback.service';
import { PlatformGroup, MOCK_ADMIN_GROUPS, getMockData } from '../../../mock-data';

@Component({
  selector: 'app-admin-groups',
  standalone: false,
  templateUrl: './admin-groups.component.html'
})
export class AdminGroupsComponent {
  private feedback = inject(FeedbackService);

  searchQuery = signal('');
  filterStatus = signal<string>('ALL');

  groups = signal<PlatformGroup[]>(getMockData(MOCK_ADMIN_GROUPS, []));

  filteredGroups = computed(() => {
    const q = this.searchQuery().toLowerCase().trim();
    const st = this.filterStatus();

    return this.groups().filter(g => {
      const matchQ = !q || g.name.toLowerCase().includes(q) || g.ownerName.toLowerCase().includes(q) || g.location.toLowerCase().includes(q);
      const matchSt = st === 'ALL' || g.status === st;
      return matchQ && matchSt;
    });
  });

  async toggleSuspend(g: PlatformGroup): Promise<void> {
    const isSuspended = g.status === 'SUSPENDED';
    const actionText = isSuspended ? 'ปลดระงับ (Activate)' : 'ระงับการใช้งานก๊วน (Suspend)';

    const confirmed = await this.feedback.confirm({
      title: `${actionText}?`,
      message: `คุณต้องการ${actionText} "${g.name}" ใช่หรือไม่?`,
      confirmText: actionText,
      type: isSuspended ? 'primary' : 'danger',
      icon: isSuspended ? 'fa-solid fa-circle-check' : 'fa-solid fa-ban'
    });

    if (confirmed) {
      g.status = isSuspended ? 'ACTIVE' : 'SUSPENDED';
      this.feedback.success(`ดำเนินการ${actionText}สำหรับ "${g.name}" เรียบร้อยแล้ว`);
    }
  }
}
