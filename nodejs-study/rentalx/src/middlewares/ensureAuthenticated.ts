import { NextFunction, Request, Response } from 'express';
import { verify } from 'jsonwebtoken';

import { AppError } from '../errors/AppError';
import { UsersRepository } from '../modules/accounts/repositories/implementations/UsersRepository';

export async function ensureAuthenticated(
  request: Request,
  response: Response,
  next: NextFunction
): Promise<void> {
  const authHeader = request.headers.authorization;

  if (!authHeader) throw new AppError('Token missing', 401);

  const [, token] = authHeader.split(' ');

  try {
    const { sub: user_id } = verify(
      token,
      '7e1019ceb67350ff8c7f75700d54bec6f8ae06ad'
    );

    const usersRepository = new UsersRepository();

    const user = await usersRepository.findById(user_id as string);

    if (!user) throw new AppError('User does not exists!', 401);

    request.user = {
      id: user_id as string,
    };

    next();
  } catch {
    throw new AppError('Invalid token!', 401);
  }
}
