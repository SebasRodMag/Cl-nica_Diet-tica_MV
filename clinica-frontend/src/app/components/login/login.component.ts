import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../../service/Auth-Service/Auth.service';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './login.component.html',
})
export class LoginComponent {
  loginForm: FormGroup;
  errorMessage: string = '';

  constructor(
    private fb: FormBuilder,
    private authService: AuthService,
    private router: Router,
  ) {
    this.loginForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(6)]],
    });
  }

  onSubmit(): void {
  if (this.loginForm.invalid) return;

  const { email, password } = this.loginForm.value;

  this.authService.login(email, password).subscribe({
    next: () => {
      const role = this.authService.getUserRole();
      switch (role) {
        case 'admin':
          this.router.navigate(['/admin']);
          break;
        case 'especialista':
          this.router.navigate(['/especialista']);
          break;
        case 'paciente':
          this.router.navigate(['/paciente']);
          break;
        default:
          this.errorMessage = 'Rol no reconocido';
      }
    },
    error: (err) => {
      this.errorMessage =
        err.error?.message || 'Credenciales incorrectas o error en el servidor';
    },
  });
}
}
