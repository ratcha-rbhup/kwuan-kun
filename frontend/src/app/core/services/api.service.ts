import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable, map } from 'rxjs';
import { SportCategory, BadmintonSkillLevel } from '../../models/sport-category.model';
import { BadmintonGroup, GroupCreationRequestPayload } from '../../models/group.model';

@Injectable({
  providedIn: 'root'
})
export class ApiService {
  private http = inject(HttpClient);
  public readonly apiUrl = 'http://localhost:8000/api';

  // ==========================================
  // 1. Profile Endpoints
  // ==========================================
  getProfile(): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/profile`).pipe(
      map(res => res.data || res)
    );
  }

  updateProfile(data: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/profile/update`, data);
  }

  uploadAvatar(file: File): Observable<any> {
    const formData = new FormData();
    formData.append('avatar', file);
    return this.http.post<any>(`${this.apiUrl}/profile/avatar`, formData);
  }

  resetAvatar(): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/profile/reset-avatar`, {});
  }

  // ==========================================
  // 2. Groups (Public & Member)
  // ==========================================
  getGroups(filters?: { province_id?: number; district_id?: number; skill_level_id?: number; query?: string }): Observable<any[]> {
    let params = new HttpParams();
    if (filters?.province_id) params = params.set('province_id', filters.province_id.toString());
    if (filters?.district_id) params = params.set('district_id', filters.district_id.toString());
    if (filters?.skill_level_id) params = params.set('skill_level_id', filters.skill_level_id.toString());
    if (filters?.query) params = params.set('query', filters.query);

    return this.http.get<any>(`${this.apiUrl}/groups`, { params }).pipe(
      map(res => res.data || res)
    );
  }

  getGroupDetail(slug: string): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/groups/${slug}`).pipe(
      map(res => res.data || res)
    );
  }

  createGroupRequest(payload: GroupCreationRequestPayload): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/group-creation-requests`, payload);
  }

  // ==========================================
  // 3. Activities / Sessions (Live Matching)
  // ==========================================
  getActivities(groupId?: number): Observable<any[]> {
    let params = new HttpParams();
    if (groupId) params = params.set('group_id', groupId.toString());
    return this.http.get<any>(`${this.apiUrl}/activities`, { params }).pipe(
      map(res => res.data || res)
    );
  }

  getActivityDetail(id: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/activities/${id}`).pipe(
      map(res => res.data || res)
    );
  }

  createActivity(payload: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/activities`, payload);
  }

  updateActivity(id: number, payload: any): Observable<any> {
    return this.http.put<any>(`${this.apiUrl}/activities/${id}`, payload);
  }

  deleteActivity(id: number): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}/activities/${id}`);
  }

  joinActivity(activityId: number, notes?: string): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/activities/${activityId}/join`, { notes });
  }

  leaveActivity(activityId: number): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/activities/${activityId}/leave`, {});
  }

  // ==========================================
  // 4. Kwuan Owner Portal
  // ==========================================
  getOwnerDashboard(groupId: number = 1): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/owner/groups/${groupId}/dashboard`).pipe(
      map(res => res.data || res)
    );
  }

  getOwnerSessions(groupId: number = 1): Observable<any[]> {
    return this.http.get<any>(`${this.apiUrl}/owner/groups/${groupId}/sessions`).pipe(
      map(res => res.data || res)
    );
  }

  getOwnerMembers(groupId: number = 1): Observable<{ members: any[]; requests: any[] }> {
    return this.http.get<any>(`${this.apiUrl}/owner/groups/${groupId}/members`).pipe(
      map(res => res.data || { members: [], requests: [] })
    );
  }

  updateMemberSkill(groupId: number, memberId: number, skillLevel: string): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/owner/groups/${groupId}/members/${memberId}/skill`, { skillLevel });
  }

  removeMember(groupId: number, memberId: number): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}/owner/groups/${groupId}/members/${memberId}`);
  }

  approveMemberRequest(groupId: number, requestId: number): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/owner/groups/${groupId}/requests/${requestId}/approve`, {});
  }

  rejectMemberRequest(groupId: number, requestId: number): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/owner/groups/${groupId}/requests/${requestId}/reject`, {});
  }

  getOwnerExpenses(groupId: number = 1): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/owner/groups/${groupId}/expenses`).pipe(
      map(res => res.data || res)
    );
  }

  togglePayment(groupId: number, participantId: number, isPaid: boolean): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/owner/groups/${groupId}/expenses/toggle-paid`, {
      participant_id: participantId,
      is_paid: isPaid
    });
  }

  updateOwnerSettings(groupId: number, settings: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/owner/groups/${groupId}/settings`, settings);
  }

  // ==========================================
  // 5. Platform Admin Portal
  // ==========================================
  getAdminGroupRequests(status?: string): Observable<any[]> {
    let params = new HttpParams();
    if (status) params = params.set('status', status);
    return this.http.get<any>(`${this.apiUrl}/admin/group-creation-requests`, { params }).pipe(
      map(res => res.data || res)
    );
  }

  approveGroupRequest(requestId: number, notes?: string): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/admin/group-creation-requests/${requestId}/approve`, { notes });
  }

  rejectGroupRequest(requestId: number, reason: string): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/admin/group-creation-requests/${requestId}/reject`, { reason });
  }

  getAdminGroups(): Observable<any[]> {
    return this.http.get<any>(`${this.apiUrl}/admin/groups`).pipe(
      map(res => res.data || res)
    );
  }

  toggleGroupStatus(groupId: number): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/admin/groups/${groupId}/toggle-status`, {});
  }

  getAdminReports(): Observable<any[]> {
    return this.http.get<any>(`${this.apiUrl}/admin/reports`).pipe(
      map(res => res.data || res)
    );
  }

  getAdminSettings(): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/admin/settings`).pipe(
      map(res => res.data || res)
    );
  }

  updateAdminSettings(settings: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/admin/settings`, settings);
  }

  // ==========================================
  // 6. Member & Master Data
  // ==========================================
  getMyGroups(): Observable<any[]> {
    return this.http.get<any>(`${this.apiUrl}/member/my-groups`).pipe(
      map(res => res.data || res)
    );
  }

  joinWithCode(code: string): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/member/join-code`, { code });
  }

  getSportCategories(): Observable<SportCategory[]> {
    return this.http.get<any>(`${this.apiUrl}/sport-categories`).pipe(
      map(res => res.data || res)
    );
  }

  getSkillLevels(): Observable<BadmintonSkillLevel[]> {
    return this.http.get<any>(`${this.apiUrl}/badminton-skill-levels`).pipe(
      map(res => res.data || res)
    );
  }

  getProvinces(): Observable<any[]> {
    return this.http.get<any>(`${this.apiUrl}/provinces`).pipe(
      map(res => res.data || res)
    );
  }

  getDistricts(provinceId: number): Observable<any[]> {
    return this.http.get<any>(`${this.apiUrl}/provinces/${provinceId}/districts`).pipe(
      map(res => res.data || res)
    );
  }

  getVenues(): Observable<any[]> {
    return this.http.get<any>(`${this.apiUrl}/master/venues`).pipe(
      map(res => res.data || res)
    );
  }
}
