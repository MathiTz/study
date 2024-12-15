import express from 'express';
import cors from 'cors';
import mongoose from 'mongoose';
import { router } from './routes';

const app = express();

app.use(cors());
app.use(express.json());
app.use(router);

mongoose.connect(
  'mongodb://127.0.0.1:27017/test?retryWrites=true&w=majority',
  { useNewUrlParser: true, useUnifiedTopology: true },
  (err) => {
    if (err) {
      console.log(err);

      return;
    }

    app.listen('8080', () => {
      console.log('Server is Running on Port 8080 🚀');
    });
  }
);
