# **Advanced Report Tool Suite (ARTS)**

ARTS (Advanced Report Tool Suite) es un plugin para OJS 3.4+ diseñado para permitir a usuarios sin conocimientos de programación generar informes avanzados utilizando archivos de configuración YAML flexibles y/o accediendo directamente a los métodos DAO de OJS.

### **Casos de Uso**

- Cuando los informes integrados de OJS no proporcionan los datos requeridos.
- Creación de indicadores de calidad y monitoreo.
- Realización de extracciones de datos automatizadas y periódicas.
- Generación de informes que evolucionan con el tiempo.
- Alimentar aplicaciones externas con datos provenientes de OJS.

### **Descripción Funcional del Plugin**

El plugin ofrece dos funcionalidades principales:

1. **Generación de Informes de Uso General**  
   Lee archivos de configuración YAML y genera informes en **JSON**, **HTML** o **CSV/ZIP**.  
   Cada informe define qué datos obtener, cómo procesarlos y cómo presentarlos.  
   El plugin expone un endpoint API para cada informe configurado, devolviendo los resultados según la consulta definida.

2. **Exposición Directa de Métodos DAO de OJS**  
   El plugin expone automáticamente todos los métodos públicos de los Data Access Objects (DAO) de OJS 3.4+ en formato **JSON**, lo que permite realizar consultas de datos unificadas a través de la API del plugin.  
   La transición progresiva hacia Eloquent ORM está en curso en OJS 3.5.

> ⚠️ **Aviso de Seguridad**
>
> El acceso a los informes y endpoints de la API requiere privilegios específicos de usuario o el token correspondiente. Actualmente, el plugin no puede acceder a los tokens de PKP, por lo que la **APIkey** se almacena directamente dentro del directorio del plugin.  
> Asegúrate de gestionar este token de forma segura para evitar accesos no autorizados.  
> Para configurarlo, renombra el archivo `TEMPLATE.env` a `.env` y añade tu token.

### **Configuración de Informes**

Cada informe incluye una sección de encabezado `config` para definir su nivel de seguridad, formato de salida y plantilla.  
La sección `data` especifica una lista de operaciones a ejecutar, parámetros y campos a devolver.

**Ejemplos de Informes:**

