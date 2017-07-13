import {Component} from '@angular/core';

@Component({
    selector: 'app-server',
    templateUrl: './server.component.html',
    styleUrls: ['./server.component.scss']
})
export class ServerComponent {
    serverId: number = 10;
    private serverStatus: string = 'offline';

    constructor() {
        this.serverStatus = Math.random() > 0.5 ? 'online' : 'offline';
    }

    getServerStatus() {
        return this.serverStatus;
    }

    /**
     * @returns {string|string}
     */
    getColor() {
        return this.serverStatus === 'online' ? 'green' : 'red';
    }
}