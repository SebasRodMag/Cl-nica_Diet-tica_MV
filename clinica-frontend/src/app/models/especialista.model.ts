import { Usuario } from './usuario.model';

export interface Especialista {
    id_especialista: number;
    id_usuario: number;
    especialidad: string;
    usuario?: Usuario; // Datos del usuario asociado, opcional si se expande la relación
    
}