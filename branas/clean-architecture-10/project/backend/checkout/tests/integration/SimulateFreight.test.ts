import Connection from '../../src/infra/database/Connection';
import PgPromise from '../../src/infra/database/PgPromiseAdapter';
import ProductRepositoryDatabase from '../../src/infra/repository/ProductRepositoryDatabase';
import SimulateFreight from '../../src/application/usecase/SimulateFreight';

let simulateFreight: SimulateFreight;
let connection: Connection;
let productRepository: ProductRepositoryDatabase;

beforeEach(() => {
  connection = new PgPromise();
  productRepository = new ProductRepositoryDatabase(connection);
  simulateFreight = new SimulateFreight(productRepository);
});

afterEach(async () => {
  await connection.close();
});

test('Deve calcular o frete para um pedido com 3 itens', async () => {
  const input = {
    cpf: '987.654.321-00',
    items: [
      { idProduct: 1, quantity: 1 },
      { idProduct: 2, quantity: 1 },
      { idProduct: 3, quantity: 3 },
    ],
    from: '222060030',
    to: '88015600',
  };

  const output = await simulateFreight.execute(input);
  expect(output.freight).toBe(280);
});
