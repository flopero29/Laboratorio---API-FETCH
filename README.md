# Laboratorio – CRUD/API FETCH

## Universidad Tecnológica de Panamá  
**Facultad de Sistemas Computacionales**  
**Licenciatura en Ingeniería de Software**  
**Asignatura:** Ingeniería Web  
**Grupo:** 1SF131  
**Segundo Semestre - Año 2025**

---

## Integrantes
- **Jose Bustamante** – 8-1011-1717  
- **Abigail Koo** – 8-997-974  

**Facilitadora:** Irina Fong  
**Fecha de Ejecución:** 13 de noviembre de 2025  

---

## Introducción
Este proyecto presenta el desarrollo de sistema CRUD completo utilizando Fetch API, PHP Orientado a Objetos y MySQL.   
El desarrollo se realizó bajo el **patrón MVC (Modelo-Vista-Controlador)** y permite la gestión de productos mediante una interfaz web moderna con Bootstrap y SweetAlert2. 

---

## Tecnologías Utilizadas
- **PHP** – Backend con POO
- **MySQL** – Base de datos  
- **Apache** – Servidor web  
- **FETCH API** – Cliente HTTP
- **PDO** – Conexión a base de datos  
- **JSON** – Formato de intercambio de datos  
- **REST** – Arquitectura de la API  
-	**Bootstrap 5** – Framework CSS
-	**SweetAlert2** – Alertas y notificaciones

---

## Metodología Implementada

### Configuración de la Base de Datos
- Se utilizó **MySQL con WAMPP**.  
- Creación de la tabla `productos` con los campos:
  ```sql
  CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(20) NOT NULL,
    producto VARCHAR(100) NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    cantidad INT NOT NULL);

  ```
### Desarrollo de funcionalidades CRUD
| Funcionalidad                     | Descripción                                                               | 
|-------------------------------------|-------------------------------------------------------------------------------|
| GUARDAR     | Crear nuevos productos mediante formulario web | 
| EDITAR | Actualizar productos existentes | 
| BUSCAR       | Buscar productos por código único |
| LISTAR       | Mostrar todos los productos en tabla |
              
### Implementacion técnica
- Programación Orientada a Objetos en PHP

- Patrón Singleton para conexión a base de datos

- Validación tanto en cliente como servidor
  
- Manejo centralizado con estructura Switch

---

## Archivos Principales
| Archivo                   | Descripción                                                   | 
|-------------------------------------|-------------------------------------------------------------------------------|
|conexion.php      | Manejo de conexión a la base de datos (Singleton) | 
| Productos.php      | Modelo del producto con métodos CRUD | 
|registrar.php   | Controlador para insertar datos | 
| index.php     | Página principal del sistema | 
| script.js   | Archivo que maneja Fetch API y eventos de la interfaz | 

---

## Resultados obtenidos
### Funcionalidades implementadas
- **GUARDAR** con validación y respuesta JSON
- **EDITAR** con actualización en tiempo real
- **BUSCAR** por código único
- **LISTAR** productos en tabla dinámica

### Características técnicas
- Validación completa (frontend + backend)
- Comunicación Fetch API asíncrona
- Interfaz responsive con Bootstrap
- Notificaciones elegantes con SweetAlert2
- Uso de PDO para evitar SQL injection

## Códigos de respuesta
| Código              | Descripción                                                   | 
|-------------------------------------|-------------------------------------------------------------------------------|
|200     | Éxito| 
| 400       | Error de validación | 
|404     | Producto no encontrado | 
| 500    | Error del servidor | 


---
## Conclusiones
Este laboratorio demostró la eficiencia de combinar PHP OOP, Fetch API y Bootstrap para desarrollar aplicaciones web modernas y responsivas.
Se logró implementar un CRUD completo con comunicación asíncrona, validaciones robustas y una interfaz intuitiva.
La arquitectura MVC y el enfoque orientado a objetos aseguraron un código ordenado, escalable y fácil de mantener.

