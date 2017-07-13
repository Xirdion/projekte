import { Component, OnInit, ViewEncapsulation } from '@angular/core';

@Component({
    selector: 'app-root',
    templateUrl: './app.component.html',
    styleUrls: ['./app.component.scss'],
    encapsulation: ViewEncapsulation.None
})
export class AppComponent implements OnInit {
    private showModus = 'recipes';

    constructor() {

    }

    ngOnInit() {

    }

    onShowModusChanged(newShowModus) {
        this.showModus = newShowModus;
    }

    getShowModus() {
        return this.showModus;
    }
}
