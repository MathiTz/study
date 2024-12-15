import mongoose, { Schema } from 'mongoose';

export const User = new Schema(
  {
    name: String,
    email: String,
    hashedPassword: String,
    isAdmin: Boolean,
  },
  {
    timestamps: true,
  }
);

const Users = mongoose.model('users', User);

export { Users };
