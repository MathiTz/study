import ZipcodeRepository from '../../src/application/repository/ZipcodeRepository';
import CalculateFreight from '../../src/application/usecase/CalculateFreight';
import Zipcode from '../../src/domain/entity/Zipcode';

let calculateFreight: CalculateFreight;

beforeEach(() => {
  const zipcodeRepository: ZipcodeRepository = {
    async get(code: string): Promise<Zipcode | undefined> {
      if (code === '222060030') {
        return new Zipcode('222060030', '', '', -27.5945, -48.5477);
      }

      if (code === '88015600') {
        return new Zipcode('88015600', '', '', -22.9129, -43.2003);
      }
    },
  };
  calculateFreight = new CalculateFreight(zipcodeRepository);
});

test('Deve calcular o frete para um pedido com 3 itens sem cep de origem e destino', async () => {
  const input = {
    cpf: '987.654.321-00',
    items: [{ width: 100, height: 30, length: 10, weight: 3, quantity: 2 }],
  };

  const output = await calculateFreight.execute(input);
  expect(output.freight).toBe(60);
});

test('Deve calcular o frete para um pedido com 3 itens com cep de origem e destino', async () => {
  const input = {
    cpf: '987.654.321-00',
    items: [{ width: 100, height: 30, length: 10, weight: 3, quantity: 1 }],
    from: '222060030',
    to: '88015600',
  };

  const output = await calculateFreight.execute(input);
  expect(output.freight).toBe(22.446653340244893);
});
