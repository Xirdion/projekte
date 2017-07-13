import { Component, EventEmitter, OnInit, Output } from '@angular/core';

@Component({
    selector: 'app-header',
    templateUrl: './header.component.html'
})
export class HeaderComponent implements OnInit {
    @Output() public changeShowModus = new EventEmitter<string>();

    constructor() {

    }

    ngOnInit() {

    }

    onSelect(modus: string) {
        this.changeShowModus.emit(modus);
    }
}