
import { Injectable } from '@angular/core';
import { HttpInterceptor, HttpRequest, HttpHandler, HttpEvent } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable()
export class AuthInterceptor implements HttpInterceptor {

    intercept(req: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
        // Suponemos que el token está almacenado en localStorage bajo 'auth_token'
        const token = localStorage.getItem('auth_token');

        if (token) {
            // Clonar la petición y añadir el header Authorization
            const authReq = req.clone({
                setHeaders: {
                    Authorization: `Bearer ${token}`
                }
            });
            return next.handle(authReq);
        }

        // Si no hay token, continuar la petición original
        return next.handle(req);
    }
}
