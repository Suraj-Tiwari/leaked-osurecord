<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<!--
browser/anyterm.html
This file is part of Anyterm; see http://anyterm.org/
(C) 2005-2007 Philip Endecott

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
-->

<html>
<head>
<title>Anyterm</title>

<script type="text/javascript" src="anyterm.js">
</script>

<script type="text/javascript">
  // To create the terminal, just call create_term.  The paramters are:
  //  - The id of a <div> element that will become the terminal.
  //  - The title.  %h and %v expand to the hostname and Anyterm version.
  //  - The number of rows and columns.
  //  - An optional parameter which is substituted for %p in the command string.
  //  - An optional character set.
  //  - An option number of lines of scrollback (default 0).

  // So the following creates an 80x25 terminal with 50 lines of scrollback:
  window.onload=function() {create_term("term","Live status display",25,80,"","",50);};

  // You might want to be able to control these settings, and you can write any
  // JavaScript code you want here to do so.  A common requirement is to be able
  // to control the terminal dimensions from the page's URL.  So you might have
  // another HTML page with links like this:
  // <a href="anyterm.html?rows=25&cols=80">Small terminal</a>
  // <a href="anyterm.html?rows=60&cols=180">Large terminal</a>
  // To make that work you need to extract those parameters from the page's URL
  // and pass them to your call to create_term().  There's a function in
  // anyterm.js to make this easier, as follows:
  // var rows = get_url_param("rows",25);
  // var cols = get_url_param("cols",80);
  // window.onload=function() {create_term("term","Terminal",rows,cols,"","",0);};
  // The second parameter to get_url_param is a default.
  //
  // Here's another example, passing the name of the host to connect to from a
  // URL paramter to the %p parameter:
  // <a href="anyterm.html?host=foo">Connect to foo</a>
  // var host = get_url_param("host","default_host");
  // window.onload=function() {create_term("term",host,25,80,host,"",0);};
  // Use this with a command like:
  //    anytermd -c 'ssh %p'
  // You probably only want to do that with trusted users and HTTP AUTH, of course.

  // When the user closes the terminal, by default they'll see a blank page.
  // Generally you'll want to be more friendly than that.  If you set the
  // variable on_close_goto_url to a URL, they'll be sent to that page after
  // closing.  You could send them back to your home page, or something.
  var on_close_goto_url = "";

</script>

<link rel="stylesheet" type="text/css" href="anyterm.css">

<style type='text/css'>
.term { font-size: 11px; font-family: "Consolas", "Courier New", monospace; }
</style>
</head>

<body>

<noscript>Javascript is essential for this page.  Please use a browser 
that supports Javascript.</noscript>

<div id="term"></div>

</body>
</html>
