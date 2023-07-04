import { Request, Response } from 'express';
import UpdateUserService from '../services/update';

class UpdateUser {
  async handler(request: Request, response: Response) {
    try {
      const { name, email, password } = request.body;

      await UpdateUserService.execute({ name, email, password });

      return response.status(204).send();
    } catch (error) {
      return response.status(400).json(error);
    }
  }
}

export default new UpdateUser();
