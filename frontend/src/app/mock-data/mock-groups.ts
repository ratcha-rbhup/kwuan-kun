/**
 * =============================================================================
 * Add Kuan (แอดก๊วน) - Mock Groups, Venue Presets & Detail Dataset
 * =============================================================================
 */

import { VenuePreset, BadmintonCourtVenue } from '../models';

export type { VenuePreset, BadmintonCourtVenue };

export const MOCK_VENUE_PRESETS: Record<string, VenuePreset> = {
  '1': {
    id: '1',
    name: 'สนามแบดมินตัน วินเนอร์ สปอร์ต คลับ (Winner Sport Club) จตุจักร',
    lat: 13.8315,
    lng: 100.5750,
    address: '123/45 ซอยพหลโยธิน 32 แขวงจตุจักร เขตจตุจักร กรุงเทพมหานคร 10900',
    province: '1',
    district: '1'
  },
  '2': {
    id: '2',
    name: 'สนามแบดมินตัน Bangna Arena Court',
    lat: 13.6685,
    lng: 100.6280,
    address: '88 หมู่ 12 ถ.บางนา-ตราด แขวงบางนา เขตบางนา กรุงเทพมหานคร 10260',
    province: '1',
    district: '2'
  },
  '3': {
    id: '3',
    name: 'สนามแบดมินตัน SCG Academy',
    lat: 13.8820,
    lng: 100.4990,
    address: 'ถนนนางลิ้นจี่ แขวงทุ่งมหาเมฆ เขตสาทร กรุงเทพมหานคร 10120',
    province: '1',
    district: '3'
  },
  '4': {
    id: '4',
    name: 'Tobacco Badminton Court สนามแบดมินตันโรงงานยาสูบ',
    lat: 13.7225,
    lng: 100.5574,
    address: 'ถ.พระรามที่ 4 แขวงคลองเตย เขตคลองเตย กรุงเทพมหานคร 10110',
    province: '1',
    district: '1'
  }
};

