import { Usuario } from './usuario.model';
import { Especialista } from './especialista.model';

export interface Paciente {
    id_paciente: number;
    id_usuario: number;
    fecha_alta: string;
    fecha_baja: string | null;
    usuario: Usuario;
    especialista?: Especialista;
    estado: 'activo' | 'inactivo';
}