export class Ingredient {
    private name: string;
    private amount: number;

    /**
     * ingredient constructor
     * @param name
     * @param amount
     */
    constructor(name: string, amount: number) {
        this.name   = name;
        this.amount = amount;
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
     * @returns {number}
     */
    getAmount() {
        return this.amount;
    }

    /**
     * @param amount
     */
    setAmount(amount: number) {
        this.amount = amount;
    }
}