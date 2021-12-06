*** 
# MantisAP ( Mantis-Accesspoint )
***

## **Beschreibung**

Dieses Paket dient der Verwaltung von MantisBT-Instanzen per REST-Api durch PHP.

Folgende Funktionen stehen zur Verfügung:
- abrufen / erstellen / bearbeiten / löschen von Projekten
- abrufen / erstellen / bearbeiten / löschen von Fehlern


## **Installation**

composer require Cevra2010/MantisAp
composer dump-autoload

## **Beispiele**

#### Konfiguration vornehmen:

```
use MantisAP\MantisAP;
$mantisAP = new MantisAP('URL','TOKEN');
```


### Abfragen:

---

#### Alle Projekte abrufen

```
use MantisAP\Objects\MantisProject;
$projects = MantisProject::all();
```


#### Ein bestimmtes Projekt abrufen

```
use MantisAP\Objects\MantisProject;
$project = MantisProject::find(2);
```

#### Ein Projekt ändern und speichern
```
use MantisAP\Objects\MantisProject;
$project->name = "Test-Projekt";
$project->save();
```

#### Einen bestimmten Fehler abrufen

```
use MantisAP\Objects\MantisIssue;
$issue = MantisIssue::find(72);
```