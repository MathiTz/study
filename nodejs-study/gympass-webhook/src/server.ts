import express from 'express';
import { Router, Request, Response } from 'express';

const app = express();
const route = Router();

app.use(express.json());

route.post('/hook', (req: Request, res: Response) => {
  console.log(req.method, req.url, req);
  // res.json({ message: 'Hello world!' });
  res.send();
});

app.use(route);
app.listen(3333, () => console.log('Server is running on port 3333 👌'));
