import { Users } from '../../../../@types/Users';
import { Users as UsersModel } from '../../../../entities/User';
import { Encrypt } from '../../../../utils/hashPassword';
import { IUserRepository } from '../IUserRepository';

interface IResponseCreate {
  name: string;
  email: string;
  password: string;
}

interface IResponseUpdate {
  name: string;
  email: string;
  password?: string;
}

class UsersRepository implements IUserRepository {
  async index(): Promise<Users[]> {
    try {
      return UsersModel.find({});
    } catch (error) {
      throw new Error('Something wrong, try again later');
    }
  }

  async findById(id: string): Promise<Users> {
    try {
      const user = await UsersModel.findById(id);

      if (!user) throw new Error('User does not exist');

      return user;
    } catch (error) {
      throw new Error('Something wrong, try again later');
    }
  }

  async create({ name, email, password }: IResponseCreate): Promise<void> {
    try {
      const findedUser = await UsersModel.findOne({ email });

      if (findedUser) throw new Error('User already exists');

      const hashedPassword = await Encrypt.cryptPassword(password);

      const user = await UsersModel.create({
        name,
        email,
        hashedPassword,
        isAdmin: false,
      });

      await user.save();
    } catch (error) {
      throw new Error('Something wrong, try again later');
    }
  }

  async update({ name, email, password }: IResponseUpdate): Promise<void> {
    try {
      if (password) {
        const hashedPassword = await Encrypt.cryptPassword(password);
        if (name) {
          return UsersModel.findOneAndUpdate(
            { email },
            { name, hashedPassword },
            { useFindAndModify: false }
          );
        } else {
          return UsersModel.findOneAndUpdate(
            { email },
            { hashedPassword },
            { useFindAndModify: false }
          );
        }
      } else {
        return UsersModel.findOneAndUpdate(
          { email },
          { name },
          { useFindAndModify: false }
        );
      }
    } catch (error) {
      throw new Error('Something wrong, try again later');
    }
  }
  async makeAdmin(id: string): Promise<void> {
    try {
      return UsersModel.findOneAndUpdate(
        { _id: id },
        { isAdmin: true },
        { useFindAndModify: false }
      );
    } catch (error) {
      throw new Error('Something wrong, try again later');
    }
  }

  async delete(id: string): Promise<void> {
    try {
      return UsersModel.findOneAndDelete(
        { _id: id },
        { useFindAndModify: false }
      );
    } catch (error) {
      throw new Error('Something wrong, try again later');
    }
  }
}

export default new UsersRepository();
