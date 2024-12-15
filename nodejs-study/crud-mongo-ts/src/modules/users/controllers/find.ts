import { Request, Response } from 'express';
import FindUserService from '../services/find';

class FindUser {
  async handler(request: Request, response: Response) {
    try {
      const { id } = request.body;

      const user = await FindUserService.execute(id);

      return response.json(user);
    } catch (error) {
      return response.status(400).json(error);
    }
  }
}

export default new FindUser();
