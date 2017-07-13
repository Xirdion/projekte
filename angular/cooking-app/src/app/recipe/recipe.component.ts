import { Component, OnInit } from '@angular/core';
import { Recipe } from './recipe.model';

@Component({
    selector: 'app-recipe',
    templateUrl: './recipe.component.html'
})
export class RecipeComponent implements OnInit {
    private selectedRecipe: Recipe;

    constructor() {

    }

    ngOnInit() {

    }

    onSelectRecipe(recipe: Recipe) {
        this.selectedRecipe = recipe;
    }

    getSelectedRecipe() {
        return this.selectedRecipe;
    }
}