import CouponRepositoryDatabase from './CouponRepositoryDatabase';
import CurrencyGatewayHttp from './CurrencyGatewayHttp';
import ExpressAdapter from './ExpressAdapter';
import HttpController from './HttpController';
import OrderRepositoryDatabase from './OrderRepositoryDatabase';
import PgPromise from './PgPromiseAdapter';
import ProductRepositoryDatabase from './ProductRepositoryDatabase';
import Checkout from './application/usecase/Checkout';

const connection = new PgPromise();
const currencyGateway = new CurrencyGatewayHttp();
const productRepository = new ProductRepositoryDatabase(connection);
const couponRepository = new CouponRepositoryDatabase(connection);
const orderRepository = new OrderRepositoryDatabase(connection);
const checkout = new Checkout(
  currencyGateway,
  productRepository,
  couponRepository,
  orderRepository
);

const httpServer = new ExpressAdapter();
new HttpController(httpServer, checkout);

httpServer.listen(3000);
