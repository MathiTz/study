import {
  NestInterceptor,
  ExecutionContext,
  CallHandler,
  Injectable,
  ConflictException,
} from '@nestjs/common';
import { Observable, catchError } from 'rxjs';
import { ConflictError } from '../types/ConflictError';

@Injectable()
export class ConflictInterceptor implements NestInterceptor {
  intercept(context: ExecutionContext, next: CallHandler): Observable<any> {
    // next.handle() is an Observable of the controller's result value
    return next.handle().pipe(
      catchError(error => {
        if (error instanceof ConflictError) {
          throw new ConflictException(error.message);
        } else {
          throw error;
        }
      }),
    );
  }
}
