export type RolUsuario = 'paciente' | 'especialista' | 'usuario' | 'administrador';

export interface Usuario {
    id_usuario: number;
    nombre: string;
    apellidos: string;
    dni_usuario: string;
    email: string;
    fecha_nacimiento: string; // Formato ISO (ej: "2025-05-24")
    telefono: string;
    rol: RolUsuario;
    fecha_creacion: string;
    fecha_actualizacion: string;
}