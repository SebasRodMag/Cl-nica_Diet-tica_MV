export interface Documento {
    id_documento: number;
    id_historial: number;
    nombre_archivo: string;
    ruta_archivo: string;
    fecha_hora_subida: string; // Formato ISO DateTime
    fecha_hora_ultima_modificacion: string; // Formato ISO DateTime
}