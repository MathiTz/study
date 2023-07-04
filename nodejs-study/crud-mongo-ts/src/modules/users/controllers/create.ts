import { Request, Response } from 'express';
import CreateUserService from '../services/create';

class CreateUser {
  async handler(request: Request, response: Response) {
    try {
      const { name, email, password } = request.body;

      await CreateUserService.execute({ name, email, password });

      return response.status(201).send();
    } catch (error) {
      return response.status(400).json(error);
    }
  }
}

export default new CreateUser();
