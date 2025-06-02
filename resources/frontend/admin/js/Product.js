export default class Product {
    constructor(name, price) {
        this.name = name;
        this.price = price;
    }

    showInfo() {
        console.log(this.name, this.price);
    }
}
