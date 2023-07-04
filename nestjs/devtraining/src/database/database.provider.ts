import { CreateCoursesTable1661655670414 } from 'src/migrations/1661655670414-CreateCoursesTable';
import { CreateTagsTable1661656658325 } from 'src/migrations/1661656658325-CreateTagsTable';
import { CreateCoursesTagsTable1661658454106 } from 'src/migrations/1661658454106-CreateCoursesTagsTable';
import { AddCoursesIdToCoursesTagsTable1661658662144 } from 'src/migrations/1661658662144-AddCoursesIdToCoursesTagsTable';
import { AddTagsIdToCoursesTagsTable1661658924135 } from 'src/migrations/1661658924135-AddTagsIdToCoursesTagsTable';
import { DataSource } from 'typeorm';

export const databaseProviders = [
  {
    provide: 'DATA_SOURCE',
    useFactory: async () => {
      const dataSource = new DataSource({
        type: 'postgres',
        host: 'db',
        port: 5432,
        username: 'postgres',
        password: 'docker',
        database: 'cursonestjs',
        entities: [__dirname + '/../**/*.entity.js'],
        synchronize: false,
      });

      return dataSource.initialize();
    },
  },
];

export const dataSource = new DataSource({
  type: 'postgres',
  host: 'localhost',
  port: 5432,
  username: 'postgres',
  password: 'docker',
  database: 'cursonestjs',
  entities: [__dirname + '/../**/*.entity.js'],
  synchronize: false,
  migrations: [
    CreateCoursesTable1661655670414,
    CreateTagsTable1661656658325,
    CreateCoursesTagsTable1661658454106,
    AddCoursesIdToCoursesTagsTable1661658662144,
    AddTagsIdToCoursesTagsTable1661658924135,
  ],
});
