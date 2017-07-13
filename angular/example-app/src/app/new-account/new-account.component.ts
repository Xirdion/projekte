import { Component, EventEmitter, OnInit, Output } from '@angular/core';
import { LoggingService } from '../logging.service';

@Component({
  selector: 'app-new-account',
  templateUrl: './new-account.component.html',
  styleUrls: ['./new-account.component.scss'],
    providers: [LoggingService]
})
export class NewAccountComponent implements OnInit {
    @Output() accountAdded = new EventEmitter<{name: string, status: string}>();

  constructor(private loggingService: LoggingService) { }

  ngOnInit() {
  }

  onCreateAccount(accountName: string, accountStatus: string) {
      this.accountAdded.emit({
          name: accountName,
          status: accountStatus
      });
      this.loggingService.logStatusChange(accountStatus);
      // console.log('A server status changed, new status: ' + accountStatus);
  }
}
