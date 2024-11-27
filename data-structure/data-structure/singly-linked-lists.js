// Data structure that contains a head, tail and length property
// Consists of nodes, and each node has a value and a pointer to another node or null

class Node {
  constructor(val) {
    this.val = val;
    this.next = null;
  }
}

class SinglyLinkedList {
  constructor() {
    this.head = null;
    this.tail = null;
    this.length = 0;
  }
}
