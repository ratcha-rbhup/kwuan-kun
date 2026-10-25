/**
 * =============================================================================
 * Player User Profile Models
 * =============================================================================
 */

export interface PlayerProfile {
  displayName: string;
  firstName: string;
  lastName: string;
  phone: string;
  phoneVerified: boolean;
  avatar: string;
  isCustomAvatar: boolean;
  lineId: string;
  skillLevel: string;
  dominantHand: string;
  playStyle: string;
  preferredRacket: string;
  reliabilityScore: string;
  totalMatches: number;
  onTimeRate: string;
  notifyLine: boolean;
  notifyNewEvents: boolean;
}
