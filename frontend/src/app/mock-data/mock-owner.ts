/**
 * =============================================================================
 * Add Kuan (แอดก๊วน) - Mock Kwuan Owner Dataset
 * =============================================================================
 */

import {
  GroupMember,
  PendingMemberRequest,
  PlayerPayment,
  BadmintonSession,
  SessionPlayer
} from '../models';

export type {
  GroupMember,
  PendingMemberRequest,
  PlayerPayment,
  BadmintonSession,
  SessionPlayer
};

export const MOCK_OWNER_STATS = {
  totalMembers: 18,
  maxMembers: 40,
  upcomingSessions: 2,
  confirmedPlayers: 15,
  pendingJoinRequests: 2,
  uncollectedAmount: 2700
};

export const MOCK_OWNER_ACTIVE_SESSION = {
  title: 'นัดตีประจำสัปดาห์ (วันอังคาร)',
  date: 'อังคาร 29 ต.ค. 2568',
  time: '19:00 - 21:00 น. (2 ชม.)',
  courtNumbers: 'คอร์ต 3, 4',
  shuttlecockBrand: 'RSL Silver',
  registeredCount: 10,
  maxPlayers: 12,
  pricePerPerson: 180
};

export const MOCK_OWNER_PENDING_MEMBERS = [
  {
    id: 1,
    name: 'น้องออย แบดไว',
    lineId: 'oil_badminton',
    skillLevel: 'S (ปานกลาง)',
    appliedDate: '24 ต.ค. 2568',
    message: 'ตีประจำอยู่จตุจักร อยากมีก๊วนลงตีวันธรรมดาค่ะ'
  },
  {
    id: 2,
    name: 'บอล สแมชหนัก',
    lineId: 'ball_smash99',
    skillLevel: 'P (ฝีมือดี)',
    appliedDate: '23 ต.ค. 2568',
    message: 'มองหาก๊วนเกมไว วิ่งคอร์ตเหนียวครับ'
  }
];

export const MOCK_OWNER_EXPENSES_PLAYERS: PlayerPayment[] = [
  { id: 1, name: 'Coach Kitti (Host)', gamesPlayed: 4, shuttlesUsed: 2, isPaid: true, paidAt: '19:10 น.' },
  { id: 2, name: 'บอล สแมชหนัก', gamesPlayed: 4, shuttlesUsed: 3, isPaid: true, paidAt: '20:45 น.' },
  { id: 3, name: 'น้องออย แบดไว', gamesPlayed: 3, shuttlesUsed: 2, isPaid: false },
  { id: 4, name: 'ต้น หยอดเนียน', gamesPlayed: 4, shuttlesUsed: 2, isPaid: true, paidAt: '21:05 น.' },
  { id: 5, name: 'แนน สายซัพพอร์ต', gamesPlayed: 3, shuttlesUsed: 1, isPaid: true, paidAt: '20:50 น.' },
  { id: 6, name: 'ก้อง วิ่งสู้ฟัด', gamesPlayed: 3, shuttlesUsed: 2, isPaid: false },
  { id: 7, name: 'บอส แดนหลัง', gamesPlayed: 4, shuttlesUsed: 2, isPaid: true, paidAt: '21:00 น.' },
  { id: 8, name: 'เมย์ เซฟหลัง', gamesPlayed: 3, shuttlesUsed: 1, isPaid: true, paidAt: '20:55 น.' },
  { id: 9, name: 'ป๊อป ตบกระจาย', gamesPlayed: 4, shuttlesUsed: 3, isPaid: false },
  { id: 10, name: 'โจ๊ก ตีชิลล์', gamesPlayed: 3, shuttlesUsed: 1, isPaid: false }
];

export const MOCK_OWNER_MEMBERS: GroupMember[] = [
  {
    id: 1,
    name: 'Coach Kitti',
    lineId: 'coach_kitti',
    skillLevel: 'P+',
    role: 'HOST',
    joinDate: '1 ม.ค. 2568',
    sessionsCount: 48,
    status: 'ACTIVE'
  },
  {
    id: 2,
    name: 'บอล สแมชหนัก',
    lineId: 'ball_smash99',
    skillLevel: 'P',
    role: 'MEMBER',
    joinDate: '15 ก.พ. 2568',
    sessionsCount: 32,
    status: 'ACTIVE'
  },
  {
    id: 3,
    name: 'น้องออย แบดไว',
    lineId: 'oil_badminton',
    skillLevel: 'S',
    role: 'MEMBER',
    joinDate: '10 มี.ค. 2568',
    sessionsCount: 19,
    status: 'ACTIVE'
  },
  {
    id: 4,
    name: 'ต้น หยอดเนียน',
    lineId: 'ton_drop',
    skillLevel: 'S+',
    role: 'CO_HOST',
    joinDate: '5 ม.ค. 2568',
    sessionsCount: 42,
    status: 'ACTIVE'
  },
  {
    id: 5,
    name: 'แนน สายซัพพอร์ต',
    lineId: 'nan_support',
    skillLevel: 'S',
    role: 'MEMBER',
    joinDate: '2 พ.ค. 2568',
    sessionsCount: 14,
    status: 'ACTIVE'
  },
  {
    id: 6,
    name: 'ก้อง วิ่งสู้ฟัด',
    lineId: 'kong_runner',
    skillLevel: 'N+',
    role: 'MEMBER',
    joinDate: '1 มิ.ย. 2568',
    sessionsCount: 8,
    status: 'ACTIVE'
  },
  {
    id: 7,
    name: 'บอส แดนหลัง',
    lineId: 'boss_backcourt',
    skillLevel: 'P+',
    role: 'MEMBER',
    joinDate: '18 ก.ค. 2568',
    sessionsCount: 22,
    status: 'ACTIVE'
  }
];

