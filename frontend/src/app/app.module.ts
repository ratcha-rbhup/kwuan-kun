import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpClientModule } from '@angular/common/http';

import { AppRoutingModule } from './app-routing.module';

// Root Component
import { AppComponent } from './app.component';

// Layouts
import { MainLayoutComponent } from './layouts/main-layout/main-layout.component';
import { AdminLayoutComponent } from './layouts/admin-layout/admin-layout.component';
import { OwnerLayoutComponent } from './layouts/owner-layout/owner-layout.component';

// Pages
import { HomeComponent } from './pages/home/home.component';
import { GroupListComponent } from './pages/groups/group-list/group-list.component';
import { GroupDetailComponent } from './pages/groups/group-detail/group-detail.component';
import { GroupCreateComponent } from './pages/groups/group-create/group-create.component';
import { GroupRequestsComponent } from './pages/admin/group-requests/group-requests.component';

// Kwuan Owner Dedicated Pages
import { OwnerDashboardComponent } from './pages/owner/owner-dashboard/owner-dashboard.component';
import { OwnerSessionsComponent } from './pages/owner/owner-sessions/owner-sessions.component';
import { OwnerMembersComponent } from './pages/owner/owner-members/owner-members.component';
import { OwnerExpensesComponent } from './pages/owner/owner-expenses/owner-expenses.component';
import { OwnerSettingsComponent } from './pages/owner/owner-settings/owner-settings.component';

// Platform Admin Pages
import { AdminGroupsComponent } from './pages/admin/admin-groups/admin-groups.component';
import { AdminReportsComponent } from './pages/admin/admin-reports/admin-reports.component';
import { AdminSettingsComponent } from './pages/admin/admin-settings/admin-settings.component';

// Rich Menu Dedicated Pages
import { MyGroupsComponent } from './pages/my-groups/my-groups.component';
import { ActivitiesComponent } from './pages/activities/activities.component';
import { JoinCodeComponent } from './pages/join-code/join-code.component';
import { ProfileComponent } from './pages/profile/profile.component';

// Central UI Feedback Component
import { FeedbackHostComponent } from './core/components/feedback-host/feedback-host.component';
import { CourtMapPickerComponent } from './core/components/court-map-picker/court-map-picker.component';

@NgModule({
  declarations: [
    AppComponent,
    MainLayoutComponent,
    AdminLayoutComponent,
    OwnerLayoutComponent,
    FeedbackHostComponent,
    CourtMapPickerComponent,
    HomeComponent,
    GroupListComponent,
    GroupDetailComponent,
    GroupCreateComponent,
    GroupRequestsComponent,
    AdminGroupsComponent,
    AdminReportsComponent,
    AdminSettingsComponent,
    OwnerDashboardComponent,
    OwnerSessionsComponent,
    OwnerMembersComponent,
    OwnerExpensesComponent,
    OwnerSettingsComponent,
    MyGroupsComponent,
    ActivitiesComponent,
    JoinCodeComponent,
    ProfileComponent
  ],
  imports: [
    BrowserModule,
    CommonModule,
    FormsModule,
    HttpClientModule,
    AppRoutingModule,
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule {}
