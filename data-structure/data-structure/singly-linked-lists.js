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
    const newNode = new Node(val);

    if (!this.head) {
      this.head = newNode;
      this.tail = newNode;
    } else {
      this.tail.next = newNode;
      this.tail = newNode;
    }
  }
}

// var first = new Node("Hi");
// first.next = new Node("There");
// first.next.next = new Node("How");
// first.next.next.next = new Node("Are");
// first.next.next.next.next = new Node("You");

(async () => {
  const res = await fetch(
    "https://resultados.tse.jus.br/oficial/ele2022/545/dados-simplificados/br/br-c0001-e000545-r.json"
  );

  console.log(res);

  const resInJson = await res.json();

  console.log(resInJson.cand[1].pvap);
})();
