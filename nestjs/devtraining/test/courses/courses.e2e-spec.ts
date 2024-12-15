import { Test, TestingModule } from '@nestjs/testing';
import { HttpStatus, INestApplication, ValidationPipe } from '@nestjs/common';
import * as request from 'supertest';
import { CoursesModule } from '../../src/courses/courses.module';
import { TypeOrmModule } from '@nestjs/typeorm';
import { CreateCourseDto } from '../../src/courses/dto/create-course.dto';

describe('Courses: /courses', () => {
  let app: INestApplication;

  const course = {
    name: 'Nestjs com TypeORM',
    description: 'Criando apis restful com nestjs',
    tags: ['nestjs', 'typeorm', 'javascript', 'typescript'],
  };

  beforeAll(async () => {
    const moduleFixture: TestingModule = await Test.createTestingModule({
      imports: [
        CoursesModule,
        TypeOrmModule.forRoot({
          type: 'postgres',
          host: 'localhost',
          port: 5433,
          username: 'postgres',
          password: 'docker',
          database: 'testdb',
          synchronize: true,
          autoLoadEntities: true,
        }),
      ],
    }).compile();

    app = moduleFixture.createNestApplication();
    app.useGlobalPipes(
      new ValidationPipe({
        // Garante que qualquer outro atributo não listado
        // não vai ser enviado na resposta
        whitelist: true,
        // Garante que qualquer outro atributo não listado
        // a requisição não vai ser completada
        forbidNonWhitelisted: true,
        // Transforma o objeto de informações enviada
        // com o DTO
        transform: true,
      }),
    );

    await app.init();
  });

  it('Create POST /courses', () => {
    return request(app.getHttpServer())
      .post('/courses')
      .send(course as CreateCourseDto)
      .expect(HttpStatus.CREATED)
      .then(({ body }) => {
        const expectedCourse = jasmine.objectContaining({
          ...course,
          tags: jasmine.arrayContaining(
            course.tags.map((name) => jasmine.objectContaining({ name })),
          ),
        });

        expect(body).toEqual(expectedCourse);
      });
  });

  afterAll(async () => {
    await app.close();
  });
});
