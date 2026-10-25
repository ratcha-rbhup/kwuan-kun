import { Component, signal, computed, inject, OnInit } from '@angular/core';
import { FeedbackService } from '../../../core/services/feedback.service';
import { ApiService } from '../../../core/services/api.service';
import { PlayerPayment, MOCK_OWNER_EXPENSES_PLAYERS, getMockData } from '../../../mock-data';

@Component({
  selector: 'app-owner-expenses',
  standalone: false,
  templateUrl: './owner-expenses.component.html'
})
export class OwnerExpensesComponent implements OnInit {
  private feedback = inject(FeedbackService);
  private api = inject(ApiService);

  groupName = signal('ก๊วนแบดมินตัน จตุจักร สแมชเชอร์');
  selectedSession = signal('รอบวันอังคาร 27 ต.ค. (19:00 - 21:00 น.)');

  // Pricing configuration for current session
  courtHours = signal<number>(2);
  courtCount = signal<number>(2);
  courtRatePerHour = signal<number>(250); // 250 baht / hour / court
  shuttlecocksUsed = signal<number>(8); // 8 shuttles
  shuttlecockPriceEach = signal<number>(22); // 22 baht each
  waterSnackFee = signal<number>(100); // 100 baht water/ice

  calculationMode = signal<'EQUAL' | 'BY_SHUTTLE'>('EQUAL');
  promptPayId = signal('089-123-4567');
  promptPayAccountName = signal('นายกิตติ (Coach Kitti)');

  players = signal<PlayerPayment[]>([...getMockData(MOCK_OWNER_EXPENSES_PLAYERS, [])]);

  // Computed calculations
  totalCourtFee = computed(() => this.courtHours() * this.courtCount() * this.courtRatePerHour());
  totalShuttleFee = computed(() => this.shuttlecocksUsed() * this.shuttlecockPriceEach());
  grandTotal = computed(() => this.totalCourtFee() + this.totalShuttleFee() + this.waterSnackFee());

  playerCount = computed(() => this.players().length);

  costPerPlayerEqual = computed(() => {
    const count = this.playerCount();
    if (count === 0) return 0;
    return Math.ceil(this.grandTotal() / count);
  });

  paidCount = computed(() => this.players().filter(p => p.isPaid).length);
  unpaidCount = computed(() => this.players().filter(p => !p.isPaid).length);
  collectedAmount = computed(() => this.paidCount() * this.costPerPlayerEqual());
  remainingAmount = computed(() => this.grandTotal() - this.collectedAmount());

  ngOnInit(): void {
    this.loadExpenses();
  }

  loadExpenses(): void {
    this.api.getOwnerExpenses(1).subscribe({
      next: (res) => {
        if (res && res.sessionTitle) {
          this.selectedSession.set(res.sessionTitle);
        }
        if (res && res.players && res.players.length > 0) {
          this.players.set(res.players);
        }
        if (res && res.courtFee) {
          this.courtRatePerHour.set(Math.round(res.courtFee / (this.courtHours() * this.courtCount())));
        }
        if (res && res.shuttlePrice) {
          this.shuttlecockPriceEach.set(res.shuttlePrice);
        }
      },
      error: (err) => console.error('Failed to load expenses from API:', err)
    });
  }

  togglePayment(player: PlayerPayment): void {
    const nextState = !player.isPaid;
    this.api.togglePayment(1, player.id, nextState).subscribe({
      next: () => {
        player.isPaid = nextState;
        if (player.isPaid) {
          player.paidAt = new Date().toLocaleTimeString('th-TH', { hour: '2-digit', minute: '2-digit' }) + ' น.';
          this.feedback.success(`บันทึกการชำระเงินของ "${player.name}" เรียบร้อย`);
        } else {
          player.paidAt = undefined;
          this.feedback.info(`ปรับสถานะของ "${player.name}" เป็นยังไม่ชำระ`);
        }
      },
      error: () => {
        player.isPaid = nextState;
        if (player.isPaid) {
          player.paidAt = new Date().toLocaleTimeString('th-TH', { hour: '2-digit', minute: '2-digit' }) + ' น.';
          this.feedback.success(`บันทึกการชำระเงินของ "${player.name}" เรียบร้อย`);
        } else {
          player.paidAt = undefined;
          this.feedback.info(`ปรับสถานะของ "${player.name}" เป็นยังไม่ชำระ`);
        }
      }
    });
  }

  markAllPaid(): void {
    this.players().forEach(p => {
      this.api.togglePayment(1, p.id, true).subscribe();
    });
    this.players.update(list => list.map(p => ({
      ...p,
      isPaid: true,
      paidAt: p.paidAt || '21:15 น.'
    })));
    this.feedback.success('บันทึกทุกคนชำระเงินครบทั้งหมดแล้ว');
  }

  copySummaryForLine(): void {
    const text = `🏸 สรุปยอดค่าใช้จ่าย ${this.groupName()}
🗓️ ${this.selectedSession()}
-----------------------
🏟️ ค่าคอร์ต (${this.courtHours()} ชม. x ${this.courtCount()} คอร์ต): ${this.totalCourtFee()} ฿
🏸 ค่าลูกแบด (${this.shuttlecocksUsed()} ลูก): ${this.totalShuttleFee()} ฿
🥤 ค่าน้ำดื่มส่วนกลาง: ${this.waterSnackFee()} ฿
💰 ยอดรวมทั้งสิ้น: ${this.grandTotal()} ฿
👥 จำนวนผู้เล่น: ${this.playerCount()} คน
👉 ตกคนละ: ${this.costPerPlayerEqual()} ฿
-----------------------
📲 โอนพร้อมเพย์: ${this.promptPayId()}
ชื่อบัญชี: ${this.promptPayAccountName()}
โอนแล้วแปะสลิปในกลุ่มได้เลยครับ ขอบคุณทุกคนที่มาร่วมสนุกกันครับ!`;

    navigator.clipboard.writeText(text).then(() => {
      this.feedback.success('คัดลอกข้อความสรุปยอดเงินสำหรับส่งใน LINE เรียบร้อยแล้ว!');
    }).catch(() => {
      this.feedback.info('กรุณาคัดลอกข้อความสรุปจากหน้าต่าง');
    });
  }
}
