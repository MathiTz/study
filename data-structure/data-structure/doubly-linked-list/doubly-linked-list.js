const {
  module: { Node },
} = require('./node');

class DoublyLinkedList {
  constructor() {
    this.head = null;
    this.tail = null;
    this.length = 0;
  }
}

const list = new DoublyLinkedList();

console.log(list);
