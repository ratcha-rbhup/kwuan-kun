/**
 * =============================================================================
 * User Joined & Owned Groups Models
 * =============================================================================
 */

export interface MyJoinedGroup {
  name: string;
  slug: string;
  venue: string;
  location: string;
  role: string;
  skillRange: string;
  nextSession: string;
  memberCount: number;
  attendanceRate: string;
}

export interface MyOwnedGroup {
  name: string;
  slug: string;
  venue: string;
  location: string;
  role: string;
  skillRange: string;
  nextSession: string;
  memberCount: number;
  pendingRequests: number;
}

export interface MyPendingRequest {
  groupName: string;
  venue: string;
  submittedDate: string;
  status: string;
  message: string;
}
