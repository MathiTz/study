import ExpressAdapter from './infra/http/ExpressAdapter';
import HttpController from './infra/http/HttpController';

import PgPromise from './infra/database/PgPromiseAdapter';

const connection = new PgPromise();
const httpServer = new ExpressAdapter();

new HttpController(httpServer);

httpServer.listen(3003);