export const MOCK_DEFAULT_GROUP_FORM = {
  sportCategory: 'badminton',
  proposedName: '',
  proposedDescription: '',
  profileImage: '',
  coverImage: '',
  province: '1',
  district: '1',
  venue: '1',
  venueNameCustom: 'สนามแบดมินตัน วินเนอร์ สปอร์ต คลับ (Winner Sport Club)',
  courtLat: 13.8315,
  courtLng: 100.5750,
  courtAddress: '123/45 ซอยพหลโยธิน 32 แขวงจตุจักร เขตจตุจักร กรุงเทพมหานคร 10900',
  courtMapUrl: 'https://www.google.com/maps/search/?api=1&query=13.8315,100.575',
  targetSkillLevels: 'S, P-, P',
  joinPolicy: 'APPROVAL_REQUIRED',
  facilities: ['ห้องปรับอากาศ (แอร์)', 'พื้นยางมาตรฐาน BWF', 'ที่จอดรถ', 'ห้องอาบน้ำ'],
  // Session Rules & Pricing Structure
  walkInPolicy: 'NOT_ALLOWED',
  pointsPerSet: 21,
  shuttlecockBrand: 'RSL Silver (Speed 76)',
  rotationSystem: 'กระดานจัดคู่หมุนเวียนเสมอภาค',
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

export const MOCK_GROUP_PARTICIPANTS = [
  { id: 1, name: 'Fah (ฟ้าใส)', avatar: 'assets/image/player-avatar.jpg' },
  { id: 2, name: 'Duck Smashing', avatar: 'assets/image/duck-mascot.jpg' },
  { id: 3, name: 'Coach Kitti', avatar: 'assets/image/badminton-club-logo.jpg' },
  { id: 4, name: 'Arm Fast', avatar: 'assets/image/player-avatar.jpg' },
  { id: 5, name: 'Duck Pro', avatar: 'assets/image/duck-mascot.jpg' },
  { id: 6, name: 'Som Som', avatar: 'assets/image/player-avatar.jpg' },
  { id: 7, name: 'Duck Master', avatar: 'assets/image/duck-mascot.jpg' },
  { id: 8, name: 'Top Shuttle', avatar: 'assets/image/badminton-club-logo.jpg' }
];

export const MOCK_GROUP_DETAIL = {
  name: 'ก๊วนแบดมินตัน จตุจักร สแมชเชอร์ (Chatuchak Smashers)',
  profileImage: 'assets/image/badminton-club-logo.jpg',
  coverImage: 'assets/image/tobacco-court.jpg',
  ownerName: 'Coach Kitti',
  ownerAvatar: 'assets/image/player-avatar.jpg',
  skillLevel: 'S ถึง P+ (ปานกลาง - แข่งประจำ)',
  memberCount: 9,
  maxMembers: 17,
  status: 'OPEN',
  province: 'กรุงเทพมหานคร',
  district: 'เขตคลองเตย',
  sessionDate: 'วันอาทิตย์ที่ 25 ต.ค. 2569',
  sessionTime: '10:00 - 13:00 (3 ชั่วโมง)',
  description: 'ก๊วนตีแบดมินตันเพื่อสุขภาพ มิตรภาพ และออกกำลังกาย ระดับฝีมือ S ถึง P+ ตีประจำทุกวันอาทิตย์ 10:00 - 13:00 น. ณ Tobacco Badminton Court สนามแบดมินตันโรงงานยาสูบ ใช้ลูกแบดมินตันคุณภาพมาตรฐานแข่งขัน RSL Silver มีการจัดคู่มือการเล่นที่สูสีและเสมอภาค มีน้ำดื่มและที่จอดรถสะดวกสบาย',
  
  frequentCourts: [
    {
      id: 1,
      name: 'Tobacco Badminton Court สนามแบดมินตันโรงงานยาสูบ',
      isPrimary: true,
      lat: 13.7225,
      lng: 100.5574,
      address: 'ถ.พระรามที่ 4 แขวงคลองเตย เขตคลองเตย กรุงเทพมหานคร 10110',
      district: 'เขตคลองเตย',
      province: 'กรุงเทพฯ',
      landmark: 'ใกล้สวนเบญจกิติ / เข้าทางประตูโรงงานยาสูบ',
      courtsCount: '12 คอร์ตยางมาตรฐาน BWF',
      floorType: 'พื้นยางสังเคราะห์รองรับแรงกระแทก',
      regularSchedule: 'ทุกวันอาทิตย์ (10:00 - 13:00 น.)',
      facilities: [
        'ห้องปรับอากาศ & พัดลมระบายอากาศ',
        'พื้นยางมาตรฐาน BWF',
        'ที่จอดรถกว้างขวาง',
        'ห้องอาบน้ำ & ล็อกเกอร์',
        'ร้านเครื่องดื่ม & อาหารว่าง'
      ],
      googleMapsUrl: 'https://www.google.com/maps/search/?api=1&query=13.7225,100.5574',
      appleMapsUrl: 'https://maps.apple.com/?q=13.7225,100.5574',
      distance: 'ห่างจากคุณ ~2.5 กม.'
    },
    {
      id: 2,
      name: 'สนามแบดมินตัน วินเนอร์ สปอร์ต คลับ (Winner Sport Club)',
      isPrimary: false,
      lat: 13.8315,
      lng: 100.5750,
      address: '123/45 ซอยพหลโยธิน 32 แขวงจตุจักร เขตจตุจักร กรุงเทพมหานคร 10900',
      district: 'เขตจตุจักร',
      province: 'กรุงเทพฯ',
      landmark: 'ใกล้ BTS รัชโยธิน / ห่าง 800 เมตร',
      courtsCount: '8 คอร์ตยางมาตรฐาน BWF',
      floorType: 'พื้นยางสังเคราะห์ 5 มม.',
      regularSchedule: 'ทุกวันอังคาร & พฤหัสบดี (19:00 - 21:00 น.)',
      facilities: [
        'ห้องปรับอากาศ (Air-con)',
        'ที่จอดรถ 60+ คัน',
        'ห้องอาบน้ำ & ล็อกเกอร์'
      ],
      googleMapsUrl: 'https://www.google.com/maps/search/?api=1&query=13.8315,100.5750',
      appleMapsUrl: 'https://maps.apple.com/?q=13.8315,100.5750',
      distance: 'ห่างจากคุณ ~8.2 กม.'
    }
  ] as BadmintonCourtVenue[],

  sessionRules: [
    {
      id: 'walkIn',
      label: 'Walk-in',
      value: 'ไม่รับ Walk-in',
      subtext: 'ต้องลงชื่อผ่านระบบเท่านั้น',
      colorClass: 'pill-walkin-danger',
      icon: 'fa-solid fa-shoe-prints'
    },
    {
      id: 'points',
      label: 'คะแนนต่อเซต',
      value: '21',
      subtext: 'แต้มมาตรฐาน BWF (ดิวซ์ 30)',
      colorClass: 'pill-points-success',
      icon: 'fa-solid fa-bullseye'
    },
    {
      id: 'shuttlecock',
      label: 'ลูกแบดมินตัน',
      value: 'RSL Silver',
      subtext: 'Speed 76 มาตรฐานแข่ง',
      colorClass: 'pill-shuttle-primary',
      icon: 'fa-solid fa-feather-pointed'
    },
    {
      id: 'rotation',
      label: 'ระบบจัดคู่',
      value: 'สลับคู่เสมอภาค',
      subtext: 'กระดานคิว หมุนเวียนสูสี',
      colorClass: 'pill-rotation-purple',
      icon: 'fa-solid fa-arrows-rotate'
    },
    {
      id: 'courts',
      label: 'คอร์ตที่เปิดเล่น',
      value: 'คอร์ต 1 - 2',
      subtext: 'พื้นยาง BWF ติดแอร์',
      colorClass: 'pill-courts-amber',
      icon: 'fa-solid fa-layer-group'
    }
  ],

  pricing: {
    modelTitle: 'หารค่าสนามและลูกตามจริง (Divided Equal)',
    estimatedTotal: '~100 - 130 ฿ / คน',
    paymentMethod: 'PromptPay QR Code หรือ เงินสดเมื่อจบคอร์ท',
    items: [
      {
        title: 'ค่าเหยียบสนาม / ค่าคอร์ต',
        price: 'หารเท่า (~70 - 90 ฿)',
        unit: 'ต่อคน / 3 ชั่วโมง',
        badge: 'หารตามจริง',
        icon: 'fa-solid fa-building',
        description: 'คิดจากค่าเช่าคอร์ตมาตรฐาน BWF 3 ชั่วโมง หารจำนวนผู้เข้าร่วมทั้งหมด'
      },
      {
        title: 'ค่าลูกแบดมินตัน',
        price: 'ลูกละ 20 ฿ / หารเฉลี่ย',
        unit: 'เฉลี่ย ~1.5 ลูกต่อเกม',
        badge: 'ลูกแท้เกรดแข่ง',
        icon: 'fa-solid fa-feather-pointed',
        description: 'ใช้ลูก RSL Silver Speed 76 เปิดลูกใหม่ตลอดแมตช์ ไม่ใช้ลูกโทรม'
      },
      {
        title: 'ค่าน็อค / วอร์มคอร์ต',
        price: 'ฟรี 0 ฿',
        unit: '15 นาทีก่อนเริ่มรอบ',
        badge: 'รวมในสิทธิ์',
        icon: 'fa-solid fa-stopwatch',
        description: 'สามารถลงยืดเส้น วอร์มกล้ามเนื้อ และน็อคลูกได้ฟรีก่อนเริ่มนัดแรก'
      },
      {
        title: 'ค่าลงเล่นต่อเกม',
        price: 'ฟรี 0 ฿',
        unit: 'ไม่จำกัดจำนวนเกม',
        badge: 'เล่นได้เต็มที่',
        icon: 'fa-solid fa-gamepad',
        description: 'ไม่มีเก็บค่าเกมเพิ่ม ลงชื่อในกระดานคิวจัดคู่ได้ตลอด 3 ชั่วโมง'
      },
      {
        title: 'ส่วนลดสุภาพสตรี & มือใหม่',
        price: 'ลด 20 ฿',
        unit: 'หักจากยอดสรุปสุดท้าย',
        badge: 'สนับสนุนเพื่อนใหม่',
        icon: 'fa-solid fa-heart',
        description: 'สนับสนุนผู้เล่นหน้าใหม่และส่งเสริมความสนุกสนานในก๊วน'
      }
    ]
  }
};

export const MOCK_SAMPLE_GROUPS = [
  {
    name: 'ก๊วนแบดมินตัน จตุจักร สแมชเชอร์',
    slug: 'chatuchak-smashers',
    location: 'เขตจตุจักร, กรุงเทพมหานคร',
    venue: 'Winner Sport Club (คอร์ต 4-6)',
    logo: 'assets/image/badminton-club-logo.jpg',
    coverImage: 'assets/image/badminton-court-cover.jpg',
    description: 'ก๊วนตีแบดเพื่อสุขภาพและมิตรภาพ ระดับฝีมือ S ถึง P+ ตีทุกวันอังคารและพฤหัสบดี 19:00 - 21:00 น.',
    schedule: 'อังคาร & พฤหัสบดี 19:00 - 21:00 น.',
    skillRange: 'ระดับ S ถึง P+',
    joinPolicy: 'APPROVAL_REQUIRED',
    memberCount: 18,
    verified: true
  },
  {
    name: 'บางนา แบดมินตัน คลับ',
    slug: 'bangna-badminton-club',
    location: 'เขตบางนา, กรุงเทพมหานคร',
    venue: 'Bangna Arena Court (คอร์ต 1-3)',
    logo: 'assets/image/duck-mascot.jpg',
    coverImage: 'assets/image/tobacco-court.jpg',
    description: 'ก๊วนสำหรับคนรักเกมเร็ว ตบหนัก วิ่งคอร์ตเหนียว ตีประจำทุกวันเสาร์เช้า 09:00 - 12:00 น.',
    schedule: 'ทุกวันเสาร์เช้า 09:00 - 12:00 น.',
    skillRange: 'ระดับ P ถึง B',
    joinPolicy: 'APPROVAL_REQUIRED',
    memberCount: 24,
    verified: true
  },
  {
    name: 'เมืองนนท์ สปอร์ต มิตรภาพ',
    slug: 'muang-non-badminton',
    location: 'อำเภอเมืองนนทบุรี, นนทบุรี',
    venue: 'Nonthaburi Court 88 (คอร์ต 2)',
    logo: 'assets/image/add-kwuan-app-icon.svg',
    coverImage: 'assets/image/badminton-court-cover.jpg',
    description: 'ก๊วนมือใหม่และผู้เริ่มต้น (N, S-) เน้นออกกำลังกาย ตีสนุกสนาน ไม่มีกดดัน มีคนช่วยแชร์เทคนิคฟรี',
    schedule: 'พุธ & อาทิตย์ 18:00 - 20:00 น.',
    skillRange: 'ระดับ N ถึง S-',
    joinPolicy: 'OPEN',
    memberCount: 15,
    verified: true
  }
];

export interface PopularCourt {
  id: string;
  name: string;
  district: string;
  province: string;
  distanceKm: string;
  courtCount: number;
  courtType: string;
  coverImage: string;
  amenities: { name: string; icon: string }[];
  activeGroups: number;
  priceHour: string;
}

export const MOCK_POPULAR_COURTS: PopularCourt[] = [
  {
    id: 'winner-sport',
    name: 'Winner Sport Club จตุจักร',
    district: 'เขตจตุจักร',
    province: 'กรุงเทพมหานคร',
    distanceKm: '3.2 กม.',
    courtCount: 12,
    courtType: 'พื้นยาง BWF ติดแอร์',
    coverImage: 'assets/image/badminton-court-cover.jpg',
    amenities: [
      { name: 'ที่จอดรถ 60 คัน', icon: 'fa-solid fa-square-parking' },
      { name: 'ห้องอาบน้ำอุ่น', icon: 'fa-solid fa-shower' },
      { name: 'ติดแอร์เย็น', icon: 'fa-solid fa-snowflake' }
    ],
    activeGroups: 4,
    priceHour: '180 - 220 ฿/ชม.'
  },
  {
    id: 'bangna-arena',
    name: 'Bangna Arena Court บางนา',
    district: 'เขตบางนา',
    province: 'กรุงเทพมหานคร',
    distanceKm: '5.8 กม.',
    courtCount: 8,
    courtType: 'พื้นยาง BWF ติดแอร์',
    coverImage: 'assets/image/tobacco-court.jpg',
    amenities: [
      { name: 'ที่จอดรถสะดวก', icon: 'fa-solid fa-square-parking' },
      { name: 'ห้องอาบน้ำ', icon: 'fa-solid fa-shower' },
      { name: 'ร้านเครื่องดื่ม', icon: 'fa-solid fa-mug-hot' }
    ],
    activeGroups: 5,
    priceHour: '200 - 250 ฿/ชม.'
  },
  {
    id: 'tobacco-court',
    name: 'Tobacco Badminton Court โรงงานยาสูบ',
    district: 'เขตคลองเตย',
    province: 'กรุงเทพมหานคร',
    distanceKm: '4.1 กม.',
    courtCount: 6,
    courtType: 'พื้นปาร์เก้ BWF สปอร์ตคลับ',
    coverImage: 'assets/image/badminton-court-cover.jpg',
    amenities: [
      { name: 'ใกล้ MRT คลองเตย', icon: 'fa-solid fa-train-subway' },
      { name: 'ที่จอดรถในร่ม', icon: 'fa-solid fa-square-parking' },
      { name: 'ห้องอาบน้ำ', icon: 'fa-solid fa-shower' }
    ],
    activeGroups: 3,
    priceHour: '150 - 180 ฿/ชม.'
  }
];
