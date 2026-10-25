/**
 * =============================================================================
 * Add Kuan (แอดก๊วน) - Mock Parties & Group Discovery Dataset
 * =============================================================================
 */

import { TargetSkill, PartyCardItem } from '../models';

export type { TargetSkill, PartyCardItem };

export const MOCK_PROVINCES: string[] = [
  'กรุงเทพมหานคร',
  'นนทบุรี',
  'สมุทรปราการ',
  'ปทุมธานี',
  'เชียงใหม่'
];

export const MOCK_PROVINCE_DISTRICTS: Record<string, string[]> = {
  'กรุงเทพมหานคร': [
    'เขตคลองเตย',
    'เขตคลองสาน',
    'เขตบางแค',
    'เขตบางเขน',
    'เขตจตุจักร',
    'เขตบางนา',
    'เขตราชเทวี',
    'เขตห้วยขวาง',
    'เขตวัฒนา'
  ],
  'นนทบุรี': [
    'อำเภอเมืองนนทบุรี',
    'อำเภอปากเกร็ด',
    'อำเภอบางกรวย',
    'อำเภอบางใหญ่'
  ],
  'สมุทรปราการ': [
    'อำเภอบางพลี',
    'อำเภอเมืองสมุทรปราการ',
    'อำเภอพระประแดง'
  ],
  'ปทุมธานี': [
    'อำเภอเมืองปทุมธานี',
    'อำเภอคลองหลวง',
    'อำเภอธัญบุรี'
  ],
  'เชียงใหม่': [
    'อำเภอเมืองเชียงใหม่',
    'อำเภอสารภี',
    'อำเภอสันทราย',
    'อำเภอหางดง'
  ]
};

export const MOCK_DATE_STRIP = [
  { day: 'ทั้งหมด', date: 'ทุกวัน', isoDate: '' },
  { day: 'วันนี้', date: '25 ต.ค.', isoDate: '2026-10-25' },
  { day: 'จันทร์', date: '26 ต.ค.', isoDate: '2026-10-26' },
  { day: 'อังคาร', date: '27 ต.ค.', isoDate: '2026-10-27' },
  { day: 'พุธ', date: '28 ต.ค.', isoDate: '2026-10-28' },
  { day: 'พฤหัส', date: '29 ต.ค.', isoDate: '2026-10-29' },
  { day: 'ศุกร์', date: '30 ต.ค.', isoDate: '2026-10-30' },
  { day: 'เสาร์', date: '31 ต.ค.', isoDate: '2026-10-31' },
  { day: 'อาทิตย์', date: '1 พ.ย.', isoDate: '2026-11-01' },
  { day: 'จันทร์', date: '2 พ.ย.', isoDate: '2026-11-02' },
  { day: 'อังคาร', date: '3 พ.ย.', isoDate: '2026-11-03' },
  { day: 'พุธ', date: '4 พ.ย.', isoDate: '2026-11-04' },
  { day: 'พฤหัส', date: '5 พ.ย.', isoDate: '2026-11-05' },
  { day: 'ศุกร์', date: '6 พ.ย.', isoDate: '2026-11-06' },
  { day: 'เสาร์', date: '7 พ.ย.', isoDate: '2026-11-07' }
];

