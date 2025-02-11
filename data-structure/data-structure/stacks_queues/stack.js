const { Node } = require('../node');

// Stacks are LIFO data structures
// Stacks are used to manage function invocations, for undo/redo, for routing (the history object) and for parsing in compilers and interpreters
class Stack {
  constructor() {
    this.first = null;
    this.last = null;
    this.size = 0;
  }

  push(val) {
    const newNode = new Node(val);
    if (!this.first) {
      this.first = newNode;
      this.last = newNode;
    } else {
      const temp = this.first;
      this.first = newNode;
      this.first.next = temp;
    }
    return ++this.size;
  }

  pop() {
    if (!this.first) return null;
    const temp = this.first;
    if (this.first === this.last) this.last = null;

    if (this.size === 1) {
      this.first = null;
      this.last = null;
    } else {
      this.first = temp.next;
      this.size--;
    }

    return temp.val;
  }
}

var stack = new Stack();

stack.push('FIRST');
// stack.push('SECOND');
// stack.push('THIRD');
console.log(stack);
console.log(stack.pop());
console.log(stack);

// stack.pop();
// stack.pop();
