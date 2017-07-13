import { Component, EventEmitter, Input, OnInit, Output } from '@angular/core';
import { LoggingService } from '../logging.service';

@Component({
  selector: 'app-account',
  templateUrl: './account.component.html',
  styleUrls: ['./account.component.scss'],
    providers: [LoggingService]
})
export class AccountComponent implements OnInit {
    @Output() statusChanged = new EventEmitter<{id: number, newStatus: string}>();
    @Input() account: {name: string, status: string};
    @Input() id: number;

  constructor(private loggingService: LoggingService) { }

  ngOnInit() {
  }

  onSetTo(status: string) {
      this.statusChanged.emit({id: this.id, newStatus: status});
      this.loggingService.logStatusChange(status);
      // console.log('A server status changed, new status: ' + status);
  }
}
