# Física en juego

Laboratorio interactivo de física con cinco simuladores: caída libre, movimiento rectilíneo uniforme, mesa de fuerzas, movimiento parabólico y lanzamiento vertical. Cada tema tiene una escena dibujada, variables editables y una tabla que recoge todos los valores de entrada y los resultados calculados por PHP.

## Estructura

```text
Backend/
  Config/       Constantes físicas
  Physics/      Cálculos de cada simulación
  Routes/       API JSON
Frontend/
  Pages/        Interfaz del laboratorio
  Resources/    Apuntes de referencia
  Images/       Ilustraciones SVG
  Assets/       Recursos de interfaz
  CSS/          Estilos separados por propósito
  Scripts/      Navegación y animaciones
Dockerfile.vercel
Caddyfile
index.php       Enrutador y punto de entrada
```

## Ejecutar localmente

Requiere Docker Desktop. Desde esta carpeta:

```powershell
docker build -f Dockerfile.vercel -t fisica-lab .
docker run --rm -p 8080:8080 -e PORT=8080 fisica-lab
```

Abre `http://localhost:8080`.

## Desplegar en Vercel

El proyecto usa FrankenPHP dentro de `Dockerfile.vercel`, porque PHP se ejecuta como contenedor en Vercel. Inicia sesión en Vercel CLI y despliega desde la raíz:

```bash
npm install --global vercel
vercel login
vercel --prod
```

También puedes importar el repositorio en Vercel; el `Dockerfile.vercel` define el servidor PHP y Vercel proporciona la variable `PORT`.

## Supuestos del modelo

- Valor inicial de gravedad: 9.81 m/s²; se puede cambiar en los lanzamientos.
- Se ignora la resistencia del aire.
- Los cálculos y resultados se presentan en unidades SI.
- No hay topes superiores configurados para las variables ni un límite de fuerzas añadidas; se validan las condiciones físicas necesarias para calcular cada ensayo.
- Puedes elegir unidades de longitud (m/km), velocidad (m/s, km/h, km/s) y tiempo (s/min/h). El backend convierte los valores a SI antes de calcular.
- Las escenas aceleran el tiempo simulado y duran como máximo cinco segundos en pantalla.
- El avance de los temas se guarda en el almacenamiento local del navegador.
