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

  push(val) {
    var newNode = new Node(val);

    if (!this.head) {
      this.head = newNode;
      this.tail = this.head;
    } else {
      this.tail.next = newNode;
      this.tail = newNode;
    }

    this.length++;
    return this;
  }

  // traverse() {
  //   var current = this.head;
  //   while (current) {
  //     console.log(current.val);
  //     current = current.next;
  //   }
  // }

  pop() {
    if (!this.head) return undefined;
    var current = this.head;
    var newTail = current;

    while (current.next) {
      newTail = current;
      current = current.next;
    }

    this.tail = newTail;
    this.tail.next = null;
    this.length--;
    return current;
  }

  shift() {
    if (!this.head) return undefined;
    var current = this.head;
    this.head = current.next;
    this.length--;
    return current;
  }

  unshift(val) {
    var newNode = new Node(val);

    if (!this.head) {
      this.head = newNode;
      this.tail = newNode;
    } else {
      newNode.next = this.head;
      this.head = newNode;
    }

    this.length++;
    return this;
  }

  get(index) {
    if (index < 0 || index >= this.length) return null;

    var counter = 0;
    var node = this.head;

    while (counter !== index) {
      node = node.next;
      counter++;
    }

    return node;
  }

  set(index, val) {
    const node = this.get(index);

    if (!node) return false;

    node.val = val;
    return true;
  }

  insert(index, val) {
    var newNode = new Node(val);
    if (index < 0 || index > this.length) return false;

    if (index === this.length) return !!this.push(newNode);
    if (index === 0) return !!this.unshift(newNode);

    var node = this.get(index - 1);
    var nextNode = node.next;
    node.next = newNode;
    newNode.next = nextNode;
    this.length++;
    return true;
  }

  remove(index) {
    if (index < 0 || index >= this.length) return undefined;
    if (index === 0) return this.shift();
    if (index === this.length - 1) return this.pop();

    var previousNode = this.get(index - 1);
    var removed = previousNode.next;

    previousNode.next = removed.next;
    this.length--;

    return removed;
  }
}

var list = new SinglyLinkedList();
list.push(100);
list.push(200);
list.push(250);
// list.unshift(100);
// list.unshift('h');
// console.log(list.insert(1, 201));
list.remove(1);
console.log(list);
