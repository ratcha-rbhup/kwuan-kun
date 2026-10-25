import { Component, signal, inject, OnInit } from '@angular/core';
import { ApiService } from '../../core/services/api.service';
import {
  MOCK_MY_JOINED_GROUPS,
  MOCK_MY_OWNED_GROUPS,
  MOCK_MY_PENDING_REQUESTS,
  getMockData
} from '../../mock-data';

@Component({
  selector: 'app-my-groups',
  standalone: false,
  templateUrl: './my-groups.component.html'
})
export class MyGroupsComponent implements OnInit {
  private api = inject(ApiService);
  activeTab = signal<'joined' | 'owned' | 'pending'>('joined');

  joinedGroups = signal<any[]>(getMockData(MOCK_MY_JOINED_GROUPS, []));
  ownedGroups = signal<any[]>(getMockData(MOCK_MY_OWNED_GROUPS, []));
  pendingRequests = signal<any[]>(getMockData(MOCK_MY_PENDING_REQUESTS, []));

  ngOnInit(): void {
    this.api.getMyGroups().subscribe({
      next: (res: any) => {
        if (res) {
          if (res.joined && res.joined.length > 0) this.joinedGroups.set(res.joined);
          if (res.owned && res.owned.length > 0) this.ownedGroups.set(res.owned);
          if (res.pending && res.pending.length > 0) this.pendingRequests.set(res.pending);
        }
      },
      error: (err) => console.error('Failed to load my-groups from API:', err)
    });
  }
}
