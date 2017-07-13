import { Component, OnInit, EventEmitter, Output } from '@angular/core';
import { Recipe } from '../recipe.model';

@Component({
    selector: 'app-recipe-list',
    templateUrl: './recipe-list.component.html'
})
export class RecipeListComponent implements OnInit {
    @Output() selectRecipe = new EventEmitter<Recipe>();

    recipes: Recipe[] = [
        new Recipe('A Test Recipe1', 'This is simply a test', 'https://upload.wikimedia.org/wikipedia/commons/4/4f/Croissant_%28Michel_Roux_recipe%29_%285676276528%29.jpg'),
        new Recipe('A Test Recipe2', 'This is simply a test', 'https://upload.wikimedia.org/wikipedia/commons/4/4f/Croissant_%28Michel_Roux_recipe%29_%285676276528%29.jpg')
    ];

    constructor() {

    }

    ngOnInit() {

    }

    onSelectRecipe(recipe: Recipe) {
        this.selectRecipe.emit(recipe);
    }
}