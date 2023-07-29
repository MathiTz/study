import sinon from 'sinon';
import Checkout from '../src/application/usecase/Checkout';
import GetOrder from '../src/application/usecase/GetOrder';
import CurrencyGatewayHttp from '../src/CurrencyGatewayHttp';
import ProductRepositoryDatabase from '../src/ProductRepositoryDatabase';
import CouponRepositoryDatabase from '../src/CouponRepositoryDatabase';
import CurrencyGateway from '../src/CurrencyGateway';
import ProductRepository from '../src/ProductRepository';

import crypto from 'node:crypto';
import OrderRepositoryDatabase from '../src/OrderRepositoryDatabase';
import Product from '../src/domain/entity/Product';
import PgPromise from '../src/PgPromiseAdapter';
import Connection from '../src/Connection';

let checkout: Checkout;
let getOrder: GetOrder;
let connection: Connection;
let productRepository: ProductRepositoryDatabase;
let couponRepository: CouponRepositoryDatabase;
let orderRepository: OrderRepositoryDatabase;

beforeEach(() => {
  connection = new PgPromise();
  const currencyGateway = new CurrencyGatewayHttp();
  productRepository = new ProductRepositoryDatabase(connection);
  couponRepository = new CouponRepositoryDatabase(connection);
  orderRepository = new OrderRepositoryDatabase(connection);
  checkout = new Checkout(
    currencyGateway,
    productRepository,
    couponRepository,
    orderRepository
  );
  getOrder = new GetOrder(orderRepository);
});

afterEach(async () => {
  await connection.close();
});

test('Não deve fazer um pedido com cpf inválido', async () => {
  const input = {
    cpf: '987.654.321-01',
    items: [],
  };

  await expect(() => checkout.execute(input)).rejects.toThrow(
    new Error('Invalid cpf')
  );
});

test('Deve fazer um pedido com 3 produtos', async () => {
  const uuid = crypto.randomUUID();
  const input = {
    uuid,
    cpf: '987.654.321-00',
    items: [
      { idProduct: 1, quantity: 1 },
      { idProduct: 2, quantity: 1 },
      { idProduct: 3, quantity: 3 },
    ],
  };

  await checkout.execute(input);
  const output = await getOrder.execute(uuid);

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

  const output = await checkout.execute(input);
  expect(output.total).toBe(4872);
});

test('Não deve fazer um pedido onde há itens de quantidade negativa', async () => {
  const input = {
    cpf: '987.654.321-00',
    items: [{ idProduct: 1, quantity: -1 }],
    coupon: 'VALE20',
  };

  await expect(() => checkout.execute(input)).rejects.toThrow(
    new Error('Invalid quantity')
  );
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

  await expect(() => checkout.execute(input)).rejects.toThrow(
    new Error('Duplicated products')
  );
});

test('Deve criar um pedido calculando o frete', async () => {
  const input = {
    cpf: '987.654.321-00',
    items: [{ idProduct: 1, quantity: 3 }],
    from: '22060030',
    to: '88015600',
  };

  const output = await checkout.execute(input);

  expect(output.freight).toBe(90);
  expect(output.total).toBe(3090);
});

test('Não deve criar um pedido se o produto tiver alguma dimensão negativa', async () => {
  const input = {
    cpf: '987.654.321-00',
    items: [{ idProduct: 4, quantity: 1 }],
    coupon: 'VALE20',
  };

  await expect(() => checkout.execute(input)).rejects.toThrow(
    new Error('Invalid dimension')
  );
});

test('Deve criar um pedido com 1 produto calculando o frente com o valor mínimo', async () => {
  const input = {
    cpf: '987.654.321-00',
    items: [{ idProduct: 3, quantity: 1 }],
    from: '22060030',
    to: '88015600',
  };

  const output = await checkout.execute(input);
  expect(output.freight).toBe(10);
  expect(output.total).toBe(40);
});

test('Deve fazer um pedido com 1 produto em dólar usando um stub', async () => {
  const stubCurrencyGateway = sinon
    .stub(CurrencyGatewayHttp.prototype, 'getCurrencies')
    .resolves({
      usd: 3,
    });
  const stubProductRepository = sinon
    .stub(ProductRepositoryDatabase.prototype, 'getProduct')
    .resolves(new Product(5, 'A', 1000, 10, 10, 10, 10, 'USD'));
  const input = {
    cpf: '987.654.321-00',
    items: [{ idProduct: 5, quantity: 1 }],
  };

  const output = await checkout.execute(input);
  expect(output.total).toBe(3000);
  stubCurrencyGateway.restore();
  stubProductRepository.restore();
});

test('Deve fazer um pedido com 3 produtos com cupom de desconto com spy', async () => {
  const spyProductRepository = sinon.spy(
    ProductRepositoryDatabase.prototype,
    'getProduct'
  );
  const spyCouponRepository = sinon.spy(
    CouponRepositoryDatabase.prototype,
    'getCoupon'
  );
  const input = {
    cpf: '987.654.321-00',
    items: [
      { idProduct: 1, quantity: 1 },
      { idProduct: 2, quantity: 1 },
      { idProduct: 3, quantity: 3 },
    ],
    coupon: 'VALE20',
  };

  const output = await checkout.execute(input);
  expect(output.total).toBe(4872);
  expect(spyCouponRepository.calledOnce).toBeTruthy();
  expect(spyCouponRepository.calledWith('VALE20')).toBeTruthy();
  expect(spyProductRepository.calledThrice).toBeTruthy();
  spyCouponRepository.restore();
  spyProductRepository.restore();
});

test('Deve fazer um pedido com 1 produto em dólar usando mock', async () => {
  const mockCurrencyGateway = sinon.mock(CurrencyGatewayHttp.prototype);
  mockCurrencyGateway.expects('getCurrencies').atLeast(1).resolves({
    usd: 3,
  });

  const input = {
    cpf: '987.654.321-00',
    items: [{ idProduct: 5, quantity: 1 }],
  };

  const output = await checkout.execute(input);
  expect(output.total).toBe(3000);
  mockCurrencyGateway.verify();
});

test('Deve fazer um pedido com 1 produto em dólar usando um stub', async () => {
  const currencyGateway: CurrencyGateway = {
    async getCurrencies(): Promise<any> {
      return {
        usd: 3,
      };
    },
  };
  const productRepository: ProductRepository = {
    async getProduct(idProduct: number): Promise<any> {
      return new Product(idProduct, 'A', 1000, 10, 10, 10, 10, 'USD');
    },
  };
  checkout = new Checkout(
    currencyGateway,
    productRepository,
    couponRepository,
    orderRepository
  );
  const input = {
    cpf: '987.654.321-00',
    items: [{ idProduct: 6, quantity: 1 }],
  };

  const output = await checkout.execute(input);
  expect(output.total).toBe(3000);
});

test('Deve fazer um pedido e verificar o código de série', async () => {
  const stub = sinon
    .stub(OrderRepositoryDatabase.prototype, 'count')
    .resolves(1);
  const uuid = crypto.randomUUID();
  const input = {
    uuid,
    cpf: '987.654.321-00',
    items: [
      { idProduct: 1, quantity: 1 },
      { idProduct: 2, quantity: 1 },
      { idProduct: 3, quantity: 3 },
    ],
  };

  await checkout.execute(input);
  const output = await getOrder.execute(uuid);

  expect(output.code).toBe('202300000001');
  stub.restore();
});
