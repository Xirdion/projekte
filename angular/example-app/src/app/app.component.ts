import { Component, OnInit } from '@angular/core';
import { AccountsService } from './accounts.service';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss'],
    providers: [AccountsService]
})
export class AppComponent implements OnInit {
    accounts: {name: string, status: string}[] = [];

    constructor(private accountsService: AccountsService) {}

    ngOnInit() {
        this.accounts = this.accountsService.accounts;
    }

    /*private numbers     = [1, 2, 3, 4, 5];
    private oddNumbers  = [1, 3, 5];
    private evenNumbers = [2, 4];
    private onlyOdd     = false;
    private value = 10;*/


    /*public oddNumbers = [];
    public evenNumbers = [];

    onAddComponent(incrementnumber: number) {
        if (incrementnumber % 2) {
            this.oddNumbers.push(incrementnumber);
        } else {
            this.evenNumbers.push(incrementnumber);
        }
    }*/

    /*serverElements = [{type: 'server', name: 'Testserver', content: 'Just a test!'}];

    onServerAdded(serverData: {name: string, content: string}) {
        this.serverElements.push({
            type: 'server',
            name: serverData.name,
            content: serverData.content
        });
    }

    onBlueprintAdded(blueprintData: {name: string, content: string}) {
        this.serverElements.push({
            type: 'blueprint',
            name: blueprintData.name,
            content: blueprintData.content
        });
    }

    onChangeFirst() {
        this.serverElements[0].name = 'Changed';
    }

    onDestroyFirst() {
        this.serverElements.splice(0, 1);
    }*/
}
