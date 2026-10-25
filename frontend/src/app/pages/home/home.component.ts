import { Component, signal } from '@angular/core';
import { 
  MOCK_UPCOMING_SESSIONS, 
  MOCK_SAMPLE_GROUPS, 
  MOCK_POPULAR_COURTS, 
  getMockData 
} from '../../mock-data';

@Component({
  selector: 'app-home',
  standalone: false,
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.scss']
})
export class HomeComponent {
  searchQuery = signal('');
  selectedLocation = signal('all');
  showJoinModal = signal(false);
  joinCodeInput = signal('');
  joinMessage = signal<{ type: 'success' | 'error'; text: string } | null>(null);

  // Greeting based on Thai sports casual style
  greeting = signal('สวัสดีตอนเช้า');

  // Upcoming Live Badminton Sessions (TBad Style)
  upcomingSessions = signal(getMockData(MOCK_UPCOMING_SESSIONS, []));

  // Popular Badminton Courts preview
  popularCourts = signal(getMockData(MOCK_POPULAR_COURTS, []));

  // Sample Groups (Clean cards with club avatars & verified badges)
  sampleGroups = signal(getMockData(MOCK_SAMPLE_GROUPS, []));

  // Platform Highlights & Why KwuanKun?
  readonly platformFeatures = [
    {
      icon: 'fa-solid fa-arrows-rotate',
      color: '#00C261',
      bg: '#E8F8F0',
      title: 'จัดคู่เสมอภาค ด้วยระบบคิว',
      description: 'หมุนเวียนผู้เล่นฝีมือสูสีอย่างเป็นธรรม ได้ลงคอร์ตครบทุกคน ไม่ต้องนั่งคอยเหงา'
    },
    {
      icon: 'fa-solid fa-calculator',
      color: '#0284C7',
      bg: '#E0F2FE',
      title: 'คุมงบโปร่งใส แชร์ตามจริง',
      description: 'หารค่าคอร์ตและลูกแบดอัตโนมัติ เช็กบิลชัดเจน สแกน PromptPay จ่ายทันที'
    },
    {
      icon: 'fa-solid fa-shield-heart',
      color: '#F59E0B',
      bg: '#FEF3C7',
      title: 'สังคมมิตรภาพ ไม่เทก๊วน',
      description: 'มีระบบ Attendance Score ตรวจสอบประวัติก่อนรับสมาชิกเข้าเล่น หมดกังวลคนเบี้ยว'
    },
    {
      icon: 'fa-brands fa-line',
      color: '#06C755',
      bg: '#DCFCE7',
      title: 'แจ้งเตือนผ่าน LINE อัตโนมัติ',
      description: 'แจ้งเตือนรอบตีก่อนเริ่ม 2 ชั่วโมง พร้อมรายงานผลการอนุมัติและรายชื่อเพื่อนร่วมคอร์ต'
    }
  ];

  // Platform Fun & Community Stats
  readonly platformStats = [
    { number: '14+', label: 'ก๊วนแบดมินตัน', subtext: 'เปิดรับสมาชิกทุกระดับ' },
    { number: '62+', label: 'สนามคอร์ตมาตรฐาน', subtext: 'ทั่ว กทม. และปริมณฑล' },
    { number: '450+', label: 'สมาชิกออนไลน์', subtext: 'พร้อมลงสนามทุกวัน' },
    { number: '99.4%', label: 'ความพึงพอใจ', subtext: 'จากผู้เล่น 1,250+ นัด' }
  ];

  constructor() {
    const hour = new Date().getHours();
    if (hour < 12) {
      this.greeting.set('สวัสดีตอนเช้า');
    } else if (hour < 17) {
      this.greeting.set('สวัสดีตอนบ่าย');
    } else {
      this.greeting.set('สวัสดีตอนค่ำ');
    }
  }

  openJoinModal() {
    this.joinCodeInput.set('');
    this.joinMessage.set(null);
    this.showJoinModal.set(true);
  }

  closeJoinModal() {
    this.showJoinModal.set(false);
  }

  submitJoinCode() {
    const code = this.joinCodeInput().trim().toUpperCase();
    if (!code) {
      this.joinMessage.set({ type: 'error', text: 'กรุณากรอกรหัสคำเชิญเข้าร่วมก๊วน' });
      return;
    }
    if (code.length < 4) {
      this.joinMessage.set({ type: 'error', text: 'รหัสคำเชิญต้องมีความยาวอย่างน้อย 4 ตัวอักษร' });
      return;
    }

    this.joinMessage.set({
      type: 'success',
      text: `พบก๊วนสำหรับรหัส "${code}"! กำลังส่งคำขอเข้าร่วมถึงหัวหน้าก๊วน...`
    });
  }
}
