import { Transform } from 'node:stream';
import { pipeline } from 'node:stream/promises';
import { sql } from './db/client.ts';
import { createWriteStream } from 'node:fs';
import { stringify } from 'csv-stringify';

const query = sql`
  SELECT id, name
  FROM products
  WHERE price_in_cents >= 1000
`;

const cursor = query.cursor(10);

// for await (const rows of cursor) {
//   appendFile(
//     './products.txt',
//     rows.map((row) => `${row.id},${row.name}`).join('\n') + '\n'
//   );

//   break;
// }

const exampleStream = new Transform({
  objectMode: true,
  transform(chunk, encoding, callback) {
    for (const item of chunk) {
      this.push(item);
      // this.push(JSON.stringify(item) + '\n');
    }

    callback();
  },
});

console.log('Starting to write to file...');
console.time('write-to-file');

await pipeline(
  cursor,
  exampleStream,
  stringify({
    delimiter: ',',
    header: true,
    columns: [
      { key: 'id', header: 'ID' },
      { key: 'name', header: 'Name' },
    ],
  }),
  createWriteStream('./export.csv', 'utf8')
);

console.timeEnd('write-to-file');
console.log('Finished writing to file');

await sql.end();
