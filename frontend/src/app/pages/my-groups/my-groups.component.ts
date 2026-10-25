import { Component, signal } from '@angular/core';
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
export class MyGroupsComponent {
  activeTab = signal<'joined' | 'owned' | 'pending'>('joined');

  joinedGroups = signal(getMockData(MOCK_MY_JOINED_GROUPS, []));
  ownedGroups = signal(getMockData(MOCK_MY_OWNED_GROUPS, []));
  pendingRequests = signal(getMockData(MOCK_MY_PENDING_REQUESTS, []));
}