- Un clon del [plugin FECYT](https://github.com/MejorAbierta/fecytReportsPlugin): produce resultados similares al plugin FECYT utilizando llamadas DAO ([ver configuración aquí](https://github.com/MejorAbierta/ARTSPlugin/blob/main/configs/sellofecythtml.yaml)).
- Un informe general de **transparencia**: proporciona estado editorial y métricas mediante llamadas DAO y ejecución de SQL arbitrario ([ver configuración aquí](https://github.com/MejorAbierta/ARTSPlugin/blob/main/configs/transparencia_html.yaml)).

---

## **Instrucciones de Instalación**

**Método Recomendado (Galería de Plugins):**

- Inicia sesión con privilegios de administrador.
- Navega a `Configuración > Sitio web > Plugins`.
- Ve a las [versiones del plugin](https://github.com/MejorAbierta/ARTSPlugin/releases) y descarga el archivo “arts.tar.gz” correspondiente a tu versión de OJS.
- Haz clic en “Subir un nuevo plugin” y selecciona el archivo descargado.
- Habilita el plugin y pulsa el botón “Configuración” para acceder al frontend del plugin.

**Instalación Manual:**

```
1. Ve a [https://github.com/MejorAbierta/ARTSPlugin/releases](https://github.com/MejorAbierta/ARTSPlugin/releases) y descarga el archivo “arts.tar.gz” correspondiente a tu versión de OJS.
2. Extrae los archivos en plugins/generic/arts.
3. Ejecuta:
   php lib/pkp/tools/installPluginVersion.php plugins/generic/ARTS/version.xml
4. Habilita el plugin y visita /arts para ver tus informes.
```

---

## **Configuración YAML Sencilla**

```
report:
  config:
    name: Reporte
    description: Descripción del reporte
    authorization: role
    format: json

  data:
    - id: 01
      title: Nombre
      operation: userGroup
      params: 1
      output:
        fields: title
```

Ver otros ejemplos [aquí](#).

**Salida JSON Esperada**

```
{
  "data": [
    {
      "id": "01",
      "title": "Editores"
    }
  ]
}
```

---

# **Operaciones**

Estos son los métodos de la API que pueden llamarse directamente a través de la API de ARTS o referenciarse dentro de los archivos YAML como entradas `operation`.  
Exponen datos centrales de OJS y permiten la recuperación estructurada de diversas entidades.  
Los métodos están ordenados alfabéticamente como referencia.

| Operación | Salida | Campos |
|-----------|-------------|---|
| about | Información “Acerca de” de la revista. | ninguno |
| announcement | Datos de anuncios. | `announcement_id`, `announcement_settings`, `assoc_type`, `assoc_id`, `type_id`, `announcement_types`, `date_expire`, `date_posted` |
| author | Datos de autores. | `id`, `givenName`, `familyName`, `email`, `publicationId`, `userGroupId`, `country`, `affiliation`. |
| category | Datos de categorías. | `id`, `title`, `description`, `parentId`, `contextId`, `sequence`, `path`, `image`, `sortOption`. |
| decision | Datos de decisiones editoriales. | `id`, `dateDecided`, `decision`, `editorId`, `reviewRoundId`, `round`, `stageId`, `submissionId`. |
| galleys | Datos de galeradas. | `id`, `submissionFileId`, `isApproved`, `locale`, `label`, `publicationId`, `urlPath`, `urlRemote`, `doiId`. |
| institution | Datos de instituciones. | `institution_ip`, `institution_settings`, `institutional_subscriptions`, `metrics_counter_submission_institution_daily`, `metrics_counter_submission_institution_monthly`, `usage_stats_institution_temporary_records` |
| issues | Datos de números/volúmenes. | `id`, `journalId`, `volume`, `number`, `year`, `published`, `datePublished`, `dateNotified`, `lastModified`, `accessStatus`, `openAccessDate`, `showVolume`, `showNumber`, `showYear`, `showTitle`, `styleFileName`, `originalStyleFileName`, `urlPath`, `doiId`, `description`, `title`. |
| journalIdentity | Datos de identidad de la revista. | `id`, `urlPath`, `enabled`, `primaryLocale`, `currentIssueId`, `acronym`, `authorGuidelines`, `contactEmail`, `editorialTeam`, `licenseUrl`, `onlineIssn`, `printIssn`, y versión de OJS. |
| publications | Datos de publicaciones. | `id`, `accessStatus`, `datePublished`, `lastModified`, `primaryContactId`, `sectionId`, `submissionId`, `status`, `urlPath`, `version`, `doiId`, `categoryIds`, `copyrightYear`, `issueId`, `abstract`, `title`, `locale`, `authors`, `keywords`, `subjects`, `disciplines`, `languages`, `supportingAgencies`, `galleys`. |
| representation | Datos de representaciones. | `submissionFileId`, `isApproved`, `locale`, `label`, `publicationId`, `urlPath`, `urlRemote`, `doiId`. |
| reviews | Datos de revisiones. | `review_round_file_id`, `submission_id`, `submissions`, `review_round_id`, `review_rounds`, `stage_id`, `submission_file_id` |
| users | Datos de usuarios. | `id`, `givenName`, `familyName`, `email`, `publicationId`, `userGroupId`, `country`, `affiliation`. |
| section | Datos de secciones. | `id`, `contextId`, `reviewFormId`, `sequence`, `editorRestricted`, `metaIndexed`, `metaReviewed`, `abstractsNotRequired`, `hideTitle`, `hideAuthor`, `isInactive`, `wordCount`, `abbrev`, `policy`, `title`. |
| submissionFile | Datos de archivos de envío. | `assocId`, `assocType`, `createdAt`, `fileId`, `fileStage`, `genreId`, `sourceSubmissionFileId`, `submissionId`, `updatedAt`, `uploaderUserId`, `viewable`, `dateCreated`, `language`, `name`, `locale`, `path`, `mimetype`. |
| submissions | Datos de envíos. | `id`, `contextId`, `currentPublicationId`, `dateLastActivity`, `dateSubmitted`, `lastModified`, `locale`, `stageId`, `status`, `submissionProgress`, `publications`. |
| urls | URLs de la revista. | — |
| userGroup | Datos de grupos de usuarios. | `user_group_id`, `context_id`, `role_id`, `is_default`, `show_title`, `permit_self_registration`, `permit_metadata_edit`. |

---

## **Operaciones Genéricas: DAO y doQuery**

- **DAO**: Llama dinámicamente cualquier método público de cualquier clase DAO de OJS.  
  Requiere especificar `dao` (nombre de la clase DAO), `method` (nombre del método) y opcionalmente `params` (matriz de argumentos).  
  Consulta la [documentación PKP](https://docs.pkp.sfu.ca/dev/documentation/3.3/en/architecture-database#daos) para más información.  
  El plugin invocará el método DAO y devolverá el resultado en JSON.  
  Esta función es útil para consultas avanzadas no expuestas mediante operaciones estándar, pero ten en cuenta que PKP está migrando a Eloquent ORM en OJS 3.5, por lo que esta característica será pronto obsoleta. ([Ver doc.](https://docs.pkp.sfu.ca/dev/documentation/en/architecture-daos#deprecated-daos))

- **doQuery**: Ejecuta una consulta `SQL SELECT` arbitraria directamente sobre la base de datos.  
  Está intencionadamente limitada a consultas `SELECT` para evitar problemas de seguridad.  
  El SQL se proporciona en el campo `data.params` del YAML. Ten en cuenta que las demás secciones del YAML (como `data` u `output`) seguirán funcionando sobre los datos seleccionados.  
  **Advertencia:** El SQL depende de la base de datos (MariaDB, MySQL, PostgreSQL), por lo que las consultas deben adaptarse al motor utilizado.

Estas operaciones genéricas ofrecen la máxima flexibilidad, pero requieren un entendimiento del modelo DAO de OJS y de su esquema de base de datos.

---

## **Plantillas**

Al seleccionar el formato HTML, puedes especificar qué plantilla usar. Esto es útil para presentar el contenido con un diseño que mejor se adapte a tus necesidades.

Las plantillas están escritas en **Smarty 3** (el sistema de plantillas predeterminado en OJS 3.4) y se almacenan en el directorio `templates/arts` del plugin.  
Dado que OJS migrará pronto al sistema **Blade**, estas plantillas se adaptarán en consecuencia. Tenlo en cuenta si planeas crear las tuyas propias.

Se incluyen tres plantillas iniciales básicas:
- [`default.tpl`](#): Muestra todos los datos con un estilo mínimo (útil para depuración).
- [`fecyt.tpl`](#): Presenta un diseño más amigable para el informe FECYT.
- [`transparencia.tpl`](#): Presenta un diseño más amigable para el informe de transparencia.

---

## **Hoja de Ruta**

- [x] Salida JSON
- [x] Salida HTML
- [x] Salida XML
- [x] Filtro por campo
- [x] Interfaz básica
- [x] SQL arbitrario
- [ ] Comprobar compatibilidad de acciones DAO de OJS 3.4 con OJS 3.5 (ORM)
- [ ] Añadir interfaz Swagger UI
- [ ] Mejorar la interfaz para crear archivos YAML
- [ ] Integración con las API keys de OJS
- [ ] Permitir scripts PHP
- [ ] Interfaz para agregar variables antes de ejecutar el plugin
- [ ] Añadir a la galería de PKP

> [!NOTA]
> Este proyecto concluye el **31 de diciembre de 2025** y no se continuará su desarrollo hasta obtener nuevos recursos.

---

## **Notas**

- Versión de OJS: 3.4 y 3.5  
- Plugin a nivel de sitio: la configuración se aplica a todas las revistas/prensas  
- Licencia: GNU GPL v3. Véase el archivo LICENSE para más información  
- Soporte: [GitHub Issues](https://github.com/MejorAbierta/ARTSPlugin/issues)  
- Contacto: [servicio.publicaciones@urjc.es](mailto:servicio.publicaciones@urjc.es)
```
