Drupal blackbox module:
------------------------
Maintainers:
  Benjamin Reisz (https://www.drupal.org/u/benjbmc)
Requires - Drupal 7
License - GPL (see LICENSE)


Overview:
--------
Blackbox is a simple block containing an entity of your choice which appears after a certain time.
What you can change : entity displayed, time before appearing, size of your box, possibility to show a phone picto to call the popup manually.

This module is using the Colorbox module :
"Colorbox is a light-weight, customizable lightbox plugin for jQuery 1.4.3+.
This module allows for integration of Colorbox into Drupal.
The jQuery library is a part of Drupal since version 5+.
Images, iframed or inline content etc. can be displayed in a
overlay above the current page."

Features:
---------

* Responsive, by using the Colorbox module
* Easy to install, just see instructions below


Installation:
------------
1/ Configuration in /admin/config/media/colorbox : "Enable Colorbox inline"

2/ Check permissions in /admin/people/permissions :
    Blackbox
      View iframe embed code
      Allow users to view the iframe embed / link code on this domain.

      Access iframe version
      Allow users to view the iframe version of an entity

3/ Check that the "Blackbox block" is assigned to the "content" region in /admin/structure/block

4/ Flush cache