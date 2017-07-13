import { Component, ElementRef, EventEmitter, OnInit, Output, ViewChild } from '@angular/core';

@Component({
  selector: 'app-cockpit',
  templateUrl: './cockpit.component.html',
  styleUrls: ['./cockpit.component.scss']
})
export class CockpitComponent implements OnInit {
    @Output() serverCreated = new EventEmitter<{name: string, content: string}>();
    @Output('bpCreated') blueprintCreated = new EventEmitter<{name: string, content: string}>();
    // newServerName = '';
    // newServerContent = '';
    @ViewChild('serverContentInput') serverContentInput: ElementRef;

    constructor() { }

    ngOnInit() {
    }

    onAddServer(nameInput: HTMLInputElement) {
        this.serverCreated.emit({
            name: nameInput.value,
            content: this.serverContentInput.nativeElement.value
        });
    }

    onAddBlueprint(nameInput: HTMLInputElement) {
        this.blueprintCreated.emit({
            name: nameInput.value,
            content: this.serverContentInput.nativeElement.value
        });
    }
}
