import { ValidationPipe } from '@nestjs/common';
import { NestFactory } from '@nestjs/core';
import { AppModule } from './app.module';

async function bootstrap() {
  const app = await NestFactory.create(AppModule);

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

  await app.listen(3000);
}
bootstrap();
