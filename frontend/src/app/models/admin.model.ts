/**
 * =============================================================================
 * Platform Admin & Moderation Models
 * =============================================================================
 */

export interface PlatformGroup {
  id: number;
  name: string;
  slug: string;
  ownerName: string;
  location: string;
  venueName: string;
  memberCount: number;
  sessionsCount: number;
  status: 'ACTIVE' | 'SUSPENDED' | 'PENDING';
  createdAt: string;
}

export interface UserReport {
  id: number;
  reporterName: string;
  targetName: string;
  targetType: 'USER' | 'GROUP';
  category: string;
  details: string;
  date: string;
  status: 'PENDING' | 'INVESTIGATING' | 'RESOLVED' | 'DISMISSED';
}
