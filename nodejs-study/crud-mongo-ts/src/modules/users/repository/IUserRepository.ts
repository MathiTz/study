import { User } from '../../../@types/Users';

interface DTOUser {
  name: string;
  email: string;
  password: string;
}

export interface IUserRepository {
  index(): Promise<User[]>;
  findById(id: string): Promise<User>;
  create({ name, email }: DTOUser): Promise<void>;
  update({ name, email, password }: DTOUser): Promise<void>;
  makeAdmin(id: string): Promise<void>;
  delete(id: string): Promise<void>;
}
