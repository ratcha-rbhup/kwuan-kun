import { Component, signal, computed, inject, OnInit } from '@angular/core';
import { FeedbackService } from '../../../core/services/feedback.service';
import {
  TargetSkill,
  PartyCardItem,
  MOCK_PROVINCES,
  MOCK_PROVINCE_DISTRICTS,
  MOCK_DATE_STRIP,
  MOCK_PARTIES,
  getMockData
} from '../../../mock-data';

export { TargetSkill, PartyCardItem };

import { ApiService } from '../../../core/services/api.service';

@Component({
  selector: 'app-group-list',
  standalone: false,
  templateUrl: './group-list.component.html',
  styleUrls: ['./group-list.component.scss']
})
export class GroupListComponent implements OnInit {
  private feedback = inject(FeedbackService);
  private api = inject(ApiService);

  searchQuery = '';
  
  // Advanced Filters (จังหวัด, เขต/อำเภอ, ระดับ level)
  selectedProvince = '';
  selectedDistrict = '';
  selectedLevel = '';

  // User reference GPS coordinates (Bangkok center as initial default)
  userLat = signal<number>(13.7367);
  userLng = signal<number>(100.5232);
  isGpsActive = signal<boolean>(false);
  isGpsLocating = signal<boolean>(false);

  provinces: string[] = getMockData(MOCK_PROVINCES, []);
  provinceDistricts: Record<string, string[]> = getMockData(MOCK_PROVINCE_DISTRICTS, {});
  dates = getMockData(MOCK_DATE_STRIP, []);

  selectedDateIso = signal<string>('2026-10-25'); // Default to Today (25 ต.ค.)

  selectedDateLabel = computed(() => {
    const iso = this.selectedDateIso();
    if (!iso) return '';
    const found = this.dates.find(d => d.isoDate === iso);
    return found ? `${found.day} ${found.date}` : iso;
  });

  // Sort Filter: NEAREST (ระยะทางใกล้ที่สุด) vs POPULAR (ยอดนิยม)
  sortBy = signal<'NEAREST' | 'POPULAR'>('NEAREST');

  // Badminton Parties Dataset with Real Venue Coordinates & Dates
  parties = signal<PartyCardItem[]>([...getMockData(MOCK_PARTIES, [])]);

  ngOnInit(): void {
    this.recalculateDistances();
    this.loadGroupsFromApi();
  }

  loadGroupsFromApi(): void {
    this.api.getGroups().subscribe({
      next: (groups) => {
        if (!groups || groups.length === 0) return;
        const currentList = this.parties();
        const existingSlugs = new Set(currentList.map(p => p.slug));

        const newItems: PartyCardItem[] = [];
        groups.forEach((g: any) => {
          if (!existingSlugs.has(g.slug)) {
            newItems.push({
              id: g.id,
              slug: g.slug,
              type: 'KWUAN',
              typeLabel: 'ก๊วนประจำ',
              title: g.name,
              subtitle: g.description || 'ก๊วนแบดมินตันเพื่อสุขภาพและมิตรภาพ',
              province: g.province?.name_th || 'กรุงเทพมหานคร',
              district: g.district?.name_th || 'เขตจตุจักร',
              venueName: g.primary_venue?.name || 'สนามแบดมินตันมาตรฐาน',
              venueAddress: 'เขตจตุจักร กรุงเทพมหานคร',
              lat: 13.8282,
              lng: 100.5615,
              distanceKm: 3.5,
              bannerImage: 'assets/image/badminton-court-cover.jpg',
              isoDate: '2026-10-25',
              dateBadge: 'เปิดรับสมัคร',
              dateTimeDisplay: 'อังคาร 19:00 - 21:00 น.',
              targetSkills: [
                { label: 'S', cssClass: 'ak-badge-s' },
                { label: 'P', cssClass: 'ak-badge-p' }
              ],
              hostName: 'หัวหน้าก๊วน',
              hostAvatar: 'assets/image/player-avatar.jpg',
              hostRole: 'OWNER',
              currentPlayers: g.current_member_count || 1,
              maxPlayers: g.max_members || 12,
              isFull: false,
              status: 'OPEN'
            });
          }
        });

        if (newItems.length > 0) {
          this.parties.update(list => [...newItems, ...list]);
          this.recalculateDistances();
        }
      },
      error: (err) => console.error('Failed to load groups from API:', err)
    });
  }

  // Haversine formula to calculate distance in km
  calculateDistanceKm(lat1: number, lon1: number, lat2: number, lon2: number): number {
    const R = 6371; // Earth's radius in km
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a =
      Math.sin(dLat / 2) * Math.sin(dLat / 2) +
      Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
      Math.sin(dLon / 2) * Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return Math.round(R * c * 10) / 10;
  }

  recalculateDistances(): void {
    const uLat = this.userLat();
    const uLng = this.userLng();

    this.parties.update(list => list.map(item => ({
      ...item,
      distanceKm: this.calculateDistanceKm(uLat, uLng, item.lat, item.lng)
    })));
  }

