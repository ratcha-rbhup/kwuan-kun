import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';

// Layouts
import { MainLayoutComponent } from './layouts/main-layout/main-layout.component';
import { AdminLayoutComponent } from './layouts/admin-layout/admin-layout.component';
import { OwnerLayoutComponent } from './layouts/owner-layout/owner-layout.component';

// Public & Shared Pages
import { HomeComponent } from './pages/home/home.component';
import { GroupListComponent } from './pages/groups/group-list/group-list.component';
import { GroupDetailComponent } from './pages/groups/group-detail/group-detail.component';
import { GroupCreateComponent } from './pages/groups/group-create/group-create.component';
import { JoinCodeComponent } from './pages/join-code/join-code.component';

// Member Dedicated Pages (Mobile & LINE LIFF)
import { MyGroupsComponent } from './pages/my-groups/my-groups.component';
import { ActivitiesComponent } from './pages/activities/activities.component';
import { ProfileComponent } from './pages/profile/profile.component';

// Kwuan Owner Dedicated Pages
import { OwnerDashboardComponent } from './pages/owner/owner-dashboard/owner-dashboard.component';
import { OwnerSessionsComponent } from './pages/owner/owner-sessions/owner-sessions.component';
import { OwnerMembersComponent } from './pages/owner/owner-members/owner-members.component';
import { OwnerExpensesComponent } from './pages/owner/owner-expenses/owner-expenses.component';
import { OwnerSettingsComponent } from './pages/owner/owner-settings/owner-settings.component';

// Platform Admin Pages
import { GroupRequestsComponent } from './pages/admin/group-requests/group-requests.component';
import { AdminGroupsComponent } from './pages/admin/admin-groups/admin-groups.component';
import { AdminReportsComponent } from './pages/admin/admin-reports/admin-reports.component';
import { AdminSettingsComponent } from './pages/admin/admin-settings/admin-settings.component';

const routes: Routes = [
  // =========================================================================
  // 1. Public & Guest Routes (Landing, Search, Direct Code Share Link)
  // =========================================================================
  {
    path: '',
    component: MainLayoutComponent,
    children: [
      { path: '', component: HomeComponent },
      { path: 'groups', component: GroupListComponent },
      { path: 'groups/:slug', component: GroupDetailComponent },
      { path: 'join/:code', component: JoinCodeComponent },
      { path: 'join-code', component: JoinCodeComponent },

      // Backward-compatible Redirects to /member/*
      { path: 'my-groups', redirectTo: 'member/my-groups' },
      { path: 'activities', redirectTo: 'member/activities' },
      { path: 'profile', redirectTo: 'member/profile' },
      { path: 'groups/create', redirectTo: 'member/create-group' }
    ]
  },

  // =========================================================================
  // 2. /member/ - Player & Member Area (LINE LIFF & Mobile First)
  // =========================================================================
  {
    path: 'member',
    component: MainLayoutComponent,
    children: [
      { path: '', pathMatch: 'full', redirectTo: 'my-groups' },
      { path: 'dashboard', component: MyGroupsComponent },
      { path: 'my-groups', component: MyGroupsComponent },
      { path: 'activities', component: ActivitiesComponent },
      { path: 'join-code', component: JoinCodeComponent },
      { path: 'create-group', component: GroupCreateComponent },
      { path: 'profile', component: ProfileComponent }
    ]
  },

  // =========================================================================
  // 3. /kwuan-owner/ - Badminton Group Owner / Host Panel
  // =========================================================================
  {
    path: 'kwuan-owner',
    component: OwnerLayoutComponent,
    children: [
      { path: '', pathMatch: 'full', redirectTo: 'dashboard' },
      { path: 'dashboard', component: OwnerDashboardComponent },
      { path: 'sessions', component: OwnerSessionsComponent },
      { path: 'members', component: OwnerMembersComponent },
      { path: 'expenses', component: OwnerExpensesComponent },
      { path: 'settings', component: OwnerSettingsComponent }
    ]
  },

  // =========================================================================
  // 4. /admin/ - Platform Super Admin Panel
  // =========================================================================
  {
    path: 'admin',
    component: AdminLayoutComponent,
    children: [
      { path: '', pathMatch: 'full', redirectTo: 'requests' },
      { path: 'requests', component: GroupRequestsComponent },
      { path: 'groups', component: AdminGroupsComponent },
      { path: 'reports', component: AdminReportsComponent },
      { path: 'settings', component: AdminSettingsComponent }
    ]
  },

  // Fallback
  { path: '**', redirectTo: '' }
];

@NgModule({
  imports: [RouterModule.forRoot(routes, { scrollPositionRestoration: 'enabled' })],
  exports: [RouterModule]
})
export class AppRoutingModule {}
