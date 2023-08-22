export default class Item {
  private quantity = 1;

  constructor(readonly idProduct: number) {}

  incrementQuantity() {
    this.quantity += 1;
  }

  getQuantity() {
    return this.quantity;
  }
}
