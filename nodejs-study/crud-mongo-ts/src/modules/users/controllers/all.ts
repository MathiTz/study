import { Request, Response } from 'express';
import GetAllUsersService from '../services/all';

class AllUsers {
  async handler(request: Request, response: Response) {
    try {
      const users = await GetAllUsersService.execute();

      return response.json(users);
    } catch (error) {
      return response.status(400).json(error);
    }
  }
}

export default new AllUsers();
