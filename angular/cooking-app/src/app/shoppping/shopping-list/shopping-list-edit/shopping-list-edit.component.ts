import { Component, ElementRef, EventEmitter, OnInit, Output, ViewChild } from '@angular/core';
import { Ingredient } from '../../../shared/ingredient.model';

@Component({
    selector: 'app-shopping-list-edit',
    templateUrl: './shopping-list-edit.component.html'
})
export class ShoppingListEditComponent implements OnInit {
    @ViewChild('ingredientName') private name: ElementRef;
    @ViewChild('ingredientAmount') private amount: ElementRef;
    @Output() ingredientAdded = new EventEmitter<Ingredient>();

    constructor() {

    }

    ngOnInit() {

    }

    onAddIngredient() {
        const newIngredient = new Ingredient(this.name.nativeElement.value, this.amount.nativeElement.value);
        this.ingredientAdded.emit(newIngredient);

    }
}