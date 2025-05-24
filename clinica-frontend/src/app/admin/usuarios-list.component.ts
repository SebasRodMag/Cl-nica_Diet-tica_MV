import { Component, OnInit } from '@angular/core';
import { UserService, Usuario } from '../service/user.service';
import { CommonModule } from '@angular/common';

@Component({
    selector: 'app-usuarios-list',
    standalone: true,
    imports: [CommonModule],
    templateUrl: './usuarios-list.component.html',
})
export class UsuariosListComponent implements OnInit {
    usuarios: Usuario[] = [];
    cargando: boolean = true;
    error: string = '';

    constructor(private userService: UserService) { }

    ngOnInit(): void {
        this.userService.getUsuarios().subscribe({
            next: (data) => {
                this.usuarios = data;
                this.cargando = false;
            },
            error: (err) => {
                this.error = 'Error al cargar usuarios';
                this.cargando = false;
            }
        });
    }

    editarUsuario(id: number) {
        // Navegar a componente edición, o abrir modal
        console.log('Editar usuario', id);
    }

    eliminarUsuario(id: number) {
        if (confirm('¿Estás seguro de eliminar este usuario?')) {
            // Aquí llamar al servicio para eliminar y actualizar lista
            console.log('Eliminar usuario', id);
        }
    }
}
