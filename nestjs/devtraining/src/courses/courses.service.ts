import { Inject, Injectable, NotFoundException } from '@nestjs/common';
import { Repository } from 'typeorm';
import { CreateCourseDto } from './dto/create-course.dto';
import { UpdateCourseDto } from './dto/update-course.dto';
import { CourseEntity } from './entities/course.entity';
import { TagEntity } from './entities/tag.entity';

@Injectable()
export class CoursesService {
  @Inject('COURSE_REPOSITORY')
  private courseRepository: Repository<CourseEntity>;

  @Inject('TAG_REPOSITORY')
  private tagRepository: Repository<TagEntity>;

  findAll() {
    return this.courseRepository.find({
      relations: ['tags'],
    });
  }

  async findOne(id: string) {
    const course = await this.courseRepository.findOne({
      where: {
        id,
      },
      relations: ['tags'],
    });

    if (!course) throw new NotFoundException(`Course ID ${id} not found`);

    return course;
  }

  async create(createCourseDto: CreateCourseDto) {
    const tags = await Promise.all(
      createCourseDto.tags.map((name) => this.preloadTagByName(name)),
    );
    const course = this.courseRepository.create({ ...createCourseDto, tags });

    return this.courseRepository.save(course);
  }

  async update(id: string, updateCourseDto: UpdateCourseDto) {
    const tags =
      updateCourseDto.tags &&
      (await Promise.all(
        updateCourseDto.tags.map((name) => this.preloadTagByName(name)),
      ));

    const existedCourse = await this.courseRepository.preload({
      id,
      ...updateCourseDto,
      tags,
    });

    if (!existedCourse)
      throw new NotFoundException(`
    Course ID ${id} not found
    `);

    return this.courseRepository.save(existedCourse);
  }

  async remove(id: string) {
    const course = await this.courseRepository.findOne({
      where: {
        id,
      },
    });

    if (!course)
      throw new NotFoundException(`
  Course ID ${id} not found
  `);

    return this.courseRepository.remove(course);
  }

  private async preloadTagByName(name: string): Promise<TagEntity> {
    const tag = await this.tagRepository.findOne({ where: { name } });

    if (tag) {
      return tag;
    }

    return this.tagRepository.create({ name });
  }
}
