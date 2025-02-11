const q = [];

// FIFO - First In First Out
q.push(1);
q.push(2);
q.push(3);
console.log(q);
q.shift();
console.log(q);

q.unshift(1);
q.unshift(2);
q.unshift(3);
console.log(q);
q.pop();
console.log(q);
