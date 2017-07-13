import { Component, EventEmitter, Input, OnInit, Output } from '@angular/core';
import { Recipe } from '../../recipe.model';

@Component({
    selector: 'app-recipe-item',
    templateUrl: './recipe-item.component.html'
})
export class RecipeItemComponent implements OnInit {
    @Input() private recipe: Recipe;
    @Output() selectRecipe = new EventEmitter<void>();

    constructor() {

    }
    ngOnInit() {

    }

    onSelectRecipe() {
        this.selectRecipe.emit();
    }
}