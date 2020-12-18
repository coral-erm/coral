/*
**************************************************************************************************************************
** CORAL Resources Module Add-on
**
** Copyright (c) 2010 University of Notre Dame
**
** CORAL is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
**
** CORAL is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License along with CORAL.  If not, see <http://www.gnu.org/licenses/>.
**
**************************************************************************************************************************
*/

This is a simple public interface for CORAL. The public interface which we’ve been calling the PIG (Public Interface Generator).
It includes a simple search box and a results display.  There are a couple of links in the top-right of the screen:
the PIG link will bring up a popup window with a URL for the search results for embedding, the PIG XML link does the same thing only
with a link for an XML feed.

Installation:

Copy all the file and folders into the resources folder located in CORAL.

No CORAL files will be replaced additional files will be added to the resources folder and some of the sub-folders.

You should then be able to access them like any other webpage.

http://yourserver/yourcoralfolder/resources/pig.php
Is a search tool that lives outside of authentication and can be used for discovery.  It also provides a link to and XML output of search results along with a direct link which can be used to embed results in other applications.

http://yourserver/yourcoralfolder/resources/pig_search.php
Is a simple search box example that show how it can be embeded in other applications.

