/**
 * =============================================================================
 * Add Kuan (แอดก๊วน) - Mock Invite Codes Dataset
 * =============================================================================
 */

import { InviteCodeItem } from '../models';

export type { InviteCodeItem };

export const MOCK_INVITE_CODES: Record<string, InviteCodeItem> = {
  'SMASH88': {
    name: 'ก๊วนแบดมินตัน จตุจักร สแมชเชอร์',
    slug: 'chatuchak-smashers',
    venue: 'Winner Sport Club จตุจักร',
    location: 'เขตจตุจักร, กรุงเทพมหานคร',
    owner: 'Coach Kitti',
    skillLevel: 'S ถึง P+',
    memberCount: 18,
    joinPolicy: 'เข้าร่วมได้ทันทีผ่านรหัสคำเชิญ'
  },
  'BANGNA24': {
    name: 'บางนา แบดมินตัน คลับ',
    slug: 'bangna-badminton-club',
    venue: 'Bangna Arena Court',
    location: 'เขตบางนา, กรุงเทพมหานคร',
    owner: 'Captain Bird',
    skillLevel: 'P ถึง B',
    memberCount: 24,
    joinPolicy: 'เข้าร่วมได้ทันทีผ่านรหัสคำเชิญ'
  }
};
