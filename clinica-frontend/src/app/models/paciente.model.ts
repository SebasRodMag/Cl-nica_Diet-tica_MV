import { Usuario } from './usuario.model';
import { Especialista } from './especialista.model';

export interface Paciente {
    id_paciente: number;
    id_usuario: number;
    fecha_alta: string; // Formato ISO (ej: "2025-05-24")
    fecha_baja: string | null; // Si no se ha dado de baja puede ser null
    usuario: Usuario; // Datos del usuario asociado, opcional si se expande la relación
    especialista?: Especialista;
    estado: 'activo' | 'inactivo'; // Estado del paciente
}