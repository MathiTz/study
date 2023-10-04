import CalculateFreight from '../../src/application/usecase/CalculateFreight';

let calculateFreight: CalculateFreight;

beforeEach(() => {
  calculateFreight = new CalculateFreight();
});

test('Deve calcular o frete para um pedido com 3 itens', async () => {
  const input = {
    cpf: '987.654.321-00',
    items: [{ width: 100, height: 30, length: 10, weight: 3, quantity: 2 }],
    from: '222060030',
    to: '88015600',
  };

  const output = await calculateFreight.execute(input);
  expect(output.freight).toBe(60);
});