  requestUserLocation(): void {
    if (!navigator.geolocation) {
      this.feedback.info('อุปกรณ์ของคุณไม่รองรับการอ่าน GPS ระบบใช้พิกัดใจกลางกรุงเทพฯ เป็นฐานคำนวณระยะทาง');
      return;
    }

    this.isGpsLocating.set(true);
    navigator.geolocation.getCurrentPosition(
      (pos) => {
        this.isGpsLocating.set(false);
        this.isGpsActive.set(true);
        this.userLat.set(pos.coords.latitude);
        this.userLng.set(pos.coords.longitude);
        this.recalculateDistances();
        this.sortBy.set('NEAREST');
        this.feedback.success('ตรวจจับตำแหน่ง GPS ของคุณแล้ว! เรียงลำดับสนามที่อยู่ใกล้คุณที่สุดเรียบร้อย');
      },
      (err) => {
        this.isGpsLocating.set(false);
        this.feedback.info('ใช้ตำแหน่งพิกัดใจกลางกรุงเทพฯ เป็นเกณฑ์คำนวณระยะทางใกล้ที่สุด');
      },
      { timeout: 8000 }
    );
  }

  availableDistricts(): string[] {
    if (!this.selectedProvince || !this.provinceDistricts[this.selectedProvince]) {
      return [];
    }
    return this.provinceDistricts[this.selectedProvince];
  }

  onProvinceChange(): void {
    const districts = this.availableDistricts();
    if (!districts.includes(this.selectedDistrict)) {
      this.selectedDistrict = '';
    }
  }

  hasActiveFilters(): boolean {
    return !!(this.selectedProvince || this.selectedDistrict || this.selectedLevel || this.selectedDateIso() || this.searchQuery);
  }

  clearAllFilters(): void {
    this.selectedProvince = '';
    this.selectedDistrict = '';
    this.selectedLevel = '';
    this.selectedDateIso.set('');
    this.searchQuery = '';
    this.sortBy.set('NEAREST');
    this.feedback.info('ล้างตัวกรองทั้งหมดแล้ว แสดงรายการก๊วนทั้งหมด');
  }

  selectDate(isoDate: string): void {
    if (this.selectedDateIso() === isoDate) {
      // Toggle off when clicked again
      this.selectedDateIso.set('');
      this.feedback.info('แสดงนัดตีของทุกวัน (All Dates)');
    } else {
      this.selectedDateIso.set(isoDate);
      const found = this.dates.find(d => d.isoDate === isoDate);
      if (found && found.isoDate) {
        this.feedback.info(`กรองนัดตีเฉพาะวัน${found.day} (${found.date})`);
      } else {
        this.feedback.info('แสดงนัดตีของทุกวัน');
      }
    }
  }

  toggleSort(): void {
    if (this.sortBy() === 'NEAREST') {
      this.sortBy.set('POPULAR');
      this.feedback.info('เรียงตาม "ก๊วนยอดนิยม" (จำนวนผู้เล่นมากที่สุด)');
    } else {
      this.sortBy.set('NEAREST');
      if (!this.isGpsActive()) {
        this.requestUserLocation();
      } else {
        this.feedback.info('เรียงตาม "สนามใกล้คุณที่สุด" (ระยะทาง กม.)');
      }
    }
  }

  filteredParties(): PartyCardItem[] {
    let result = this.parties().filter(p => {
      // 1. วันที่ (Date Filter)
      const selectedIso = this.selectedDateIso();
      const matchDate = !selectedIso || p.isoDate === selectedIso;

      // 2. จังหวัด (Province)
      const matchProvince = !this.selectedProvince || p.province === this.selectedProvince;

      // 3. เขต/อำเภอ (District)
      const matchDistrict = !this.selectedDistrict || p.district === this.selectedDistrict;

      // 4. ระดับฝีมือ (Level)
      const matchLevel = !this.selectedLevel || p.targetSkills.some(s => {
        const lvl = this.selectedLevel.toLowerCase().trim();
        const skill = s.label.toLowerCase().trim();
        if (lvl === 's') return (skill.startsWith('s') || skill.includes('s')) && !skill.includes('p');
        if (lvl === 'p') return skill.includes('p');
        if (lvl === 'n') return skill.includes('n');
        if (lvl === 'bg') return skill.includes('bg');
        if (lvl === 'หน้าบ้าน') return skill.includes('หน้าบ้าน');
        return skill.includes(lvl);
      });

      // 5. คำค้นหา (Search Query)
      const query = this.searchQuery.toLowerCase().trim();
      const matchSearch = !query || 
        p.title.toLowerCase().includes(query) || 
        p.venueName.toLowerCase().includes(query) ||
        p.hostName.toLowerCase().includes(query) ||
        p.province.toLowerCase().includes(query) ||
        p.district.toLowerCase().includes(query) ||
        p.subtitle.toLowerCase().includes(query);

      return matchDate && matchProvince && matchDistrict && matchLevel && matchSearch;
    });

    // 6. Sort: NEAREST (ระยะทางใกล้ที่สุด) vs POPULAR (ยอดนิยม)
    if (this.sortBy() === 'NEAREST') {
      result.sort((a, b) => a.distanceKm - b.distanceKm);
    } else {
      result.sort((a, b) => b.currentPlayers - a.currentPlayers);
    }

    return result;
  }
}
