import { Request, Response } from 'express';
import MakeUserAdminService from '../services/makeAdmin';

class MakeUserAdmin {
  async handler(request: Request, response: Response) {
    try {
      const { id } = request.body;

      await MakeUserAdminService.execute(id);

      return response.status(200).send();
    } catch (error) {
      return response.status(400).json(error);
    }
  }
}

export default new MakeUserAdmin();
