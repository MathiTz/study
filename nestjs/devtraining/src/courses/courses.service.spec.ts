import { NotFoundException } from '@nestjs/common';
import { randomUUID } from 'crypto';
import { CoursesService } from './courses.service';
import { CreateCourseDto } from './dto/create-course.dto';
import { UpdateCourseDto } from './dto/update-course.dto';

// type MockRepository<T = any> = Partial<Record<keyof Repository<T>, jest.Mock>>;

// const createMockRepository = <T = any>(): MockRepository<T> => ({
//   findOne: jest.fn(),
// });

describe('CoursesService', () => {
  let service: CoursesService;
  let id;
  let date;

  beforeEach(async () => {
    // const module: TestingModule = await Test.createTestingModule({
    //   providers: [
    //     CoursesService,
    //     { provide: Connection, useValue: {} },
    //     {
    //       provide: getRepositoryToken(CourseEntity),
    //       useValue: createMockRepository(),
    //     },
    //     {
    //       provide: getRepositoryToken(TagEntity),
    //       useValue: createMockRepository(),
    //     },
    //   ],
    // }).compile();

    // service = module.get<CoursesService>(CoursesService);
    // courseRepository = module.get<MockRepository>(
    //   getRepositoryToken(CourseEntity),
    // );

    service = new CoursesService();
    id = randomUUID();
    date = new Date();
  });

  it('should be defined', () => {
    expect(service).toBeDefined();
  });

  it('should create a course', async () => {
    const expectOutputTags = [
      {
        id,
        name: 'nestjs',
        created_at: date,
      },
    ];

    const expectOutputCourse = {
      id,
      name: 'test',
      description: 'Test description',
      created_at: date,
      tags: expectOutputTags,
    };

    const mockCourseRepository = {
      create: jest.fn().mockReturnValue(Promise.resolve(expectOutputCourse)),
      save: jest.fn().mockReturnValue(Promise.resolve(expectOutputCourse)),
    };

    const mockTagRepository = {
      create: jest.fn().mockReturnValue(Promise.resolve(expectOutputTags)),
      findOne: jest.fn(),
    };

    // @ts-expect-error defined parts
    service['courseRepository'] = mockCourseRepository;
    // @ts-expect-error defined parts
    service['tagRepository'] = mockTagRepository;

    const createCourseDto: CreateCourseDto = {
      name: 'Test',
      description: 'Test description',
      tags: ['nestjs'],
    };

    const newCourse = await service.create(createCourseDto);

    expect(mockCourseRepository.save).toHaveBeenCalled();
    expect(expectOutputCourse).toStrictEqual(newCourse);
  });

  it('should list all courses', async () => {
    const expectOutputTags = [
      {
        id,
        name: 'nestjs',
        created_at: date,
      },
    ];

    const expectOutputCourse = [
      {
        id,
        name: 'test',
        description: 'Test description',
        created_at: date,
        tags: expectOutputTags,
      },
    ];

    const mockCourseRepository = {
      findAll: jest.fn().mockReturnValue(Promise.resolve(expectOutputCourse)),
      find: jest.fn().mockReturnValue(Promise.resolve(expectOutputCourse)),
    };

    // @ts-expect-error defined parts
    service['courseRepository'] = mockCourseRepository;

    const courses = await service.findAll();

    expect(mockCourseRepository.find).toHaveBeenCalled();
    expect(expectOutputCourse).toStrictEqual(courses);
  });

  it('should get a course', async () => {
    const expectOutputTags = [
      {
        id,
        name: 'nestjs',
        created_at: date,
      },
    ];

    const expectOutputCourse = [
      {
        id,
        name: 'test',
        description: 'Test description',
        created_at: date,
        tags: expectOutputTags,
      },
    ];

    const mockCourseRepository = {
      findOne: jest.fn().mockReturnValue(Promise.resolve(expectOutputCourse)),
    };

    // @ts-expect-error defined parts
    service['courseRepository'] = mockCourseRepository;

    const course = await service.findOne(id);

    expect(mockCourseRepository.findOne).toHaveBeenCalled();
    expect(expectOutputCourse).toStrictEqual(course);
  });

  it('should update a course', async () => {
    const expectOutputTags = [
      {
        id,
        name: 'nestjs',
        created_at: date,
      },
    ];

    const expectOutputCourse = {
      id,
      name: 'test',
      description: 'Test description',
      created_at: date,
      tags: expectOutputTags,
    };

    const mockCourseRepository = {
      update: jest.fn().mockReturnValue(Promise.resolve(expectOutputCourse)),
      save: jest.fn().mockReturnValue(Promise.resolve(expectOutputCourse)),
      preload: jest.fn().mockReturnValue(Promise.resolve(expectOutputCourse)),
    };

    const mockTagRepository = {
      create: jest.fn().mockReturnValue(Promise.resolve(expectOutputTags)),
      findOne: jest.fn(),
    };

    // @ts-expect-error defined parts
    service['courseRepository'] = mockCourseRepository;
    // @ts-expect-error defined parts
    service['tagRepository'] = mockTagRepository;

    const updateCourseDto: UpdateCourseDto = {
      name: 'Test',
      description: 'Test description',
      tags: ['nestjs'],
    };

    const course = await service.update(id, updateCourseDto);

    expect(mockCourseRepository.save).toHaveBeenCalled();
    expect(expectOutputCourse).toStrictEqual(course);
  });

  it('should delete a course', async () => {
    const expectOutputTags = [
      {
        id,
        name: 'nestjs',
        created_at: date,
      },
    ];

    const expectOutputCourse = [
      {
        id,
        name: 'test',
        description: 'Test description',
        created_at: date,
        tags: expectOutputTags,
      },
    ];

    const mockCourseRepository = {
      findOne: jest.fn().mockReturnValue(Promise.resolve(expectOutputCourse)),
      remove: jest.fn().mockReturnValue(Promise.resolve(expectOutputCourse)),
    };

    // @ts-expect-error defined parts
    service['courseRepository'] = mockCourseRepository;

    const course = await service.remove(id);

    expect(mockCourseRepository.remove).toHaveBeenCalled();
    expect(expectOutputCourse).toStrictEqual(course);
  });
});
