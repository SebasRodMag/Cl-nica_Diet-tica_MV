import { CommonModule } from '@angular/common';
import { PacienteEditComponent } from './modal/paciente-edit.component';
import { Paciente } from '../models/paciente.model';
import { Component } from '@angular/core';

@Component({
    selector: 'app-pacientes-list',
    standalone: true,
    imports: [CommonModule, PacienteEditComponent],
    templateUrl: './pacientes-list.component.html',
})
export class PacientesListComponent {
    pacientes: Paciente[] = [
        // Aquí tu lista inicial de pacientes
    ];

    pacienteEditando: Paciente | null = null;

    abrirModal(paciente: Paciente) {
        // Clonamos para no mutar directamente el objeto original
        this.pacienteEditando = { ...paciente };
    }

    actualizar(pacienteActualizado: Paciente) {
        const index = this.pacientes.findIndex(p => p.id_paciente === pacienteActualizado.id_paciente);
        if (index > -1) {
            this.pacientes[index] = { ...pacienteActualizado };
        }
        this.pacienteEditando = null;
    }

    eliminar(id: number) {
        this.pacientes = this.pacientes.filter(p => p.id_paciente !== id);
    }

}
