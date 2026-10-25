import { Component, inject } from '@angular/core';
import { FeedbackService } from '../../services/feedback.service';

@Component({
  selector: 'app-feedback-host',
  standalone: false,
  templateUrl: './feedback-host.component.html'
})
export class FeedbackHostComponent {
  feedback = inject(FeedbackService);

  onConfirm(val: boolean) {
    const dialog = this.feedback.activeConfirm();
    if (dialog) {
      dialog.resolve(val);
    }
  }

  onAlertClose() {
    const dialog = this.feedback.activeAlert();
    if (dialog) {
      dialog.resolve();
    }
  }

  onSelectBottomSheetOption(optionId: string) {
    const sheet = this.feedback.activeBottomSheet();
    if (sheet) {
      sheet.resolve(optionId);
    }
  }

  onCloseBottomSheet() {
    this.feedback.closeBottomSheet();
  }
}
