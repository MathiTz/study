import axios from 'axios';

axios.defaults.validateStatus = function () {
  return true;
};

test('Não deve fazer um pedido com cpf inválido', async () => {
  const input = {
    cpf: '987.654.321-01',
  };

  const response = await axios.post('http://localhost:3000/checkout', input);
  expect(response.status).toBe(422);

  const output = response.data;
  expect(output.message).toBe('Invalid cpf');
});

test('Deve fazer um pedido com 3 produtos', async () => {
  const input = {
    cpf: '987.654.321-00',
    items: [
      { idProduct: 1, quantity: 1 },
      { idProduct: 2, quantity: 1 },
      { idProduct: 3, quantity: 3 },
    ],
  };

  const response = await axios.post('http://localhost:3000/checkout', input);
  const output = response.data;
  expect(output.total).toBe(6090);
});

test('Deve fazer um pedido com 3 produtos com cupom de desconto', async () => {
  const input = {
    cpf: '987.654.321-00',
    items: [
      { idProduct: 1, quantity: 1 },
      { idProduct: 2, quantity: 1 },
      { idProduct: 3, quantity: 3 },
    ],
    coupon: 'VALE20',
  };

  const response = await axios.post('http://localhost:3000/checkout', input);
  const output = response.data;
  expect(output.total).toBe(4872);
});

test('Não deve fazer um pedido onde há itens de quantidade negativa', async () => {
  const input = {
    cpf: '987.654.321-00',
    items: [{ idProduct: 1, quantity: -1 }],
    coupon: 'VALE20',
  };

  const response = await axios.post('http://localhost:3000/checkout', input);
  expect(response.status).toBe(422);

  const output = response.data;
  expect(output.message).toBe('Invalid quantity');
});

test('Não deve fazer um pedido onde o mesmo item foi informado mais de uma vez', async () => {
  const input = {
    cpf: '987.654.321-00',
    items: [
      { idProduct: 1, quantity: 1 },
      { idProduct: 1, quantity: 1 },
    ],
    coupon: 'VALE20',
  };

  const response = await axios.post('http://localhost:3000/checkout', input);
  expect(response.status).toBe(422);

  const output = response.data;
  expect(output.message).toBe('Duplicated products');
});

test('Deve criar um pedido calculando o frete', async () => {
  const input = {
    cpf: '987.654.321-00',
    items: [{ idProduct: 1, quantity: 3 }],
    from: '22060030',
    to: '88015600',
  };

  const response = await axios.post('http://localhost:3000/checkout', input);
  const output = response.data;
  expect(output.freight).toBe(90);
  expect(output.total).toBe(3090);
});

test('Deve criar um pedido com 1 produto calculando o frente com o valor mínimo', async () => {
  const input = {
    cpf: '987.654.321-00',
    items: [{ idProduct: 3, quantity: 1 }],
    from: '22060030',
    to: '88015600',
  };

  const response = await axios.post('http://localhost:3000/checkout', input);
  const output = response.data;
  expect(output.freight).toBe(10);
  expect(output.total).toBe(40);
});

test('Não deve criar um pedido se o produto tiver alguma dimensão negativa', async () => {
  const input = {
    cpf: '987.654.321-00',
    items: [{ idProduct: 4, quantity: 1 }],
    coupon: 'VALE20',
  };

  const response = await axios.post('http://localhost:3000/checkout', input);
  expect(response.status).toBe(422);

  const output = response.data;
  expect(output.message).toBe('Invalid dimension');
});
