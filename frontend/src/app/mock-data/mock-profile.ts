/**
 * =============================================================================
 * Add Kuan (แอดก๊วน) - Mock Player User Profile Dataset
 * =============================================================================
 */

import { PlayerProfile } from '../models';

export type { PlayerProfile };

export const MOCK_PLAYER_PROFILE: PlayerProfile = {
  displayName: 'Coach Kitti',
  firstName: 'กิตติ',
  lastName: 'ชัยมงคล',
  phone: '081-234-5678',
  phoneVerified: true,
  avatar: 'assets/image/player-avatar.jpg',
  isCustomAvatar: false,
  lineId: 'line_kitti_badminton',
  skillLevel: 'P+ (ชำนาญสูง/แข่งประจำ)',
  dominantHand: 'ขวา (Right-handed)',
  playStyle: 'เกมบุกตบหนัก & บัญชาการแดนหน้า',
  preferredRacket: 'Yonex Astrox 88D Pro',
  reliabilityScore: '98%',
  totalMatches: 42,
  onTimeRate: '100%',
  notifyLine: true,
  notifyNewEvents: true
};
