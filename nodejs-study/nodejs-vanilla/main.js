const { createServer } = require('http');
const { db } = require('./database');

const stripUrl = (str) => str.split('/');

const server = createServer(async (req, res) => {
  if (req.method === 'GET') {
    const parsedUrl = stripUrl(req.url);

    if (!parsedUrl[1]) {
      db.all('SELECT * FROM todo', (err, rows) => {
        if (err) {
          res.writeHead(500);
          res.end('Internal server error');
        }

        res.writeHead(200, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify(rows));
      });

      return;
    }

    db.all('SELECT * FROM todo WHERE id = ?', [parsedUrl[1]], (err, rows) => {
      if (err) {
        res.writeHead(500);
        res.end('Internal server error');
      }

      res.writeHead(200, { 'Content-Type': 'application/json' });
      res.end(JSON.stringify(rows[0]));

      return;
    });
  }

  if (req.method === 'POST') {
    let body;

    req
      .on('data', (chunk) => {
        body = JSON.parse(Buffer.from(chunk).toString());
      })
      .on('end', () => {
        console.log('Data received: ', body);

        db.all(
          'INSERT INTO todo (id, description, finished) VALUES (?, ?, ?)',
          [null, body.description, false]
        );

        res.writeHead(201, { 'Content-Type': 'application/json' });
        res.end(body);
      });

    return;
  }

  if (req.method === 'PUT') {
  }

  if (req.method === 'PATCH') {
  }

  if (req.method === 'DELETE') {
  }

  console.log(req.method, req.url);
});

server.listen(8080, () => {
  console.log('Server is running!');
});
