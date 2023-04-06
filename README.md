# Crud forms generator for WaterWeb
Cruddy kan php forms genereren voor Database operator (Create, Read, Update en Delete). \
(Geplande) nieuwe functionaliteiten t.o.v. cruddiy
- [x] Laat colom comments zien bij velden
- [ ] Comments van een tabel toevoegen aan de paginas.
- [ ] Laat SQL errors zien op het scherm. 
- [ ] Foreign key doorverwijzen naar dat record.
- [ ] Vanuit de foreign key, laat records zien die deze key gebuiken.
- [x] Edit, delete knop maken op read pagina.
- [ ] Boolean / tinyint goed weergegeven. 
- [ ] SQL injections fixen.
- [ ] Formateer datum -> dd-mm-yyyy.
- [x] Sorteer standaard op id?
- [x] Count records met een count ipv `result.num_records()`.

## Setup
Clone deze repository in de root van WaterWeb, het zit al in de gitignore, dus waterweb heeft hier geen last van.

## Usage
1. Ga naar je webhost/cruddiy, daar zijn instructies. 
2. Zodra je nieuwe bestanden hebt. Vanuit de root, gebruik de volgende commands om nieuw gegenereerde crud bestanden over te zetten (Dit overschrijft de huidige)
```
cp -r cruddiy/core/app/ cruddiy/core/temp/
rm  cruddiy/core/temp/config.php cruddiy/core/temp/error.php cruddiy/core/temp/helpers.php cruddiy/core/temp/index.php cruddiy/core/temp/navbar.php
cp cruddiy/core/temp/* manager/modules/crud/
rm -r cruddiy/core/temp
```

### CRUDDIY
(FORK FROM CRUDDIY)
Cruddiy is a free **no-code**  PHP Bootstrap CRUD generator with foreign key support.

With Cruddiy you can easily generate some simple, but beautiful, PHP Bootstrap 4 CRUD pages (Create, Read, Update and Delete) with search, pagination and foreign key awareness.
