import { Component, EventEmitter, OnInit, Output } from '@angular/core';

@Component({
  selector: 'app-game-control',
  templateUrl: './game-control.component.html',
  styleUrls: ['./game-control.component.scss']
})
export class GameControlComponent implements OnInit {
    public number = 0;
    private intervalId;
    @Output() onInterval = new EventEmitter<number>();

    constructor() { }

    ngOnInit() {
    }

    onStartGame() {
        this.intervalId = setInterval(
            () => {
                this.number += 1;
                this.onInterval.emit(this.number);
            }, 1000
        );
    }

    onEndGame() {
        clearInterval(this.intervalId);
    }
}
