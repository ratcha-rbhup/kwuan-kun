import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable, of } from 'rxjs';
import { SportCategory, BadmintonSkillLevel } from '../../models/sport-category.model';
import { BadmintonGroup, GroupCreationRequestPayload } from '../../models/group.model';

@Injectable({
  providedIn: 'root'
})
export class ApiService {
  private http = inject(HttpClient);
  private apiUrl = '/api';

  // --- Sport Categories & Skills ---
  getSportCategories(): Observable<SportCategory[]> {
    return this.http.get<SportCategory[]>(`${this.apiUrl}/sport-categories`);
  }

  getSkillLevels(): Observable<BadmintonSkillLevel[]> {
    return this.http.get<BadmintonSkillLevel[]>(`${this.apiUrl}/badminton-skill-levels`);
  }

  // --- Geographic Hierarchy ---
  getProvinces(): Observable<any[]> {
    return this.http.get<any[]>(`${this.apiUrl}/provinces`);
  }

  getDistricts(provinceId: number): Observable<any[]> {
    return this.http.get<any[]>(`${this.apiUrl}/provinces/${provinceId}/districts`);
  }

  // --- Groups ---
  getGroups(filters?: { province_id?: number; district_id?: number; skill_level_id?: number; query?: string }): Observable<BadmintonGroup[]> {
    let params = new HttpParams();
    if (filters?.province_id) params = params.set('province_id', filters.province_id.toString());
    if (filters?.district_id) params = params.set('district_id', filters.district_id.toString());
    if (filters?.skill_level_id) params = params.set('skill_level_id', filters.skill_level_id.toString());
    if (filters?.query) params = params.set('query', filters.query);

    return this.http.get<BadmintonGroup[]>(`${this.apiUrl}/groups`, { params });
  }

  getGroupDetail(slug: string): Observable<BadmintonGroup> {
    return this.http.get<BadmintonGroup>(`${this.apiUrl}/groups/${slug}`);
  }

  createGroupRequest(payload: GroupCreationRequestPayload): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/group-creation-requests`, payload);
  }

  // --- Activities ---
  getActivities(groupId?: number): Observable<any[]> {
    let params = new HttpParams();
    if (groupId) params = params.set('group_id', groupId.toString());
    return this.http.get<any[]>(`${this.apiUrl}/activities`, { params });
  }

  getActivityDetail(id: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/activities/${id}`);
  }

  joinActivity(activityId: number, notes?: string): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/activities/${activityId}/join`, { notes });
  }

  // --- Admin Panel API ---
  getAdminGroupRequests(status?: string): Observable<any[]> {
    let params = new HttpParams();
    if (status) params = params.set('status', status);
    return this.http.get<any[]>(`${this.apiUrl}/admin/group-creation-requests`, { params });
  }

  approveGroupRequest(requestId: number, notes?: string): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/admin/group-creation-requests/${requestId}/approve`, { notes });
  }

  rejectGroupRequest(requestId: number, reason: string): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/admin/group-creation-requests/${requestId}/reject`, { reason });
  }
}
