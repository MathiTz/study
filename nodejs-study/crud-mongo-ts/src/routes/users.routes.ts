import { Router } from 'express';
import GetUsersController from '../modules/users/controllers/all';
import CreateUserController from '../modules/users/controllers/create';
import DeleteUserController from '../modules/users/controllers/delete';
import FindUserController from '../modules/users/controllers/find';
import UpdateUserController from '../modules/users/controllers/update';
import MakeUserAdminController from '../modules/users/controllers/makeAdmin';

const usersRoutes = Router();

usersRoutes.get('/', GetUsersController.handler);
usersRoutes.get('/find', FindUserController.handler);
usersRoutes.post('/', CreateUserController.handler);
usersRoutes.put('/', UpdateUserController.handler);
usersRoutes.put('/admin', MakeUserAdminController.handler);
usersRoutes.delete('/', DeleteUserController.handler);

export { usersRoutes };
