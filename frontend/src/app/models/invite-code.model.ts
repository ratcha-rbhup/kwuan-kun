/**
 * =============================================================================
 * Group Invite Codes Models
 * =============================================================================
 */

export interface InviteCodeItem {
  name: string;
  slug: string;
  venue: string;
  location: string;
  owner: string;
  skillLevel: string;
  memberCount: number;
  joinPolicy: string;
}