export const MOCK_PARTIES: PartyCardItem[] = [
  {
    id: 1,
    slug: 'saraphi-badminton-friends',
    type: 'FIND_PLAYERS',
    typeLabel: 'หาเพื่อนตี',
    title: 'หาเพื่อนตีแบดครับ (สารภี คลองสาน)',
    subtitle: 'มาได้ป่าว นัดตีรอบดึก บรรยากาศสบายๆ ลูกสปีด 76',
    province: 'กรุงเทพมหานคร',
    district: 'เขตคลองสาน',
    venueName: 'สนามแบดสารภี20 (เอกตรา)',
    venueAddress: 'ซอยสารภี 20 แขวงคลองสาน เขตคลองสาน กรุงเทพมหานคร',
    lat: 13.7280,
    lng: 100.4990,
    distanceKm: 2.8,
    bannerImage: 'assets/image/tobacco-court.jpg',
    isoDate: '2026-10-25',
    dateBadge: 'วันนี้',
    dateTimeDisplay: 'วันอาทิตย์ที่ 25 ต.ค. 2569 02:20 (4 ชั่วโมง)',
    targetSkills: [
      { label: 'หน้าบ้าน', cssClass: 'skill-sky' },
      { label: 'BG', cssClass: 'skill-green' },
      { label: 'N', cssClass: 'skill-amber' }
    ],
    hostName: 'BIT28',
    hostAvatar: 'assets/image/player-avatar.jpg',
    hostRole: 'ผู้จัด',
    currentPlayers: 1,
    maxPlayers: 4,
    isFull: false,
    status: 'OPEN'
  },
  {
    id: 2,
    slug: 'midweek-private-class',
    type: 'KWUAN',
    typeLabel: 'ก๊วน',
    title: 'ก๊วนMidweek (Private Class)',
    subtitle: 'สอนแบดไพรเวท วางคอร์ท ซ้อมหน้าเน็ตและแดนหลัง',
    province: 'กรุงเทพมหานคร',
    district: 'เขตบางแค',
    venueName: 'สนามแบดมินตัน สแปน Spann Badminton',
    venueAddress: 'ถนนพุทธมณฑลสาย 2 แขวงบางไผ่ เขตบางแค กรุงเทพมหานคร',
    lat: 13.7310,
    lng: 100.3890,
    distanceKm: 14.5,
    bannerImage: 'assets/image/badminton-court-cover.jpg',
    isoDate: '2026-10-25',
    dateBadge: 'วันนี้',
    dateTimeDisplay: 'วันอาทิตย์ที่ 25 ต.ค. 2569 09:00 (1 ชั่วโมง)',
    targetSkills: [
      { label: 'N', cssClass: 'skill-amber' },
      { label: 'S', cssClass: 'skill-purple' }
    ],
    hostName: 'M.Mo',
    hostAvatar: 'assets/image/player-avatar.jpg',
    hostRole: 'ผู้จัด',
    currentPlayers: 4,
    maxPlayers: 4,
    isFull: true,
    status: 'FULL'
  },
  {
    id: 3,
    slug: 'tier1-badminton-equal-share',
    type: 'KWUAN',
    typeLabel: 'ก๊วน',
    title: 'กลุ่มแบดหารเท่ากัน (TIER1 รามอินทรา)',
    subtitle: 'หาเพื่อนใหม่ ออกกำลังกาย ลูก RSL Silver หมุนเวียนคู่สูสี',
    province: 'กรุงเทพมหานคร',
    district: 'เขตบางเขน',
    venueName: 'TIER1 badminton court',
    venueAddress: 'ซอยรามอินทรา 34 แขวงท่าแร้ง เขตบางเขน กรุงเทพมหานคร',
    lat: 13.8620,
    lng: 100.6410,
    distanceKm: 14.1,
    bannerImage: 'assets/image/duck-mascot.jpg',
    isoDate: '2026-10-25',
    dateBadge: 'วันนี้',
    dateTimeDisplay: 'วันอาทิตย์ที่ 25 ต.ค. 2569 09:00 (3 ชั่วโมง)',
    targetSkills: [
      { label: 'S-', cssClass: 'skill-sky' },
      { label: 'S', cssClass: 'skill-purple' },
      { label: 'P', cssClass: 'skill-rose' }
    ],
    hostName: 'Tao Thitiwit ...',
    hostAvatar: 'assets/image/duck-mascot.jpg',
    hostRole: 'ผู้จัด',
    currentPlayers: 7,
    maxPlayers: 10,
    isFull: false,
    status: 'OPEN'
  },
  {
    id: 4,
    slug: 'chatuchak-smashers',
    type: 'KWUAN',
    typeLabel: 'ก๊วน',
    title: 'ก๊วนแบดมินตัน จตุจักร สแมชเชอร์ (Chatuchak Smashers)',
    subtitle: 'นัดตีสนุกสนาน ลูก RSL Silver ลุยคู่สลับมือ มิตรภาพอบอุ่น',
    province: 'กรุงเทพมหานคร',
    district: 'เขตคลองเตย',
    venueName: 'Tobacco Badminton Court สนามแบดมินตันโรงงานยาสูบ',
    venueAddress: 'ถ.พระรามที่ 4 แขวงคลองเตย เขตคลองเตย กรุงเทพมหานคร',
    lat: 13.7225,
    lng: 100.5574,
    distanceKm: 2.5,
    bannerImage: 'assets/image/tobacco-court.jpg',
    isoDate: '2026-10-26',
    dateBadge: 'จันทร์ 26 ต.ค.',
    dateTimeDisplay: 'วันจันทร์ที่ 26 ต.ค. 2569 19:00 - 21:00 น.',
    targetSkills: [
      { label: 'S', cssClass: 'skill-purple' },
      { label: 'P', cssClass: 'skill-rose' },
      { label: 'P+', cssClass: 'skill-green' }
    ],
    hostName: 'Coach Kitti',
    hostAvatar: 'assets/image/player-avatar.jpg',
    hostRole: 'ผู้จัด',
    currentPlayers: 9,
    maxPlayers: 17,
    isFull: false,
    status: 'OPEN'
  },
  {
    id: 5,
    slug: 'winner-chatuchak-tuesday',
    type: 'KWUAN',
    typeLabel: 'ก๊วน',
    title: 'นัดตีกระชับมิตร วินเนอร์ สปอร์ต คลับ จตุจักร',
    subtitle: 'คอร์ตปรับอากาศ พื้นยาง BWF ดิวซ์ 30 แต้ม เล่นกันเต็มอิ่ม 2 ชั่วโมง',
    province: 'กรุงเทพมหานคร',
    district: 'เขตจตุจักร',
    venueName: 'สนามแบดมินตัน วินเนอร์ สปอร์ต คลับ (Winner Sport Club)',
    venueAddress: '123/45 ซอยพหลโยธิน 32 แขวงจตุจักร เขตจตุจักร กรุงเทพมหานคร',
    lat: 13.8315,
    lng: 100.5750,
    distanceKm: 8.2,
    bannerImage: 'assets/image/badminton-court-cover.jpg',
    isoDate: '2026-10-27',
    dateBadge: 'อังคาร 27 ต.ค.',
    dateTimeDisplay: 'วันอังคารที่ 27 ต.ค. 2569 19:00 - 21:00 น.',
    targetSkills: [
      { label: 'S', cssClass: 'skill-purple' },
      { label: 'P', cssClass: 'skill-rose' },
      { label: 'P+', cssClass: 'skill-green' }
    ],
    hostName: 'Coach Kitti',
    hostAvatar: 'assets/image/player-avatar.jpg',
    hostRole: 'ผู้จัด',
    currentPlayers: 10,
    maxPlayers: 12,
    isFull: false,
    status: 'OPEN'
  },
  {
    id: 6,
    slug: 'bangna-badminton-club',
    type: 'KWUAN',
    typeLabel: 'ก๊วน',
    title: 'บางนา แบดมินตัน คลับ (Bangna Arena)',
    subtitle: 'วิ่งคอร์ตเหนียว ตบหนัก มีน้ำดื่มเย็นบริการ สมาชิกเป็นกันเอง',
    province: 'กรุงเทพมหานคร',
    district: 'เขตบางนา',
    venueName: 'สนามแบดมินตัน Bangna Arena Court',
    venueAddress: '88 หมู่ 12 ถ.บางนา-ตราด แขวงบางนา เขตบางนา กรุงเทพมหานคร',
    lat: 13.6685,
    lng: 100.6280,
    distanceKm: 12.8,
    bannerImage: 'assets/image/tobacco-court.jpg',
    isoDate: '2026-10-28',
    dateBadge: 'พุธ 28 ต.ค.',
    dateTimeDisplay: 'วันพุธที่ 28 ต.ค. 2569 19:00 - 21:00 น.',
    targetSkills: [
      { label: 'P', cssClass: 'skill-rose' },
      { label: 'B', cssClass: 'skill-purple' }
    ],
    hostName: 'Captain Bird',
    hostAvatar: 'assets/image/player-avatar.jpg',
    hostRole: 'ผู้จัด',
    currentPlayers: 8,
    maxPlayers: 10,
    isFull: false,
    status: 'OPEN'
  },
  {
    id: 7,
    slug: 'muang-non-friendly',
    type: 'KWUAN',
    typeLabel: 'ก๊วน',
    title: 'เมืองนนท์ สปอร์ต มิตรภาพ (Nonthaburi Court 88)',
    subtitle: 'ก๊วนมือใหม่ ชิลล์ๆ ไม่ตึง มีสอนการจับกริปและก้าวเท้าฟรี',
    province: 'นนทบุรี',
    district: 'อำเภอเมืองนนทบุรี',
    venueName: 'สนามแบดมินตัน นนทบุรี คอร์ต 88',
    venueAddress: 'ถนนติวานนท์ ตำบลตลาดขวัญ อำเภอเมืองนนทบุรี นนทบุรี',
    lat: 13.8540,
    lng: 100.5120,
    distanceKm: 15.6,
    bannerImage: 'assets/image/badminton-court-cover.jpg',
    isoDate: '2026-10-29',
    dateBadge: 'พฤหัส 29 ต.ค.',
    dateTimeDisplay: 'วันพฤหัสบดีที่ 29 ต.ค. 2569 20:00 - 22:00 น.',
    targetSkills: [
      { label: 'N', cssClass: 'skill-amber' },
      { label: 'S-', cssClass: 'skill-sky' }
    ],
    hostName: 'P\'Non',
    hostAvatar: 'assets/image/duck-mascot.jpg',
    hostRole: 'ผู้จัด',
    currentPlayers: 6,
    maxPlayers: 8,
    isFull: false,
    status: 'OPEN'
  },
  {
    id: 8,
    slug: 'saraphi-arena-chiangmai',
    type: 'KWUAN',
    typeLabel: 'ก๊วน',
    title: 'สารภี แบดมินตัน เฟรนด์ส (เชียงใหม่)',
    subtitle: 'ก๊วนคนรักแบดโซนสารภี เชียงใหม่ เล่นสนุก เหงื่อท่วมทุกสุดสัปดาห์',
    province: 'เชียงใหม่',
    district: 'อำเภอสารภี',
    venueName: 'สนามแบดมินตัน สารภี อารีน่า',
    venueAddress: 'ถนนเชียงใหม่-ลำพูน ตำบลหนองผึ้ง อำเภอสารภี เชียงใหม่',
    lat: 18.7120,
    lng: 99.0340,
    distanceKm: 580.0,
    bannerImage: 'assets/image/tobacco-court.jpg',
    isoDate: '2026-10-31',
    dateBadge: 'เสาร์ 31 ต.ค.',
    dateTimeDisplay: 'วันเสาร์ที่ 31 ต.ค. 2569 16:00 - 19:00 น.',
    targetSkills: [
      { label: 'N', cssClass: 'skill-amber' },
      { label: 'S', cssClass: 'skill-purple' }
    ],
    hostName: 'พี่หนุ่ม สารภี',
    hostAvatar: 'assets/image/player-avatar.jpg',
    hostRole: 'ผู้จัด',
    currentPlayers: 12,
    maxPlayers: 16,
    isFull: false,
    status: 'OPEN'
  },
  {
    id: 9,
    slug: 'pathumthani-smash-saturdays',
    type: 'FIND_PLAYERS',
    typeLabel: 'หาเพื่อนตี',
    title: 'หาเพื่อนตีแบด เสาร์บ่าย รังสิต-คลองหลวง',
    subtitle: 'ขาดอีก 2 คนลงครบ 2 คอร์ต เล่นชิลล์ๆ ไม่ตึง มีลูกแบดพร้อม',
    province: 'ปทุมธานี',
    district: 'อำเภอคลองหลวง',
    venueName: 'สนามแบดมินตัน รังสิต สปอร์ต คลับ',
    venueAddress: 'ถนนพหลโยธิน คลองหนึ่ง อำเภอคลองหลวง ปทุมธานี',
    lat: 14.0620,
    lng: 100.6120,
    distanceKm: 34.2,
    bannerImage: 'assets/image/badminton-court-cover.jpg',
    isoDate: '2026-10-31',
    dateBadge: 'เสาร์ 31 ต.ค.',
    dateTimeDisplay: 'วันเสาร์ที่ 31 ต.ค. 2569 14:00 - 17:00 น.',
    targetSkills: [
      { label: 'BG', cssClass: 'skill-green' },
      { label: 'N', cssClass: 'skill-amber' }
    ],
    hostName: 'แบดมินตัน คลองหลวง',
    hostAvatar: 'assets/image/duck-mascot.jpg',
    hostRole: 'ผู้จัด',
    currentPlayers: 6,
    maxPlayers: 8,
    isFull: false,
    status: 'OPEN'
  },
  {
    id: 10,
    slug: 'scg-academy-sathorn',
    type: 'KWUAN',
    typeLabel: 'ก๊วน',
    title: 'ก๊วนสาทร-สีลม (SCG Academy)',
    subtitle: 'คอร์ตมาตรฐาน สปีดลูกไว เหมาะกับคนทำงานหลังเลิกงาน',
    province: 'กรุงเทพมหานคร',
    district: 'เขตราชเทวี',
    venueName: 'สนามแบดมินตัน SCG Academy',
    venueAddress: 'ถนนนางลิ้นจี่ แขวงทุ่งมหาเมฆ เขตสาทร กรุงเทพมหานคร',
    lat: 13.8820,
    lng: 100.4990,
    distanceKm: 16.5,
    bannerImage: 'assets/image/tobacco-court.jpg',
    isoDate: '2026-11-01',
    dateBadge: 'อาทิตย์ 1 พ.ย.',
    dateTimeDisplay: 'วันอาทิตย์ที่ 1 พ.ย. 2569 10:00 - 13:00 น.',
    targetSkills: [
      { label: 'S', cssClass: 'skill-purple' },
      { label: 'P', cssClass: 'skill-rose' },
      { label: 'B', cssClass: 'skill-purple' }
    ],
    hostName: 'Coach Oat',
    hostAvatar: 'assets/image/player-avatar.jpg',
    hostRole: 'ผู้จัด',
    currentPlayers: 10,
    maxPlayers: 14,
    isFull: false,
    status: 'OPEN'
  }
];
