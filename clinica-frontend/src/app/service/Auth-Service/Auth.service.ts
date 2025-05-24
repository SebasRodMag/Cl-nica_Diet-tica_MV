import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { tap, map } from 'rxjs/operators';
import { Observable } from 'rxjs';

interface LoginResponse {
    token: string;
    user: any;
}

@Injectable({
    providedIn: 'root'
})
export class AuthService {
    private apiUrl = 'http://localhost:8000/api';
    private tokenKey = 'auth_token';

    constructor(private http: HttpClient) { }

    login(email: string, password: string): Observable<any> {
        return this.http.post(`${this.apiUrl}/login`, { email, password }).pipe(
            tap((res: any) => {
                localStorage.setItem('token', res.access_token);
                localStorage.setItem('user', JSON.stringify(res.user));
            })
        );
    }

    logout(): void {
        localStorage.removeItem(this.tokenKey);
    }

    isLoggedIn(): boolean {
        return !!localStorage.getItem(this.tokenKey);
    }

    getToken(): string | null {
        return localStorage.getItem(this.tokenKey);
    }

    getUserRole(): string | null {
        const user = localStorage.getItem('user');
        return user ? JSON.parse(user).rol : null;
    }
}