export const MOCK_OWNER_MEMBER_REQUESTS: PendingMemberRequest[] = [
  {
    id: 101,
    name: 'พี่เอก กทม.',
    lineId: 'ek_bkk',
    skillLevel: 'P (ฝีมือดี)',
    appliedDate: '24 ต.ค. 2569',
    message: 'มองหาก๊วนเล่นประจำวันธรรมดาครับ ตีมา 4 ปีแล้ว'
  },
  {
    id: 102,
    name: 'มายด์ มือใหม่หัดหยอด',
    lineId: 'mild_bad',
    skillLevel: 'N (มือใหม่)',
    appliedDate: '23 ต.ค. 2569',
    message: 'เพิ่งเริ่มเล่นแบด อยากมีเพื่อนซ้อมและได้ออกกำลังกายค่ะ'
  }
];

export const MOCK_OWNER_SESSIONS: BadmintonSession[] = [
  {
    id: 101,
    title: 'นัดตีกระชับมิตรประจำสัปดาห์ (รอบวันอังคาร)',
    date: 'อังคาร 27 ต.ค. 2569',
    time: '19:00 - 21:00 น. (2 ชม.)',
    courtVenue: 'Winner Sport Club',
    courtNumbers: 'คอร์ต 3, 4 (พื้นยาง BWF)',
    shuttlecockBrand: 'RSL Silver (Speed 76)',
    maxPlayers: 12,
    registeredCount: 10,
    priceEstimate: 180,
    status: 'OPEN',
    players: [
      { name: 'Coach Kitti (เจ้าของก๊วน)', level: 'P+', isPaid: true },
      { name: 'บอล สแมชหนัก', level: 'P', isPaid: true },
      { name: 'น้องออย แบดไว', level: 'S', isPaid: false },
      { name: 'ต้น หยอดเนียน', level: 'S', isPaid: true },
      { name: 'ป๊อป ตบกระจาย', level: 'P', isPaid: false },
      { name: 'แนน สายซัพพอร์ต', level: 'S', isPaid: true },
      { name: 'ก้อง วิ่งสู้ฟัด', level: 'N+', isPaid: false },
      { name: 'บอส แดนหลัง', level: 'P+', isPaid: true },
      { name: 'เมย์ เซฟหลัง', level: 'S', isPaid: true },
      { name: 'โจ๊ก ตีชิลล์', level: 'N', isPaid: false }
    ]
  },
  {
    id: 102,
    title: 'นัดซ้อมสุดสัปดาห์ จัดคิวเต็มแมตช์ (รอบวันเสาร์)',
    date: 'เสาร์ 31 ต.ค. 2569',
    time: '14:00 - 17:00 น. (3 ชม.)',
    courtVenue: 'Tobacco Badminton Court คลองเตย',
    courtNumbers: 'คอร์ต 1, 2, 3',
    shuttlecockBrand: 'Yonex Aerosensa 20',
    maxPlayers: 16,
    registeredCount: 16,
    priceEstimate: 220,
    status: 'FULL',
    players: [
      { name: 'Coach Kitti', level: 'P+', isPaid: true },
      { name: 'แชมป์ สุขุมวิท', level: 'C', isPaid: true },
      { name: 'ดิว ขาแรง', level: 'P', isPaid: true }
    ]
  },
  {
    id: 99,
    title: 'รอบตีพิเศษ วอร์มอัปกลางสัปดาห์',
    date: 'พฤหัสบดี 22 ต.ค. 2569',
    time: '19:00 - 21:00 น.',
    courtVenue: 'Winner Sport Club',
    courtNumbers: 'คอร์ต 5',
    shuttlecockBrand: 'RSL Silver',
    maxPlayers: 8,
    registeredCount: 8,
    priceEstimate: 160,
    status: 'COMPLETED',
    players: []
  }
];

export const MOCK_OWNER_SETTINGS_GROUP = {
  name: 'ก๊วนแบดมินตัน จตุจักร สแมชเชอร์ (Chatuchak Smashers)',
  description: 'ก๊วนแบดมินตันเพื่อสุขภาพ มิตรภาพ และออกกำลังกาย ระดับฝีมือ S ถึง P+ ตีประจำทุกวันอังคาร 19:00 - 21:00 น. ณ Winner Sport Club ใช้ลูกแบดมินตันคุณภาพมาตรฐานแข่งขัน RSL Silver มีการจัดคู่มือการเล่นที่สูสีและเสมอภาค มีน้ำดื่มและที่จอดรถสะดวกสบาย',
  location: 'เขตจตุจักร กรุงเทพมหานคร',
  venueName: 'Winner Sport Club (คอร์ต 3, 4)',
  scheduleDays: 'ทุกวันอังคาร (19:00 - 21:00 น.)',
  walkInPolicy: 'ไม่รับ Walk-in',
  pointsPerSet: 21,
  shuttlecockBrand: 'RSL Silver Speed 76',
  rotationSystem: 'สลับคู่เสมอภาค (กระดานคิว)',
  maxCapacity: 40,
  promptPayId: '089-123-4567',
  promptPayName: 'นายกิตติ (Coach Kitti)',
  // Pricing Structure & Discounts
  feeModel: 'DIVIDED_EQUAL',
  courtEntryFee: '80',
  shuttlecockFee: '20',
  knockFee: '0',
  gameFee: '0',
  femaleDiscount: '20',
  beginnerDiscount: '20',
  waterSnackFee: '0',
  paymentMethods: 'PromptPay QR Code / เงินสดเมื่อจบคอร์ท',
  pricingNotes: 'ไม่มีค่าแรกเข้า ชำระตามที่เล่นจริงเมื่อจบรอบ'
};
