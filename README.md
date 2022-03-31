# STIN
![PHPUnit](https://github.com/vvoleman/stin/actions/workflows/phpunit.yml/badge.svg)
![PHPStan](https://github.com/vvoleman/stin/actions/workflows/phpstan.yml/badge.svg)

Semestrální práce pro STIN 2021/22

## Specifikace požadavků
### Uživatelské požadavky
- **Ověření uživatele**
	- Uživatel je při vstupu na do aplikace požádán o ověřovací klíč
	- Pokud je klíč správný, je uživatel vpustěn
	- Pokud není, je uživateli zobrana chybová hláška
- **Komunikace s chatbotem**
	-  Uživatel do textového vstupu zadá svojí otázku a stisknutím tlačítka "Odeslat" u textového vstupu či tlačítka "Enter" na klávesnici je otázka odeslána
	- Otázka se zpracuje na serveru a vrátí do chatovacího okna odpověď
	- Server si nepamatuje předchozí otázky, nelze tedy vázat otázky na předchozí kontext
- **Otázky**
	- Mělo by být snadné přidávat další příkazy, i s parametrem
	- Aktuální otázky:
		- "Jaký je čas?"
			- Server vrátí aktuální čas
		- "Jak se jmenuješ?"
			- Server vrátí jméno chatbota
		- "Jaký je kurz /CURRENCY/?"
			- Vrátí aktuální kurz eura
			- Jako  /CURRENCY/ očekává server kód měny dle ISO 4217 (EUR, USD, CZK atd.)
### Systémové požadavky
