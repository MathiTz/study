import Connection from '../../src/infra/database/Connection';
import CouponRepositoryDatabase from '../../src/infra/repository/CouponRepositoryDatabase';
import PgPromise from '../../src/infra/database/PgPromiseAdapter';
import ProductRepositoryDatabase from '../../src/infra/repository/ProductRepositoryDatabase';
import ValidateCoupon from '../../src/application/usecase/ValidateCoupon';

let validateCoupon: ValidateCoupon;
let connection: Connection;
let couponRepository: CouponRepositoryDatabase;

beforeEach(() => {
  connection = new PgPromise();
  couponRepository = new CouponRepositoryDatabase(connection);
  validateCoupon = new ValidateCoupon(couponRepository);
});

afterEach(async () => {
  await connection.close();
});

test('Deve validar um cupom de desconto valido', async () => {
  const input = 'VALE20';

  const output = await validateCoupon.execute(input);
  expect(output).toBeTruthy();
});

test('Deve validar um cupom de desconto expirado', async () => {
  const input = 'VALE50';

  const output = await validateCoupon.execute(input);
  expect(output).toBeFalsy();
});
