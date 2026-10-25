/**
 * =============================================================================
 * Add Kuan (แอดก๊วน) - Mock User's Joined & Owned Groups Dataset
 * =============================================================================
 */

import { MyJoinedGroup, MyOwnedGroup, MyPendingRequest } from '../models';

export type { MyJoinedGroup, MyOwnedGroup, MyPendingRequest };

export const MOCK_MY_JOINED_GROUPS: MyJoinedGroup[] = [
  {
    name: 'ก๊วนแบดมินตัน จตุจักร สแมชเชอร์',
    slug: 'chatuchak-smashers',
    venue: 'Winner Sport Club (คอร์ต 4-6)',
    location: 'เขตจตุจักร, กรุงเทพมหานคร',
    role: 'MEMBER',
    skillRange: 'ระดับ S ถึง P+',
    nextSession: 'อังคารนี้ 19:00 - 21:00 น.',
    memberCount: 18,
    attendanceRate: '100% (ไม่เคยเทก๊วน)'
  },
  {
    name: 'บางนา แบดมินตัน คลับ',
    slug: 'bangna-badminton-club',
    venue: 'Bangna Arena Court',
    location: 'เขตบางนา, กรุงเทพมหานคร',
    role: 'ADMIN',
    skillRange: 'ระดับ P ถึง B',
    nextSession: 'เสาร์ 09:00 - 12:00 น.',
    memberCount: 24,
    attendanceRate: '96%'
  }
];

export const MOCK_MY_OWNED_GROUPS: MyOwnedGroup[] = [
  {
    name: 'เมืองนนท์ สปอร์ต มิตรภาพ',
    slug: 'muang-non-badminton',
    venue: 'Nonthaburi Court 88 (คอร์ต 2)',
    location: 'อำเภอเมืองนนทบุรี, นนทบุรี',
    role: 'OWNER',
    skillRange: 'ระดับ N ถึง S-',
    nextSession: 'พฤหัสบดี 20:00 - 22:00 น.',
    memberCount: 15,
    pendingRequests: 2
  }
];

export const MOCK_MY_PENDING_REQUESTS: MyPendingRequest[] = [
  {
    groupName: 'สาทร แบดมินตัน สปิริต',
    venue: 'Sathorn Arena Court',
    submittedDate: '24 ต.ค. 2568',
    status: 'PENDING_APPROVAL',
    message: 'รอการตอบรับจากเจ้าของก๊วน'
  }
];
