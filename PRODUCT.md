# Product

## Register

product

## Users

Operadores de oficina de IPOSTEL, el servicio postal estatal de Venezuela. Trabajan
sentados frente a un monitor de escritorio, bajo luz de oficina, en **turnos largos**:
la misma pantalla durante horas.

No son usuarios casuales. Conocen el dominio postal (valijas, sacas, despachos,
encaminamiento) y usan el sistema como herramienta de trabajo diaria, no como algo
que exploran. Repiten las mismas operaciones muchas veces al día, así que la
velocidad de reconocimiento importa más que la de aprendizaje.

El trabajo a realizar en el módulo de Valijas: crear valijas con destino, encontrar
una valija concreta entre muchas, y alternar entre valijas abiertas y cerradas para
verificar el estado del despacho.

## Product Purpose

Gestión operativa del correo postal nacional: envíos, sacas/valijas, viajes,
encaminamiento e incidencias, más el servicio de telegramas. Es un sistema interno
de registro — el éxito es que el operador complete su tarea sin dudar y sin errores
de captura, no que la pantalla impresione.

Los datos que se registran aquí tienen consecuencia física: una valija mal creada o
un destino equivocado desvía correo real.

## Brand Personality

Institucional, sobrio, confiable. Es un organismo del Estado: la interfaz debe
transmitir formalidad y seriedad, no cercanía comercial ni personalidad de producto.

El color institucional (`#6b1820`, un vino/oxblood oscuro) ya está comprometido en
el sistema de diseño y es el ancla de identidad. No se reemplaza.

## Anti-references

- **Dashboards SaaS efectistas**: tarjetas de métricas gigantes, gradientes,
  ilustraciones decorativas. Aquí no hay nada que vender.
- **Interfaces "amigables" con relleno excesivo**: en turnos largos, el aire de más
  significa más scroll y menos filas visibles por pantalla.
- **Gris claro sobre blanco para texto de datos.** Es el mayor riesgo de fatiga
  visual en jornadas largas y ya aparece en la base Mosaic heredada.
- Ruptura visual con el resto del sistema: esta pantalla debe parecerse a los demás
  módulos, no destacar entre ellos.

## Design Principles

1. **Densidad al servicio del turno largo.** Maximizar filas útiles por pantalla sin
   apretar el texto. El espaciado se gana donde separa grupos, no como relleno
   uniforme.
2. **El estado siempre visible.** Abiertas vs. cerradas es la distinción central del
   módulo; nunca debe deducirse del texto de un botón.
3. **Contraste de lectura, no elegancia pálida.** Texto de datos a ≥4.5:1 real. En
   turnos largos el gris claro se paga en cansancio.
4. **Sin controles mentirosos.** Ningún elemento que parezca accionable si no hace
   nada (los encabezados de orden actuales son botones muertos).
5. **Coherencia sobre novedad.** Reutilizar los componentes y tokens existentes;
   apartarse solo cuando resuelve un problema real de uso.

## Accessibility & Inclusion

- Objetivo WCAG 2.1 AA en contraste de texto y controles.
- Solo modo claro. `darkMode: 'class'` existe en la config de Tailwind heredada de
  la plantilla Mosaic, pero no se usa: no añadir variantes `dark:` nuevas.
- El estado (abierta/cerrada, activo/inactivo) nunca debe comunicarse **solo** por
  color — siempre acompañado de texto o forma.
- Respetar `prefers-reduced-motion` en cualquier transición que se añada.
- Interfaz íntegramente en español de Venezuela.
