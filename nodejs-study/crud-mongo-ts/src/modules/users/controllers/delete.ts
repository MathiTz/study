import { Request, Response } from 'express';
import DeleteUserService from '../services/delete';

class DeleteUser {
  async handler(request: Request, response: Response) {
    try {
      const { id } = request.body;

      await DeleteUserService.execute(id);

      return response.status(204).send();
    } catch (error) {
      return response.status(400).json(error);
    }
  }
}

export default new DeleteUser();
