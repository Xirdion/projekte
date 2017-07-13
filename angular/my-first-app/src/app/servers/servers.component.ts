import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-servers',
  templateUrl: './servers.component.html',
  styleUrls: ['./servers.component.scss']
})
export class ServersComponent implements OnInit {
    showDetails = false;
    detailsClicks = [];

    allowServer = false;
    serverCreationStatus = 'No server was created';
    serverName = 'Testserver';
    username = '';
    serverCreated = false;
    servers = ['Testserver', 'Testserver 2'];

    constructor() {
      setTimeout(() => {
          this.allowServer = true;
      }, 2000);
    }

    onShowDetails() {
        this.showDetails = !this.showDetails;
        this.detailsClicks.push(new Date());
    }

    getDetailBackground(count) {
        return count >= 4 ? 'blue' : 'white';
    }


    ngOnInit() {
    }

    onCreateServer() {
        this.serverCreated = true;
        this.servers.push(this.serverName);
        this.serverCreationStatus = 'Server was created! Name is ' + this.serverName;
    }

    onUpdateServerName(event: any) {
      this.serverName = event.target.value;
    }

    disableButton() {
      return !(this.username !== '');
    }
    onResetUsername() {
      this.username = '';
    }

}
