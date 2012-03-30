## QR-Generator-PHP Refactoring project

It is my intention to take the original project, at 
https://github.com/edent/QR-Generator-PHP and refactor it into a 
library, with supporting files, suitable to use as-is (for example, 
as a git submodule in a vendor, or library directory, included 
and called from the parent project.

Directory layout
QR-Generator-PHP
  + library
    + QR
      + data
      + image
  README
  demo.php
  LICENCE, etc

### TODO
* Put createQR() function into library directory, within a class structure
* rebuild qr.php to use the new library, and serve as demo/live-web-producer
* Refactor code within the class
