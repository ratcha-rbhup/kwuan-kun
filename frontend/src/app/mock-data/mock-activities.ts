/**
 * =============================================================================
 * Add Kuan (แอดก๊วน) - Mock Activities & Live Sessions Dataset
 * =============================================================================
 */

import { ActivityItem, ActivityPlayer } from '../models';

export type { ActivityItem, ActivityPlayer };

export const MOCK_ACTIVITIES: ActivityItem[] = [
  {
    id: 101,
    title: 'นัดตีแบดมินตัน ประจำวันอังคาร (เน้นเกมสนุก ออกกำลังกาย)',
    groupName: 'จตุจักร สแมชเชอร์',
    groupSlug: 'chatuchak-smashers',
    venue: 'Winner Sport Club จตุจักร',
    courtNumbers: 'คอร์ต 3, 4',
    date: 'อังคาร 28 ต.ค. 2568',
    time: '19:00 - 21:00 น. (2 ชม.)',
    skillLevel: 'ระดับ S ถึง P+',
    priceEst: '150 - 180 บาท/คน (แชร์ตามจริงรวมลูกแบด RSL)',
    maxPlayers: 12,
    confirmedPlayers: 10,
    isJoined: false,
    players: [
      { name: 'Coach Kitti', role: 'OWNER', skill: 'P+' },
      { name: 'P\'Arm', role: 'ADMIN', skill: 'P' },
      { name: 'Nong Som', role: 'MEMBER', skill: 'S' },
      { name: 'Bank Smash', role: 'MEMBER', skill: 'S+' }
    ],
    facilities: ['ที่จอดรถ', 'ห้องอาบน้ำ', 'แอร์เย็น', 'ลูกแบด RSL Silver']
  },
  {
    id: 102,
    title: 'เสาร์เช้า เกมเร็ว ตบหนัก วิ่งเหนียว',
    groupName: 'บางนา แบดมินตัน คลับ',
    groupSlug: 'bangna-badminton-club',
    venue: 'Bangna Arena Court',
    courtNumbers: 'คอร์ต 1, 2',
    date: 'เสาร์ 1 พ.ย. 2568',
    time: '09:00 - 12:00 น. (3 ชม.)',
    skillLevel: 'ระดับ P ถึง B',
    priceEst: '200 บาท/คน',
    maxPlayers: 10,
    confirmedPlayers: 7,
    isJoined: true,
    players: [
      { name: 'Captain Bird', role: 'OWNER', skill: 'B' },
      { name: 'คุณ', role: 'MEMBER', skill: 'P' },
      { name: 'Golf Power', role: 'MEMBER', skill: 'P+' }
    ],
    facilities: ['ที่จอดรถ', 'ห้องอาบน้ำ', 'ร้านเครื่องดื่ม']
  },
  {
    id: 103,
    title: 'แบดมินตันมือใหม่ & ตีชิลหลังเลิกงาน มีสอนกริปฟรี',
    groupName: 'เมืองนนท์ สปอร์ต มิตรภาพ',
    groupSlug: 'muang-non-badminton',
    venue: 'Nonthaburi Court 88',
    courtNumbers: 'คอร์ต 2',
    date: 'พฤหัสบดี 30 ต.ค. 2568',
    time: '20:00 - 22:00 น. (2 ชม.)',
    skillLevel: 'ระดับ N ถึง S-',
    priceEst: '120 บาท/คน',
    maxPlayers: 8,
    confirmedPlayers: 5,
    isJoined: false,
    players: [
      { name: 'P\'Non', role: 'OWNER', skill: 'S' },
      { name: 'น้องเจน', role: 'MEMBER', skill: 'N' }
    ],
    facilities: ['ที่จอดรถ', 'ห้องน้ำสะอาด', 'โค้ชแนะแนว']
  }
];

export const MOCK_UPCOMING_SESSIONS = [
  {
    id: 1,
    title: '[ก๊วน] มาตีแบดกัน ชวนออกกำลังกาย',
    groupName: 'จตุจักร สแมชเชอร์',
    groupSlug: 'chatuchak-smashers',
    date: 'พุธ 23 ต.ค. 2568',
    dateBadge: 'วันพุธนี้',
    time: '19:00 - 21:00 น. (2 ชม.)',
    venue: 'Winner Sport Club (คอร์ต 4)',
    distance: '3.2 กม.',
    image: 'assets/image/badminton-court-cover.jpg',
    groupLogo: 'assets/image/badminton-club-logo.jpg',
    slotsAvailable: 3,
    totalSlots: 8,
    skillLevel: 'ระดับ S ถึง P+',
    price: '150 บาท/คน (รวมลูกแบด)',
    facilities: [
      { name: 'ที่จอดรถ', icon: 'fa-solid fa-square-parking' },
      { name: 'ห้องอาบน้ำ', icon: 'fa-solid fa-shower' },
      { name: 'ห้องน้ำ', icon: 'fa-solid fa-restroom' },
      { name: 'ติดแอร์', icon: 'fa-solid fa-snowflake' },
      { name: 'ลูกแบด RSL', icon: 'fa-solid fa-shuttlecock' }
    ]
  },
  {
    id: 2,
    title: '[ก๊วน] บางนา แบดมินตัน วิ่งคอร์ตเหนียว',
    groupName: 'บางนา แบดมินตัน คลับ',
    groupSlug: 'bangna-badminton-club',
    date: 'เสาร์ 26 ต.ค. 2568',
    dateBadge: 'เสาร์นี้',
    time: '09:00 - 12:00 น. (3 ชม.)',
    venue: 'Bangna Arena Court (คอร์ต 1-2)',
    distance: '5.8 กม.',
    image: 'assets/image/tobacco-court.jpg',
    groupLogo: 'assets/image/duck-mascot.jpg',
    slotsAvailable: 2,
    totalSlots: 6,
    skillLevel: 'ระดับ P ถึง B',
    price: '180 บาท/คน',
    facilities: [
      { name: 'ที่จอดรถ', icon: 'fa-solid fa-square-parking' },
      { name: 'ห้องอาบน้ำ', icon: 'fa-solid fa-shower' },
      { name: 'ร้านเครื่องดื่ม', icon: 'fa-solid fa-mug-hot' }
    ]
  },
  {
    id: 3,
    title: '[ก๊วน] แบดมินตันมือใหม่ & ชิลหลังเลิกงาน',
    groupName: 'เมืองนนท์ สปอร์ต มิตรภาพ',
    groupSlug: 'muang-non-badminton',
    date: 'พฤหัส 24 ต.ค. 2568',
    dateBadge: 'พฤหัสนี้',
    time: '20:00 - 22:00 น. (2 ชม.)',
    venue: 'Nonthaburi Court 88 (คอร์ต 2)',
    distance: '7.5 กม.',
    image: 'assets/image/badminton-court-cover.jpg',
    groupLogo: 'assets/image/add-kwuan-app-icon.svg',
    slotsAvailable: 4,
    totalSlots: 8,
    skillLevel: 'ระดับ N ถึง S-',
    price: '120 บาท/คน',
    facilities: [
      { name: 'ที่จอดรถ', icon: 'fa-solid fa-square-parking' },
      { name: 'ห้องน้ำสะอาด', icon: 'fa-solid fa-restroom' },
      { name: 'มีโค้ชแนะแนว', icon: 'fa-solid fa-user-graduate' }
    ]
  }
];
