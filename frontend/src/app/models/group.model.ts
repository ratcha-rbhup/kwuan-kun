import { SportCategory, BadmintonSkillLevel } from './sport-category.model';

export type GroupRole = 'OWNER' | 'ADMIN' | 'MODERATOR' | 'MEMBER';
export type GroupMemberStatus = 'ACTIVE' | 'SUSPENDED' | 'LEFT' | 'REMOVED';
export type GroupJoinPolicy = 'OPEN' | 'APPROVAL_REQUIRED' | 'INVITE_ONLY' | 'CLOSED';

export interface BadmintonGroup {
  id: number;
  sport_category_id: number;
  sport_category?: SportCategory;
  name: string;
  slug: string;
  description: string;
  logo_url?: string;
  cover_url?: string;
  province_id: number;
  district_id: number;
  primary_venue_id?: number;
  min_skill_level_id?: number;
  min_skill_level?: BadmintonSkillLevel;
  max_skill_level_id?: number;
  max_skill_level?: BadmintonSkillLevel;
  join_policy: GroupJoinPolicy;
  max_members?: number;
  current_member_count: number;
  status: 'ACTIVE' | 'SUSPENDED' | 'CLOSED';
  created_by_user_id: number;
  created_at: string;
  updated_at: string;
}

export interface GroupMemberRecord {
  id: number;
  group_id: number;
  user_id: number;
  role: GroupRole;
  status: GroupMemberStatus;
  joined_at?: string;
  left_at?: string;
  display_name?: string;
  avatar_url?: string;
}

export type GroupMemberDb = GroupMemberRecord;

export interface GroupCreationRequestPayload {
  sport_category_id: number;
  proposed_name: string;
  proposed_description: string;
  province_id: number;
  district_id: number;
  primary_venue_id?: number;
  target_skill_levels?: string;
  join_policy: GroupJoinPolicy;
  supporting_info?: string;
}
