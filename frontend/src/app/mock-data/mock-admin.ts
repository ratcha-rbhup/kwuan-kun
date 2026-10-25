/**
 * =============================================================================
 * Add Kuan (แอดก๊วน) - Mock Platform Admin Dataset
 * =============================================================================
 */

import { PlatformGroup, UserReport } from '../models';

export type { PlatformGroup, UserReport };

export const MOCK_ADMIN_GROUPS: PlatformGroup[] = [
  {
    id: 1,
    name: 'ก๊วนแบดมินตัน จตุจักร สแมชเชอร์',
    slug: 'chatuchak-smashers',
    ownerName: 'Coach Kitti',
    location: 'เขตจตุจักร กรุงเทพฯ',
    venueName: 'Winner Sport Club',
    memberCount: 18,
    sessionsCount: 52,
    status: 'ACTIVE',
    createdAt: '1 ม.ค. 2568'
  },
  {
    id: 2,
    name: 'สารภี แบดมินตัน เฟรนด์ส (เชียงใหม่)',
    slug: 'saraphi-badminton-friends',
    ownerName: 'พี่หนุ่ม สารภี',
    location: 'อำเภอสารภี เชียงใหม่',
    venueName: 'สนามแบดมินตัน สารภี อารีน่า',
    memberCount: 24,
    sessionsCount: 38,
    status: 'ACTIVE',
    createdAt: '15 ก.พ. 2568'
  },
  {
    id: 3,
    name: 'Bangna Smash Club บางนา',
    slug: 'bangna-smash-club',
    ownerName: 'คุณวรุตม์',
    location: 'เขตบางนา กรุงเทพฯ',
    venueName: 'Bangna Arena Court',
    memberCount: 12,
    sessionsCount: 19,
    status: 'ACTIVE',
    createdAt: '10 มี.ค. 2568'
  },
  {
    id: 4,
    name: 'ก๊วนแบดมินตัน พระราม 3 สปิริต',
    slug: 'rama3-spirit',
    ownerName: 'คุณชัชชัย',
    location: 'เขตยานนาวา กรุงเทพฯ',
    venueName: 'คอร์ตแบดมินตัน สาธุประดิษฐ์',
    memberCount: 8,
    sessionsCount: 5,
    status: 'SUSPENDED',
    createdAt: '20 ส.ค. 2568'
  }
];

export const MOCK_ADMIN_REPORTS: UserReport[] = [
  {
    id: 201,
    reporterName: 'Coach Kitti',
    targetName: 'ผู้เล่นไม่ระบุนาม (LINE: noname_bad)',
    targetType: 'USER',
    category: 'ลงชื่อแล้วไม่มา & ไม่จ่ายค่าคอร์ต',
    details: 'ลงชื่อตีรอบวันอังคาร 2 ชม. แต่ไม่มาตามนัดและบล็อกไลน์ติดต่อไม่ได้',
    date: '24 ต.ค. 2569',
    status: 'PENDING'
  },
  {
    id: 202,
    reporterName: 'น้องออย แบดไว',
    targetName: 'ก๊วนแบดเถื่อนพระราม 2',
    targetType: 'GROUP',
    category: 'ข้อมูลสนามเป็นเท็จ',
    details: 'ระบุว่ามีคอร์ทปรับอากาศ แต่พอไปจริงเป็นคอร์ทพัดลมเก่าและเก็บเงินเกินจริง',
    date: '20 ต.ค. 2569',
    status: 'INVESTIGATING'
  },
  {
    id: 198,
    reporterName: 'บอล สแมชหนัก',
    targetName: 'user_troll_99',
    targetType: 'USER',
    category: 'พฤติกรรมก้าวร้าวในสนาม',
    details: 'ใช้ถ้อยคำหยาบคายและขว้างไม้ใส่ตาข่าย',
    date: '15 ต.ค. 2569',
    status: 'RESOLVED'
  }
];

export const MOCK_GROUP_REQUESTS = [
  {
    id: 1,
    proposedName: 'ก๊วนแบดมินตัน จตุจักร สแมชเชอร์',
    description: 'ก๊วนตีแบดมินตันเพื่อสุขภาพ มิตรภาพ และออกกำลังกาย ระดับฝีมือ S ถึง P+ ตีทุกวันอังคาร 19:00 - 21:00 น.',
    requesterName: 'Coach Kitti (LINE: U1289...)',
    location: 'เขตจตุจักร, กรุงเทพมหานคร',
    venue: 'Winner Sport Club (8 คอร์ต)',
    skillLevel: 'S, P-, P, P+',
    date: '24 ต.ค. 2026',
    status: 'PENDING'
  },
  {
    id: 2,
    proposedName: 'บางนา แบดมินตัน คลับ',
    description: 'ก๊วนคนทำงานย่านบางนา เน้นซ้อมเกมคู่ ตบหนัก วิ่งคอร์ตเหนียว',
    requesterName: 'Player Teerasak',
    location: 'เขตบางนา, กรุงเทพมหานคร',
    venue: 'Bangna Arena Court',
    skillLevel: 'P ถึง B',
    date: '24 ต.ค. 2026',
    status: 'PENDING'
  }
];

export const MOCK_ADMIN_SETTINGS = {
  platformName: 'KwuanKun (Badminton Community Platform)',
  lineChannelId: '2006789123',
  lineChannelSecret: '********************************',
  lineWebhookUrl: 'https://api.kwuankun.com/api/line/webhook',
  allowPublicRegistration: true,
  maintenanceMode: false,
  systemBannerEnabled: true,
  systemBannerMessage: 'ยินดีต้อนรับสู่ KwuanKun แพลตฟอร์มบริหารก๊วนแบดมินตันยุคใหม่ เชื่อมโยงสนามและผู้เล่นทั่วไทย',
  maxGroupsPerUser: 5,
  platformFeePercent: 0
};
