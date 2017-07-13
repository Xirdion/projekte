export class Recipe {
    private name: string;
    private description: string;
    private imagePath: string;

    /**
     * Recipe constructor
     * @param name
     * @param description
     * @param imagePath
     */
    constructor(name: string, description: string, imagePath: string) {
        this.name        = name;
        this.description = description;
        this.imagePath   = imagePath;
    }

    /**
     * @returns {string}
     */
    getName() {
        return this.name;
    }

    /**
     * @param name
     */
    setName(name: string) {
        this.name = name;
    }

    /**
     * @returns {string}
     */
    getDescription() {
        return this.description;
    }

    /**
     * @param description
     */
    setDescription(description: string) {
        this.description = description;
    }

    /**
     * @returns {string}
     */
    getImagePath() {
        return this.imagePath;
    }

    /**
     * @param imagePath
     */
    setImagePath(imagePath: string) {
        this.imagePath = imagePath;
    }
}