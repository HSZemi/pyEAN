pyEAN
=====

A python and a PHP script to generate an EAN barcode as an svg file.

Python
-----

Usage: ./makebarcode.py [EAN]

Example: ./makebarcode 0037600175340

Barcode is generated as barcode.svg and is overwritten if it already exists.

PHP
---

Usage: makebarcode.php?code=[EAN] to display code in the browser;
       makebarcode.php?code=[EAN]?dl=1 to download it as barcode.svg

Example: makebarcode.php?code=0037600175340
         makebarcode.php?code=0037600175340?dl=1
